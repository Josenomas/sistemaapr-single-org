<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Auditoria;
use App\Helpers\ActividadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventario::activos()->orderBy('nombre');

        // Filtros
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('codigo_producto', 'LIKE', "%{$buscar}%")
                  ->orWhere('descripcion', 'LIKE', "%{$buscar}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('alerta') && $request->alerta == 'bajo_stock') {
            $query->bajoStock();
        }

        $inventario = $query->paginate(15);

        // Estadísticas
        $estadisticas = [
            'total_productos' => Inventario::activos()->count(),
            'bajo_stock' => Inventario::activos()->bajoStock()->count(),
            'agotados' => Inventario::activos()->agotados()->count(),
            'valor_total' => Inventario::activos()->get()->sum('valor_total')
        ];

        return view('inventario.index', compact('inventario', 'estadisticas'));
    }

    public function create()
    {
        $codigoProducto = Inventario::generarCodigoProducto();
        return view('inventario.create', compact('codigoProducto'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_producto' => 'required|string|max:50|unique:inventario,codigo_producto',
            'nombre' => 'required|string|max:200',
            'categoria' => 'required|in:materiales,equipos,herramientas,insumos,quimicos,repuestos,otro',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:50',
            'cantidad_actual' => 'required|numeric|min:0',
            'cantidad_minima' => 'required|numeric|min:0',
            'cantidad_maxima' => 'nullable|numeric|min:0',
            'precio_unitario' => 'nullable|numeric|min:0',
            'ubicacion' => 'nullable|string|max:100',
            'proveedor' => 'nullable|string|max:200',
            'fecha_ultima_compra' => 'nullable|date',
            'observaciones' => 'nullable|string'
        ]);

        $validated['activo'] = 1;
        $validated['fecha_ultimo_movimiento'] = now();

        // Determinar estado inicial
        if ($validated['cantidad_actual'] <= 0) {
            $validated['estado'] = 'agotado';
        } elseif ($validated['cantidad_actual'] <= $validated['cantidad_minima']) {
            $validated['estado'] = 'bajo_stock';
        } else {
            $validated['estado'] = 'disponible';
        }

        DB::beginTransaction();
        try {
            $producto = Inventario::create($validated);

            // Registrar actividad
            $categoriaTexto = $producto->categoria_texto;
            $estadoTexto = $producto->estado_texto;

            ActividadHelper::registrar(
                'Inventario',
                "Nuevo producto registrado: {$producto->codigo_producto} - {$producto->nombre} - {$categoriaTexto} - Cantidad: {$producto->cantidad_actual_formateada} - Estado: {$estadoTexto}",
                auth()->id()
            );

            // Registrar en auditoría
            Auditoria::registrar(
                'inventario',
                'crear',
                "Creó producto: {$producto->codigo_producto} - {$producto->nombre} - Cantidad: {$producto->cantidad_actual_formateada}",
                'inventario',
                $producto->id,
                null,
                $producto->toArray()
            );

            DB::commit();
            return redirect()->route('inventario.index')->with('success', 'Producto registrado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al registrar el producto: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $producto = Inventario::where('activo', 1)->findOrFail($id);
        return view('inventario.show', compact('producto'));
    }

    public function edit($id)
    {
        $producto = Inventario::where('activo', 1)->findOrFail($id);
        return view('inventario.edit', compact('producto'));
    }

    public function update(Request $request, $id)
    {
        $producto = Inventario::where('activo', 1)->findOrFail($id);

        $validated = $request->validate([
            'codigo_producto' => 'required|string|max:50|unique:inventario,codigo_producto,' . $id,
            'nombre' => 'required|string|max:200',
            'categoria' => 'required|in:materiales,equipos,herramientas,insumos,quimicos,repuestos,otro',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:50',
            'cantidad_actual' => 'required|numeric|min:0',
            'cantidad_minima' => 'required|numeric|min:0',
            'cantidad_maxima' => 'nullable|numeric|min:0',
            'precio_unitario' => 'nullable|numeric|min:0',
            'ubicacion' => 'nullable|string|max:100',
            'proveedor' => 'nullable|string|max:200',
            'fecha_ultima_compra' => 'nullable|date',
            'estado' => 'required|in:disponible,agotado,bajo_stock,descontinuado',
            'observaciones' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Capturar datos antes de actualizar para auditoría
            $datosAnteriores = $producto->toArray();

            // Capturar cambios para auditoría
            $cambios = [];

            // Mapeo de nombres de campos
            $nombresCampos = [
                'codigo_producto' => 'Código',
                'nombre' => 'Nombre',
                'categoria' => 'Categoría',
                'descripcion' => 'Descripción',
                'unidad_medida' => 'Unidad de Medida',
                'cantidad_actual' => 'Cantidad Actual',
                'cantidad_minima' => 'Cantidad Mínima',
                'cantidad_maxima' => 'Cantidad Máxima',
                'precio_unitario' => 'Precio Unitario',
                'ubicacion' => 'Ubicación',
                'proveedor' => 'Proveedor',
                'fecha_ultima_compra' => 'Última Compra',
                'estado' => 'Estado',
                'observaciones' => 'Observaciones'
            ];

            // Detectar si hubo cambio en cantidad
            $cantidadAnterior = $datosAnteriores['cantidad_actual'];
            $cantidadNueva = $validated['cantidad_actual'];

            if ($cantidadAnterior != $cantidadNueva) {
                $validated['fecha_ultimo_movimiento'] = now();
            }

            foreach ($validated as $campo => $valorNuevo) {
                $valorAnterior = $datosAnteriores[$campo] ?? null;

                // Convertir null a string vacío para comparación
                $valorAnterior = $valorAnterior ?? '';
                $valorNuevo = $valorNuevo ?? '';

                if ($valorAnterior != $valorNuevo) {
                    $nombreCampo = $nombresCampos[$campo] ?? $campo;

                    // Formatear valores según el tipo de campo
                    if (in_array($campo, ['cantidad_actual', 'cantidad_minima', 'cantidad_maxima'])) {
                        $valorAnterior = number_format($valorAnterior, 2, ',', '.');
                        $valorNuevo = number_format($valorNuevo, 2, ',', '.');
                    } elseif ($campo == 'precio_unitario') {
                        $valorAnterior = $valorAnterior ? '$' . number_format($valorAnterior, 0, ',', '.') : 'No asignado';
                        $valorNuevo = $valorNuevo ? '$' . number_format($valorNuevo, 0, ',', '.') : 'No asignado';
                    } elseif ($campo == 'fecha_ultima_compra') {
                        $valorAnterior = $valorAnterior ? date('d/m/Y', strtotime($valorAnterior)) : 'Sin fecha';
                        $valorNuevo = $valorNuevo ? date('d/m/Y', strtotime($valorNuevo)) : 'Sin fecha';
                    } elseif ($campo == 'categoria') {
                        $categorias = [
                            'materiales' => 'Materiales',
                            'equipos' => 'Equipos',
                            'herramientas' => 'Herramientas',
                            'insumos' => 'Insumos',
                            'quimicos' => 'Químicos',
                            'repuestos' => 'Repuestos',
                            'otro' => 'Otro'
                        ];
                        $valorAnterior = $categorias[$valorAnterior] ?? $valorAnterior;
                        $valorNuevo = $categorias[$valorNuevo] ?? $valorNuevo;
                    } elseif ($campo == 'estado') {
                        $estados = [
                            'disponible' => 'Disponible',
                            'agotado' => 'Agotado',
                            'bajo_stock' => 'Bajo Stock',
                            'descontinuado' => 'Descontinuado'
                        ];
                        $valorAnterior = $estados[$valorAnterior] ?? $valorAnterior;
                        $valorNuevo = $estados[$valorNuevo] ?? $valorNuevo;
                    }

                    $cambios[] = "{$nombreCampo}: '{$valorAnterior}' → '{$valorNuevo}'";
                }
            }

            $producto->update($validated);

            // Registrar actividad si hubo cambios
            if (!empty($cambios)) {
                $descripcionCambios = implode(', ', $cambios);

                ActividadHelper::registrar(
                    'Inventario',
                    "Producto actualizado: {$producto->codigo_producto} - {$producto->nombre}. Cambios: {$descripcionCambios}",
                    auth()->id()
                );

                // Registrar en auditoría
                Auditoria::registrar(
                    'inventario',
                    'editar',
                    "Editó producto: {$producto->codigo_producto} - {$producto->nombre}. Cambios: {$descripcionCambios}",
                    'inventario',
                    $producto->id,
                    $datosAnteriores,
                    $producto->fresh()->toArray()
                );
            }

            DB::commit();
            return redirect()->route('inventario.index')->with('success', 'Producto actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $producto = Inventario::where('activo', 1)->findOrFail($id);

        DB::beginTransaction();
        try {
            $codigoProducto = $producto->codigo_producto;
            $nombre = $producto->nombre;
            $datosAnteriores = $producto->toArray();

            $producto->update(['activo' => 0]);

            ActividadHelper::registrar(
                'Inventario',
                "Producto eliminado: {$codigoProducto} - {$nombre}",
                auth()->id()
            );

            // Registrar en auditoría
            Auditoria::registrar(
                'inventario',
                'eliminar',
                "Eliminó producto: {$codigoProducto} - {$nombre}",
                'inventario',
                null,
                $datosAnteriores,
                null
            );

            DB::commit();
            return redirect()->route('inventario.index')->with('success', 'Producto eliminado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }
}
