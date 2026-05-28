<?php

namespace App\Services;

use App\Models\Boleta;
use App\Models\ConfiguracionDTE;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\PdfExportService;

class SimpleFacturaService
{
    protected $client;
    protected $config;
    protected $token;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('simplefactura.url', 'https://api.simplefactura.cl'),
            'timeout' => config('simplefactura.timeout', 30),
            'verify' => true,
        ]);
    }

    /**
     * Configurar servicio para una organización específica
     */
    public function setOrganizacion($idOrganizacion)
    {
        $this->config = ConfiguracionDTE::where('id_organizacion', $idOrganizacion)
            ->where('activo', true)
            ->first();

        if (!$this->config || !$this->config->estaConfigurado()) {
            throw new \Exception('SimpleFactura no está configurado para esta organización');
        }

        // Autenticar y obtener token JWT
        $this->autenticar();

        return $this;
    }

    /**
     * Autenticar con SimpleFactura y obtener token JWT
     */
    protected function autenticar()
    {
        // Cache key único por organización
        $cacheKey = "simplefactura_token_{$this->config->id_organizacion}";

        // Cachear token por 23 horas (válido 24h)
        $this->token = Cache::remember($cacheKey, 82800, function () {
            try {
                // Usar credenciales de BD o fallback a .env
                $username = $this->config->simplefactura_usuario ?? config('simplefactura.username');
                $password = $this->config->simplefactura_password
                    ? decrypt($this->config->simplefactura_password)
                    : config('simplefactura.password');

                $response = $this->client->post('/authentication', [
                    'json' => [
                        'username' => $username,
                        'password' => $password,
                    ],
                ]);

                $body = json_decode($response->getBody()->getContents(), true);

                if (!isset($body['data'])) {
                    throw new \Exception('No se pudo obtener token de SimpleFactura');
                }

                Log::info('SimpleFactura: Token JWT obtenido', [
                    'organizacion' => $this->config->id_organizacion,
                ]);

                return $body['data']; // Token JWT

            } catch (GuzzleException $e) {
                Log::error('SimpleFactura: Error en autenticación', [
                    'error' => $e->getMessage(),
                ]);
                throw new \Exception('Error al autenticar con SimpleFactura: ' . $e->getMessage());
            }
        });
    }

    /**
     * Emitir boleta electrónica usando SimpleFactura
     */
    public function emitirBoleta(Boleta $boleta)
    {
        if (!$this->config || !$this->token) {
            throw new \Exception('Debe configurar la organización primero usando setOrganizacion()');
        }

        // Preparar datos del DTE
        $dte = $this->prepararDTEBoleta($boleta);

        // Enviar a SimpleFactura
        $response = $this->enviarDTE($dte);

        // Extraer timbre y TED del XML de respuesta
        $timbreBase64 = $this->extraerTimbreDesdeXML($response['data']['dte'] ?? null);
        $ted = $this->extraerTEDDesdeXML($response['data']['dte'] ?? null);

        // Actualizar boleta con datos legales (folio, timbre, ted, xml)
        $boleta->update([
            'tipo_dte' => $dte['Encabezado']['IdDoc']['TipoDTE'] ?? 39,
            'estado_dte' => 'emitida',
            'folio_sii' => $response['data']['folio'] ?? null,
            'xml_dte' => $response['data']['dte'] ?? null,
            'timbre_base64' => $timbreBase64,
            'ted' => $ted,
            'fecha_emision_dte' => now(),
        ]);

        // Generar y guardar PDF unificado con timbre del SII
        $pdfPath = $this->generarYGuardarPDFUnificado($boleta->fresh());

        if ($pdfPath) {
            $boleta->update(['pdf_personalizado_path' => $pdfPath]);
        }

        return $response;
    }

    /**
     * Preparar estructura de datos para boleta/factura/nota electrónica
     */
    protected function prepararDTEBoleta(Boleta $boleta)
    {
        $socio = $boleta->socio;
        $organizacion = $boleta->organizacion;

        // Determinar tipo de DTE
        if ($boleta->tipo_dte) {
            $tipoDTE = $boleta->tipo_dte;
            $esFactura = in_array($tipoDTE, [33, 56, 61]);
        } else {
            $esFactura = !empty($boleta->rut_receptor);
            $tipoDTE = $esFactura ? 33 : 39;
        }

        // Datos del receptor
        if ($esFactura) {
            $rutReceptor = $boleta->rut_receptor;
            $nombreReceptor = $boleta->razon_social_receptor;
            $giroReceptor = $boleta->giro_receptor ?? 'Sin especificar';
            $direccionReceptor = $boleta->direccion_receptor ?? ($socio->direccion ?? 'Sin dirección');
            $comunaReceptor = $boleta->comuna_receptor ?? ($socio->comuna ?? $this->config->comuna);
        } else {
            $rutReceptor = $socio->rut ?? '66666666-6';
            $nombreReceptor = $socio->nombre_completo ?? 'Consumidor Final';
            $giroReceptor = null;
            $direccionReceptor = $socio->direccion ?? 'Sin dirección';
            $comunaReceptor = $socio->comuna ?? $this->config->comuna;
        }

        $dte = [
            'Encabezado' => [
                'IdDoc' => [
                    'TipoDTE' => $tipoDTE,
                    'Folio' => 0, // 0 = asignación automática
                    'FchEmis' => now()->format('Y-m-d'),
                ],
                'Emisor' => [
                    'RUTEmisor' => $this->limpiarRut($this->config->rut_emisor),
                    'RznSoc' => $this->config->razon_social,
                    'GiroEmis' => $this->config->giro,
                    'Acteco' => '360000',
                    'DirOrigen' => $this->config->direccion_casa_matriz,
                    'CmnaOrigen' => $this->config->comuna,
                    'CiudadOrigen' => $this->config->ciudad,
                ],
                'Receptor' => array_filter([
                    'RUTRecep' => $this->limpiarRut($rutReceptor),
                    'RznSocRecep' => $nombreReceptor,
                    'GiroRecep' => $esFactura ? $giroReceptor : null,
                    'DirRecep' => $direccionReceptor,
                    'CmnaRecep' => $comunaReceptor,
                ]),
                'Totales' => [
                    'MntNeto' => (int) round(($boleta->monto_nota ?? $boleta->total) / 1.19),
                    'TasaIVA' => 19,
                    'IVA' => (int) round(($boleta->monto_nota ?? $boleta->total) - (($boleta->monto_nota ?? $boleta->total) / 1.19)),
                    'MntTotal' => (int) ($boleta->monto_nota ?? $boleta->total),
                ],
            ],
            'Detalle' => [
                [
                    'NroLinDet' => 1,
                    'NmbItem' => $this->obtenerNombreItem($boleta),
                    'DscItem' => $this->obtenerDescripcionItem($boleta),
                    'QtyItem' => 1,
                    'PrcItem' => (int) ($boleta->monto_nota ?? $boleta->total),
                    'MontoItem' => (int) ($boleta->monto_nota ?? $boleta->total),
                ],
            ],
        ];

        // Agregar referencia para notas de crédito/débito
        if (in_array($tipoDTE, [56, 61]) && $boleta->boletaReferencia) {
            $docRef = $boleta->boletaReferencia;
            $dte['Referencia'] = [
                [
                    'NroLinRef' => 1,
                    'TpoDocRef' => $docRef->tipo_dte ?? 39,
                    'FolioRef' => $docRef->folio_sii,
                    'FchRef' => $docRef->fecha_emision_dte->format('Y-m-d'),
                    'CodRef' => $tipoDTE == 61 ? 1 : 2,
                    'RazonRef' => $boleta->motivo_nota ?? 'Corrección de documento',
                ],
            ];
        }

        return $dte;
    }

    /**
     * Enviar DTE a la API de SimpleFactura
     */
    protected function enviarDTE(array $dte)
    {
        try {
            $response = $this->client->post('/dte/emitir', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'credenciales' => [
                        'rutEmisor' => $this->limpiarRut($this->config->rut_emisor),
                        'nombreSucursal' => 'Casa Matriz',
                    ],
                    'dte' => $dte,
                    'certificado' => $this->obtenerCertificado(),
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (config('simplefactura.log_requests')) {
                Log::info('SimpleFactura Response', ['response' => $body]);
            }

            if ($body['status'] !== 200) {
                throw new \Exception($body['message'] ?? 'Error desconocido de SimpleFactura');
            }

            return $body;

        } catch (GuzzleException $e) {
            Log::error('SimpleFactura Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            throw new \Exception('Error al comunicarse con SimpleFactura: ' . $e->getMessage());
        }
    }

    /**
     * Obtener certificado digital
     */
    protected function obtenerCertificado()
    {
        if (!$this->config->certificado_digital) {
            throw new \Exception('No hay certificado digital configurado');
        }

        // El certificado YA está en base64 en la BD
        return [
            'certificado' => $this->config->certificado_digital,
            'password' => decrypt($this->config->certificado_password),
        ];
    }

    /**
     * Generar y guardar PDF unificado (boleta interna + timbre SII)
     */
    protected function generarYGuardarPDFUnificado(Boleta $boleta)
    {
        try {
            $pdfService = new PdfExportService();
            $pdf = $pdfService->boleta($boleta, $boleta->organizacion);

            $año = now()->format('Y');
            $mes = now()->format('m');
            $directorio = "boletas/{$año}/{$mes}";

            if (!Storage::exists($directorio)) {
                Storage::makeDirectory($directorio, 0755, true);
            }

            $nombreArchivo = sprintf(
                'boleta_%s_F%s_%s.pdf',
                $boleta->id,
                $boleta->folio_sii ?? 'PENDING',
                now()->timestamp
            );

            $rutaCompleta = "{$directorio}/{$nombreArchivo}";
            Storage::put($rutaCompleta, $pdf->output());

            Log::info('PDF unificado guardado', [
                'boleta_id' => $boleta->id,
                'folio_sii' => $boleta->folio_sii,
                'ruta' => $rutaCompleta,
            ]);

            return $rutaCompleta;

        } catch (\Exception $e) {
            Log::error('Error al generar PDF unificado', [
                'boleta_id' => $boleta->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Extraer imagen del timbre desde XML DTE (TED)
     */
    protected function extraerTimbreDesdeXML($xml)
    {
        if (!$xml) {
            return null;
        }

        try {
            $xmlObj = simplexml_load_string($xml);

            $ted = null;
            if (isset($xmlObj->TED)) {
                $ted = $xmlObj->TED->asXML();
            } elseif (isset($xmlObj->Signature->Object->TED)) {
                $ted = $xmlObj->Signature->Object->TED->asXML();
            }

            if (!$ted) {
                Log::warning('No se encontró TED en el XML de SimpleFactura');
                return null;
            }

            return $this->generarImagenPDF417($ted);

        } catch (\Exception $e) {
            Log::error('Error extrayendo timbre de XML', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Generar imagen PNG del código PDF417 desde el TED
     */
    protected function generarImagenPDF417($tedXML)
    {
        try {
            $pdf = new \TCPDF('P', 'mm', array(80, 30), true, 'UTF-8', false);
            $pdf->SetPrintHeader(false);
            $pdf->SetPrintFooter(false);
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false, 0);
            $pdf->AddPage();

            $style = [
                'border' => 0,
                'vpadding' => 0,
                'hpadding' => 0,
                'fgcolor' => array(0, 0, 0),
                'bgcolor' => false,
                'module_width' => 1,
                'module_height' => 1,
            ];

            $pdf->write2DBarcode($tedXML, 'PDF417', 0, 0, 80, 30, $style, 'N');
            $imageData = $pdf->Output('', 'S');

            if (extension_loaded('imagick')) {
                $imagick = new \Imagick();
                $imagick->setResolution(300, 300);
                $imagick->readImageBlob($imageData);
                $imagick->setImageFormat('png');
                $imagick->setImageBackgroundColor('white');
                $imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $pngData = $imagick->getImageBlob();
                $imagick->clear();
                $imagick->destroy();

                return base64_encode($pngData);
            }

            return base64_encode($imageData);

        } catch (\Exception $e) {
            Log::error('Error generando imagen PDF417', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Extraer TED completo desde XML
     */
    protected function extraerTEDDesdeXML($xml)
    {
        if (!$xml) {
            return null;
        }

        try {
            $xmlObj = simplexml_load_string($xml);

            if (isset($xmlObj->TED)) {
                return $xmlObj->TED->asXML();
            } elseif (isset($xmlObj->Signature->Object->TED)) {
                return $xmlObj->Signature->Object->TED->asXML();
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error extrayendo TED de XML', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Obtener nombre del item según tipo de documento
     */
    protected function obtenerNombreItem(Boleta $boleta)
    {
        if ($boleta->esNotaCredito()) {
            return "Nota de Crédito - Consumo de Agua Potable";
        } elseif ($boleta->esNotaDebito()) {
            return "Nota de Débito - Cargo Adicional";
        }
        return "Consumo de Agua Potable - {$boleta->mes_texto}";
    }

    /**
     * Obtener descripción del item según tipo de documento
     */
    protected function obtenerDescripcionItem(Boleta $boleta)
    {
        if ($boleta->esNota()) {
            return $boleta->motivo_nota ?? 'Corrección de documento';
        }
        return "Consumo: {$boleta->consumo_m3} m³";
    }

    /**
     * Limpiar RUT (quitar puntos, dejar solo guión)
     */
    protected function limpiarRut($rut)
    {
        return str_replace('.', '', $rut);
    }

    /**
     * Verificar conexión con SimpleFactura
     */
    public function verificarConexion()
    {
        try {
            $this->autenticar();
            return !empty($this->token);
        } catch (\Exception $e) {
            return false;
        }
    }
}
