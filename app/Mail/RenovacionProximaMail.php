<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\RenovacionSuscripcion;

class RenovacionProximaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $renovacion;
    public $diasRestantes;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(RenovacionSuscripcion $renovacion, $diasRestantes)
    {
        $this->renovacion = $renovacion;
        $this->diasRestantes = $diasRestantes;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $diasTexto = $this->diasRestantes == 1 ? '1 día' : "$this->diasRestantes días";

        return new Envelope(
            subject: "Tu suscripción vence en $diasTexto - Sistema APR",
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
            view: 'emails.renovacion-proxima',
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
