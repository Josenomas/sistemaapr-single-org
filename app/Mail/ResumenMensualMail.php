<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Organizacion;

class ResumenMensualMail extends Mailable
{
    use Queueable, SerializesModels;

    public $organizacion;
    public $stats;
    public $mes;
    public $anio;

    /**
     * Create a new message instance.
     */
    public function __construct(Organizacion $organizacion, array $stats, string $mes, int $anio)
    {
        $this->organizacion = $organizacion;
        $this->stats = $stats;
        $this->mes = $mes;
        $this->anio = $anio;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('📊 Resumen Mensual - ' . $this->mes . ' ' . $this->anio . ' - ' . $this->organizacion->nombre_apr)
                    ->view('emails.resumen-mensual')
                    ->with([
                        'organizacion' => $this->organizacion,
                        'stats' => $this->stats,
                        'mes' => $this->mes,
                        'anio' => $this->anio,
                    ]);
    }
}
