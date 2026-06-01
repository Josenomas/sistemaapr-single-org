<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;

class EliminarPagosOrganizacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pagos:eliminar-organizacion {organizacion_id} {--mes= : Mes específico (formato YYYY-MM)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eliminar pagos de una organización (opcionalmente filtrar por mes)';

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
        $query = Pago::where('id_organizacion', $organizacionId);

        if ($mes) {
            $query->whereYear('fecha_pago', '=', substr($mes, 0, 4))
                  ->whereMonth('fecha_pago', '=', substr($mes, 5, 2));
            $this->warn("⚠️  ADVERTENCIA: Esta acción eliminará los pagos de {$mes} para la organización ID: {$organizacionId}");
        } else {
            $this->warn("⚠️  ADVERTENCIA: Esta acción eliminará TODOS los pagos de la organización ID: {$organizacionId}");
        }

        // Contar pagos antes de eliminar
        $totalPagos = $query->count();

        if ($totalPagos === 0) {
            $mensaje = $mes
                ? "✅ No se encontraron pagos para {$mes} en la organización ID: {$organizacionId}"
                : "✅ No se encontraron pagos para la organización ID: {$organizacionId}";
            $this->info($mensaje);
            return Command::SUCCESS;
        }

        $this->info("📊 Total de pagos a eliminar: {$totalPagos}");

        // Mostrar detalle por mes
        if (!$mes) {
            $pagosPorMes = Pago::where('id_organizacion', $organizacionId)
                ->select(
                    DB::raw('DATE_FORMAT(fecha_pago, "%Y-%m") as mes'),
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(monto_pagado) as monto_total')
                )
                ->groupBy('mes')
                ->orderBy('mes', 'desc')
                ->get();

            $this->table(
                ['Mes', 'Cantidad', 'Monto Total'],
                $pagosPorMes->map(function($item) {
                    return [
                        $item->mes,
                        $item->total,
                        '$' . number_format($item->monto_total, 0, ',', '.')
                    ];
                })
            );
        }

        if (!$this->confirm('¿Está seguro de que desea eliminar estos pagos? Esta acción NO se puede deshacer.')) {
            $this->info('❌ Operación cancelada por el usuario.');
            return Command::FAILURE;
        }

        // Eliminar pagos
        try {
            DB::beginTransaction();

            $eliminados = $query->delete();

            DB::commit();

            $mensaje = $mes
                ? "✅ Se eliminaron exitosamente {$eliminados} pagos de {$mes} para la organización ID: {$organizacionId}"
                : "✅ Se eliminaron exitosamente {$eliminados} pagos de la organización ID: {$organizacionId}";

            $this->info($mensaje);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error al eliminar pagos: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
