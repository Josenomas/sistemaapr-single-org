<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class RecuperarPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function envelope()
    {
        return new Envelope(
            from: new Address('cuenta@sistemaapr.cl', 'Sistema APR'),
            subject: 'Recuperar Contraseña - Sistema APR',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.recuperar-password',
        );
    }

    public function attachments()
    {
        return [];
    }
}
