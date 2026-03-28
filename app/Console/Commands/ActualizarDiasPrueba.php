<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Organizacion;
use Carbon\Carbon;

class ActualizarDiasPrueba extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suscripciones:actualizar-dias-prueba';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los días de prueba restantes para organizaciones en período de prueba';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Actualizando días de prueba...');

        // Obtener todas las organizaciones en prueba
        $organizacionesEnPrueba = Organizacion::where('estado_suscripcion', 'prueba')
            ->where('dias_prueba_restantes', '>', 0)
            ->get();

        $this->info("Encontradas {$organizacionesEnPrueba->count()} organizaciones en prueba");

        $actualizadas = 0;
        $vencidas = 0;

        foreach ($organizacionesEnPrueba as $org) {
            // Calcular días transcurridos desde el inicio de la suscripción
            $diasTranscurridos = Carbon::parse($org->fecha_inicio_suscripcion)->diffInDays(Carbon::today());
            $diasRestantes = max(0, 30 - $diasTranscurridos);

            // Actualizar días restantes
            $org->dias_prueba_restantes = $diasRestantes;

            // Si se acabó la prueba, cambiar estado a vencida
            if ($diasRestantes <= 0) {
                $org->estado_suscripcion = 'vencida';
                $vencidas++;
                $this->warn("  → {$org->nombre_apr} - Prueba VENCIDA");
            } else {
                $this->line("  → {$org->nombre_apr} - {$diasRestantes} días restantes");
            }

            $org->save();
            $actualizadas++;
        }

        $this->info("\n✓ Proceso completado:");
        $this->info("  - {$actualizadas} organizaciones actualizadas");
        $this->info("  - {$vencidas} organizaciones vencidas");

        return Command::SUCCESS;
    }
}
