<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Boleta;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BoletaDTEMail extends Mailable
{
    use Queueable, SerializesModels;

    public $boleta;

    /**
     * Create a new message instance.
     */
    public function __construct(Boleta $boleta)
    {
        $this->boleta = $boleta;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Verificar que tenga PDF timbrado
        if (!$this->boleta->pdf_url) {
            Log::warning("Boleta {$this->boleta->id} no tiene PDF timbrado para enviar por email");
            throw new \Exception('La boleta no tiene PDF timbrado disponible');
        }

        try {
            // 1. Descargar PDF timbrado desde LibreDTE (DTE oficial)
            $pdfDteContent = $this->descargarPdfTimbrado($this->boleta->pdf_url);

            // 2. Generar PDF completo (tu diseño bonito con gráficos)
            $pdfDetalleContent = $this->generarPdfDetallado();

            $tipoDte = $this->boleta->tipo_dte == 39 ? 'Boleta Electrónica' : 'Documento Tributario Electrónico';

            // Nombres de archivos
            $nombreDte = 'DTE_' . $this->boleta->numero_boleta . '_F' . $this->boleta->folio_sii . '.pdf';
            $nombreDetalle = 'Detalle_Boleta_' . $this->boleta->numero_boleta . '.pdf';

            return $this->from(config('mail.from.address'), config('mail.from.name'))
                        ->subject("{$tipoDte} N° {$this->boleta->numero_boleta} - {$this->boleta->mes_texto}")
                        ->view('emails.boleta-dte')
                        ->with([
                            'boleta' => $this->boleta,
                            'socio' => $this->boleta->socio,
                            'tipoDte' => $tipoDte
                        ])
                        // Adjuntar PDF detallado (TU diseño) - primero
                        ->attachData($pdfDetalleContent, $nombreDetalle, [
                            'mime' => 'application/pdf',
                        ])
                        // Adjuntar PDF timbrado (LibreDTE) - segundo
                        ->attachData($pdfDteContent, $nombreDte, [
                            'mime' => 'application/pdf',
                        ]);

        } catch (\Exception $e) {
            Log::error("Error al generar email de DTE para boleta {$this->boleta->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generar PDF detallado con tu diseño (gráficos, historial, deuda)
     */
    private function generarPdfDetallado()
    {
        try {
            // Obtener historial de consumo del socio (últimos 12 meses)
            $historialConsumo = \App\Models\HistorialConsumo::where('id_socio', $this->boleta->id_socio)
                ->where('activo', 1)
                ->orderBy('periodo', 'desc')
                ->limit(12)
                ->get()
                ->reverse()
                ->values()
                ->map(function($h) {
                    $fecha = \Carbon\Carbon::createFromFormat('Y-m', $h->periodo);
                    return [
                        'mes' => $h->periodo,
                        'mes_texto' => $fecha->locale('es')->isoFormat('MMMM Y'),
                        'consumo' => $h->consumo_m3
                    ];
                });

            // Obtener último pago realizado
            $ultimoPago = \DB::table('pagos')
                ->where('id_socio', $this->boleta->id_socio)
                ->orderBy('fecha_pago', 'desc')
                ->first();

            // Obtener boletas pendientes/vencidas (deuda)
            $boletasPendientes = \App\Models\Boleta::activos()
                ->where('id_socio', $this->boleta->id_socio)
                ->whereIn('estado', ['pendiente', 'vencida'])
                ->with('pagos')
                ->orderBy('mes', 'asc')
                ->get();

            // Calcular total adeudado considerando pagos parciales
            $totalAdeudado = 0;
            foreach ($boletasPendientes as $boletaPendiente) {
                $totalPagado = $boletaPendiente->pagos->sum('monto_pagado');
                $saldoPendiente = $boletaPendiente->total - $totalPagado;
                $totalAdeudado += $saldoPendiente;
            }
            $mesesAdeudados = $boletasPendientes->count();

            // Generar HTML con tu vista bonita
            $html = view('boletas.pdf_new', compact(
                'boleta',
                'historialConsumo',
                'ultimoPago',
                'boletasPendientes',
                'totalAdeudado',
                'mesesAdeudados'
            ))->with('boleta', $this->boleta)->render();

            // Guardar HTML temporal
            $tempHtmlPath = storage_path('app/temp/boleta_detalle_' . $this->boleta->id . '_' . time() . '.html');

            // Crear directorio si no existe
            if (!file_exists(dirname($tempHtmlPath))) {
                mkdir(dirname($tempHtmlPath), 0755, true);
            }

            file_put_contents($tempHtmlPath, $html);

            // Generar PDF con wkhtmltopdf
            $pdfPath = storage_path('app/temp/boleta_detalle_' . $this->boleta->id . '_' . time() . '.pdf');

            // Detectar sistema operativo y usar ruta correcta
            $wkhtmltopdfPath = PHP_OS_FAMILY === 'Windows'
                ? '"C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe"'
                : '/usr/bin/wkhtmltopdf';

            // Convertir path a formato file://
            $fileUrl = 'file:///' . str_replace('\\', '/', $tempHtmlPath);

            $command = $wkhtmltopdfPath . ' --enable-local-file-access --page-size Letter "' . $fileUrl . '" "' . $pdfPath . '" 2>&1';
            exec($command, $output, $returnCode);

            // Verificar que el PDF se generó
            if (!file_exists($pdfPath)) {
                Log::error('Error al generar PDF detallado para email DTE', [
                    'boleta_id' => $this->boleta->id,
                    'command' => $command,
                    'output' => $output,
                    'return_code' => $returnCode
                ]);
                throw new \Exception('Error al generar PDF detallado: ' . implode("\n", $output));
            }

            // Leer el PDF generado
            $pdfContent = file_get_contents($pdfPath);

            // Eliminar archivos temporales
            @unlink($tempHtmlPath);
            @unlink($pdfPath);

            return $pdfContent;

        } catch (\Exception $e) {
            Log::error("Error al generar PDF detallado: " . $e->getMessage(), [
                'boleta_id' => $this->boleta->id,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception("Error al generar PDF detallado: " . $e->getMessage());
        }
    }

    /**
     * Descargar PDF timbrado desde LibreDTE o URL externa
     */
    private function descargarPdfTimbrado($url)
    {
        try {
            // Intentar descargar con HTTP client
            $response = Http::timeout(30)->get($url);

            if ($response->successful()) {
                return $response->body();
            }

            // Fallback: usar file_get_contents
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'user_agent' => 'Sistema APR',
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ]);

            $content = file_get_contents($url, false, $context);

            if ($content === false) {
                throw new \Exception("No se pudo descargar el PDF desde: {$url}");
            }

            return $content;

        } catch (\Exception $e) {
            Log::error("Error al descargar PDF timbrado: " . $e->getMessage(), [
                'url' => $url,
                'boleta_id' => $this->boleta->id
            ]);
            throw new \Exception("Error al descargar PDF timbrado: " . $e->getMessage());
        }
    }
}
