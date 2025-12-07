<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Inventario;
use App\Models\Funcionario;
use App\Helpers\ActividadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimientosInventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = MovimientoInventario::activos()->with(['producto', 'responsable']);

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_movimiento', 'like', "%{$search}%")
                  ->orWhere('motivo', 'like', "%{$search}%")
                  ->orWhere('destino', 'like', "%{$search}%")
                  ->orWhereHas('producto', function($pq) use ($search) {
                      $pq->where('nombre', 'like', "%{$search}%")
                        ->orWhere('codigo_producto', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por tipo
        if ($request->filled('tipo_movimiento')) {
            $query->porTipo($request->tipo_movimiento);
        }

        // Filtro por producto
        if ($request->filled('id_producto')) {
            $query->porProducto($request->id_producto);
        }

        // Filtro por responsable
        if ($request->filled('id_responsable')) {
            $query->where('id_responsable', $request->id_responsable);
        }

        // Filtro por fecha desde
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_movimiento', '>=', $request->fecha_desde);
        }

        // Filtro por fecha hasta
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_movimiento', '<=', $request->fecha_hasta);
        }

        $movimientos = $query->orderBy('fecha_movimiento', 'desc')
                            ->orderBy('id', 'desc')
                            ->paginate(15);

        // Estadísticas
        $estadisticas = [
            'total_movimientos' => MovimientoInventario::activos()->count(),
            'entradas' => MovimientoInventario::activos()->entradas()->count(),
            'salidas' => MovimientoInventario::activos()->salidas()->count(),
            'ajustes' => MovimientoInventario::activos()->ajustes()->count()
        ];

        $productos = Inventario::activos()->orderBy('nombre')->get();
        $funcionarios = Funcionario::activos()->where('estado', 'activo')->orderBy('nombre')->get();

        return view('movimientos-inventario.index', compact('movimientos', 'estadisticas', 'productos', 'funcionarios'));
    }

    public function create()
    {
        $productos = Inventario::activos()->orderBy('nombre')->get();
        $funcionarios = Funcionario::activos()->where('estado', 'activo')->orderBy('nombre')->get();

        return view('movimientos-inventario.create', compact('productos', 'funcionarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_producto' => 'required|exists:inventario,id',
            'tipo_movimiento' => 'required|in:entrada,salida,ajuste',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'id_responsable' => 'nullable|exists:funcionarios,id',
            'destino' => 'nullable|string|max:200',
            'documento_referencia' => 'nullable|string|max:100',
            'fecha_movimiento' => 'required|date',
            'observaciones' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Obtener producto
            $producto = Inventario::findOrFail($validated['id_producto']);

            // Validar cantidad para salidas
            if ($validated['tipo_movimiento'] === 'salida') {
                if ($validated['cantidad'] > $producto->cantidad_actual) {
                    return redirect()->back()
                                   ->withInput()
                                   ->with('error', "No hay suficiente stock. Disponible: {$producto->cantidad_actual} {$producto->unidad_medida}");
                }
            }

            // Calcular cantidades
            $cantidadAnterior = $producto->cantidad_actual;
            $cantidadNueva = $cantidadAnterior;

            switch ($validated['tipo_movimiento']) {
                case 'entrada':
                    $cantidadNueva = $cantidadAnterior + $validated['cantidad'];
                    break;
                case 'salida':
                    $cantidadNueva = $cantidadAnterior - $validated['cantidad'];
                    break;
                case 'ajuste':
                    $cantidadNueva = $validated['cantidad'];
                    break;
            }

            // Generar número de movimiento
            $validated['numero_movimiento'] = MovimientoInventario::generarNumeroMovimiento();
            $validated['cantidad_anterior'] = $cantidadAnterior;
            $validated['cantidad_nueva'] = $cantidadNueva;
            $validated['activo'] = 1;

            // Crear movimiento
            $movimiento = MovimientoInventario::create($validated);

            // Actualizar cantidad en inventario
            $producto->update([
                'cantidad_actual' => $cantidadNueva,
                'fecha_ultimo_movimiento' => $validated['fecha_movimiento']
            ]);

            // Actualizar estado del producto
            if ($cantidadNueva <= 0) {
                $producto->update(['estado' => 'agotado']);
            } elseif ($cantidadNueva <= $producto->cantidad_minima) {
                $producto->update(['estado' => 'bajo_stock']);
            } else {
                $producto->update(['estado' => 'disponible']);
            }

            // Registrar actividad
            $detalles = [
                'N°: ' . $movimiento->numero_movimiento,
                'Tipo: ' . $movimiento->tipo_movimiento_texto,
                'Producto: ' . $producto->nombre,
                'Cantidad: ' . $validated['cantidad'] . ' ' . $producto->unidad_medida,
                'Stock: ' . $cantidadAnterior . ' → ' . $cantidadNueva
            ];

            if (!empty($validated['destino'])) {
                $detalles[] = 'Destino: ' . $validated['destino'];
            }

            ActividadHelper::registrar(
                'Movimientos Inventario',
                'Nuevo movimiento creado: ' . implode(' | ', $detalles),
                auth()->id()
            );

            DB::commit();

            return redirect()->route('movimientos-inventario.imprimir', $movimiento->id)
                           ->with('success', 'Movimiento registrado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al crear el movimiento: ' . $e->getMessage());
        }
    }

    public function imprimir(MovimientoInventario $movimientosInventario)
    {
        $movimientosInventario->load(['producto', 'responsable']);
        return view('movimientos-inventario.imprimir', ['movimiento' => $movimientosInventario]);
    }

    public function show(MovimientoInventario $movimientosInventario)
    {
        $movimientosInventario->load(['producto', 'responsable']);
        return view('movimientos-inventario.show', ['movimiento' => $movimientosInventario]);
    }

    public function edit(MovimientoInventario $movimientosInventario)
    {
        $productos = Inventario::activos()->orderBy('nombre')->get();
        $funcionarios = Funcionario::activos()->where('estado', 'activo')->orderBy('nombre')->get();

        return view('movimientos-inventario.edit', [
            'movimiento' => $movimientosInventario,
            'productos' => $productos,
            'funcionarios' => $funcionarios
        ]);
    }

    public function update(Request $request, MovimientoInventario $movimientosInventario)
    {
        $validated = $request->validate([
            'motivo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'id_responsable' => 'nullable|exists:funcionarios,id',
            'destino' => 'nullable|string|max:200',
            'documento_referencia' => 'nullable|string|max:100',
            'fecha_movimiento' => 'required|date',
            'observaciones' => 'nullable|string'
        ]);

        try {
            // Tracking de cambios
            $cambios = [];

            if ($movimientosInventario->motivo != $validated['motivo']) {
                $cambios[] = "Motivo: '{$movimientosInventario->motivo}' → '{$validated['motivo']}'";
            }

            if ($movimientosInventario->destino != $validated['destino']) {
                $cambios[] = "Destino: '{$movimientosInventario->destino}' → '{$validated['destino']}'";
            }

            if ($movimientosInventario->documento_referencia != $validated['documento_referencia']) {
                $cambios[] = "Doc. Ref.: '{$movimientosInventario->documento_referencia}' → '{$validated['documento_referencia']}'";
            }

            $movimientosInventario->update($validated);

            if (!empty($cambios)) {
                ActividadHelper::registrar(
                    'Movimientos Inventario',
                    "Movimiento actualizado [{$movimientosInventario->numero_movimiento}]: " . implode(' | ', $cambios),
                    auth()->id()
                );
            }

            return redirect()->route('movimientos-inventario.show', $movimientosInventario->id)
                           ->with('success', 'Movimiento actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar el movimiento: ' . $e->getMessage());
        }
    }

    public function destroy(MovimientoInventario $movimientosInventario)
    {
        try {
            $numeroMovimiento = $movimientosInventario->numero_movimiento;
            $movimientosInventario->activo = 0;
            $movimientosInventario->save();

            ActividadHelper::registrar(
                'Movimientos Inventario',
                "Movimiento eliminado: {$numeroMovimiento}",
                auth()->id()
            );

            return redirect()->route('movimientos-inventario.index')
                           ->with('success', 'Movimiento eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error al eliminar el movimiento: ' . $e->getMessage());
        }
    }
}
