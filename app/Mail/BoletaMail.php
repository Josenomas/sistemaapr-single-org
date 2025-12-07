<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Boleta;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $ultimoPago = \DB::table('pagos')
            ->where('id_socio', $this->boleta->id_socio)
            ->orderBy('fecha_pago', 'desc')
            ->first();

        // Obtener boletas pendientes/vencidas (deuda)
        $boletasPendientes = \App\Models\Boleta::activos()
            ->where('id_socio', $this->boleta->id_socio)
            ->whereIn('estado', ['pendiente', 'vencida'])
            ->orderBy('mes', 'asc')
            ->get();

        $totalAdeudado = $boletasPendientes->sum('total');
        $mesesAdeudados = $boletasPendientes->count();

        // Generar PDF de la boleta
        $pdf = PDF::loadView('boletas.pdf', [
            'boleta' => $this->boleta,
            'historialConsumo' => $historialConsumo,
            'ultimoPago' => $ultimoPago,
            'boletasPendientes' => $boletasPendientes,
            'totalAdeudado' => $totalAdeudado,
            'mesesAdeudados' => $mesesAdeudados
        ]);

        return $this->subject('Boleta de Agua N° ' . $this->boleta->numero_boleta . ' - ' . $this->boleta->mes_texto)
                    ->view('emails.boleta')
                    ->with([
                        'boleta' => $this->boleta,
                        'socio' => $this->boleta->socio
                    ])
                    ->attachData($pdf->output(), 'boleta_' . $this->boleta->numero_boleta . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
