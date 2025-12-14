<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistorialConsumo;
use App\Models\Socio;
use App\Models\Lectura;
use Illuminate\Support\Facades\DB;

class HistorialConsumoController extends Controller
{
    /**
     * Mostrar el historial de consumo con análisis
     */
    public function index(Request $request)
    {
        $query = HistorialConsumo::with(['socio', 'lectura'])->where('activo', 1);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('socio', function($sq) use ($search) {
                    $sq->where('nombre', 'like', "%{$search}%")
                       ->orWhere('apellido_paterno', 'like', "%{$search}%")
                       ->orWhere('apellido_materno', 'like', "%{$search}%")
                       ->orWhere('rut', 'like', "%{$search}%");
                })->orWhere('periodo', 'like', "%{$search}%");
            });
        }

        if ($request->filled('socio')) {
            $query->where('id_socio', $request->socio);
        }

        if ($request->filled('periodo')) {
            $query->where('periodo', $request->periodo);
        }

        if ($request->filled('anomalia')) {
            $query->where('anomalia', $request->anomalia);
        }

        if ($request->filled('consumo_min')) {
            $query->where('consumo_m3', '>=', $request->consumo_min);
        }

        if ($request->filled('consumo_max')) {
            $query->where('consumo_m3', '<=', $request->consumo_max);
        }

        // Estadísticas
        $estadisticas = [
            'total_registros' => HistorialConsumo::where('activo', 1)->count(),
            'consumo_total' => HistorialConsumo::where('activo', 1)->sum('consumo_m3'),
            'promedio_consumo' => HistorialConsumo::where('activo', 1)->avg('consumo_m3'),
            'con_anomalias' => HistorialConsumo::where('activo', 1)->whereIn('anomalia', ['alto', 'bajo', 'cero'])->count(),
            'consumo_alto' => HistorialConsumo::where('activo', 1)->where('anomalia', 'alto')->count(),
            'consumo_bajo' => HistorialConsumo::where('activo', 1)->where('anomalia', 'bajo')->count(),
            'sin_consumo' => HistorialConsumo::where('activo', 1)->where('anomalia', 'cero')->count()
        ];

        // Obtener períodos disponibles para el filtro
        $periodos = HistorialConsumo::where('activo', 1)
                                   ->select('periodo')
                                   ->distinct()
                                   ->orderBy('periodo', 'desc')
                                   ->pluck('periodo');

        $socios = Socio::activos()->orderBy('nombre')->get();

        $historiales = $query->orderBy('periodo', 'desc')
                            ->orderBy('id', 'desc')
                            ->paginate(20);

        return view('historial-consumo.index', compact('historiales', 'estadisticas', 'periodos', 'socios'));
    }

    /**
     * Mostrar detalle del historial de consumo de un socio
     */
    public function show($id)
    {
        $historial = HistorialConsumo::with(['socio', 'lectura'])->findOrFail($id);

        // Obtener historial completo del socio (últimos 12 meses)
        $tendencia = HistorialConsumo::obtenerTendenciaPorSocio($historial->id_socio, 12);

        return view('historial-consumo.show', compact('historial', 'tendencia'));
    }

    /**
     * Mostrar análisis detallado por socio
     */
    public function analisisSocio($idSocio)
    {
        $socio = Socio::findOrFail($idSocio);

        // Obtener todo el historial del socio
        $historiales = HistorialConsumo::where('id_socio', $idSocio)
                                      ->where('activo', 1)
                                      ->orderBy('periodo', 'desc')
                                      ->get();

        // Estadísticas del socio
        $estadisticas = [
            'total_periodos' => $historiales->count(),
            'consumo_total' => $historiales->sum('consumo_m3'),
            'consumo_promedio' => $historiales->avg('consumo_m3'),
            'consumo_maximo' => $historiales->max('consumo_m3'),
            'consumo_minimo' => $historiales->min('consumo_m3'),
            'monto_total' => $historiales->sum('monto_consumo'),
            'anomalias' => $historiales->whereIn('anomalia', ['alto', 'bajo', 'cero'])->count(),
            'ultimo_periodo' => $historiales->first() ? $historiales->first()->periodo : 'N/A',
            'promedio_6_meses' => HistorialConsumo::calcularPromedioPorSocio($idSocio, 6),
            'promedio_12_meses' => HistorialConsumo::calcularPromedioPorSocio($idSocio, 12)
        ];

        // Datos para gráfico de tendencia
        $tendencia = HistorialConsumo::obtenerTendenciaPorSocio($idSocio, 12);

        return view('historial-consumo.analisis-socio', compact('socio', 'historiales', 'estadisticas', 'tendencia'));
    }

    /**
     * Comparar consumo entre socios
     */
    public function comparar(Request $request)
    {
        $socios = Socio::activos()->orderBy('nombre')->get();

        $comparacion = null;
        $periodo = $request->periodo ?? date('Y-m');

        if ($request->filled('socios_comparar')) {
            $sociosIds = $request->socios_comparar;

            $comparacion = HistorialConsumo::whereIn('id_socio', $sociosIds)
                                          ->where('periodo', $periodo)
                                          ->where('activo', 1)
                                          ->with('socio')
                                          ->get();

            // Calcular estadísticas de comparación
            $estadisticasComparacion = [
                'promedio_grupo' => $comparacion->avg('consumo_m3'),
                'maximo' => $comparacion->max('consumo_m3'),
                'minimo' => $comparacion->min('consumo_m3'),
                'desviacion' => $comparacion->count() > 1 ? $this->calcularDesviacionEstandar($comparacion->pluck('consumo_m3')->toArray()) : 0
            ];
        } else {
            $estadisticasComparacion = null;
        }

        // Obtener períodos disponibles
        $periodos = HistorialConsumo::where('activo', 1)
                                   ->select('periodo')
                                   ->distinct()
                                   ->orderBy('periodo', 'desc')
                                   ->pluck('periodo');

        return view('historial-consumo.comparar', compact('socios', 'comparacion', 'estadisticasComparacion', 'periodo', 'periodos'));
    }

    /**
     * Generar reporte de consumo
     */
    public function reporte(Request $request)
    {
        $validated = $request->validate([
            'tipo_reporte' => 'required|in:periodo,rango,socio,anomalias',
            'periodo' => 'nullable|string',
            'periodo_inicio' => 'nullable|string',
            'periodo_fin' => 'nullable|string',
            'id_socio' => 'nullable|exists:socios,id'
        ]);

        $query = HistorialConsumo::with(['socio', 'lectura'])->where('activo', 1);

        switch ($validated['tipo_reporte']) {
            case 'periodo':
                if ($request->filled('periodo')) {
                    $query->where('periodo', $request->periodo);
                }
                break;

            case 'rango':
                if ($request->filled('periodo_inicio')) {
                    $query->where('periodo', '>=', $request->periodo_inicio);
                }
                if ($request->filled('periodo_fin')) {
                    $query->where('periodo', '<=', $request->periodo_fin);
                }
                break;

            case 'socio':
                if ($request->filled('id_socio')) {
                    $query->where('id_socio', $request->id_socio);
                }
                break;

            case 'anomalias':
                $query->whereIn('anomalia', ['alto', 'bajo', 'cero']);
                break;
        }

        $datos = $query->orderBy('periodo', 'desc')->get();

        // Estadísticas del reporte
        $estadisticas = [
            'total_registros' => $datos->count(),
            'consumo_total' => $datos->sum('consumo_m3'),
            'consumo_promedio' => $datos->avg('consumo_m3'),
            'monto_total' => $datos->sum('monto_consumo')
        ];

        return view('historial-consumo.reporte', compact('datos', 'estadisticas', 'validated'));
    }

    /**
     * Sincronizar historial desde lecturas
     */
    public function sincronizar(Request $request)
    {
        DB::beginTransaction();
        try {
            // Obtener todas las lecturas que no tienen historial de consumo
            $lecturas = Lectura::with(['socio', 'boleta'])
                              ->whereNotIn('id', function($query) {
                                  $query->select('id_lectura')
                                        ->from('historial_consumo')
                                        ->where('activo', 1);
                              })
                              ->get();

            $sincronizados = 0;

            foreach ($lecturas as $lectura) {
                // Obtener la lectura anterior del socio
                $lecturaAnterior = Lectura::where('id_socio', $lectura->id_socio)
                                         ->where('fecha_lectura', '<', $lectura->fecha_lectura)
                                         ->orderBy('fecha_lectura', 'desc')
                                         ->first();

                $lecturaAnt = $lecturaAnterior ? $lecturaAnterior->lectura_actual : 0;
                $consumo = $lectura->lectura_actual - $lecturaAnt;

                // Calcular promedio histórico para detectar anomalías
                $promedioHistorico = HistorialConsumo::calcularPromedioPorSocio($lectura->id_socio, 6);
                $anomalia = HistorialConsumo::detectarAnomalia($consumo, $promedioHistorico);

                // Calcular promedio diario (asumiendo 30 días por mes)
                $promedioDiario = $consumo / 30;

                // Calcular periodo en formato YYYY-MM
                $periodo = $lectura->fecha_lectura->format('Y-m');

                // Crear o actualizar registro de historial (evitar duplicados)
                HistorialConsumo::updateOrCreate(
                    [
                        'id_socio' => $lectura->id_socio,
                        'periodo' => $periodo
                    ],
                    [
                        'id_lectura' => $lectura->id,
                        'lectura_anterior' => $lecturaAnt,
                        'lectura_actual' => $lectura->lectura_actual,
                        'consumo_m3' => $consumo,
                        'monto_consumo' => $lectura->boleta ? $lectura->boleta->total : 0,
                        'promedio_diario' => $promedioDiario,
                        'anomalia' => $anomalia,
                        'observaciones' => $lectura->observaciones
                    ]
                );

                $sincronizados++;
            }

            DB::commit();

            return redirect()->route('historial-consumo.index')
                           ->with('success', "Se sincronizaron {$sincronizados} registros de consumo exitosamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error al sincronizar historial: ' . $e->getMessage());
        }
    }

    /**
     * Descargar comparación de consumo en PDF
     */
    public function descargarComparacion(Request $request)
    {
        $periodo = $request->periodo ?? date('Y-m');
        $sociosIds = $request->socios_comparar ?? [];

        if (empty($sociosIds)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos un socio para generar el PDF.');
        }

        $comparacion = HistorialConsumo::whereIn('id_socio', $sociosIds)
                                      ->where('periodo', $periodo)
                                      ->where('activo', 1)
                                      ->with('socio')
                                      ->get();

        if ($comparacion->isEmpty()) {
            return redirect()->back()->with('error', 'No hay datos para el período y socios seleccionados.');
        }

        // Calcular estadísticas de comparación
        $estadisticasComparacion = [
            'promedio_grupo' => $comparacion->avg('consumo_m3'),
            'maximo' => $comparacion->max('consumo_m3'),
            'minimo' => $comparacion->min('consumo_m3'),
            'total' => $comparacion->sum('consumo_m3'),
            'desviacion' => $comparacion->count() > 1 ? $this->calcularDesviacionEstandar($comparacion->pluck('consumo_m3')->toArray()) : 0
        ];

        $pdf = \PDF::loadView('historial-consumo.pdf.comparacion', compact('comparacion', 'estadisticasComparacion', 'periodo'));
        return $pdf->download('comparacion-consumo-' . $periodo . '.pdf');
    }

    /**
     * Calcular desviación estándar
     */
    private function calcularDesviacionEstandar($valores)
    {
        $count = count($valores);
        if ($count == 0) return 0;

        $promedio = array_sum($valores) / $count;
        $varianza = 0;

        foreach ($valores as $valor) {
            $varianza += pow($valor - $promedio, 2);
        }

        $varianza = $varianza / $count;
        return sqrt($varianza);
    }
}
