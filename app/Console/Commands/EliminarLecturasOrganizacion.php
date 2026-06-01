<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lectura;
use Illuminate\Support\Facades\DB;

class EliminarLecturasOrganizacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lecturas:eliminar-organizacion {organizacion_id} {--mes= : Mes específico (formato YYYY-MM)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eliminar lecturas de una organización (opcionalmente filtrar por mes)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $organizacionId = $this->argument('organizacion_id');
        $mes = $this->option('mes');

        // Construir query
        $query = Lectura::where('id_organizacion', $organizacionId);

        if ($mes) {
            $query->where('mes', $mes);
            $this->warn("⚠️  ADVERTENCIA: Esta acción eliminará las lecturas de {$mes} para la organización ID: {$organizacionId}");
        } else {
            $this->warn("⚠️  ADVERTENCIA: Esta acción eliminará TODAS las lecturas de la organización ID: {$organizacionId}");
        }

        // Contar lecturas antes de eliminar
        $totalLecturas = $query->count();

        if ($totalLecturas === 0) {
            $mensaje = $mes
                ? "✅ No se encontraron lecturas para {$mes} en la organización ID: {$organizacionId}"
                : "✅ No se encontraron lecturas para la organización ID: {$organizacionId}";
            $this->info($mensaje);
            return Command::SUCCESS;
        }

        $this->info("📊 Total de lecturas a eliminar: {$totalLecturas}");

        // Mostrar detalle por mes
        if (!$mes) {
            $lecturasporMes = Lectura::where('id_organizacion', $organizacionId)
                ->select('mes', DB::raw('COUNT(*) as total'))
                ->groupBy('mes')
                ->orderBy('mes', 'desc')
                ->get();

            $this->table(['Mes', 'Cantidad'], $lecturasporMes->map(function($item) {
                return [$item->mes, $item->total];
            }));
        }

        if (!$this->confirm('¿Está seguro de que desea eliminar estas lecturas? Esta acción NO se puede deshacer.')) {
            $this->info('❌ Operación cancelada por el usuario.');
            return Command::FAILURE;
        }

        // Eliminar lecturas
        try {
            DB::beginTransaction();

            $eliminadas = $query->delete();

            DB::commit();

            $mensaje = $mes
                ? "✅ Se eliminaron exitosamente {$eliminadas} lecturas de {$mes} para la organización ID: {$organizacionId}"
                : "✅ Se eliminaron exitosamente {$eliminadas} lecturas de la organización ID: {$organizacionId}";

            $this->info($mensaje);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error al eliminar lecturas: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
