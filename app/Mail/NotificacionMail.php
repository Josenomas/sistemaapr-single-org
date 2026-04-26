<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use App\Models\Notificacion;
use App\Models\Socio;

class NotificacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notificacion;
    public $socio;

    /**
     * Create a new message instance.
     */
    public function __construct(Notificacion $notificacion, Socio $socio)
    {
        $this->notificacion = $notificacion;
        $this->socio = $socio;
    }

    /**
     * Get the message envelope.
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address('notificaciones@sistemaapr.cl', 'Sistema APR - Notificaciones'),
            subject: $this->notificacion->titulo,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        return new Content(
            view: 'emails.notificacion',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments()
    {
        return [];
    }
}
