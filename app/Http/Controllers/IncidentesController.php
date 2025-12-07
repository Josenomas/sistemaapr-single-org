<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidente;
use App\Models\Socio;
use App\Models\Usuario;
use App\Helpers\ActividadHelper;

class IncidentesController extends Controller
{
    /**
     * Listar todos los incidentes
     */
    public function index(Request $request)
    {
        $query = Incidente::with(['socioReporta', 'usuarioAsignado']);

        // Filtros
        if ($request->has('estado') && $request->estado) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('prioridad') && $request->prioridad) {
            $query->where('prioridad', $request->prioridad);
        }

        if ($request->has('tipo') && $request->tipo) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->has('sector') && $request->sector) {
            $query->where('sector', $request->sector);
        }

        // Por defecto mostrar solo activos
        if (!$request->has('mostrar_todos')) {
            $query->activos();
        }

        $incidentes = $query->orderByRaw("
            CASE prioridad
                WHEN 'critica' THEN 1
                WHEN 'alta' THEN 2
                WHEN 'media' THEN 3
                WHEN 'baja' THEN 4
            END
        ")->orderBy('fecha_reporte', 'desc')
          ->paginate(20);

        // Estadísticas
        $criticos = Incidente::criticos()->activos()->count();
        $activos = Incidente::activos()->count();

        return view('incidentes.index', compact('incidentes', 'criticos', 'activos'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $socios = Socio::activos()->orderBy('numero_socio')->get();
        $usuarios = Usuario::where('activo', 1)
                          ->whereIn('rol', ['Administrador', 'Operador'])
                          ->orderBy('nombre')
                          ->get();

        return view('incidentes.create', compact('socios', 'usuarios'));
    }

    /**
     * Guardar nuevo incidente
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:fuga,corte,baja_presion,contaminacion,otro',
            'descripcion' => 'required|string',
            'ubicacion' => 'required|string|max:255',
            'sector' => 'nullable|string|max:100',
            'id_socio_reporta' => 'nullable|exists:socios,id',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'id_usuario_asignado' => 'nullable|exists:usuarios,id',
            'observaciones' => 'nullable|string',
        ]);

        $validated['estado'] = 'reportado';
        $validated['fecha_reporte'] = now();

        $incidente = Incidente::create($validated);

        // Registrar actividad
        ActividadHelper::registrar(
            'Incidentes',
            "Nuevo incidente reportado: " . ucfirst($validated['tipo']) . " en " . $validated['ubicacion'],
            auth()->id()
        );

        // Si es crítico, enviar notificación (implementar después)
        if ($validated['prioridad'] === 'critica') {
            // Enviar notificación
        }

        return redirect()->route('incidentes.show', $incidente->id)
                        ->with('success', 'Incidente reportado exitosamente');
    }

    /**
     * Mostrar detalle del incidente
     */
    public function show($id)
    {
        $incidente = Incidente::with(['socioReporta', 'usuarioAsignado'])->findOrFail($id);
        return view('incidentes.show', compact('incidente'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $incidente = Incidente::findOrFail($id);
        $socios = Socio::activos()->orderBy('numero_socio')->get();
        $usuarios = Usuario::where('activo', 1)
                          ->whereIn('rol', ['Administrador', 'Operador'])
                          ->orderBy('nombre')
                          ->get();

        return view('incidentes.edit', compact('incidente', 'socios', 'usuarios'));
    }

    /**
     * Actualizar incidente
     */
    public function update(Request $request, $id)
    {
        $incidente = Incidente::findOrFail($id);

        $validated = $request->validate([
            'tipo' => 'required|in:fuga,corte,baja_presion,contaminacion,otro',
            'descripcion' => 'required|string',
            'ubicacion' => 'required|string|max:255',
            'sector' => 'nullable|string|max:100',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'estado' => 'required|in:reportado,en_atencion,resuelto,cerrado',
            'id_usuario_asignado' => 'nullable|exists:usuarios,id',
            'solucion' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        // Actualizar fechas según el estado
        if ($validated['estado'] === 'en_atencion' && !$incidente->fecha_atencion) {
            $validated['fecha_atencion'] = now();
        }

        if ($validated['estado'] === 'resuelto' && !$incidente->fecha_resolucion) {
            $validated['fecha_resolucion'] = now();
        }

        $incidente->update($validated);

        return redirect()->route('incidentes.show', $id)
                        ->with('success', 'Incidente actualizado exitosamente');
    }

    /**
     * Asignar usuario al incidente
     */
    public function asignar(Request $request, $id)
    {
        $incidente = Incidente::findOrFail($id);

        $validated = $request->validate([
            'id_usuario_asignado' => 'required|exists:usuarios,id',
        ]);

        $incidente->update([
            'id_usuario_asignado' => $validated['id_usuario_asignado'],
            'estado' => 'en_atencion',
            'fecha_atencion' => $incidente->fecha_atencion ?? now(),
        ]);

        return redirect()->route('incidentes.show', $id)
                        ->with('success', 'Incidente asignado exitosamente');
    }

    /**
     * Marcar como resuelto
     */
    public function resolver(Request $request, $id)
    {
        $incidente = Incidente::findOrFail($id);

        $validated = $request->validate([
            'solucion' => 'required|string',
        ]);

        $incidente->update([
            'solucion' => $validated['solucion'],
            'estado' => 'resuelto',
            'fecha_resolucion' => now(),
        ]);

        return redirect()->route('incidentes.show', $id)
                        ->with('success', 'Incidente marcado como resuelto');
    }

    /**
     * Cerrar incidente
     */
    public function cerrar($id)
    {
        $incidente = Incidente::findOrFail($id);

        if ($incidente->estado !== 'resuelto') {
            return redirect()->route('incidentes.show', $id)
                           ->with('error', 'Solo se pueden cerrar incidentes resueltos');
        }

        $incidente->update(['estado' => 'cerrado']);

        return redirect()->route('incidentes.index')
                        ->with('success', 'Incidente cerrado exitosamente');
    }

    /**
     * Mapa de incidentes activos
     */
    public function mapa()
    {
        $incidentes = Incidente::with('socioReporta')
                              ->activos()
                              ->orderBy('prioridad')
                              ->get();

        return view('incidentes.mapa', compact('incidentes'));
    }
}
