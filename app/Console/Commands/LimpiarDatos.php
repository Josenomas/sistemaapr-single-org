<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimpiarDatos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:limpiar-datos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia todos los datos excepto usuarios y configuraciones del sistema';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->confirm('¿Está seguro de eliminar TODOS los datos (socios, lecturas, boletas, pagos, etc.)? Los usuarios NO se eliminarán.')) {
            $this->info('Operación cancelada.');
            return Command::SUCCESS;
        }

        $this->info('Limpiando base de datos...');

        DB::beginTransaction();

        try {
            // Desactivar verificación de claves foráneas temporalmente
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Limpiar datos operacionales
            $tablas = [
                'actividades',
                'pagos',
                'boletas',
                'lecturas',
                'socios',
                'eventos',
                // Agregar más tablas si es necesario
            ];

            foreach ($tablas as $tabla) {
                // Verificar si la tabla existe
                $existe = DB::select("SHOW TABLES LIKE '{$tabla}'");

                if (!empty($existe)) {
                    DB::table($tabla)->truncate();
                    $this->info("✓ Tabla '{$tabla}' limpiada");
                } else {
                    $this->warn("⚠ Tabla '{$tabla}' no existe, omitida");
                }
            }

            // Reactivar verificación de claves foráneas
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            DB::commit();

            $this->info('');
            $this->info('✓ Base de datos limpiada exitosamente');
            $this->info('✓ Usuarios y configuraciones del sistema conservados');

        } catch (\Exception $e) {
            DB::rollback();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            $this->error('Error al limpiar la base de datos: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
