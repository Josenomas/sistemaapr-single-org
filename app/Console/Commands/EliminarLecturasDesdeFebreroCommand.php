<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lectura;
use Illuminate\Support\Facades\DB;

class EliminarLecturasDesdeFebreroCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lecturas:eliminar-meses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina lecturas de julio y agosto 2025';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->warn('⚠️  ADVERTENCIA: Este comando eliminará las lecturas de julio y agosto 2025.');
        $this->warn('Esta acción NO se puede deshacer.');

        if (!$this->confirm('¿Estás seguro de continuar?', false)) {
            $this->info('Operación cancelada.');
            return Command::SUCCESS;
        }

        $this->info('Contando lecturas a eliminar...');

        $count = Lectura::whereIn('mes', ['2025-07', '2025-08'])->count();

        $this->info("Se encontraron {$count} lecturas de julio y agosto 2025.");

        if ($count === 0) {
            $this->info('No hay lecturas para eliminar.');
            return Command::SUCCESS;
        }

        if (!$this->confirm("¿Confirmas eliminar estas {$count} lecturas?", false)) {
            $this->info('Operación cancelada.');
            return Command::SUCCESS;
        }

        $this->info('Eliminando lecturas...');

        DB::beginTransaction();

        try {
            $deleted = Lectura::whereIn('mes', ['2025-07', '2025-08'])->delete();

            DB::commit();

            $this->info("✅ Se eliminaron {$deleted} lecturas correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error al eliminar lecturas: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
