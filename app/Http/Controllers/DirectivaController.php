<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Directiva;
use App\Models\Socio;
use App\Helpers\ActividadHelper;
use Illuminate\Support\Facades\DB;

class DirectivaController extends Controller
{
    /**
     * Listar todos los miembros de la directiva
     */
    public function index(Request $request)
    {
        $query = Directiva::with('socio')->where('activo', 1);

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

        if ($request->filled('cargo')) {
            $query->where('cargo', $request->cargo);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('periodo')) {
            $query->where('periodo', $request->periodo);
        }

        if ($request->filled('socio')) {
            $query->where('id_socio', $request->socio);
        }

        // Estadísticas
        $estadisticas = [
            'total_miembros' => Directiva::where('activo', 1)->count(),
            'activos' => Directiva::where('activo', 1)->where('estado', 'activo')->count(),
            'total_periodos' => Directiva::where('activo', 1)->distinct('periodo')->count('periodo'),
            'presidente_actual' => Directiva::where('activo', 1)
                                          ->where('estado', 'activo')
                                          ->where('cargo', 'presidente')
                                          ->with('socio')
                                          ->first()
        ];

        // Obtener periodos dinámicamente para el filtro
        $periodos = Directiva::where('activo', 1)
                            ->select('periodo')
                            ->distinct()
                            ->orderBy('periodo', 'desc')
                            ->pluck('periodo');

        $socios = Socio::activos()->orderBy('nombre')->get();

        $directiva = $query->orderBy('estado', 'asc')
                          ->orderBy('fecha_inicio', 'desc')
                          ->orderBy('id', 'desc')
                          ->paginate(20);

        return view('directiva.index', compact('directiva', 'estadisticas', 'periodos', 'socios'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $socios = Socio::activos()->orderBy('nombre')->get();
        return view('directiva.create', compact('socios'));
    }

    /**
     * Guardar nuevo miembro de directiva
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_socio' => 'required|exists:socios,id',
            'cargo' => 'required|in:presidente,vicepresidente,secretario,tesorero,director,vocal,suplente',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'nullable|date|after:fecha_inicio',
            'estado' => 'required|in:activo,finalizado,renunciado',
            'periodo' => 'required|string|max:20',
            'acta_nombramiento' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $directiva = Directiva::create($validated);

            $socio = Socio::find($validated['id_socio']);

            // Registrar actividad
            $detalles = [
                "Socio: {$socio->nombre_completo}",
                "Cargo: " . ucfirst($validated['cargo']),
                "Periodo: {$validated['periodo']}",
                "Estado: " . ucfirst($validated['estado'])
            ];

            ActividadHelper::registrar(
                'Directiva',
                'Nuevo miembro de directiva registrado: ' . implode(' | ', $detalles),
                auth()->id()
            );

            DB::commit();

            return redirect()->route('directiva.index')
                           ->with('success', 'Miembro de directiva registrado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al registrar el miembro de directiva: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle del miembro de directiva
     */
    public function show($id)
    {
        $directiva = Directiva::with('socio')->findOrFail($id);
        return view('directiva.show', compact('directiva'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $directiva = Directiva::findOrFail($id);
        $socios = Socio::activos()->orderBy('nombre')->get();
        return view('directiva.edit', compact('directiva', 'socios'));
    }

    /**
     * Actualizar miembro de directiva
     */
    public function update(Request $request, $id)
    {
        $directiva = Directiva::findOrFail($id);

        $validated = $request->validate([
            'id_socio' => 'required|exists:socios,id',
            'cargo' => 'required|in:presidente,vicepresidente,secretario,tesorero,director,vocal,suplente',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'nullable|date|after:fecha_inicio',
            'estado' => 'required|in:activo,finalizado,renunciado',
            'periodo' => 'required|string|max:20',
            'acta_nombramiento' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Capturar cambios antes de actualizar
            $cambios = [];

            $socioAnterior = Socio::find($directiva->id_socio);
            $socioNuevo = Socio::find($validated['id_socio']);

            if ($directiva->id_socio != $validated['id_socio']) {
                $cambios[] = "Socio: '{$socioAnterior->nombre_completo}' → '{$socioNuevo->nombre_completo}'";
            }

            if ($directiva->cargo != $validated['cargo']) {
                $cambios[] = "Cargo: '" . ucfirst($directiva->cargo) . "' → '" . ucfirst($validated['cargo']) . "'";
            }

            if ($directiva->estado != $validated['estado']) {
                $cambios[] = "Estado: '" . ucfirst($directiva->estado) . "' → '" . ucfirst($validated['estado']) . "'";
            }

            if ($directiva->periodo != $validated['periodo']) {
                $cambios[] = "Periodo: '{$directiva->periodo}' → '{$validated['periodo']}'";
            }

            $fechaInicioAnterior = $directiva->fecha_inicio->format('Y-m-d');
            if ($fechaInicioAnterior != $validated['fecha_inicio']) {
                $cambios[] = "Fecha Inicio: '" . date('d/m/Y', strtotime($fechaInicioAnterior)) . "' → '" . date('d/m/Y', strtotime($validated['fecha_inicio'])) . "'";
            }

            $fechaTerminoAnterior = $directiva->fecha_termino ? $directiva->fecha_termino->format('Y-m-d') : null;
            $fechaTerminoNueva = $validated['fecha_termino'] ?? null;
            if ($fechaTerminoAnterior != $fechaTerminoNueva) {
                $fechaAnteriorTexto = $fechaTerminoAnterior ? date('d/m/Y', strtotime($fechaTerminoAnterior)) : 'Sin fecha';
                $fechaNuevaTexto = $fechaTerminoNueva ? date('d/m/Y', strtotime($fechaTerminoNueva)) : 'Sin fecha';
                $cambios[] = "Fecha Término: '{$fechaAnteriorTexto}' → '{$fechaNuevaTexto}'";
            }

            $directiva->update($validated);

            // Registrar actividad con cambios
            if (!empty($cambios)) {
                $descripcionCambios = implode(' | ', $cambios);
                ActividadHelper::registrar(
                    'Directiva',
                    "Miembro de directiva actualizado [{$socioNuevo->nombre_completo} - {$validated['cargo']}]: {$descripcionCambios}",
                    auth()->id()
                );
            } else {
                ActividadHelper::registrar(
                    'Directiva',
                    "Miembro de directiva actualizado [{$socioNuevo->nombre_completo} - {$validated['cargo']}]",
                    auth()->id()
                );
            }

            DB::commit();

            return redirect()->route('directiva.show', $id)
                           ->with('success', 'Miembro de directiva actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar el miembro de directiva: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar miembro de directiva (soft delete)
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $directiva = Directiva::findOrFail($id);
            $socio = $directiva->socio;
            $cargo = $directiva->cargo;
            $periodo = $directiva->periodo;

            $directiva->update(['activo' => 0]);

            // Registrar actividad
            ActividadHelper::registrar(
                'Directiva',
                "Miembro de directiva eliminado [{$socio->nombre_completo}] - Cargo: {$cargo} - Periodo: {$periodo}",
                auth()->id()
            );

            DB::commit();

            return redirect()->route('directiva.index')
                           ->with('success', 'Miembro de directiva eliminado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error al eliminar el miembro de directiva: ' . $e->getMessage());
        }
    }
}
