<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use App\Models\RenovacionSuscripcion;

class RenovacionProximaMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $renovacion;
    public $diasRestantes;

    public function __construct(RenovacionSuscripcion $renovacion, $diasRestantes)
    {
        $this->renovacion = $renovacion;
        $this->diasRestantes = $diasRestantes;
    }

    public function envelope(): Envelope
    {
        $urgencia = $this->diasRestantes <= 3 ? '⚠️ URGENTE - ' : '';
        return new Envelope(
            from: new Address('suscripciones@sistemaapr.cl', 'Sistema APR - Suscripciones'),
            subject: "{$urgencia}Renovación de suscripción próxima en {$this->diasRestantes} días - Sistema APR",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.renovacion-proxima',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
