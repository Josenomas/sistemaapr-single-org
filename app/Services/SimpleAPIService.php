<?php

namespace App\Services;

use App\Models\Boleta;
use App\Models\ConfiguracionDTE;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SimpleAPIService
{
    protected $client;
    protected $config;
    protected $apiKey;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->apiKey = config('simpleapi.api_key');

        $this->client = new Client([
            'base_uri' => config('simpleapi.url'),
            'timeout' => config('simpleapi.timeout', 30),
            'verify' => true,
            'headers' => [
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
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
            throw new \Exception('SimpleAPI no está configurado para esta organización');
        }

        return $this;
    }

    /**
     * Emitir boleta electrónica usando SimpleAPI
     */
    public function emitirBoleta(Boleta $boleta)
    {
        if (!$this->config) {
            throw new \Exception('Debe configurar la organización primero usando setOrganizacion()');
        }

        // Preparar datos del DTE
        $dte = $this->prepararDTEBoleta($boleta);

        // Enviar a SimpleAPI
        $response = $this->enviarDTE($dte);

        // Descargar y guardar PDF localmente
        $pdfLocalPath = null;
        if (isset($response['pdfBase64'])) {
            $pdfLocalPath = $this->guardarPDFDesdeBase64($response['pdfBase64'], $boleta);
        }

        // Actualizar boleta con los datos del DTE
        $boleta->update([
            'tipo_dte' => $dte['Encabezado']['IdDoc']['TipoDTE'] ?? 39,
            'estado_dte' => 'emitida',
            'folio_sii' => $response['folio'] ?? null,
            'xml_dte' => $response['xml'] ?? null,
            'pdf_url' => null, // SimpleAPI no proporciona URL, solo base64
            'pdf_local_path' => $pdfLocalPath,
            'fecha_emision_dte' => now(),
        ]);

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

        // Detalles de la boleta
        $detalles = [];
        $descripcion = $boleta->descripcion ?? 'Consumo de agua mes ' . now()->format('m/Y');

        $detalles[] = [
            'NroLinDet' => 1,
            'NmbItem' => $descripcion,
            'QtyItem' => 1,
            'PrcItem' => (int)round($boleta->monto),
            'MontoItem' => (int)round($boleta->monto),
        ];

        // Estructura completa del DTE según formato SimpleAPI
        return [
            'Encabezado' => [
                'IdDoc' => [
                    'TipoDTE' => $tipoDTE,
                    'Folio' => 0, // SimpleAPI asigna automáticamente desde CAF
                    'FchEmis' => now()->format('Y-m-d'),
                ],
                'Emisor' => [
                    'RUTEmisor' => $this->config->rut_empresa,
                    'RznSoc' => $this->config->razon_social,
                    'GiroEmis' => $this->config->giro,
                    'Acteco' => $this->config->actividad_economica,
                    'DirOrigen' => $this->config->direccion,
                    'CmnaOrigen' => $this->config->comuna,
                ],
                'Receptor' => [
                    'RUTRecep' => $rutReceptor,
                    'RznSocRecep' => $nombreReceptor,
                    'GiroRecep' => $giroReceptor,
                    'DirRecep' => $direccionReceptor,
                    'CmnaRecep' => $comunaReceptor,
                ],
                'Totales' => [
                    'MntNeto' => $esFactura ? (int)round($boleta->monto / 1.19) : null,
                    'TasaIVA' => $esFactura ? 19 : null,
                    'IVA' => $esFactura ? (int)round($boleta->monto - ($boleta->monto / 1.19)) : null,
                    'MntTotal' => (int)round($boleta->monto),
                ],
            ],
            'Detalle' => $detalles,
        ];
    }

    /**
     * Enviar DTE a SimpleAPI
     */
    protected function enviarDTE($dte)
    {
        try {
            $certificado = $this->obtenerCertificado();
            $ambiente = config('simpleapi.ambiente') === 'produccion' ? 0 : 1;

            $payload = [
                'dte' => $dte,
                'ambiente' => $ambiente,
                'certificado' => $certificado,
            ];

            if (config('simpleapi.log_requests')) {
                Log::info('SimpleAPI Request', [
                    'endpoint' => '/dte/generar',
                    'payload' => $payload
                ]);
            }

            $response = $this->client->post('/api/v1/dte/generar', [
                'json' => $payload
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (config('simpleapi.log_requests')) {
                Log::info('SimpleAPI Response', ['response' => $body]);
            }

            if (!isset($body['folio'])) {
                throw new \Exception('Error al emitir DTE: ' . ($body['mensaje'] ?? 'Respuesta inválida'));
            }

            return $body;

        } catch (GuzzleException $e) {
            Log::error('SimpleAPI Error', [
                'message' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null
            ]);
            throw new \Exception('Error de conexión con SimpleAPI: ' . $e->getMessage());
        }
    }

    /**
     * Obtener certificado digital desde configuración
     */
    protected function obtenerCertificado()
    {
        if (!$this->config->certificado_digital) {
            throw new \Exception('No hay certificado digital configurado');
        }

        return [
            'certificado' => base64_encode(Storage::get($this->config->certificado_digital)),
            'password' => decrypt($this->config->password_certificado),
        ];
    }

    /**
     * Guardar PDF desde base64
     */
    protected function guardarPDFDesdeBase64($pdfBase64, Boleta $boleta)
    {
        try {
            $filename = "boletas/pdf/boleta_{$boleta->id}_" . time() . ".pdf";
            $pdfContent = base64_decode($pdfBase64);

            Storage::disk('public')->put($filename, $pdfContent);

            return $filename;
        } catch (\Exception $e) {
            Log::error('Error guardando PDF: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Anular DTE emitiendo Nota de Crédito
     */
    public function anularBoleta(Boleta $boletaOriginal, $motivo = 'Anulación de documento')
    {
        if (!$this->config) {
            throw new \Exception('Debe configurar la organización primero');
        }

        if (!$boletaOriginal->folio_sii) {
            throw new \Exception('La boleta no tiene folio SII asignado');
        }

        // Crear nota de crédito
        $notaCredito = new Boleta();
        $notaCredito->id_organizacion = $boletaOriginal->id_organizacion;
        $notaCredito->id_socio = $boletaOriginal->id_socio;
        $notaCredito->monto = $boletaOriginal->monto;
        $notaCredito->descripcion = $motivo;
        $notaCredito->tipo_dte = 61; // Nota de Crédito
        $notaCredito->save();

        // Preparar datos de nota de crédito con referencia
        $dte = $this->prepararDTEBoleta($notaCredito);

        // Agregar referencias al documento original
        $dte['Referencia'] = [
            [
                'NroLinRef' => 1,
                'TpoDocRef' => $boletaOriginal->tipo_dte,
                'FolioRef' => $boletaOriginal->folio_sii,
                'FchRef' => $boletaOriginal->fecha_emision_dte->format('Y-m-d'),
                'CodRef' => 1, // 1 = Anula documento
                'RazonRef' => $motivo,
            ]
        ];

        // Enviar a SimpleAPI
        $response = $this->enviarDTE($dte);

        // Actualizar nota de crédito
        $notaCredito->update([
            'estado_dte' => 'emitida',
            'folio_sii' => $response['folio'] ?? null,
            'xml_dte' => $response['xml'] ?? null,
            'fecha_emision_dte' => now(),
        ]);

        // Actualizar boleta original
        $boletaOriginal->update([
            'estado_dte' => 'anulada',
        ]);

        return $response;
    }

    /**
     * Verificar estado del DTE en el SII
     */
    public function verificarEstadoSII($folio, $tipoDTE = 39)
    {
        try {
            $response = $this->client->get('/api/v1/dte/estado', [
                'query' => [
                    'rut' => $this->config->rut_empresa,
                    'tipo_dte' => $tipoDTE,
                    'folio' => $folio,
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'estado' => $body['estado'] ?? 'desconocido',
                'glosa' => $body['glosa'] ?? 'Sin información',
                'fecha_recepcion' => $body['fecha_recepcion'] ?? null,
            ];

        } catch (GuzzleException $e) {
            Log::error('Error verificando estado SII', [
                'folio' => $folio,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Error al verificar estado en SII');
        }
    }

    /**
     * Obtener folios disponibles
     */
    public function obtenerFoliosDisponibles($tipoDTE = 39)
    {
        try {
            $response = $this->client->get('/api/v1/folios/disponibles', [
                'query' => [
                    'rut' => $this->config->rut_empresa,
                    'tipo_dte' => $tipoDTE,
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'disponibles' => $body['disponibles'] ?? 0,
                'desde' => $body['desde'] ?? null,
                'hasta' => $body['hasta'] ?? null,
            ];

        } catch (GuzzleException $e) {
            Log::error('Error consultando folios', ['error' => $e->getMessage()]);
            return ['disponibles' => 0];
        }
    }
}
