<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Socio;
use Illuminate\Support\Collection;

class BoletaVencidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $socio;
    public $boletasVencidas;
    public $totalAdeudado;
    public $diasVencimiento;

    /**
     * Create a new message instance.
     */
    public function __construct(Socio $socio, Collection $boletasVencidas)
    {
        $this->socio = $socio;
        $this->boletasVencidas = $boletasVencidas;
        $this->totalAdeudado = $boletasVencidas->sum('total');

        // Calcular días de vencimiento de la boleta más antigua
        $boletaMasAntigua = $boletasVencidas->sortBy('fecha_vencimiento')->first();
        $this->diasVencimiento = $boletaMasAntigua
            ? now()->diffInDays($boletaMasAntigua->fecha_vencimiento, false) * -1
            : 0;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $cantidadBoletas = $this->boletasVencidas->count();

        return $this->subject('⚠️ Recordatorio: ' . $cantidadBoletas . ' boleta' . ($cantidadBoletas > 1 ? 's' : '') . ' vencida' . ($cantidadBoletas > 1 ? 's' : '') . ' - APR Pitrelahue')
                    ->view('emails.boleta-vencida')
                    ->with([
                        'socio' => $this->socio,
                        'boletasVencidas' => $this->boletasVencidas,
                        'totalAdeudado' => $this->totalAdeudado,
                        'diasVencimiento' => $this->diasVencimiento,
                        'cantidadBoletas' => $cantidadBoletas
                    ]);
    }
}
