<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;
use App\Models\Socio;
use App\Models\Boleta;
use Illuminate\Support\Facades\DB;

class HistorialPagosController extends Controller
{
    /**
     * Mostrar el historial de pagos con análisis
     */
    public function index(Request $request)
    {
        $query = Pago::with(['socio', 'boleta']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('socio', function($sq) use ($search) {
                    $sq->where('nombre', 'like', "%{$search}%")
                       ->orWhere('apellido_paterno', 'like', "%{$search}%")
                       ->orWhere('apellido_materno', 'like', "%{$search}%")
                       ->orWhere('rut', 'like', "%{$search}%");
                })->orWhere('numero_comprobante', 'like', "%{$search}%");
            });
        }

        if ($request->filled('socio')) {
            $query->where('id_socio', $request->socio);
        }

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_pago', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_pago', '<=', $request->fecha_hasta);
        }

        if ($request->filled('monto_min')) {
            $query->where('monto_pagado', '>=', $request->monto_min);
        }

        if ($request->filled('monto_max')) {
            $query->where('monto_pagado', '<=', $request->monto_max);
        }

        // Estadísticas
        $estadisticas = [
            'total_pagos' => Pago::count(),
            'monto_total' => Pago::sum('monto_pagado'),
            'monto_promedio' => Pago::avg('monto_pagado'),
            'pagos_mes_actual' => Pago::whereMonth('fecha_pago', date('m'))
                                     ->whereYear('fecha_pago', date('Y'))
                                     ->count(),
            'monto_mes_actual' => Pago::whereMonth('fecha_pago', date('m'))
                                      ->whereYear('fecha_pago', date('Y'))
                                      ->sum('monto_pagado'),
            'por_metodo' => [
                'efectivo' => Pago::where('metodo_pago', 'efectivo')->count(),
                'transferencia' => Pago::where('metodo_pago', 'transferencia')->count(),
                'cheque' => Pago::where('metodo_pago', 'cheque')->count(),
            ]
        ];

        $socios = Socio::activos()->orderBy('nombre')->get();

        $pagos = $query->orderBy('fecha_pago', 'desc')
                      ->orderBy('id', 'desc')
                      ->paginate(20);

        return view('historial-pagos.index', compact('pagos', 'estadisticas', 'socios'));
    }

    /**
     * Mostrar detalle del pago
     */
    public function show($id)
    {
        $pago = Pago::with(['socio', 'boleta'])->findOrFail($id);

        // Obtener historial de pagos del socio (últimos 12 meses)
        $historialSocio = Pago::where('id_socio', $pago->id_socio)
                             ->whereDate('fecha_pago', '>=', now()->subMonths(12))
                             ->orderBy('fecha_pago', 'desc')
                             ->limit(10)
                             ->get();

        return view('historial-pagos.show', compact('pago', 'historialSocio'));
    }

    /**
     * Mostrar análisis detallado por socio
     */
    public function analisisSocio($idSocio)
    {
        $socio = Socio::findOrFail($idSocio);

        // Obtener todo el historial de pagos del socio
        $pagos = Pago::where('id_socio', $idSocio)
                    ->orderBy('fecha_pago', 'desc')
                    ->get();

        // Estadísticas del socio
        $estadisticas = [
            'total_pagos' => $pagos->count(),
            'monto_total_pagado' => $pagos->sum('monto_pagado'),
            'monto_promedio' => $pagos->avg('monto_pagado'),
            'monto_maximo' => $pagos->max('monto_pagado'),
            'monto_minimo' => $pagos->min('monto_pagado'),
            'ultimo_pago' => $pagos->first(),
            'pagos_ultimo_año' => $pagos->filter(function($pago) {
                return $pago->fecha_pago >= now()->subYear();
            })->count(),
            'monto_ultimo_año' => $pagos->filter(function($pago) {
                return $pago->fecha_pago >= now()->subYear();
            })->sum('monto_pagado'),
            'promedio_mensual' => $this->calcularPromedioMensual($pagos),
            'metodo_preferido' => $this->obtenerMetodoPreferido($pagos),
            'puntualidad' => $this->calcularPuntualidad($idSocio)
        ];

        // Datos para gráfico de tendencia (últimos 12 meses)
        $tendencia = $this->obtenerTendenciaPagos($idSocio, 12);

        return view('historial-pagos.analisis-socio', compact('socio', 'pagos', 'estadisticas', 'tendencia'));
    }

    /**
     * Comparar pagos entre socios
     */
    public function comparar(Request $request)
    {
        $socios = Socio::activos()->orderBy('nombre')->get();

        $comparacion = null;
        $fechaInicio = $request->fecha_inicio ?? date('Y-m-01');
        $fechaFin = $request->fecha_fin ?? date('Y-m-t');

        if ($request->filled('socios_comparar')) {
            $sociosIds = $request->socios_comparar;

            $comparacion = [];
            foreach ($sociosIds as $socioId) {
                $socio = Socio::find($socioId);
                if ($socio) {
                    $pagosSocio = Pago::where('id_socio', $socioId)
                                     ->whereDate('fecha_pago', '>=', $fechaInicio)
                                     ->whereDate('fecha_pago', '<=', $fechaFin)
                                     ->get();

                    $comparacion[] = (object) [
                        'id' => $socio->id,
                        'nombre_completo' => $socio->nombre_completo,
                        'rut' => $socio->rut,
                        'total_pagos' => $pagosSocio->count(),
                        'monto_total' => $pagosSocio->sum('monto_pagado'),
                        'monto_promedio' => $pagosSocio->avg('monto_pagado') ?? 0
                    ];
                }
            }

            // Calcular estadísticas de comparación
            $socioMayorPago = collect($comparacion)->sortByDesc('monto_total')->first();
            $socioMenorPago = collect($comparacion)->sortBy('monto_total')->first();

            $estadisticasComparacion = [
                'total_socios' => count($comparacion),
                'monto_total_global' => collect($comparacion)->sum('monto_total'),
                'promedio_global' => collect($comparacion)->avg('monto_total'),
                'socio_mayor_pago' => $socioMayorPago ? $socioMayorPago->nombre_completo : 'N/A',
                'socio_menor_pago' => $socioMenorPago ? $socioMenorPago->nombre_completo : 'N/A'
            ];
        } else {
            $estadisticasComparacion = null;
        }

        return view('historial-pagos.comparar', compact('socios', 'comparacion', 'estadisticasComparacion', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte de recaudación por período
     */
    public function reporteRecaudacion(Request $request)
    {
        $periodo = $request->periodo ?? 'mes'; // mes, trimestre, año
        $fechaInicio = $request->fecha_inicio ?? date('Y-m-01');
        $fechaFin = $request->fecha_fin ?? date('Y-m-t');

        $query = Pago::whereDate('fecha_pago', '>=', $fechaInicio)
                    ->whereDate('fecha_pago', '<=', $fechaFin);

        $datos = $query->get();

        // Agrupar por período
        $recaudacionPorPeriodo = $this->agruparPorPeriodo($datos, $periodo);

        // Estadísticas
        $estadisticas = [
            'total_recaudado' => $datos->sum('monto_pagado'),
            'total_pagos' => $datos->count(),
            'promedio_pago' => $datos->avg('monto_pagado'),
            'por_metodo' => [
                'efectivo' => $datos->where('metodo_pago', 'efectivo')->sum('monto_pagado'),
                'transferencia' => $datos->where('metodo_pago', 'transferencia')->sum('monto_pagado'),
                'cheque' => $datos->where('metodo_pago', 'cheque')->sum('monto_pagado'),
                'credito' => $datos->where('metodo_pago', 'credito')->sum('monto_pagado'),
            ]
        ];

        return view('historial-pagos.reporte-recaudacion', compact('recaudacionPorPeriodo', 'estadisticas', 'periodo', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Calcular promedio mensual de pagos
     */
    private function calcularPromedioMensual($pagos)
    {
        if ($pagos->isEmpty()) {
            return 0;
        }

        $mesesUnicos = $pagos->groupBy(function($pago) {
            return $pago->fecha_pago->format('Y-m');
        })->count();

        return $mesesUnicos > 0 ? $pagos->sum('monto_pagado') / $mesesUnicos : 0;
    }

    /**
     * Obtener método de pago preferido
     */
    private function obtenerMetodoPreferido($pagos)
    {
        if ($pagos->isEmpty()) {
            return 'N/A';
        }

        $porMetodo = $pagos->groupBy('metodo_pago')->map->count();
        return $porMetodo->sortDesc()->keys()->first() ?? 'N/A';
    }

    /**
     * Calcular puntualidad de pagos
     */
    private function calcularPuntualidad($idSocio)
    {
        $boletas = Boleta::where('id_socio', $idSocio)
                        ->where('activo', 1)
                        ->whereHas('pagos')
                        ->get();

        if ($boletas->isEmpty()) {
            return 100;
        }

        $pagosPuntuales = 0;
        foreach ($boletas as $boleta) {
            $primerPago = $boleta->pagos()->orderBy('fecha_pago')->first();
            if ($primerPago && $primerPago->fecha_pago <= $boleta->fecha_vencimiento) {
                $pagosPuntuales++;
            }
        }

        return round(($pagosPuntuales / $boletas->count()) * 100, 1);
    }

    /**
     * Obtener tendencia de pagos
     */
    private function obtenerTendenciaPagos($idSocio, $meses = 12)
    {
        $fechaInicio = now()->subMonths($meses);

        $pagos = Pago::where('id_socio', $idSocio)
                    ->whereDate('fecha_pago', '>=', $fechaInicio)
                    ->orderBy('fecha_pago')
                    ->get();

        // Agrupar por mes
        return $pagos->groupBy(function($pago) {
            return $pago->fecha_pago->format('Y-m');
        })->map(function($grupo, $periodo) {
            return [
                'periodo' => $periodo,
                'total_pagos' => $grupo->count(),
                'monto_total' => $grupo->sum('monto_pagado'),
                'monto_promedio' => $grupo->avg('monto_pagado')
            ];
        })->values();
    }

    /**
     * Agrupar pagos por período
     */
    private function agruparPorPeriodo($pagos, $periodo)
    {
        switch ($periodo) {
            case 'dia':
                return $pagos->groupBy(function($pago) {
                    return $pago->fecha_pago->format('Y-m-d');
                });
            case 'mes':
                return $pagos->groupBy(function($pago) {
                    return $pago->fecha_pago->format('Y-m');
                });
            case 'trimestre':
                return $pagos->groupBy(function($pago) {
                    return $pago->fecha_pago->format('Y') . '-Q' . ceil($pago->fecha_pago->format('n') / 3);
                });
            case 'año':
                return $pagos->groupBy(function($pago) {
                    return $pago->fecha_pago->format('Y');
                });
            default:
                return $pagos->groupBy(function($pago) {
                    return $pago->fecha_pago->format('Y-m');
                });
        }
    }
}
