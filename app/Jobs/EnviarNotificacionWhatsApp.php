<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Notificacion;
use App\Models\Socio;
use App\Services\WhatsAppService;

class EnviarNotificacionWhatsApp implements ShouldQueue
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
            // Verificar que el socio tenga teléfono
            if (!$this->socio->telefono) {
                \Log::warning("Socio {$this->socio->id} no tiene teléfono configurado");
                $this->notificacion->increment('total_errores');
                return;
            }

            // Crear instancia del servicio de WhatsApp
            $whatsappService = new WhatsAppService();

            // Preparar mensaje
            $mensaje = "*{$this->notificacion->titulo}*\n\n";
            $mensaje .= $this->notificacion->mensaje . "\n\n";
            $mensaje .= "_Enviado por Sistema APR_";

            // Enviar mensaje
            $resultado = $whatsappService->enviarMensaje(
                $this->socio->telefono,
                $mensaje
            );

            if ($resultado['success']) {
                // Actualizar estadísticas
                $this->notificacion->increment('total_enviados');
                $this->notificacion->update(['enviado_whatsapp' => true]);

                \Log::info("WhatsApp enviado a {$this->socio->telefono} - Notificación {$this->notificacion->id}");
            } else {
                throw new \Exception($resultado['error'] ?? 'Error desconocido al enviar WhatsApp');
            }

        } catch (\Exception $e) {
            // Incrementar contador de errores
            $this->notificacion->increment('total_errores');

            \Log::error("Error al enviar WhatsApp a {$this->socio->telefono}: " . $e->getMessage());

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
        \Log::error("Job de WhatsApp falló completamente para socio {$this->socio->id}: " . $exception->getMessage());
        $this->notificacion->increment('total_errores');
    }
}
