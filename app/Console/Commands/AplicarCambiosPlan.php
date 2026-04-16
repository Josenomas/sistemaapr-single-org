<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CambioPlan;
use App\Models\Organizacion;
use Carbon\Carbon;

class AplicarCambiosPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cambios-plan:aplicar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aplicar cambios de plan pendientes (downgrades) al final del período de facturación';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔄 Verificando cambios de plan pendientes...');

        // Buscar todos los downgrades pendientes
        $cambiosPendientes = CambioPlan::with(['organizacion', 'suscripcionNueva', 'suscripcionAnterior'])
            ->where('tipo', 'downgrade')
            ->where('estado', 'pendiente')
            ->where('activo', 1)
            ->get();

        if ($cambiosPendientes->isEmpty()) {
            $this->info('   ✅ No hay cambios de plan pendientes');
            return Command::SUCCESS;
        }

        $this->info("   📋 Encontrados {$cambiosPendientes->count()} cambios pendientes");

        $aplicados = 0;

        foreach ($cambiosPendientes as $cambio) {
            $organizacion = $cambio->organizacion;

            // Verificar si ya llegó la fecha de fin de suscripción
            if (!$organizacion->fecha_fin_suscripcion) {
                $this->warn("   ⚠️  {$organizacion->nombre_apr}: Sin fecha_fin_suscripcion, omitiendo");
                continue;
            }

            $fechaFin = Carbon::parse($organizacion->fecha_fin_suscripcion);

            // Si la fecha de fin ya pasó o es hoy, aplicar el cambio
            if ($fechaFin->isPast() || $fechaFin->isToday()) {
                $this->info("   → Aplicando downgrade para {$organizacion->nombre_apr}");
                $this->info("      Plan: {$cambio->suscripcionAnterior->nombre} → {$cambio->suscripcionNueva->nombre}");

                // Aplicar el cambio
                if ($cambio->aplicar()) {
                    $this->line("      ✅ Cambio aplicado exitosamente");
                    $aplicados++;
                } else {
                    $this->error("      ✗ Error al aplicar cambio");
                }
            } else {
                $diasRestantes = now()->diffInDays($fechaFin);
                $this->line("   → {$organizacion->nombre_apr}: Aún faltan {$diasRestantes} días para aplicar");
            }
        }

        if ($aplicados > 0) {
            $this->info("   ✅ Total aplicados: {$aplicados}");
        } else {
            $this->info("   ℹ️  No hay cambios listos para aplicar hoy");
        }

        return Command::SUCCESS;
    }
}
