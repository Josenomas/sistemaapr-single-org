<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Ticket;
use App\Models\Socio;
use App\Mail\TicketMail;

class EnviarTicketEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ticket;
    public $socio;

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
    public function __construct(Ticket $ticket, Socio $socio)
    {
        $this->ticket = $ticket;
        $this->socio = $socio;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Verificar que el socio tenga email
            if (!$this->socio->email) {
                Log::warning("El socio {$this->socio->id} no tiene email configurado para recibir ticket");
                return;
            }

            // Enviar el email
            Mail::to($this->socio->email)->send(new TicketMail($this->ticket, $this->socio));

            Log::info("Email de ticket enviado exitosamente al socio {$this->socio->id}");

        } catch (\Exception $e) {
            Log::error("Error al enviar email de ticket al socio {$this->socio->id}: " . $e->getMessage());
            throw $e; // Relanzar para que se reintente
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job de envío de email de ticket falló para socio {$this->socio->id}: " . $exception->getMessage());
    }
}
