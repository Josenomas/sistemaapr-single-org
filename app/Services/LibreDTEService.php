<?php

namespace App\Services;

use App\Models\Boleta;
use App\Models\ConfiguracionDTE;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

        // Descargar y guardar PDF localmente
        $pdfLocalPath = null;
        if (isset($response['pdf'])) {
            $pdfLocalPath = $this->descargarYGuardarPDF($response['pdf'], $boleta);
        }

        // Actualizar boleta con los datos del DTE
        $boleta->update([
            'tipo_dte' => 39, // Boleta Electrónica
            'estado_dte' => 'emitida',
            'folio_sii' => $response['folio'] ?? null,
            'xml_dte' => $response['xml'] ?? null,
            'pdf_url' => $response['pdf'] ?? null,
            'pdf_local_path' => $pdfLocalPath,
            'fecha_emision_dte' => now(),
        ]);

        return $response;
    }

    /**
     * Preparar estructura de datos para boleta electrónica
     */
    protected function prepararDTEBoleta(Boleta $boleta)
    {
        $socio = $boleta->socio;
        $organizacion = $boleta->organizacion;

        // Determinar si es boleta nominativa o no nominativa
        $rutReceptor = $socio->rut ?? '66666666-6'; // RUT genérico para boletas no nominativas
        $nombreReceptor = $socio->nombre_completo ?? 'Consumidor Final';

        return [
            'Encabezado' => [
                'IdDoc' => [
                    'TipoDTE' => 39, // Boleta Electrónica
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
                'Receptor' => [
                    'RUTRecep' => $this->limpiarRut($rutReceptor),
                    'RznSocRecep' => $nombreReceptor,
                    'DirRecep' => $socio->direccion ?? 'Sin dirección',
                    'CmnaRecep' => $socio->comuna ?? $this->config->comuna,
                ],
                'Totales' => [
                    'MntNeto' => (int) round($boleta->total / 1.19), // Sin IVA
                    'TasaIVA' => 19,
                    'IVA' => (int) round($boleta->total - ($boleta->total / 1.19)),
                    'MntTotal' => (int) $boleta->total,
                ],
            ],
            'Detalle' => [
                [
                    'NroLinDet' => 1,
                    'NmbItem' => "Consumo de Agua Potable - {$boleta->mes_texto}",
                    'DscItem' => "Consumo: {$boleta->consumo_m3} m³",
                    'QtyItem' => 1,
                    'PrcItem' => (int) $boleta->total,
                    'MontoItem' => (int) $boleta->total,
                ],
            ],
        ];
    }

    /**
     * Enviar DTE a la API de LibreDTE
     */
    protected function enviarDTE(array $dte)
    {
        try {
            $url = rtrim($this->config->libredte_url, '/') . '/api/dte/documentos/emitir';

            if (config('libredte.log_requests')) {
                Log::info('LibreDTE Request', ['url' => $url, 'data' => $dte]);
            }

            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config->libredte_hash,
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
            $url = rtrim($this->config->libredte_url, '/') . '/api/dte/documentos/estado';

            $response = $this->client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config->libredte_hash,
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
            $url = rtrim($this->config->libredte_url, '/') . '/api/ping';

            $response = $this->client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config->libredte_hash,
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
     */
    public function obtenerFoliosDisponibles()
    {
        if (!$this->config) {
            throw new \Exception('Debe configurar la organización primero usando setOrganizacion()');
        }

        try {
            // Consultar folios para Boleta Electrónica (tipo 39)
            $url = rtrim($this->config->libredte_url, '/') . '/api/dte/documentos/disponibles';

            $response = $this->client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config->libredte_hash,
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
    }

    /**
     * Descargar PDF desde LibreDTE y guardarlo localmente
     */
    protected function descargarYGuardarPDF($pdfUrl, Boleta $boleta)
    {
        try {
            // Descargar PDF desde LibreDTE
            $pdfContent = $this->client->get($pdfUrl)->getBody()->getContents();

            // Crear estructura de directorios: dtes/YYYY/MM/
            $año = now()->format('Y');
            $mes = now()->format('m');
            $directorio = "dtes/{$año}/{$mes}";

            // Crear directorio si no existe
            if (!Storage::exists($directorio)) {
                Storage::makeDirectory($directorio, 0755, true);
            }

            // Nombre del archivo: DTE_[tipo]_[folio]_[id_boleta].pdf
            $nombreArchivo = sprintf(
                'DTE_%s_F%s_B%s.pdf',
                $boleta->tipo_dte ?? 39,
                $boleta->folio_sii ?? 'PENDING',
                $boleta->id
            );

            $rutaCompleta = "{$directorio}/{$nombreArchivo}";

            // Guardar PDF en storage/app/dtes/
            Storage::put($rutaCompleta, $pdfContent);

            Log::info('PDF DTE guardado localmente', [
                'boleta_id' => $boleta->id,
                'ruta' => $rutaCompleta,
                'tamaño' => strlen($pdfContent) . ' bytes',
            ]);

            return $rutaCompleta;

        } catch (\Exception $e) {
            Log::error('Error al guardar PDF localmente', [
                'boleta_id' => $boleta->id,
                'pdf_url' => $pdfUrl,
                'error' => $e->getMessage(),
            ]);

            // No lanzar excepción - el PDF ya está en LibreDTE
            return null;
        }
    }
}
