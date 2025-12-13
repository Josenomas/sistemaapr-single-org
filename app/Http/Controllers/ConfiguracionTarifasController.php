<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfiguracionTarifa;
use App\Helpers\ActividadHelper;
use Illuminate\Support\Facades\DB;

class ConfiguracionTarifasController extends Controller
{
    /**
     * Mostrar listado de configuraciones tarifarias
     */
    public function index(Request $request)
    {
        $query = ConfiguracionTarifa::activos()->ordenados();

        // Filtro por tipo de cliente
        if ($request->filled('tipo_cliente')) {
            $query->porTipoCliente($request->tipo_cliente);
        }

        // Filtro por nombre de tarifa
        if ($request->filled('nombre_tarifa')) {
            $query->porNombreTarifa($request->nombre_tarifa);
        }

        $tarifas = $query->get();

        // Agrupar por tipo_cliente y nombre_tarifa para mejor visualización
        $tarifasAgrupadas = $tarifas->groupBy(['tipo_cliente', 'nombre_tarifa']);

        $estadisticas = [
            'total_tramos' => $tarifas->count(),
            'monto_minimo' => $tarifas->min('monto'),
            'monto_maximo' => $tarifas->max('monto'),
            'tipos_cliente' => $tarifas->pluck('tipo_cliente')->unique()->count(),
        ];

        // Obtener listas para filtros
        $tiposCliente = ['residencial', 'comercial', 'industrial'];
        $nombresTarifas = ConfiguracionTarifa::activos()
                                            ->distinct()
                                            ->pluck('nombre_tarifa')
                                            ->filter()
                                            ->sort()
                                            ->values();

        return view('configuraciones-tarifas.index', compact('tarifas', 'tarifasAgrupadas', 'estadisticas', 'tiposCliente', 'nombresTarifas'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('configuraciones-tarifas.create');
    }

    /**
     * Guardar nueva configuración tarifaria
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo_cliente' => 'required|in:residencial,comercial,industrial',
            'nombre_tarifa' => 'required|string|max:100',
            'consumo_desde' => 'required|numeric|min:0',
            'consumo_hasta' => 'nullable|numeric|min:0|gt:consumo_desde',
            'monto' => 'required|numeric|min:0',
            'cargo_fijo' => 'nullable|numeric|min:0',
            'iva' => 'nullable|numeric|min:0|max:100',
            'orden' => 'required|integer|min:1',
            'vigente_desde' => 'required|date',
            'vigente_hasta' => 'nullable|date|after_or_equal:vigente_desde',
            'activo' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $tarifa = ConfiguracionTarifa::create($validated);

            ActividadHelper::registrar(
                'Configuraciones Tarifarias',
                "Nueva configuración tarifaria creada: {$tarifa->nombre_tarifa} - {$tarifa->nombre} ({$tarifa->tipo_cliente})",
                auth()->id()
            );

            DB::commit();

            return redirect()->route('configuraciones-tarifas.index')
                           ->with('success', 'Configuración tarifaria creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al crear la configuración: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $tarifa = ConfiguracionTarifa::findOrFail($id);
        return view('configuraciones-tarifas.edit', compact('tarifa'));
    }

    /**
     * Actualizar configuración tarifaria
     */
    public function update(Request $request, $id)
    {
        $tarifa = ConfiguracionTarifa::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo_cliente' => 'required|in:residencial,comercial,industrial',
            'nombre_tarifa' => 'required|string|max:100',
            'consumo_desde' => 'required|numeric|min:0',
            'consumo_hasta' => 'nullable|numeric|min:0|gt:consumo_desde',
            'monto' => 'required|numeric|min:0',
            'cargo_fijo' => 'nullable|numeric|min:0',
            'iva' => 'nullable|numeric|min:0|max:100',
            'orden' => 'required|integer|min:1',
            'vigente_desde' => 'required|date',
            'vigente_hasta' => 'nullable|date|after_or_equal:vigente_desde',
            'activo' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $tarifa->update($validated);

            ActividadHelper::registrar(
                'Configuraciones Tarifarias',
                "Configuración tarifaria actualizada: {$tarifa->nombre_tarifa} - {$tarifa->nombre} ({$tarifa->tipo_cliente})",
                auth()->id()
            );

            DB::commit();

            return redirect()->route('configuraciones-tarifas.index')
                           ->with('success', 'Configuración tarifaria actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar la configuración: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar (desactivar) configuración tarifaria
     */
    public function destroy($id)
    {
        $tarifa = ConfiguracionTarifa::findOrFail($id);

        DB::beginTransaction();
        try {
            $tarifa->update(['activo' => 0]);

            ActividadHelper::registrar(
                'Configuraciones Tarifarias',
                "Configuración tarifaria eliminada: {$tarifa->nombre}",
                auth()->id()
            );

            DB::commit();

            return redirect()->route('configuraciones-tarifas.index')
                           ->with('success', 'Configuración tarifaria eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error al eliminar la configuración: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar orden de tramos
     */
    public function updateOrden(Request $request)
    {
        $validated = $request->validate([
            'ordenes' => 'required|array',
            'ordenes.*' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['ordenes'] as $id => $orden) {
                ConfiguracionTarifa::where('id', $id)->update(['orden' => $orden]);
            }

            ActividadHelper::registrar(
                'Configuraciones Tarifarias',
                "Orden de tramos tarifarios actualizado",
                auth()->id()
            );

            DB::commit();

            return redirect()->route('configuraciones-tarifas.index')
                           ->with('success', 'Orden actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error al actualizar el orden: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar simulador de tarifas
     */
    public function simulador()
    {
        $tarifas = ConfiguracionTarifa::activos()->ordenados()->get();
        return view('configuraciones-tarifas.simulador', compact('tarifas'));
    }

    /**
     * Calcular tarifa según consumo y tipo de cliente (AJAX)
     */
    public function calcular(Request $request)
    {
        $validated = $request->validate([
            'consumo' => 'required|numeric|min:0',
            'tipo_cliente' => 'required|in:residencial,comercial,industrial',
        ]);

        $consumo = $validated['consumo'];
        $tipoCliente = $validated['tipo_cliente'];

        // Usar el método del modelo para calcular
        $resultado = ConfiguracionTarifa::calcularMontoPorConsumo($tipoCliente, $consumo);

        if (isset($resultado['error'])) {
            return response()->json([
                'success' => false,
                'message' => $resultado['error']
            ], 404);
        }

        // Formatear desglose de tramos para la vista
        $desgloseTramos = [];
        if (isset($resultado['tramos_detalle']) && !empty($resultado['tramos_detalle'])) {
            foreach ($resultado['tramos_detalle'] as $tramo) {
                $desgloseTramos[] = [
                    'nombre' => $tramo['nombre'],
                    'rango' => $tramo['rango'],
                    'consumo_m3' => $tramo['m3_en_tramo'],
                    'precio_unitario' => $tramo['valor_unitario'],
                    'monto' => $tramo['subtotal'],
                    'monto_formateado' => '$' . number_format($tramo['subtotal'], 0, ',', '.')
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tipo_cliente' => ucfirst($tipoCliente),
                'consumo' => number_format($consumo, 2, ',', '.'),
                'tramo' => $resultado['tramo']->nombre,
                'rango' => $resultado['tramo']->rango_descripcion,
                'nombre_tarifa' => $resultado['tramo']->nombre_tarifa,
                'monto_base' => $resultado['monto_base'],
                'monto_base_formateado' => '$' . number_format($resultado['monto_base'], 0, ',', '.'),
                'cargo_fijo' => $resultado['cargo_fijo'],
                'cargo_fijo_formateado' => '$' . number_format($resultado['cargo_fijo'], 0, ',', '.'),
                'cargo_consumo' => $resultado['cargo_consumo'],
                'cargo_consumo_formateado' => '$' . number_format($resultado['cargo_consumo'], 0, ',', '.'),
                'porcentaje_iva' => $resultado['iva_porcentaje'],
                'monto_iva' => $resultado['iva'],
                'monto_iva_formateado' => '$' . number_format($resultado['iva'], 0, ',', '.'),
                'total' => $resultado['total'],
                'total_formateado' => '$' . number_format($resultado['total'], 0, ',', '.'),
                'desglose_tramos' => $desgloseTramos
            ]
        ]);
    }
}
