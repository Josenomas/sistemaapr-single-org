<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use App\Models\TicketRespuesta;

class RespuestaTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $respuesta;

    /**
     * Create a new message instance.
     */
    public function __construct(TicketRespuesta $respuesta)
    {
        $this->respuesta = $respuesta;
    }

    /**
     * Get the message envelope.
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address('tickets@sistemaapr.cl', 'Sistema APR - Tickets'),
            subject: 'Nueva Respuesta en Ticket #' . $this->respuesta->ticket->numero_ticket . ' - Sistema APR',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        return new Content(
            view: 'emails.respuesta-ticket',
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
