<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionMail;
use App\Models\Notificacion;
use App\Models\Socio;

class EnviarNotificacionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $notificacion;
    public $socio;
    public $tries = 3; // Reintentos
    public $timeout = 120; // Timeout en segundos

    /**
     * Create a new job instance.
     */
    public function __construct(Notificacion $notificacion, Socio $socio)
    {
        $this->notificacion = $notificacion;
        $this->socio = $socio;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            // Verificar que el socio tenga email
            if (!$this->socio->email) {
                \Log::warning("Socio {$this->socio->id} no tiene email configurado");
                $this->notificacion->increment('total_errores');
                return;
            }

            // Enviar el correo
            Mail::to($this->socio->email)->send(new NotificacionMail($this->notificacion, $this->socio));

            // Actualizar estadísticas
            $this->notificacion->increment('total_enviados');
            $this->notificacion->update(['enviado_email' => true]);

            \Log::info("Email enviado a {$this->socio->email} - Notificación {$this->notificacion->id}");

        } catch (\Exception $e) {
            // Incrementar contador de errores
            $this->notificacion->increment('total_errores');

            \Log::error("Error al enviar email a {$this->socio->email}: " . $e->getMessage());

            // Si es el último intento, marcar como fallido
            if ($this->attempts() >= $this->tries) {
                \Log::error("Falló después de {$this->tries} intentos");
            }

            throw $e; // Re-lanzar para que Laravel maneje los reintentos
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        \Log::error("Job falló completamente para socio {$this->socio->id}: " . $exception->getMessage());
        $this->notificacion->increment('total_errores');
    }
}
