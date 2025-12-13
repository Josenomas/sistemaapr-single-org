<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Socio;
use App\Models\Lectura;
use App\Models\Boleta;
use App\Models\Pago;
use App\Models\Ticket;
use App\Models\Funcionario;
use App\Models\CorteSuministro;
use App\Models\TrabajoRealizado;
use App\Models\Compra;
use App\Models\Inventario;
use App\Models\HistorialConsumo;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportesController extends Controller
{
    /**
     * Centro de Reportes - Vista Principal
     */
    public function index()
    {
        // Estadísticas Generales
        $estadisticasGenerales = [
            'total_socios' => Socio::where('activo', 1)->where('estado', 'activo')->count(),
            'total_funcionarios' => Funcionario::where('activo', 1)->where('estado', 'activo')->count(),
            'total_medidores' => Socio::where('activo', 1)->where('estado', 'activo')->count(),
            'tickets_abiertos' => Ticket::where('activo', 1)->whereIn('estado', ['abierto', 'en_proceso'])->count(),
        ];

        // Estadísticas Financieras (Mes Actual)
        $mesActual = date('Y-m');
        $estadisticasFinancieras = [
            'ingresos_mes' => Pago::whereRaw("DATE_FORMAT(fecha_pago, '%Y-%m') = ?", [$mesActual])->sum('monto_pagado'),
            'boletas_emitidas_mes' => Boleta::where('activo', 1)->whereRaw("DATE_FORMAT(fecha_emision, '%Y-%m') = ?", [$mesActual])->count(),
            'pagos_pendientes' => Boleta::where('activo', 1)
                                        ->where('estado', 'pendiente')
                                        ->sum('total'),
            'compras_mes' => Compra::where('activo', 1)->whereRaw("DATE_FORMAT(fecha_compra, '%Y-%m') = ?", [$mesActual])->sum('total'),
        ];

        // Estadísticas de Consumo
        $estadisticasConsumo = [
            'consumo_promedio' => HistorialConsumo::where('activo', 1)->avg('consumo_m3') ?? 0,
            'consumo_total_mes' => HistorialConsumo::where('activo', 1)
                                                   ->where('periodo', $mesActual)
                                                   ->sum('consumo_m3'),
            'anomalias_detectadas' => HistorialConsumo::where('activo', 1)
                                                      ->where('periodo', $mesActual)
                                                      ->whereIn('anomalia', ['alto', 'bajo', 'cero'])
                                                      ->count(),
            'lecturas_mes' => Lectura::whereRaw("DATE_FORMAT(fecha_lectura, '%Y-%m') = ?", [$mesActual])->count(),
        ];

        // Estadísticas Operacionales
        $estadisticasOperacionales = [
            'cortes_activos' => CorteSuministro::where('activo', 1)->whereIn('estado', ['pendiente', 'ejecutado'])->count(),
            'trabajos_pendientes' => TrabajoRealizado::where('activo', 1)->whereIn('estado', ['planificado', 'en_proceso'])->count(),
            'inventario_bajo_stock' => Inventario::where('activo', 1)
                                                 ->whereRaw('cantidad_actual <= cantidad_minima')
                                                 ->count(),
            'tickets_urgentes' => Ticket::where('activo', 1)
                                        ->whereIn('estado', ['abierto', 'en_proceso'])
                                        ->where('prioridad', 'urgente')
                                        ->count(),
        ];

        // Gráfico: Ingresos por mes (últimos 12 meses)
        $ingresosPorMes = $this->obtenerIngresosPorMes(12);

        // Gráfico: Consumo por mes (últimos 12 meses)
        $consumoPorMes = $this->obtenerConsumoPorMes(12);

        // Gráfico: Tickets por estado
        $ticketsPorEstado = Ticket::where('activo', 1)
                                  ->select('estado', DB::raw('count(*) as total'))
                                  ->groupBy('estado')
                                  ->get();

        // Gráfico: Pagos por método de pago (mes actual)
        $pagosPorMetodo = Pago::whereRaw("DATE_FORMAT(fecha_pago, '%Y-%m') = ?", [$mesActual])
                              ->select('metodo_pago', DB::raw('count(*) as total'), DB::raw('sum(monto_pagado) as monto'))
                              ->groupBy('metodo_pago')
                              ->get();

        // Top 10 Socios con mayor consumo (mes actual)
        $topConsumidores = HistorialConsumo::with('socio')
                                          ->where('activo', 1)
                                          ->where('periodo', $mesActual)
                                          ->orderBy('consumo_m3', 'desc')
                                          ->limit(10)
                                          ->get();

        // Top 10 Socios con mayor deuda
        $topDeudores = Boleta::with('socio')
                            ->where('activo', 1)
                            ->where('estado', 'pendiente')
                            ->select('id_socio', DB::raw('sum(total) as deuda_total'))
                            ->groupBy('id_socio')
                            ->orderBy('deuda_total', 'desc')
                            ->limit(10)
                            ->get();

        return view('reportes.index', compact(
            'estadisticasGenerales',
            'estadisticasFinancieras',
            'estadisticasConsumo',
            'estadisticasOperacionales',
            'ingresosPorMes',
            'consumoPorMes',
            'ticketsPorEstado',
            'pagosPorMetodo',
            'topConsumidores',
            'topDeudores'
        ));
    }

    /**
     * Reporte de Socios
     */
    public function reporteSocios(Request $request)
    {
        $query = Socio::where('activo', 1);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('sector')) {
            $query->where('sector', $request->sector);
        }

        $socios = $query->get();

        // Estadísticas
        $estadisticas = [
            'total' => $socios->count(),
            'activos' => $socios->where('estado', 'activo')->count(),
            'inactivos' => $socios->where('estado', 'inactivo')->count(),
            'por_sector' => $socios->groupBy('sector')->map->count(),
        ];

        // Obtener sectores únicos para filtro
        $sectores = Socio::where('activo', 1)
                        ->distinct()
                        ->pluck('sector')
                        ->filter()
                        ->sort()
                        ->values();

        return view('reportes.socios', compact('socios', 'estadisticas', 'sectores'));
    }

    /**
     * Descargar Reporte de Socios en PDF
     */
    public function descargarReporteSocios(Request $request)
    {
        $query = Socio::where('activo', 1);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('sector')) {
            $query->where('sector', $request->sector);
        }

        $socios = $query->get();

        $estadisticas = [
            'total' => $socios->count(),
            'activos' => $socios->where('estado', 'activo')->count(),
            'inactivos' => $socios->where('estado', 'inactivo')->count(),
            'por_sector' => $socios->groupBy('sector')->map->count(),
        ];

        $pdf = Pdf::loadView('reportes.pdf.socios', compact('socios', 'estadisticas'));
        return $pdf->download('reporte-socios-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Reporte Financiero
     */
    public function reporteFinanciero(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? date('Y-m-01');
        $fechaFin = $request->fecha_fin ?? date('Y-m-t');

        // Ingresos
        $ingresos = Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                       ->select('metodo_pago', DB::raw('sum(monto_pagado) as total'))
                       ->groupBy('metodo_pago')
                       ->get();

        $totalIngresos = Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])->sum('monto_pagado');

        // Egresos (Compras)
        $egresos = Compra::where('activo', 1)
                        ->whereBetween('fecha_compra', [$fechaInicio, $fechaFin])
                        ->select('tipo_compra', DB::raw('sum(total) as total'))
                        ->groupBy('tipo_compra')
                        ->get();

        $totalEgresos = Compra::where('activo', 1)
                             ->whereBetween('fecha_compra', [$fechaInicio, $fechaFin])
                             ->sum('total');

        // Balance
        $balance = $totalIngresos - $totalEgresos;

        // Boletas pendientes
        $boletasPendientes = Boleta::where('activo', 1)
                                   ->where('estado', 'pendiente')
                                   ->whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
                                   ->sum('total');

        // Gráfico de ingresos vs egresos por mes
        $mesesPeriodo = $this->obtenerMesesEntreFechas($fechaInicio, $fechaFin);
        $comparativoMensual = [];

        foreach ($mesesPeriodo as $mes) {
            $ingresosMes = Pago::whereRaw("DATE_FORMAT(fecha_pago, '%Y-%m') = ?", [$mes])->sum('monto_pagado');
            $egresosMes = Compra::where('activo', 1)->whereRaw("DATE_FORMAT(fecha_compra, '%Y-%m') = ?", [$mes])->sum('total');

            $comparativoMensual[] = [
                'mes' => $mes,
                'ingresos' => $ingresosMes,
                'egresos' => $egresosMes,
                'balance' => $ingresosMes - $egresosMes
            ];
        }

        return view('reportes.financiero', compact(
            'ingresos',
            'totalIngresos',
            'egresos',
            'totalEgresos',
            'balance',
            'boletasPendientes',
            'comparativoMensual',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Descargar Reporte Financiero en PDF
     */
    public function descargarReporteFinanciero(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? date('Y-m-01');
        $fechaFin = $request->fecha_fin ?? date('Y-m-t');

        $ingresos = Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                       ->select('metodo_pago', DB::raw('sum(monto_pagado) as total'))
                       ->groupBy('metodo_pago')
                       ->get();

        $totalIngresos = Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])->sum('monto_pagado');

        $egresos = Compra::where('activo', 1)
                        ->whereBetween('fecha_compra', [$fechaInicio, $fechaFin])
                        ->select('tipo_compra', DB::raw('sum(total) as total'))
                        ->groupBy('tipo_compra')
                        ->get();

        $totalEgresos = Compra::where('activo', 1)
                             ->whereBetween('fecha_compra', [$fechaInicio, $fechaFin])
                             ->sum('total');

        $balance = $totalIngresos - $totalEgresos;

        $boletasPendientes = Boleta::where('activo', 1)
                                   ->where('estado', 'pendiente')
                                   ->whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
                                   ->sum('total');

        $mesesPeriodo = $this->obtenerMesesEntreFechas($fechaInicio, $fechaFin);
        $comparativoMensual = [];

        foreach ($mesesPeriodo as $mes) {
            $ingresosMes = Pago::whereRaw("DATE_FORMAT(fecha_pago, '%Y-%m') = ?", [$mes])->sum('monto_pagado');
            $egresosMes = Compra::where('activo', 1)->whereRaw("DATE_FORMAT(fecha_compra, '%Y-%m') = ?", [$mes])->sum('total');

            $comparativoMensual[] = [
                'mes' => $mes,
                'ingresos' => $ingresosMes,
                'egresos' => $egresosMes,
                'balance' => $ingresosMes - $egresosMes
            ];
        }

        $pdf = Pdf::loadView('reportes.pdf.financiero', compact(
            'ingresos',
            'totalIngresos',
            'egresos',
            'totalEgresos',
            'balance',
            'boletasPendientes',
            'comparativoMensual',
            'fechaInicio',
            'fechaFin'
        ));

        return $pdf->download('reporte-financiero-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Reporte de Consumo
     */
    public function reporteConsumo(Request $request)
    {
        $periodo = $request->periodo ?? date('Y-m');

        $consumos = HistorialConsumo::with('socio')
                                   ->where('activo', 1)
                                   ->where('periodo', $periodo)
                                   ->orderBy('consumo_m3', 'desc')
                                   ->get();

        $estadisticas = [
            'total_registros' => $consumos->count(),
            'consumo_total' => $consumos->sum('consumo_m3'),
            'consumo_promedio' => $consumos->avg('consumo_m3'),
            'consumo_maximo' => $consumos->max('consumo_m3'),
            'consumo_minimo' => $consumos->min('consumo_m3'),
            'anomalias' => [
                'alto' => $consumos->where('anomalia', 'alto')->count(),
                'bajo' => $consumos->where('anomalia', 'bajo')->count(),
                'cero' => $consumos->where('anomalia', 'cero')->count(),
            ]
        ];

        // Distribución de consumo por rangos
        $distribucion = [
            '0-5 m³' => $consumos->where('consumo_m3', '<=', 5)->count(),
            '6-10 m³' => $consumos->whereBetween('consumo_m3', [6, 10])->count(),
            '11-15 m³' => $consumos->whereBetween('consumo_m3', [11, 15])->count(),
            '16-20 m³' => $consumos->whereBetween('consumo_m3', [16, 20])->count(),
            '21+ m³' => $consumos->where('consumo_m3', '>', 20)->count(),
        ];

        return view('reportes.consumo', compact('consumos', 'estadisticas', 'distribucion', 'periodo'));
    }

    /**
     * Descargar Reporte de Consumo en PDF
     */
    public function descargarReporteConsumo(Request $request)
    {
        $periodo = $request->periodo ?? date('Y-m');

        $consumos = HistorialConsumo::with('socio')
                                   ->where('activo', 1)
                                   ->where('periodo', $periodo)
                                   ->orderBy('consumo_m3', 'desc')
                                   ->get();

        $estadisticas = [
            'total_registros' => $consumos->count(),
            'consumo_total' => $consumos->sum('consumo_m3'),
            'consumo_promedio' => $consumos->avg('consumo_m3'),
            'consumo_maximo' => $consumos->max('consumo_m3'),
            'consumo_minimo' => $consumos->min('consumo_m3'),
            'anomalias' => [
                'alto' => $consumos->where('anomalia', 'alto')->count(),
                'bajo' => $consumos->where('anomalia', 'bajo')->count(),
                'cero' => $consumos->where('anomalia', 'cero')->count(),
            ]
        ];

        $distribucion = [
            '0-5 m³' => $consumos->where('consumo_m3', '<=', 5)->count(),
            '6-10 m³' => $consumos->whereBetween('consumo_m3', [6, 10])->count(),
            '11-15 m³' => $consumos->whereBetween('consumo_m3', [11, 15])->count(),
            '16-20 m³' => $consumos->whereBetween('consumo_m3', [16, 20])->count(),
            '21+ m³' => $consumos->where('consumo_m3', '>', 20)->count(),
        ];

        $pdf = Pdf::loadView('reportes.pdf.consumo', compact('consumos', 'estadisticas', 'distribucion', 'periodo'));
        return $pdf->download('reporte-consumo-' . $periodo . '.pdf');
    }

    /**
     * Reporte Operacional
     */
    public function reporteOperacional(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? date('Y-m-01');
        $fechaFin = $request->fecha_fin ?? date('Y-m-t');

        // Tickets
        $tickets = Ticket::where('activo', 1)
                        ->whereBetween('fecha_reporte', [$fechaInicio, $fechaFin])
                        ->get();

        $ticketsEstadisticas = [
            'total' => $tickets->count(),
            'por_estado' => $tickets->groupBy('estado')->map->count(),
            'por_prioridad' => $tickets->groupBy('prioridad')->map->count(),
            'por_tipo' => $tickets->groupBy('tipo_ticket')->map->count(),
            'tiempo_promedio_resolucion' => $tickets->where('estado', 'resuelto')->avg('tiempo_resolucion'),
        ];

        // Trabajos
        $trabajos = TrabajoRealizado::where('activo', 1)
                          ->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                          ->get();

        $trabajosEstadisticas = [
            'total' => $trabajos->count(),
            'por_estado' => $trabajos->groupBy('estado')->map->count(),
            'por_tipo' => $trabajos->groupBy('tipo_trabajo')->map->count(),
            'costo_total' => $trabajos->sum('costo_real'),
        ];

        // Cortes
        $cortes = CorteSuministro::where('activo', 1)
                      ->whereBetween('fecha_corte', [$fechaInicio, $fechaFin])
                      ->get();

        $cortesEstadisticas = [
            'total' => $cortes->count(),
            'por_estado' => $cortes->groupBy('estado')->map->count(),
            'por_motivo' => $cortes->groupBy('motivo')->map->count(),
            'reconexiones' => $cortes->where('estado', 'reconectado')->count(),
        ];

        return view('reportes.operacional', compact(
            'tickets',
            'ticketsEstadisticas',
            'trabajos',
            'trabajosEstadisticas',
            'cortes',
            'cortesEstadisticas',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Descargar Reporte Operacional en PDF
     */
    public function descargarReporteOperacional(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? date('Y-m-01');
        $fechaFin = $request->fecha_fin ?? date('Y-m-t');

        $tickets = Ticket::where('activo', 1)
                        ->whereBetween('fecha_reporte', [$fechaInicio, $fechaFin])
                        ->get();

        $ticketsEstadisticas = [
            'total' => $tickets->count(),
            'por_estado' => $tickets->groupBy('estado')->map->count(),
            'por_prioridad' => $tickets->groupBy('prioridad')->map->count(),
            'por_tipo' => $tickets->groupBy('tipo_ticket')->map->count(),
            'tiempo_promedio_resolucion' => $tickets->where('estado', 'resuelto')->avg('tiempo_resolucion'),
        ];

        $trabajos = TrabajoRealizado::where('activo', 1)
                          ->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                          ->get();

        $trabajosEstadisticas = [
            'total' => $trabajos->count(),
            'por_estado' => $trabajos->groupBy('estado')->map->count(),
            'por_tipo' => $trabajos->groupBy('tipo_trabajo')->map->count(),
            'costo_total' => $trabajos->sum('costo_real'),
        ];

        $cortes = CorteSuministro::where('activo', 1)
                      ->whereBetween('fecha_corte', [$fechaInicio, $fechaFin])
                      ->get();

        $cortesEstadisticas = [
            'total' => $cortes->count(),
            'por_estado' => $cortes->groupBy('estado')->map->count(),
            'por_motivo' => $cortes->groupBy('motivo')->map->count(),
            'reconexiones' => $cortes->where('estado', 'reconectado')->count(),
        ];

        $pdf = Pdf::loadView('reportes.pdf.operacional', compact(
            'tickets',
            'ticketsEstadisticas',
            'trabajos',
            'trabajosEstadisticas',
            'cortes',
            'cortesEstadisticas',
            'fechaInicio',
            'fechaFin'
        ));

        return $pdf->download('reporte-operacional-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Métodos auxiliares
     */
    private function obtenerIngresosPorMes($meses = 12)
    {
        $resultado = [];

        for ($i = $meses - 1; $i >= 0; $i--) {
            $fecha = date('Y-m', strtotime("-$i months"));
            $ingresos = Pago::whereRaw("DATE_FORMAT(fecha_pago, '%Y-%m') = ?", [$fecha])->sum('monto_pagado');

            $resultado[] = [
                'mes' => $fecha,
                'total' => $ingresos
            ];
        }

        return $resultado;
    }

    private function obtenerConsumoPorMes($meses = 12)
    {
        $resultado = [];

        for ($i = $meses - 1; $i >= 0; $i--) {
            $periodo = date('Y-m', strtotime("-$i months"));
            $consumo = HistorialConsumo::where('activo', 1)
                                      ->where('periodo', $periodo)
                                      ->sum('consumo_m3');

            $resultado[] = [
                'mes' => $periodo,
                'total' => $consumo
            ];
        }

        return $resultado;
    }

    private function obtenerMesesEntreFechas($fechaInicio, $fechaFin)
    {
        $meses = [];
        $inicio = strtotime($fechaInicio);
        $fin = strtotime($fechaFin);

        while ($inicio <= $fin) {
            $meses[] = date('Y-m', $inicio);
            $inicio = strtotime('+1 month', $inicio);
        }

        return $meses;
    }
}
