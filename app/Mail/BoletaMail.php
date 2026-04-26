<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Boleta;
use Illuminate\Support\Facades\DB;

class BoletaMail extends Mailable
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
        $ultimoPago = DB::table('pagos')
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

        // Generar PDF con wkhtmltopdf
        $boleta = $this->boleta;
        $html = view('boletas.pdf_new', compact('boleta', 'historialConsumo', 'ultimoPago', 'boletasPendientes', 'totalAdeudado', 'mesesAdeudados'))->render();

        // Guardar HTML temporal
        $tempHtmlPath = public_path('temp_boleta_email_' . $this->boleta->id . '_' . time() . '.html');
        file_put_contents($tempHtmlPath, $html);

        // Generar PDF
        $pdfPath = storage_path('app/temp_boleta_email_' . $this->boleta->id . '_' . time() . '.pdf');

        // Detectar sistema operativo y usar ruta correcta
        $wkhtmltopdfPath = PHP_OS_FAMILY === 'Windows'
            ? '"C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe"'
            : '/usr/bin/wkhtmltopdf';

        // Convertir path a formato file://
        $fileUrl = 'file:///' . str_replace('\\', '/', $tempHtmlPath);

        $command = $wkhtmltopdfPath . ' --enable-local-file-access --page-size Letter "' . $fileUrl . '" "' . $pdfPath . '" 2>&1';
        exec($command, $output, $returnCode);

        // Leer el PDF generado
        if (!file_exists($pdfPath)) {
            throw new \Exception('Error al generar PDF para email: ' . implode("\n", $output));
        }

        $pdfContent = file_get_contents($pdfPath);

        // Eliminar archivos temporales
        @unlink($tempHtmlPath);
        @unlink($tempHtmlPath);
        @unlink($pdfPath);

        return $this->from("boletas@sistemaapr.cl", "Sistema APR - Boletas")
                    ->subject("Boleta de Agua N° " . $this->boleta->numero_boleta . " - " . $this->boleta->mes_texto)
                    ->view('emails.boleta')
                    ->with([
                        'boleta' => $this->boleta,
                        'socio' => $this->boleta->socio
                    ])
                    ->attachData($pdfContent, 'boleta_' . $this->boleta->numero_boleta . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
