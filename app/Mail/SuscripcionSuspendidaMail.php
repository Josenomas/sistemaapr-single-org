<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\RenovacionSuscripcion;

class SuscripcionSuspendidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $renovacion;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(RenovacionSuscripcion $renovacion)
    {
        $this->renovacion = $renovacion;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Tu suscripción ha sido suspendida - Sistema APR',
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
            view: 'emails.suscripcion-suspendida',
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
