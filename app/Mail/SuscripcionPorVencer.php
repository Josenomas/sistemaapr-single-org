<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\PagoSuscripcion;

class SuscripcionPorVencer extends Mailable
{
    use Queueable, SerializesModels;

    public $pago;
    public $dias;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PagoSuscripcion $pago, $dias = 7)
    {
        $this->pago = $pago;
        $this->dias = $dias;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $urgencia = $this->dias <= 3 ? '⚠️ URGENTE - ' : '';

        return new Envelope(
            subject: "{$urgencia}Tu suscripción vence en {$this->dias} días - Sistema APR",
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.suscripcion-por-vencer',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
