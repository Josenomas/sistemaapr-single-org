<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Organizacion;
use App\Models\Boleta;
use App\Models\Pago;
use App\Models\Socio;
use App\Models\Lectura;
use App\Models\Incidente;
use App\Models\CorteSuministro;
use App\Models\TrabajoRealizado;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResumenMensualMail;
use Carbon\Carbon;

class EnviarResumenMensual extends Command
{
    protected $signature = 'resumen:mensual {--organizacion_id=}';
    protected $description = 'Envía resumen mensual de actividad a todas las organizaciones';

    public function handle()
    {
        $this->info('📊 Generando resúmenes mensuales...');

        $mesAnterior = Carbon::now()->subMonth();
        $mes = $this->getNombreMes($mesAnterior->month);
        $anio = $mesAnterior->year;

        $inicioPeriodo = $mesAnterior->startOfMonth()->copy();
        $finPeriodo = $mesAnterior->endOfMonth()->copy();

        $organizacionId = $this->option('organizacion_id');

        $query = Organizacion::where('activo', true);

        if ($organizacionId) {
            $query->where('id', $organizacionId);
        }

        $organizaciones = $query->get();

        $enviados = 0;

        foreach ($organizaciones as $org) {
            try {
                if (!$org->email_contacto) {
                    $this->warn("  ⚠️  {$org->nombre_apr}: Sin email de contacto");
                    continue;
                }

                $stats = $this->generarEstadisticas($org, $inicioPeriodo, $finPeriodo);

                Mail::to($org->email_contacto)->send(
                    new ResumenMensualMail($org, $stats, $mes, $anio)
                );

                $this->info("  ✓ Enviado a: {$org->nombre_apr}");
                $enviados++;

            } catch (\Exception $e) {
                $this->error("  ✗ Error con {$org->nombre_apr}: {$e->getMessage()}");
            }
        }

        $this->info("\n✅ Proceso completado: {$enviados} resúmenes enviados");
        return Command::SUCCESS;
    }

