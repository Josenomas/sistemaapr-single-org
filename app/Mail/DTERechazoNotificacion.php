<?php

namespace App\Mail;

use App\Models\Boleta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DTERechazoNotificacion extends Mailable
{
    use Queueable, SerializesModels;

    public $boleta;
    public $glosa;

    /**
     * Create a new message instance.
     */
    public function __construct(Boleta $boleta, $glosa = null)
    {
        $this->boleta = $boleta;
        $this->glosa = $glosa;
    }

    /**
     * Get the message envelope.
     */
    public function envelope()
    {
        return new Envelope(
            subject: '⚠️ DTE Rechazado por SII - Folio ' . $this->boleta->folio_sii,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        return new Content(
            view: 'emails.dte-rechazo',
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
