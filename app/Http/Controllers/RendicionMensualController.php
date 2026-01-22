<?php

namespace App\Http\Controllers;

use App\Models\RendicionMensual;
use App\Models\ActividadReciente;
use App\Models\Pago;
use App\Models\Compra;
use App\Models\Sueldo;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class RendicionMensualController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RendicionMensual::query()->where('activo', true);

        // Filtros
        if ($request->filled('anio')) {
            $query->where('anio', $request->anio);
        }

        if ($request->filled('mes')) {
            $query->where('mes', $request->mes);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('codigo_rendicion', 'like', "%{$search}%")
                  ->orWhere('periodo', 'like', "%{$search}%");
            });
        }

        $rendiciones = $query->orderBy('anio', 'desc')
                             ->orderBy('mes', 'desc')
                             ->paginate(12)
                             ->appends($request->only(['anio', 'mes', 'estado', 'search']));

        // Estadísticas generales
        $estadisticas = [
            'total_rendiciones' => RendicionMensual::where('activo', true)->count(),
            'abiertas' => RendicionMensual::where('activo', true)->where('estado', 'abierto')->count(),
            'cerradas' => RendicionMensual::where('activo', true)->where('estado', 'cerrado')->count(),
            'anio_actual' => RendicionMensual::where('activo', true)->where('anio', date('Y'))->count()
        ];

        // Obtener años disponibles para el filtro
        $aniosDisponibles = RendicionMensual::where('activo', true)
            ->selectRaw('DISTINCT anio')
            ->orderBy('anio', 'desc')
            ->pluck('anio');

        return view('rendiciones-mensuales.index', compact('rendiciones', 'estadisticas', 'aniosDisponibles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Obtener el último saldo final para usarlo como saldo anterior
        $ultimaRendicion = RendicionMensual::where('activo', true)
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->first();

        $saldoAnterior = $ultimaRendicion ? $ultimaRendicion->saldo_final : 0;

        // Si se envió mes y año, calcular montos automáticos
        $montosCalculados = null;
        $detalles = null;

        if ($request->filled('mes') && $request->filled('anio')) {
            $mes = $request->mes;
            $anio = $request->anio;
            $periodo = sprintf('%04d-%02d', $anio, $mes);

            // Calcular fechas del periodo
            $fechaInicio = "{$anio}-{$mes}-01";
            $fechaFin = date("Y-m-t", strtotime($fechaInicio));

            $montosCalculados = $this->calcularMontosAutomaticos($periodo, $fechaInicio, $fechaFin);
            $detalles = $this->obtenerDetallesTransacciones($periodo, $fechaInicio, $fechaFin);
        }

        return view('rendiciones-mensuales.create', compact('saldoAnterior', 'montosCalculados', 'detalles'));
    }

    /**
     * Calcular montos automáticos desde las tablas del sistema
     */
    private function calcularMontosAutomaticos($periodo, $fechaInicio, $fechaFin)
    {
        // INGRESOS

        // Ingresos por consumo de agua (pagos recibidos)
        $ingresosConsumoAgua = Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
            ->sum('monto_pagado');

        // EGRESOS

        // Remuneraciones (sueldos pagados)
        $egresosRemuneraciones = Sueldo::where('periodo', $periodo)
            ->where('estado', 'pagado')
            ->where('activo', true)
            ->sum('total_liquido');

        // Energía eléctrica (compras con descripción relacionada)
        $egresosEnergiaElectrica = Compra::whereBetween('fecha_compra', [$fechaInicio, $fechaFin])
            ->where('activo', true)
            ->where('estado', 'pagada')
            ->where(function($q) {
                $q->where('descripcion', 'LIKE', '%energía%')
                  ->orWhere('descripcion', 'LIKE', '%eléctrica%')
                  ->orWhere('descripcion', 'LIKE', '%electricidad%')
                  ->orWhere('descripcion', 'LIKE', '%luz%');
            })
            ->sum('total');

        // Productos químicos (compras con descripción relacionada)
        $egresosProductosQuimicos = Compra::whereBetween('fecha_compra', [$fechaInicio, $fechaFin])
            ->where('activo', true)
            ->where('estado', 'pagada')
            ->where(function($q) {
                $q->where('descripcion', 'LIKE', '%químico%')
                  ->orWhere('descripcion', 'LIKE', '%cloro%')
                  ->orWhere('descripcion', 'LIKE', '%hipoclorito%')
                  ->orWhere('descripcion', 'LIKE', '%sulfato%');
            })
            ->sum('total');

        // Reparaciones (compras tipo materiales, herramientas, equipos)
        $egresosReparaciones = Compra::whereBetween('fecha_compra', [$fechaInicio, $fechaFin])
            ->where('activo', true)
            ->where('estado', 'pagada')
            ->whereIn('tipo_compra', ['materiales', 'herramientas', 'equipos'])
            ->where(function($q) {
                // Excluir las que ya se contaron en energía y químicos
                $q->where('descripcion', 'NOT LIKE', '%energía%')
                  ->where('descripcion', 'NOT LIKE', '%eléctrica%')
                  ->where('descripcion', 'NOT LIKE', '%electricidad%')
                  ->where('descripcion', 'NOT LIKE', '%luz%')
                  ->where('descripcion', 'NOT LIKE', '%químico%')
                  ->where('descripcion', 'NOT LIKE', '%cloro%')
                  ->where('descripcion', 'NOT LIKE', '%hipoclorito%')
                  ->where('descripcion', 'NOT LIKE', '%sulfato%');
            })
            ->sum('total');

        // Gastos administrativos (compras tipo servicios e insumos)
        $egresosGastosAdministrativos = Compra::whereBetween('fecha_compra', [$fechaInicio, $fechaFin])
            ->where('activo', true)
            ->where('estado', 'pagada')
            ->whereIn('tipo_compra', ['servicios', 'insumos'])
            ->where(function($q) {
                // Excluir las ya contadas
                $q->where('descripcion', 'NOT LIKE', '%energía%')
                  ->where('descripcion', 'NOT LIKE', '%eléctrica%')
                  ->where('descripcion', 'NOT LIKE', '%electricidad%')
                  ->where('descripcion', 'NOT LIKE', '%luz%');
            })
            ->sum('total');

        return [
            'ingresos_consumo_agua' => $ingresosConsumoAgua ?? 0,
            'ingresos_subsidios' => 0,
            'ingresos_aportes_socios' => 0,
            'ingresos_multas' => 0,
            'ingresos_incorporaciones' => 0,
            'ingresos_otros' => 0,
            'egresos_energia_electrica' => $egresosEnergiaElectrica ?? 0,
            'egresos_productos_quimicos' => $egresosProductosQuimicos ?? 0,
            'egresos_reparaciones' => $egresosReparaciones ?? 0,
            'egresos_remuneraciones' => $egresosRemuneraciones ?? 0,
            'egresos_gastos_administrativos' => $egresosGastosAdministrativos ?? 0,
            'egresos_otros' => 0,
        ];
    }

    /**
     * Obtener detalles de las transacciones para mostrar el desglose
     */
    private function obtenerDetallesTransacciones($periodo, $fechaInicio, $fechaFin)
    {
        return [
            'pagos' => Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                ->with('socio')
                ->orderBy('fecha_pago', 'desc')
                ->get(),

            'sueldos' => Sueldo::where('periodo', $periodo)
                ->where('estado', 'pagado')
                ->where('activo', true)
                ->with('funcionario')
                ->get(),

            'compras' => Compra::whereBetween('fecha_compra', [$fechaInicio, $fechaFin])
                ->where('activo', true)
                ->where('estado', 'pagada')
                ->orderBy('fecha_compra', 'desc')
                ->get(),
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mes' => 'required|integer|min:1|max:12',
            'anio' => 'required|integer|min:2020|max:2100',
            'saldo_anterior' => 'required|numeric|min:0',
            'ingresos_consumo_agua' => 'nullable|numeric|min:0',
            'ingresos_subsidios' => 'nullable|numeric|min:0',
            'ingresos_aportes_socios' => 'nullable|numeric|min:0',
            'ingresos_multas' => 'nullable|numeric|min:0',
            'ingresos_incorporaciones' => 'nullable|numeric|min:0',
            'ingresos_otros' => 'nullable|numeric|min:0',
            'egresos_energia_electrica' => 'nullable|numeric|min:0',
            'egresos_productos_quimicos' => 'nullable|numeric|min:0',
            'egresos_reparaciones' => 'nullable|numeric|min:0',
            'egresos_remuneraciones' => 'nullable|numeric|min:0',
            'egresos_gastos_administrativos' => 'nullable|numeric|min:0',
            'egresos_otros' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        // Verificar si ya existe una rendición para este periodo
        $existe = RendicionMensual::where('mes', $validated['mes'])
            ->where('anio', $validated['anio'])
            ->where('activo', true)
            ->exists();

        if ($existe) {
            return back()->withErrors(['mes' => 'Ya existe una rendición para este periodo.'])->withInput();
        }

        // Crear rendición
        $rendicion = new RendicionMensual($validated);
        $rendicion->codigo_rendicion = RendicionMensual::generarCodigoRendicion();
        $rendicion->periodo = sprintf('%04d-%02d', $validated['anio'], $validated['mes']);

        // Asignar responsable solo si el usuario existe en la BD
        $userId = Auth::id();
        if (\App\Models\Usuario::find($userId)) {
            $rendicion->id_responsable = $userId;
        }

        // Calcular totales
        $rendicion->calcularTotales();
        $rendicion->save();

        // Registrar actividad
        ActividadReciente::registrar(
            'rendicion_creada',
            'Nueva rendición mensual creada: ' . $rendicion->codigo_rendicion . ' - ' . $rendicion->periodo_texto,
            $rendicion->id
        );

        return redirect()->route('rendiciones-mensuales.show', $rendicion->id)
            ->with('success', 'Rendición mensual creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $rendicion = RendicionMensual::with(['usuarioCierre', 'responsable'])->findOrFail($id);

        // Obtener rendición del mes anterior para comparación
        $rendicionAnterior = RendicionMensual::where('activo', true)
            ->where(function($q) use ($rendicion) {
                $q->where('anio', $rendicion->anio)
                  ->where('mes', '<', $rendicion->mes);
            })
            ->orWhere(function($q) use ($rendicion) {
                $q->where('anio', '<', $rendicion->anio);
            })
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->first();

        // Calcular variaciones
        $variaciones = null;
        if ($rendicionAnterior) {
            $variaciones = [
                'ingresos' => $rendicion->total_ingresos - $rendicionAnterior->total_ingresos,
                'egresos' => $rendicion->total_egresos - $rendicionAnterior->total_egresos,
                'saldo' => $rendicion->saldo_final - $rendicionAnterior->saldo_final,
                'ingresos_porcentaje' => $rendicionAnterior->total_ingresos > 0
                    ? (($rendicion->total_ingresos - $rendicionAnterior->total_ingresos) / $rendicionAnterior->total_ingresos) * 100
                    : 0,
                'egresos_porcentaje' => $rendicionAnterior->total_egresos > 0
                    ? (($rendicion->total_egresos - $rendicionAnterior->total_egresos) / $rendicionAnterior->total_egresos) * 100
                    : 0
            ];
        }

        return view('rendiciones-mensuales.show', compact('rendicion', 'rendicionAnterior', 'variaciones'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $rendicion = RendicionMensual::findOrFail($id);

        // No permitir editar si está cerrada
        if ($rendicion->estado === 'cerrado') {
            return redirect()->route('rendiciones-mensuales.show', $rendicion->id)
                ->with('error', 'No se puede editar una rendición cerrada. Debe reabrirla primero.');
        }

        return view('rendiciones-mensuales.edit', compact('rendicion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rendicion = RendicionMensual::findOrFail($id);

        // No permitir editar si está cerrada
        if ($rendicion->estado === 'cerrado') {
            return redirect()->route('rendiciones-mensuales.show', $rendicion->id)
                ->with('error', 'No se puede editar una rendición cerrada.');
        }

        $validated = $request->validate([
            'mes' => 'required|integer|min:1|max:12',
            'anio' => 'required|integer|min:2020|max:2100',
            'saldo_anterior' => 'required|numeric|min:0',
            'ingresos_consumo_agua' => 'nullable|numeric|min:0',
            'ingresos_subsidios' => 'nullable|numeric|min:0',
            'ingresos_aportes_socios' => 'nullable|numeric|min:0',
            'ingresos_multas' => 'nullable|numeric|min:0',
            'ingresos_incorporaciones' => 'nullable|numeric|min:0',
            'ingresos_otros' => 'nullable|numeric|min:0',
            'egresos_energia_electrica' => 'nullable|numeric|min:0',
            'egresos_productos_quimicos' => 'nullable|numeric|min:0',
            'egresos_reparaciones' => 'nullable|numeric|min:0',
            'egresos_remuneraciones' => 'nullable|numeric|min:0',
            'egresos_gastos_administrativos' => 'nullable|numeric|min:0',
            'egresos_otros' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        // Verificar si el periodo cambió y ya existe otra rendición
        if ($rendicion->mes != $validated['mes'] || $rendicion->anio != $validated['anio']) {
            $existe = RendicionMensual::where('mes', $validated['mes'])
                ->where('anio', $validated['anio'])
                ->where('id', '!=', $id)
                ->where('activo', true)
                ->exists();

            if ($existe) {
                return back()->withErrors(['mes' => 'Ya existe una rendición para este periodo.'])->withInput();
            }
        }

        $rendicion->fill($validated);
        $rendicion->periodo = sprintf('%04d-%02d', $validated['anio'], $validated['mes']);

        // Recalcular totales
        $rendicion->calcularTotales();
        $rendicion->save();

        // Registrar actividad
        ActividadReciente::registrar(
            'rendicion_actualizada',
            'Rendición mensual actualizada: ' . $rendicion->codigo_rendicion . ' - ' . $rendicion->periodo_texto,
            $rendicion->id
        );

        return redirect()->route('rendiciones-mensuales.show', $rendicion->id)
            ->with('success', 'Rendición mensual actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $rendicion = RendicionMensual::findOrFail($id);

        // No permitir eliminar si está cerrada
        if ($rendicion->estado === 'cerrado') {
            return redirect()->route('rendiciones-mensuales.index')
                ->with('error', 'No se puede eliminar una rendición cerrada.');
        }

        $codigo = $rendicion->codigo_rendicion;
        $periodo = $rendicion->periodo_texto;

        // Soft delete
        $rendicion->activo = false;
        $rendicion->save();

        // Registrar actividad
        ActividadReciente::registrar(
            'rendicion_eliminada',
            'Rendición mensual eliminada: ' . $codigo . ' - ' . $periodo,
            null
        );

        return redirect()->route('rendiciones-mensuales.index')
            ->with('success', 'Rendición mensual eliminada exitosamente.');
    }

    /**
     * Cerrar mes de rendición
     */
    public function cerrarMes(Request $request, $id)
    {
        $rendicion = RendicionMensual::findOrFail($id);

        if ($rendicion->estado === 'cerrado') {
            return back()->with('error', 'Esta rendición ya está cerrada.');
        }

        $request->validate([
            'notas_cierre' => 'nullable|string|max:500'
        ]);

        $rendicion->cerrar(Auth::id(), $request->notas_cierre);

        return back()->with('success', 'Rendición cerrada exitosamente. Ya no se puede modificar.');
    }

    /**
     * Reabrir mes de rendición
     */
    public function reabrirMes($id)
    {
        $rendicion = RendicionMensual::findOrFail($id);

        if ($rendicion->estado === 'abierto') {
            return back()->with('error', 'Esta rendición ya está abierta.');
        }

        $rendicion->reabrir(Auth::id());

        return back()->with('success', 'Rendición reabierta exitosamente. Ahora puede ser modificada.');
    }

    /**
     * Exportar a PDF
     */
    public function exportarPDF($id)
    {
        $rendicion = RendicionMensual::with(['usuarioCierre', 'responsable'])->findOrFail($id);

        $pdf = Pdf::loadView('rendiciones-mensuales.pdf', compact('rendicion'));

        $nombreArchivo = 'Rendicion_' . $rendicion->codigo_rendicion . '_' . $rendicion->periodo . '.pdf';

        // Registrar actividad
        ActividadReciente::registrar(
            'rendicion_exportada',
            'Rendición mensual exportada a PDF: ' . $rendicion->codigo_rendicion,
            $rendicion->id
        );

        return $pdf->download($nombreArchivo);
    }
}
