<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\Boleta;
use App\Mail\BoletaDTEMail;

class EnviarBoletaDTEEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $boleta;

    /**
     * Número de intentos
     */
    public $tries = 3;

    /**
     * Timeout del job
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(Boleta $boleta)
    {
        $this->boleta = $boleta;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            // Verificar que el socio tenga email
            if (!$this->boleta->socio || !$this->boleta->socio->email) {
                \Log::warning("Boleta DTE {$this->boleta->id} - Socio sin email", [
                    'boleta_id' => $this->boleta->id,
                    'socio_id' => $this->boleta->socio->id ?? null
                ]);
                return;
            }

            // Verificar que tenga PDF timbrado
            if (!$this->boleta->pdf_url) {
                \Log::warning("Boleta DTE {$this->boleta->id} - Sin PDF timbrado", [
                    'boleta_id' => $this->boleta->id
                ]);
                return;
            }

            // Enviar el email
            Mail::to($this->boleta->socio->email)
                ->send(new BoletaDTEMail($this->boleta));

            \Log::info("DTE enviado por email exitosamente", [
                'boleta_id' => $this->boleta->id,
                'numero_boleta' => $this->boleta->numero_boleta,
                'folio_sii' => $this->boleta->folio_sii,
                'email_destino' => $this->boleta->socio->email
            ]);

        } catch (\Exception $e) {
            // Registrar el error
            \Log::error('Error al enviar DTE por email: ' . $e->getMessage(), [
                'boleta_id' => $this->boleta->id,
                'socio_id' => $this->boleta->socio->id ?? null,
                'email' => $this->boleta->socio->email ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-lanzar excepción para que el job se reintente
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        \Log::error('Job de envío de DTE falló después de ' . $this->tries . ' intentos', [
            'boleta_id' => $this->boleta->id,
            'error' => $exception->getMessage()
        ]);
    }
}
