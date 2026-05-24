<?php

namespace App\Mail;

use App\Models\Boleta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class DTEEmitidoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $boleta;
    public $organizacion;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Boleta $boleta)
    {
        $this->boleta = $boleta->load('socio', 'organizacion');
        $this->organizacion = $boleta->organizacion;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $tipoDoc = $this->boleta->tipo_documento;
        $folio = $this->boleta->folio_sii;

        return new Envelope(
            subject: "{$tipoDoc} N° {$folio} - {$this->organizacion->nombre}",
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
            view: 'emails.dte-emitido',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        $attachments = [];

        // Adjuntar PDF del DTE si existe
        if ($this->boleta->pdf_url && file_exists(storage_path('app/public/' . $this->boleta->pdf_url))) {
            $attachments[] = Attachment::fromPath(storage_path('app/public/' . $this->boleta->pdf_url))
                ->as("DTE_{$this->boleta->folio_sii}.pdf")
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}