    /**
     * Generar estadísticas del mes
     */
    private function generarEstadisticas($organizacion, $inicio, $fin)
    {
        // Estadísticas financieras
        $boletasEmitidas = Boleta::where('id_organizacion', $organizacion->id)
            ->whereBetween('fecha_emision', [$inicio, $fin])
            ->count();

        $pagosRecibidos = Pago::where('id_organizacion', $organizacion->id)
            ->whereBetween('fecha_pago', [$inicio, $fin])
            ->count();

        $ingresosTotales = Pago::where('id_organizacion', $organizacion->id)
            ->whereBetween('fecha_pago', [$inicio, $fin])
            ->sum('monto_pagado');

        $boletasPendientes = Boleta::where('id_organizacion', $organizacion->id)
            ->whereBetween('fecha_emision', [$inicio, $fin])
            ->where('estado', 'pendiente')
            ->count();

        $boletasVencidas = Boleta::where('id_organizacion', $organizacion->id)
            ->whereBetween('fecha_emision', [$inicio, $fin])
            ->where('estado', 'vencida')
            ->count();

        // Estadísticas de socios
        $sociosActivos = Socio::where('id_organizacion', $organizacion->id)
            ->where('estado', 'activo')
            ->count();

        $nuevosSocios = Socio::where('id_organizacion', $organizacion->id)
            ->whereBetween('created_at', [$inicio, $fin])
            ->count();

        $lecturasRegistradas = Lectura::where('id_organizacion', $organizacion->id)
            ->whereBetween('fecha_lectura', [$inicio, $fin])
            ->count();

        $consumoPromedio = Lectura::where('id_organizacion', $organizacion->id)
            ->whereBetween('fecha_lectura', [$inicio, $fin])
            ->avg('consumo') ?? 0;

        // Top consumidores
        $topConsumidores = Lectura::where('id_organizacion', $organizacion->id)
            ->whereBetween('fecha_lectura', [$inicio, $fin])
            ->with('socio')
            ->get()
            ->groupBy('id_socio')
            ->map(function ($lecturas) {
                return [
                    'nombre' => $lecturas->first()->socio->nombre_completo ?? 'N/A',
                    'consumo' => $lecturas->sum('consumo'),
                ];
            })
            ->sortByDesc('consumo')
            ->take(5)
            ->values()
            ->toArray();

        // Servicio y mantención
        $incidentesReportados = Incidente::where('id_organizacion', $organizacion->id)
            ->whereBetween('created_at', [$inicio, $fin])
            ->count();

        $incidentesResueltos = Incidente::where('id_organizacion', $organizacion->id)
            ->whereBetween('created_at', [$inicio, $fin])
            ->where('estado', 'resuelto')
            ->count();

        $cortesSuministro = CorteSuministro::where('id_organizacion', $organizacion->id)
            ->whereBetween('fecha_corte', [$inicio, $fin])
            ->count();

        $trabajosRealizados = TrabajoRealizado::where('id_organizacion', $organizacion->id)
            ->whereBetween('fecha_trabajo', [$inicio, $fin])
            ->count();

        // Comparación con mes anterior
        $mesAnteriorInicio = $inicio->copy()->subMonth()->startOfMonth();
        $mesAnteriorFin = $inicio->copy()->subMonth()->endOfMonth();

        $ingresosMesAnterior = Pago::where('id_organizacion', $organizacion->id)
            ->whereBetween('fecha_pago', [$mesAnteriorInicio, $mesAnteriorFin])
            ->sum('monto_pagado');

        $sociosMesAnterior = Socio::where('id_organizacion', $organizacion->id)
            ->where('created_at', '<=', $mesAnteriorFin)
            ->where('estado', 'activo')
            ->count();

        $consumoMesAnterior = Lectura::where('id_organizacion', $organizacion->id)
            ->whereBetween('fecha_lectura', [$mesAnteriorInicio, $mesAnteriorFin])
            ->sum('consumo') ?? 0;

        $consumoActual = Lectura::where('id_organizacion', $organizacion->id)
            ->whereBetween('fecha_lectura', [$inicio, $fin])
            ->sum('consumo') ?? 0;

        $comparacion = null;
        if ($ingresosMesAnterior > 0 || $sociosMesAnterior > 0) {
            $comparacion = [
                'ingresos_cambio' => $ingresosMesAnterior > 0
                    ? round((($ingresosTotales - $ingresosMesAnterior) / $ingresosMesAnterior) * 100, 1)
                    : 0,
                'socios_cambio' => $sociosMesAnterior > 0
                    ? round((($sociosActivos - $sociosMesAnterior) / $sociosMesAnterior) * 100, 1)
                    : 0,
                'consumo_cambio' => $consumoMesAnterior > 0
                    ? round((($consumoActual - $consumoMesAnterior) / $consumoMesAnterior) * 100, 1)
                    : 0,
            ];
        }

        return [
            'ingresos_totales' => $ingresosTotales,
            'boletas_emitidas' => $boletasEmitidas,
            'pagos_recibidos' => $pagosRecibidos,
            'boletas_pendientes' => $boletasPendientes,
            'boletas_vencidas' => $boletasVencidas,
            'socios_activos' => $sociosActivos,
            'nuevos_socios' => $nuevosSocios,
            'lecturas_registradas' => $lecturasRegistradas,
            'consumo_promedio' => round($consumoPromedio, 1),
            'top_consumidores' => $topConsumidores,
            'incidentes_reportados' => $incidentesReportados,
            'incidentes_resueltos' => $incidentesResueltos,
            'cortes_suministro' => $cortesSuministro,
            'trabajos_realizados' => $trabajosRealizados,
            'comparacion' => $comparacion,
        ];
    }

    /**
     * Obtener nombre del mes en español
     */
    private function getNombreMes($mes)
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        return $meses[$mes] ?? 'Mes';
    }
}
