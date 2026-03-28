<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Pago;

class ReciboPagoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pago;
    public $organizacion;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Pago $pago)
    {
        $this->pago = $pago;
        $this->organizacion = auth()->user()->organizacion ?? null;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $nombreOrg = $this->organizacion ? $this->organizacion->nombre_apr : 'Sistema APR';

        return new Envelope(
            subject: "Recibo de Pago #{$this->pago->numero_recibo} - {$nombreOrg}",
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
            view: 'emails.recibo-pago',
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
