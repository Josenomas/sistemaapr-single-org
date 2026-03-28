<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\PagoSuscripcion;
use App\Models\Organizacion;

class ConfirmacionPagoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pago;
    public $organizacion;

    /**
     * Create a new message instance.
     */
    public function __construct(PagoSuscripcion $pago, Organizacion $organizacion)
    {
        $this->pago = $pago;
        $this->organizacion = $organizacion;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('✅ Pago Confirmado - Comprobante #' . $this->pago->id)
                    ->view('emails.confirmacion-pago')
                    ->with([
                        'pago' => $this->pago,
                        'organizacion' => $this->organizacion,
                    ]);
    }
}
