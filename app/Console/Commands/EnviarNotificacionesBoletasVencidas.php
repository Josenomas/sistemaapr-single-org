<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Boleta;
use App\Models\Socio;
use App\Mail\BoletaVencidaMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionesBoletasVencidas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificaciones:boletas-vencidas {--test : Modo de prueba (no envía correos reales)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía notificaciones por correo electrónico a socios con boletas vencidas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Buscando boletas vencidas...');

        $testMode = $this->option('test');

        if ($testMode) {
            $this->warn('⚠️  MODO DE PRUEBA: No se enviarán correos reales');
        }

        // Obtener solo las boletas que vencieron AYER (para enviar notificación solo 1 vez)
        $ayer = now()->subDay()->startOfDay();
        $boletasVencidas = Boleta::activos()
            ->with('socio')
            ->where('estado', 'vencida')
            ->whereDate('fecha_vencimiento', '=', $ayer->toDateString())
            ->get()
            ->groupBy('id_socio');

        if ($boletasVencidas->isEmpty()) {
            $this->info('✅ No hay boletas vencidas para notificar');
            return 0;
        }

        $this->info("📧 Se encontraron {$boletasVencidas->count()} socios con boletas vencidas");

        $enviados = 0;
        $errores = 0;
        $sinEmail = 0;

        $this->output->progressStart($boletasVencidas->count());

        foreach ($boletasVencidas as $idSocio => $boletas) {
            $socio = $boletas->first()->socio;

            // Verificar que el socio tenga email
            if (empty($socio->email)) {
                $this->output->progressAdvance();
                $sinEmail++;
                Log::warning("Socio {$socio->numero_socio} sin email registrado");
                continue;
            }

            // Verificar que el email sea válido
            if (!filter_var($socio->email, FILTER_VALIDATE_EMAIL)) {
                $this->output->progressAdvance();
                $sinEmail++;
                Log::warning("Socio {$socio->numero_socio} con email inválido: {$socio->email}");
                continue;
            }

            try {
                if (!$testMode) {
                    // Enviar correo real
                    Mail::to($socio->email)->send(new BoletaVencidaMail($socio, $boletas));

                    Log::info("Notificación enviada a {$socio->numero_socio} - {$socio->nombre_completo} ({$socio->email}) - {$boletas->count()} boleta(s)");
                } else {
                    // Modo de prueba: solo simular
                    $this->line("  [TEST] Enviaría a: {$socio->email} - {$boletas->count()} boleta(s) vencida(s)");
                }

                $enviados++;
            } catch (\Exception $e) {
                $errores++;
                Log::error("Error al enviar notificación a {$socio->numero_socio}: {$e->getMessage()}");
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        // Resumen
        $this->newLine(2);
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 RESUMEN DE ENVÍO');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line("✅ Enviados exitosamente: <fg=green>{$enviados}</>");
        $this->line("❌ Errores: <fg=red>{$errores}</>");
        $this->line("⚠️  Sin email válido: <fg=yellow>{$sinEmail}</>");
        $this->line("📧 Total procesados: " . ($enviados + $errores + $sinEmail));
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($testMode) {
            $this->newLine();
            $this->warn('⚠️  Esto fue una prueba. Para enviar correos reales, ejecuta sin --test');
        }

        return 0;
    }
}
