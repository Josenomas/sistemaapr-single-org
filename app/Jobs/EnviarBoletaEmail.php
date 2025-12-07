<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\Boleta;
use App\Mail\BoletaMail;

class EnviarBoletaEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $boleta;

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
                throw new \Exception('El socio no tiene email registrado');
            }

            // Enviar el email
            Mail::to($this->boleta->socio->email)
                ->send(new BoletaMail($this->boleta));

        } catch (\Exception $e) {
            // Registrar el error
            \Log::error('Error al enviar boleta por email: ' . $e->getMessage(), [
                'boleta_id' => $this->boleta->id,
                'socio_id' => $this->boleta->socio->id ?? null
            ]);

            throw $e;
        }
    }
}
