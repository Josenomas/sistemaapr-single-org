<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\RegistroOrganizacion;

class VerificacionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $registro;

    /**
     * Create a new message instance.
     */
    public function __construct(RegistroOrganizacion $registro)
    {
        $this->registro = $registro;
    }

    /**
     * Get the message envelope.
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Verifica tu cuenta - Sistema APR',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        return new Content(
            view: 'emails.verificacion-email',
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
