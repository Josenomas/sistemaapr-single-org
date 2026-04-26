<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;
use App\Models\Socio;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $socio;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, Socio $socio)
    {
        $this->ticket = $ticket;
        $this->socio = $socio;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('tickets@sistemaapr.cl', 'Sistema APR - Tickets'),
            subject: 'Ticket Registrado #' . $this->ticket->numero_ticket . ' - Sistema APR',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
