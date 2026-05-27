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

class LibreDTEService
{
    protected $client;
    protected $config;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->client = new Client([
            'timeout' => config('libredte.timeout', 30),
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
            throw new \Exception('LibreDTE no está configurado para esta organización');
        }

        return $this;
    }

    /**
     * Emitir boleta electrónica
     */
    public function emitirBoleta(Boleta $boleta)
    {
        if (!$this->config) {
            throw new \Exception('Debe configurar la organización primero usando setOrganizacion()');
        }

        // Preparar datos del DTE
        $dte = $this->prepararDTEBoleta($boleta);

        // Enviar a LibreDTE
        $response = $this->enviarDTE($dte);

        // Extraer timbre y TED del XML de respuesta
        $timbreBase64 = $this->extraerTimbreDesdeXML($response['xml'] ?? null);
        $ted = $this->extraerTEDDesdeXML($response['xml'] ?? null);

        // Actualizar boleta con datos legales (folio, timbre, ted, xml)
        $boleta->update([
            'tipo_dte' => 39, // Boleta Electrónica
            'estado_dte' => 'emitida',
            'folio_sii' => $response['folio'] ?? null,
            'xml_dte' => $response['xml'] ?? null,
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
            // Si ya tiene tipo asignado (notas de crédito/débito), usar ese
            $tipoDTE = $boleta->tipo_dte;
            $esFactura = in_array($tipoDTE, [33, 56, 61]); // Facturas y sus notas
        } else {
            // AUTO-DETECCIÓN: Si tiene RUT receptor → Factura (33), sino → Boleta (39)
            $esFactura = !empty($boleta->rut_receptor);
            $tipoDTE = $esFactura ? 33 : 39;
        }

        // Datos del receptor
        if ($esFactura) {
            // FACTURA: Usar datos del receptor desde la boleta
            $rutReceptor = $boleta->rut_receptor;
            $nombreReceptor = $boleta->razon_social_receptor;
            $giroReceptor = $boleta->giro_receptor ?? 'Sin especificar';
            $direccionReceptor = $boleta->direccion_receptor ?? ($socio->direccion ?? 'Sin dirección');
            $comunaReceptor = $boleta->comuna_receptor ?? ($socio->comuna ?? $this->config->comuna);
        } else {
            // BOLETA: Usar datos del socio o genéricos
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
                    'Folio' => 0, // 0 = asignación automática por LibreDTE
                    'FchEmis' => now()->format('Y-m-d'),
                ],
                'Emisor' => [
                    'RUTEmisor' => $this->limpiarRut($this->config->rut_emisor),
                    'RznSoc' => $this->config->razon_social,
                    'GiroEmis' => $this->config->giro,
                    'Acteco' => '360000', // Código actividad económica: Captación, depuración y distribución de agua
                    'DirOrigen' => $this->config->direccion_casa_matriz,
                    'CmnaOrigen' => $this->config->comuna,
                    'CiudadOrigen' => $this->config->ciudad,
                ],
                'Receptor' => array_filter([
                    'RUTRecep' => $this->limpiarRut($rutReceptor),
                    'RznSocRecep' => $nombreReceptor,
                    'GiroRecep' => $esFactura ? $giroReceptor : null, // Solo para facturas
                    'DirRecep' => $direccionReceptor,
                    'CmnaRecep' => $comunaReceptor,
                ]),
                'Totales' => [
                    'MntNeto' => (int) round(($boleta->monto_nota ?? $boleta->total) / 1.19), // Sin IVA
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

        // Si es una nota de crédito o débito, agregar referencia al documento original
        if (in_array($tipoDTE, [56, 61]) && $boleta->boletaReferencia) {
            $docRef = $boleta->boletaReferencia;
            $dte['Referencia'] = [
                [
                    'NroLinRef' => 1,
                    'TpoDocRef' => $docRef->tipo_dte ?? 39,
                    'FolioRef' => $docRef->folio_sii,
                    'FchRef' => $docRef->fecha_emision_dte->format('Y-m-d'),
                    'CodRef' => $tipoDTE == 61 ? 1 : 2, // 1=Anula documento, 2=Corrige monto
                    'RazonRef' => $boleta->motivo_nota ?? 'Corrección de documento',
                ],
            ];
        }

        return $dte;
    }

    /**
     * Enviar DTE a la API de LibreDTE
     */
    protected function enviarDTE(array $dte)
    {
        try {
            $url = rtrim($this->config->getUrlActiva(), '/') . '/api/dte/documentos/emitir';

            if (config('libredte.log_requests')) {
                Log::info('LibreDTE Request', ['url' => $url, 'data' => $dte, 'ambiente' => $this->config->ambiente]);
            }

            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config->getHashActivo(),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $dte,
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (config('libredte.log_requests')) {
                Log::info('LibreDTE Response', ['response' => $body]);
            }

            return $body;

        } catch (GuzzleException $e) {
            Log::error('LibreDTE Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            throw new \Exception('Error al comunicarse con LibreDTE: ' . $e->getMessage());
        }
    }

    /**
     * Consultar estado de un DTE en el SII
     */
    public function consultarEstado(Boleta $boleta)
    {
        if (!$boleta->tieneDTE()) {
            throw new \Exception('La boleta no tiene DTE emitido');
        }

        try {
            $url = rtrim($this->config->getUrlActiva(), '/') . '/api/dte/documentos/estado';

            $response = $this->client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config->getHashActivo(),
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'dte' => $boleta->tipo_dte,
                    'folio' => $boleta->folio_sii,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            Log::error('LibreDTE consultarEstado Error', [
                'message' => $e->getMessage(),
                'boleta_id' => $boleta->id,
            ]);

            throw new \Exception('Error al consultar estado: ' . $e->getMessage());
        }
    }

    /**
     * Anular un DTE (Nota de Crédito)
     */
    public function anularDocumento(Boleta $boleta, $motivo = 'Anulación por solicitud del cliente')
    {
        if (!$boleta->tieneDTE()) {
            throw new \Exception('La boleta no tiene DTE emitido para anular');
        }

        // Preparar Nota de Crédito
        $notaCredito = $this->prepararNotaCredito($boleta, $motivo);

        // Enviar a LibreDTE
        $response = $this->enviarDTE($notaCredito);

        // Marcar boleta como anulada
        $boleta->update([
            'estado_dte' => 'anulada',
            'estado' => 'anulada',
            'observaciones' => ($boleta->observaciones ?? '') . " | Anulada: {$motivo}",
        ]);

        return $response;
    }

    /**
     * Preparar Nota de Crédito para anular boleta
     */
    protected function prepararNotaCredito(Boleta $boleta, $motivo)
    {
        $socio = $boleta->socio;
        $rutReceptor = $socio->rut ?? '66666666-6';
        $nombreReceptor = $socio->nombre_completo ?? 'Consumidor Final';

        return [
            'Encabezado' => [
                'IdDoc' => [
                    'TipoDTE' => 61, // Nota de Crédito Electrónica
                    'Folio' => 0,
                    'FchEmis' => now()->format('Y-m-d'),
                ],
                'Emisor' => [
                    'RUTEmisor' => $this->limpiarRut($this->config->rut_emisor),
                    'RznSoc' => $this->config->razon_social,
                    'GiroEmis' => $this->config->giro,
                    'Acteco' => '360000',
                    'DirOrigen' => $this->config->direccion_casa_matriz,
                    'CmnaOrigen' => $this->config->comuna,
                ],
                'Receptor' => [
                    'RUTRecep' => $this->limpiarRut($rutReceptor),
                    'RznSocRecep' => $nombreReceptor,
                    'DirRecep' => $socio->direccion ?? 'Sin dirección',
                    'CmnaRecep' => $socio->comuna ?? $this->config->comuna,
                ],
                'Totales' => [
                    'MntNeto' => (int) round($boleta->total / 1.19),
                    'TasaIVA' => 19,
                    'IVA' => (int) round($boleta->total - ($boleta->total / 1.19)),
                    'MntTotal' => (int) $boleta->total,
                ],
                'Referencia' => [
                    [
                        'TpoDocRef' => $boleta->tipo_dte,
                        'FolioRef' => $boleta->folio_sii,
                        'FchRef' => $boleta->fecha_emision_dte->format('Y-m-d'),
                        'CodRef' => 1, // Anula Documento de Referencia
                        'RazonRef' => $motivo,
                    ],
                ],
            ],
            'Detalle' => [
                [
                    'NroLinDet' => 1,
                    'NmbItem' => "Anulación - Consumo de Agua Potable",
                    'QtyItem' => 1,
                    'PrcItem' => (int) $boleta->total,
                    'MontoItem' => (int) $boleta->total,
                ],
            ],
        ];
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
     * Verificar si LibreDTE está disponible
     */
    public function verificarConexion()
    {
        try {
            $url = rtrim($this->config->getUrlActiva(), '/') . '/api/ping';

            $response = $this->client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config->getHashActivo(),
                ],
                'timeout' => 10,
            ]);

            return $response->getStatusCode() === 200;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtener información de folios disponibles
     * Cache: 5 minutos para reducir llamadas a API LibreDTE
     */
    public function obtenerFoliosDisponibles()
    {
        if (!$this->config) {
            throw new \Exception('Debe configurar la organización primero usando setOrganizacion()');
        }

        // Cache key único por organización y ambiente
        $cacheKey = sprintf(
            'folios_disponibles_%s_%s',
            $this->config->id_organizacion,
            $this->config->ambiente
        );

        // Cache por 5 minutos (300 segundos)
        return Cache::remember($cacheKey, 300, function () {
            try {
                // Consultar folios para Boleta Electrónica (tipo 39)
                $url = rtrim($this->config->getUrlActiva(), '/') . '/api/dte/documentos/disponibles';

                $response = $this->client->get($url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->config->getHashActivo(),
                        'Accept' => 'application/json',
                    ],
                    'query' => [
                        'dte' => 39, // Boleta Electrónica
                    ],
                    'timeout' => 15,
                ]);

                $data = json_decode($response->getBody()->getContents(), true);

                // Estructura esperada de respuesta de LibreDTE
                return [
                    'tipo_dte' => 39,
                    'disponibles' => $data['disponibles'] ?? 0,
                    'siguiente' => $data['siguiente'] ?? null,
                    'alerta' => ($data['disponibles'] ?? 0) < 50,
                ];

            } catch (\Exception $e) {
                Log::error('Error al consultar folios LibreDTE', [
                    'error' => $e->getMessage(),
                ]);

                return [
                    'tipo_dte' => 39,
                    'disponibles' => null,
                    'siguiente' => null,
                    'alerta' => false,
                    'error' => $e->getMessage(),
                ];
            }
        });
    }

    /**
     * Verificar estado de un DTE en el SII a través de LibreDTE
     */
    public function verificarEstadoSII($folio, $tipoDTE = 39)
    {
        if (!$this->config) {
            throw new \Exception('Debe configurar la organización primero usando setOrganizacion()');
        }

        try {
            $url = rtrim($this->config->getUrlActiva(), '/') . '/api/dte/documentos/estado';

            $response = $this->client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config->getHashActivo(),
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'dte' => $tipoDTE,
                    'folio' => $folio,
                ],
                'timeout' => 20,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            // Estructura esperada: ['estado' => 'ACEPTADO' | 'RECHAZADO' | 'PENDIENTE', 'glosa' => '...']
            return [
                'success' => true,
                'estado' => $data['estado'] ?? 'PENDIENTE',
                'glosa' => $data['glosa'] ?? null,
                'fecha_verificacion' => now(),
            ];

        } catch (\Exception $e) {
            Log::error('Error al verificar estado DTE en SII', [
                'folio' => $folio,
                'tipo_dte' => $tipoDTE,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generar y guardar PDF unificado (boleta interna + timbre SII)
     */
    protected function generarYGuardarPDFUnificado(Boleta $boleta)
    {
        try {
            // Generar PDF usando el servicio de PDFs
            $pdfService = new PdfExportService();
            $pdf = $pdfService->boleta($boleta, $boleta->organizacion);

            // Crear estructura de directorios: boletas/YYYY/MM/
            $año = now()->format('Y');
            $mes = now()->format('m');
            $directorio = "boletas/{$año}/{$mes}";

            // Crear directorio si no existe
            if (!Storage::exists($directorio)) {
                Storage::makeDirectory($directorio, 0755, true);
            }

            // Nombre del archivo: boleta_[id]_F[folio]_[timestamp].pdf
            $nombreArchivo = sprintf(
                'boleta_%s_F%s_%s.pdf',
                $boleta->id,
                $boleta->folio_sii ?? 'PENDING',
                now()->timestamp
            );

            $rutaCompleta = "{$directorio}/{$nombreArchivo}";

            // Guardar PDF en storage/app/boletas/
            Storage::put($rutaCompleta, $pdf->output());

            Log::info('PDF unificado con timbre guardado', [
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
     * El TED contiene el código de barras PDF417 que debe mostrarse en el documento
     */
    protected function extraerTimbreDesdeXML($xml)
    {
        if (!$xml) {
            return null;
        }

        try {
            // Parsear XML
            $xmlObj = simplexml_load_string($xml);

            // Registrar namespace si existe
            $namespaces = $xmlObj->getNamespaces(true);

            // Extraer TED (Timbre Electrónico Digital)
            $ted = null;
            if (isset($xmlObj->TED)) {
                $ted = $xmlObj->TED->asXML();
            } elseif (isset($xmlObj->Signature->Object->TED)) {
                $ted = $xmlObj->Signature->Object->TED->asXML();
            }

            if (!$ted) {
                Log::warning('No se encontró TED en el XML de LibreDTE');
                return null;
            }

            // Generar imagen del timbre usando la librería chilean-bundle o similar
            // Por ahora retornamos null y el PDF mostrará el placeholder
            // TODO: Implementar generación de imagen PDF417 desde TED

            return null;

        } catch (\Exception $e) {
            Log::error('Error extrayendo timbre de XML', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Extraer TED (Timbre Electrónico Digital) completo desde XML
     */
    protected function extraerTEDDesdeXML($xml)
    {
        if (!$xml) {
            return null;
        }

        try {
            $xmlObj = simplexml_load_string($xml);

            // Buscar TED en diferentes ubicaciones posibles
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
}
