<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LinkPagoFlowMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $socio;
    public $boleta;
    public $linkPago;
    public $monto;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($socio, $boleta, $linkPago, $monto)
    {
        $this->socio = $socio;
        $this->boleta = $boleta;
        $this->linkPago = $linkPago;
        $this->monto = $monto;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Link de Pago - Sistema APR',
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
            view: 'emails.link-pago-flow',
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
