<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivoFijo;
use App\Models\Usuario;
use App\Helpers\ActividadHelper;
use Illuminate\Support\Facades\DB;

class ActivosFijosController extends Controller
{
    /**
     * Listar todos los activos fijos
     */
    public function index(Request $request)
    {
        $query = ActivoFijo::activos()->with('responsable');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('codigo_activo', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%")
                  ->orWhere('modelo', 'like', "%{$search}%")
                  ->orWhere('ubicacion', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->porCategoria($request->categoria);
        }

        if ($request->filled('estado')) {
            $query->porEstado($request->estado);
        }

        if ($request->filled('responsable')) {
            $query->where('id_responsable', $request->responsable);
        }

        // Estadísticas
        $estadisticas = [
            'total_activos' => ActivoFijo::activos()->count(),
            'valor_total' => ActivoFijo::activos()->sum('valor_adquisicion'),
            'valor_actual' => ActivoFijo::activos()->sum('valor_actual'),
            'por_categoria' => ActivoFijo::activos()
                ->select('categoria', DB::raw('count(*) as total'))
                ->groupBy('categoria')
                ->pluck('total', 'categoria'),
            'por_estado' => ActivoFijo::activos()
                ->select('estado', DB::raw('count(*) as total'))
                ->groupBy('estado')
                ->pluck('total', 'estado'),
        ];

        $activos = $query->orderBy('fecha_creacion', 'desc')->paginate(15);

        $responsables = Usuario::where('activo', 1)->orderBy('nombre')->get();

        return view('activos-fijos.index', compact('activos', 'estadisticas', 'responsables'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $responsables = Usuario::where('activo', 1)->orderBy('nombre')->get();
        $codigo = ActivoFijo::generarCodigoActivo();

        return view('activos-fijos.create', compact('responsables', 'codigo'));
    }

    /**
     * Guardar nuevo activo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_activo' => 'required|string|max:50|unique:activos_fijos,codigo_activo',
            'nombre' => 'required|string|max:150',
            'categoria' => 'required|in:mobiliario,equipos_computo,equipos_oficina,herramientas,vehiculos,equipamiento_tecnico,otros',
            'descripcion' => 'nullable|string',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'numero_serie' => 'nullable|string|max:100',
            'fecha_adquisicion' => 'required|date',
            'valor_adquisicion' => 'required|numeric|min:0',
            'valor_actual' => 'nullable|numeric|min:0',
            'proveedor' => 'nullable|string|max:150',
            'ubicacion' => 'nullable|string|max:150',
            'estado' => 'required|in:excelente,bueno,regular,malo,en_reparacion,dado_de_baja',
            'id_responsable' => 'nullable|exists:usuarios,id',
            'observaciones' => 'nullable|string',
            'vida_util_anos' => 'nullable|integer|min:1',
            'fecha_ultimo_mantenimiento' => 'nullable|date',
            'proxima_revision' => 'nullable|date|after_or_equal:fecha_ultimo_mantenimiento',
        ]);

        DB::beginTransaction();
        try {
            $activo = ActivoFijo::create($validated);

            // Registrar actividad
            $detalles = [
                "Código: {$activo->codigo_activo}",
                "Nombre: {$activo->nombre}",
                "Categoría: {$activo->categoria_nombre}",
                "Valor: {$activo->valor_adquisicion_formateado}",
            ];

            ActividadHelper::registrar(
                'Activos Fijos',
                'Nuevo activo registrado: ' . implode(' | ', $detalles),
                auth()->id()
            );

            DB::commit();

            return redirect()->route('activos-fijos.index')
                           ->with('success', 'Activo fijo registrado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al registrar el activo: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle del activo
     */
    public function show($id)
    {
        $activo = ActivoFijo::with('responsable')->findOrFail($id);

        return view('activos-fijos.show', compact('activo'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $activo = ActivoFijo::findOrFail($id);
        $responsables = Usuario::where('activo', 1)->orderBy('nombre')->get();

        return view('activos-fijos.edit', compact('activo', 'responsables'));
    }

    /**
     * Actualizar activo
     */
    public function update(Request $request, $id)
    {
        $activo = ActivoFijo::findOrFail($id);

        $validated = $request->validate([
            'codigo_activo' => 'required|string|max:50|unique:activos_fijos,codigo_activo,' . $id,
            'nombre' => 'required|string|max:150',
            'categoria' => 'required|in:mobiliario,equipos_computo,equipos_oficina,herramientas,vehiculos,equipamiento_tecnico,otros',
            'descripcion' => 'nullable|string',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'numero_serie' => 'nullable|string|max:100',
            'fecha_adquisicion' => 'required|date',
            'valor_adquisicion' => 'required|numeric|min:0',
            'valor_actual' => 'nullable|numeric|min:0',
            'proveedor' => 'nullable|string|max:150',
            'ubicacion' => 'nullable|string|max:150',
            'estado' => 'required|in:excelente,bueno,regular,malo,en_reparacion,dado_de_baja',
            'id_responsable' => 'nullable|exists:usuarios,id',
            'observaciones' => 'nullable|string',
            'vida_util_anos' => 'nullable|integer|min:1',
            'fecha_ultimo_mantenimiento' => 'nullable|date',
            'proxima_revision' => 'nullable|date|after_or_equal:fecha_ultimo_mantenimiento',
        ]);

        DB::beginTransaction();
        try {
            $cambios = [];
            foreach ($validated as $campo => $valor) {
                if ($activo->$campo != $valor) {
                    $cambios[] = $campo;
                }
            }

            $activo->update($validated);

            // Registrar actividad
            if (!empty($cambios)) {
                ActividadHelper::registrar(
                    'Activos Fijos',
                    "Activo {$activo->codigo_activo} - {$activo->nombre} actualizado. Campos modificados: " . implode(', ', $cambios),
                    auth()->id()
                );
            }

            DB::commit();

            return redirect()->route('activos-fijos.show', $activo->id)
                           ->with('success', 'Activo actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar el activo: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar activo (soft delete)
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $activo = ActivoFijo::findOrFail($id);
            $activo->update(['activo' => 0]);

            // Registrar actividad
            ActividadHelper::registrar(
                'Activos Fijos',
                "Activo dado de baja: {$activo->codigo_activo} - {$activo->nombre}",
                auth()->id()
            );

            DB::commit();

            return redirect()->route('activos-fijos.index')
                           ->with('success', 'Activo dado de baja exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error al dar de baja el activo: ' . $e->getMessage());
        }
    }
}
