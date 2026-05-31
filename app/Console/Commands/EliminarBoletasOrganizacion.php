<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Boleta;
use Illuminate\Support\Facades\DB;

class EliminarBoletasOrganizacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boletas:eliminar-organizacion {organizacion_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eliminar todas las boletas de consumo de una organización específica';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $organizacionId = $this->argument('organizacion_id');

        // Confirmar acción
        $this->warn("⚠️  ADVERTENCIA: Esta acción eliminará TODAS las boletas de la organización ID: {$organizacionId}");

        // Contar boletas antes de eliminar
        $totalBoletas = Boleta::where('id_organizacion', $organizacionId)->count();

        if ($totalBoletas === 0) {
            $this->info("✅ No se encontraron boletas para la organización ID: {$organizacionId}");
            return Command::SUCCESS;
        }

        $this->info("📊 Total de boletas a eliminar: {$totalBoletas}");

        if (!$this->confirm('¿Está seguro de que desea eliminar todas estas boletas? Esta acción NO se puede deshacer.')) {
            $this->info('❌ Operación cancelada por el usuario.');
            return Command::FAILURE;
        }

        // Eliminar boletas
        try {
            DB::beginTransaction();

            $eliminadas = Boleta::where('id_organizacion', $organizacionId)->delete();

            DB::commit();

            $this->info("✅ Se eliminaron exitosamente {$eliminadas} boletas de la organización ID: {$organizacionId}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error al eliminar boletas: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
