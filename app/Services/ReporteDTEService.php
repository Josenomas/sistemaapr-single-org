<?php

namespace App\Services;

use App\Models\Boleta;
use App\Models\ConfiguracionDTE;
use App\Models\Organizacion;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteDTEService
{
    /**
     * Obtener resumen general de facturación DTE
     */
    public function obtenerResumenGeneral()
    {
        $totalOrganizaciones = Organizacion::where('activo', true)->count();
        $organizacionesConDTE = ConfiguracionDTE::where('activo', true)
            ->distinct('id_organizacion')->count();

        $porcentajeAdopcion = $totalOrganizaciones > 0
            ? round(($organizacionesConDTE / $totalOrganizaciones) * 100, 1)
            : 0;

        // Total de DTEs emitidos (histórico)
        $totalDTEsEmitidos = Boleta::whereNotNull('tipo_dte')
            ->whereIn('estado_dte', ['emitida', 'aceptada'])
            ->count();

        // DTEs del mes actual
        $dtesEsteMes = Boleta::whereNotNull('tipo_dte')
            ->whereIn('estado_dte', ['emitida', 'aceptada'])
            ->whereYear('fecha_emision_dte', now()->year)
            ->whereMonth('fecha_emision_dte', now()->month)
            ->count();

        // Ingresos totales facturados (solo DTEs válidos)
        $ingresosTotales = Boleta::whereNotNull('tipo_dte')
            ->whereIn('estado_dte', ['emitida', 'aceptada'])
            ->whereIn('tipo_dte', [33, 39]) // Solo facturas y boletas (no NC/ND)
            ->sum('total');

        // Ingresos del mes actual
        $ingresosEsteMes = Boleta::whereNotNull('tipo_dte')
            ->whereIn('estado_dte', ['emitida', 'aceptada'])
            ->whereIn('tipo_dte', [33, 39])
            ->whereYear('fecha_emision_dte', now()->year)
            ->whereMonth('fecha_emision_dte', now()->month)
            ->sum('total');

        return [
            'total_organizaciones' => $totalOrganizaciones,
            'organizaciones_con_dte' => $organizacionesConDTE,
            'porcentaje_adopcion' => $porcentajeAdopcion,
            'total_dtes_emitidos' => $totalDTEsEmitidos,
            'dtes_este_mes' => $dtesEsteMes,
            'ingresos_totales' => $ingresosTotales,
            'ingresos_este_mes' => $ingresosEsteMes,
        ];
    }

    /**
     * Obtener facturación por organización
     */
    public function obtenerFacturacionPorOrganizacion($filtros = [])
    {
        $query = Boleta::select(
                'boletas.id_organizacion',
                DB::raw('COUNT(*) as total_dtes'),
                DB::raw('SUM(CASE WHEN boletas.tipo_dte IN (33, 39) THEN boletas.total ELSE 0 END) as ingresos_totales'),
                DB::raw('SUM(CASE WHEN boletas.tipo_dte = 39 THEN 1 ELSE 0 END) as total_boletas'),
                DB::raw('SUM(CASE WHEN boletas.tipo_dte = 33 THEN 1 ELSE 0 END) as total_facturas'),
                DB::raw('SUM(CASE WHEN boletas.tipo_dte = 61 THEN 1 ELSE 0 END) as total_nc'),
                DB::raw('SUM(CASE WHEN boletas.tipo_dte = 56 THEN 1 ELSE 0 END) as total_nd'),
                DB::raw('MAX(boletas.fecha_emision_dte) as ultimo_dte')
            )
            ->join('organizaciones', 'boletas.id_organizacion', '=', 'organizaciones.id')
            ->whereNotNull('boletas.tipo_dte')
            ->whereIn('boletas.estado_dte', ['emitida', 'aceptada'])
            ->groupBy('boletas.id_organizacion');

        // Aplicar filtros
        if (isset($filtros['fecha_desde'])) {
            $query->where('boletas.fecha_emision_dte', '>=', $filtros['fecha_desde']);
        }

        if (isset($filtros['fecha_hasta'])) {
            $query->where('boletas.fecha_emision_dte', '<=', $filtros['fecha_hasta']);
        }

        $resultados = $query->get();

        // Enriquecer con datos de la organización
        foreach ($resultados as $resultado) {
            $org = Organizacion::find($resultado->id_organizacion);
            $resultado->nombre_organizacion = $org->nombre ?? 'Sin nombre';
            $resultado->razon_social = $org->razon_social ?? 'N/A';
        }

        return $resultados->sortByDesc('ingresos_totales')->values();
    }

    /**
     * Obtener evolución mensual de DTEs (últimos 12 meses)
     */
    public function obtenerEvolucionMensual()
    {
        $meses = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $mes = $fecha->format('Y-m');
            $mesNombre = ucfirst($fecha->locale('es')->isoFormat('MMM YYYY'));

            $meses[] = $mesNombre;

            // Contar DTEs del mes
            $totalDTEs = Boleta::whereNotNull('tipo_dte')
                ->whereIn('estado_dte', ['emitida', 'aceptada'])
                ->whereYear('fecha_emision_dte', $fecha->year)
                ->whereMonth('fecha_emision_dte', $fecha->month)
                ->count();

            // Ingresos del mes
            $ingresosMes = Boleta::whereNotNull('tipo_dte')
                ->whereIn('estado_dte', ['emitida', 'aceptada'])
                ->whereIn('tipo_dte', [33, 39])
                ->whereYear('fecha_emision_dte', $fecha->year)
                ->whereMonth('fecha_emision_dte', $fecha->month)
                ->sum('total');

            $data[] = [
                'mes' => $mes,
                'mes_nombre' => $mesNombre,
                'total_dtes' => $totalDTEs,
                'ingresos' => $ingresosMes,
            ];
        }

        return [
            'meses' => $meses,
            'data' => $data,
        ];
    }

    /**
     * Obtener distribución por tipo de DTE
     */
    public function obtenerDistribucionPorTipo($filtros = [])
    {
        $query = Boleta::select(
                'tipo_dte',
                DB::raw('COUNT(*) as cantidad'),
                DB::raw('SUM(total) as monto_total')
            )
            ->whereNotNull('tipo_dte')
            ->whereIn('estado_dte', ['emitida', 'aceptada'])
            ->groupBy('tipo_dte');

        // Aplicar filtros
        if (isset($filtros['fecha_desde'])) {
            $query->where('fecha_emision_dte', '>=', $filtros['fecha_desde']);
        }

        if (isset($filtros['fecha_hasta'])) {
            $query->where('fecha_emision_dte', '<=', $filtros['fecha_hasta']);
        }

        $resultados = $query->get();

        // Mapear nombres de tipos
        $tiposNombres = [
            33 => 'Factura Electrónica',
            39 => 'Boleta Electrónica',
            61 => 'Nota de Crédito',
            56 => 'Nota de Débito',
        ];

        foreach ($resultados as $resultado) {
            $resultado->nombre_tipo = $tiposNombres[$resultado->tipo_dte] ?? "Tipo {$resultado->tipo_dte}";
        }

        return $resultados;
    }

    /**
     * Obtener análisis de adopción (organizaciones que han empezado a usar DTE)
     */
    public function obtenerAnalisisAdopcion()
    {
        // Organizaciones con al menos 1 DTE emitido
        $orgsConDTEs = Boleta::select('id_organizacion')
            ->whereNotNull('tipo_dte')
            ->whereIn('estado_dte', ['emitida', 'aceptada'])
            ->distinct()
            ->count();

        // Organizaciones con configuración DTE pero sin emisiones
        $orgsConfiguradas = ConfiguracionDTE::where('activo', true)->count();
        $orgsSinEmisiones = $orgsConfiguradas - $orgsConDTEs;

        // Organizaciones sin configuración DTE
        $totalOrgs = Organizacion::where('activo', true)->count();
        $orgsSinConfiguracion = $totalOrgs - $orgsConfiguradas;

        // Evolución de adopción (primeras emisiones por mes, últimos 12 meses)
        $adopcionMensual = [];
        for ($i = 11; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);

            // Contar organizaciones que emitieron su primer DTE este mes
            $primerasEmisiones = DB::table('boletas')
                ->select('id_organizacion')
                ->whereNotNull('tipo_dte')
                ->whereIn('estado_dte', ['emitida', 'aceptada'])
                ->whereYear('fecha_emision_dte', $fecha->year)
                ->whereMonth('fecha_emision_dte', $fecha->month)
                ->groupBy('id_organizacion')
                ->havingRaw('MIN(fecha_emision_dte) >= ? AND MIN(fecha_emision_dte) < ?', [
                    $fecha->startOfMonth()->toDateString(),
                    $fecha->copy()->addMonth()->startOfMonth()->toDateString()
                ])
                ->count();

            $adopcionMensual[] = [
                'mes' => ucfirst($fecha->locale('es')->isoFormat('MMM YYYY')),
                'nuevas_organizaciones' => $primerasEmisiones,
            ];
        }

        return [
            'organizaciones_activas' => $orgsConDTEs,
            'organizaciones_configuradas_sin_emision' => $orgsSinEmisiones,
            'organizaciones_sin_configuracion' => $orgsSinConfiguracion,
            'total_organizaciones' => $totalOrgs,
            'adopcion_mensual' => $adopcionMensual,
        ];
    }

    /**
     * Obtener top 10 organizaciones por facturación
     */
    public function obtenerTop10Organizaciones($filtros = [])
    {
        $query = Boleta::select(
                'boletas.id_organizacion',
                DB::raw('SUM(CASE WHEN boletas.tipo_dte IN (33, 39) THEN boletas.total ELSE 0 END) as ingresos_totales'),
                DB::raw('COUNT(*) as total_dtes')
            )
            ->whereNotNull('boletas.tipo_dte')
            ->whereIn('boletas.estado_dte', ['emitida', 'aceptada'])
            ->groupBy('boletas.id_organizacion');

        // Aplicar filtros
        if (isset($filtros['fecha_desde'])) {
            $query->where('boletas.fecha_emision_dte', '>=', $filtros['fecha_desde']);
        }

        if (isset($filtros['fecha_hasta'])) {
            $query->where('boletas.fecha_emision_dte', '<=', $filtros['fecha_hasta']);
        }

        $resultados = $query->orderBy('ingresos_totales', 'desc')
            ->limit(10)
            ->get();

        // Enriquecer con datos de la organización
        foreach ($resultados as $resultado) {
            $org = Organizacion::find($resultado->id_organizacion);
            $resultado->nombre_organizacion = $org->nombre ?? 'Sin nombre';
        }

        return $resultados;
    }
}
