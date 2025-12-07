<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\TicketRespuesta;
use App\Mail\RespuestaTicketMail;

class EnviarRespuestaTicketEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $respuesta;
    public $destinatarioEmail;

    /**
     * Número de intentos de reintento
     */
    public $tries = 3;

    /**
     * Timeout en segundos
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(TicketRespuesta $respuesta, string $destinatarioEmail)
    {
        $this->respuesta = $respuesta;
        $this->destinatarioEmail = $destinatarioEmail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Verificar que el email sea válido
            if (!filter_var($this->destinatarioEmail, FILTER_VALIDATE_EMAIL)) {
                Log::warning("Email inválido para respuesta de ticket: {$this->destinatarioEmail}");
                return;
            }

            // Cargar relaciones necesarias
            $this->respuesta->load(['ticket', 'ticket.socio', 'usuario', 'socio']);

            // Enviar el email
            Mail::to($this->destinatarioEmail)->send(new RespuestaTicketMail($this->respuesta));

            // Marcar como notificado
            $this->respuesta->update(['notificado' => true]);

            Log::info("Email de respuesta de ticket enviado exitosamente a {$this->destinatarioEmail}");

        } catch (\Exception $e) {
            Log::error("Error al enviar email de respuesta de ticket: " . $e->getMessage());
            throw $e; // Relanzar para que se reintente
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job de envío de email de respuesta de ticket falló: " . $exception->getMessage());
    }
}
