<?php

namespace App\Http\Controllers;

use App\Models\TrabajoRealizado;
use App\Models\Funcionario;
use App\Helpers\ActividadHelper;
use Illuminate\Http\Request;

class TrabajosRealizadosController extends Controller
{
    public function index(Request $request)
    {
        $query = TrabajoRealizado::with('responsable')->activos();

        // Filtro por tipo de trabajo
        if ($request->filled('tipo_trabajo')) {
            $query->where('tipo_trabajo', $request->tipo_trabajo);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por prioridad
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        // Filtro por responsable
        if ($request->filled('id_responsable')) {
            $query->where('id_responsable', $request->id_responsable);
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_inicio', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_inicio', '<=', $request->fecha_hasta);
        }

        $trabajos = $query->orderBy('fecha_inicio', 'desc')->paginate(20);
        $funcionarios = Funcionario::activos()->orderBy('nombre')->get();

        return view('trabajos.index', compact('trabajos', 'funcionarios'));
    }

    public function create()
    {
        $funcionarios = Funcionario::activos()->orderBy('nombre')->get();
        return view('trabajos.create', compact('funcionarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'required|string',
            'tipo_trabajo' => 'required|in:mantenimiento,reparacion,instalacion,inspeccion,otro',
            'ubicacion' => 'nullable|string|max:255',
            'fecha_inicio' => 'required|date',
            'estado' => 'required|in:planificado,en_proceso,completado,cancelado',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'costo_estimado' => 'nullable|numeric|min:0',
            'id_responsable' => 'nullable|exists:funcionarios,id',
            'materiales_utilizados' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        $trabajo = TrabajoRealizado::create($validated);

        ActividadHelper::registrar(
            'Trabajos Realizados',
            "Trabajo registrado: {$trabajo->titulo} - Tipo: {$trabajo->tipo_trabajo_formateado} - Prioridad: {$trabajo->prioridad_formateada}",
            auth()->id()
        );

        return redirect()->route('trabajos.show', $trabajo->id)
            ->with('success', 'Trabajo registrado exitosamente.');
    }

    public function show($id)
    {
        $trabajo = TrabajoRealizado::with('responsable')->findOrFail($id);
        return view('trabajos.show', compact('trabajo'));
    }

    public function edit($id)
    {
        $trabajo = TrabajoRealizado::findOrFail($id);
        $funcionarios = Funcionario::activos()->orderBy('nombre')->get();
        return view('trabajos.edit', compact('trabajo', 'funcionarios'));
    }

    public function update(Request $request, $id)
    {
        $trabajo = TrabajoRealizado::findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'required|string',
            'tipo_trabajo' => 'required|in:mantenimiento,reparacion,instalacion,inspeccion,otro',
            'ubicacion' => 'nullable|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'nullable|date',
            'estado' => 'required|in:planificado,en_proceso,completado,cancelado',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'costo_estimado' => 'nullable|numeric|min:0',
            'costo_real' => 'nullable|numeric|min:0',
            'id_responsable' => 'nullable|exists:funcionarios,id',
            'materiales_utilizados' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        // Capturar cambios antes de actualizar
        $cambios = [];
        $camposTraducidos = [
            'titulo' => 'Título',
            'descripcion' => 'Descripción',
            'tipo_trabajo' => 'Tipo de Trabajo',
            'ubicacion' => 'Ubicación',
            'fecha_inicio' => 'Fecha de Inicio',
            'fecha_termino' => 'Fecha de Término',
            'estado' => 'Estado',
            'prioridad' => 'Prioridad',
            'costo_estimado' => 'Costo Estimado',
            'costo_real' => 'Costo Real',
            'id_responsable' => 'Responsable',
            'materiales_utilizados' => 'Materiales Utilizados',
            'observaciones' => 'Observaciones',
        ];

        foreach ($validated as $campo => $valorNuevo) {
            $valorAnterior = $trabajo->$campo;

            if ($valorAnterior != $valorNuevo) {
                $nombreCampo = $camposTraducidos[$campo] ?? $campo;

                // Formatear valores monetarios
                if (in_array($campo, ['costo_estimado', 'costo_real'])) {
                    $valorAnterior = $valorAnterior ? '$' . number_format($valorAnterior, 0, ',', '.') : 'Sin costo';
                    $valorNuevo = $valorNuevo ? '$' . number_format($valorNuevo, 0, ',', '.') : 'Sin costo';
                }
                // Formatear fechas
                elseif (in_array($campo, ['fecha_inicio', 'fecha_termino'])) {
                    $valorAnterior = $valorAnterior ? date('d/m/Y', strtotime($valorAnterior)) : 'Sin fecha';
                    $valorNuevo = $valorNuevo ? date('d/m/Y', strtotime($valorNuevo)) : 'Sin fecha';
                }
                // Formatear responsable
                elseif ($campo == 'id_responsable') {
                    $responsableAnterior = Funcionario::find($valorAnterior);
                    $responsableNuevo = Funcionario::find($valorNuevo);
                    $valorAnterior = $responsableAnterior ? $responsableAnterior->nombre_completo : 'Sin asignar';
                    $valorNuevo = $responsableNuevo ? $responsableNuevo->nombre_completo : 'Sin asignar';
                }
                // Formatear tipo de trabajo
                elseif ($campo == 'tipo_trabajo') {
                    $tipos = [
                        'mantenimiento' => 'Mantenimiento',
                        'reparacion' => 'Reparación',
                        'instalacion' => 'Instalación',
                        'inspeccion' => 'Inspección',
                        'otro' => 'Otro',
                    ];
                    $valorAnterior = $tipos[$valorAnterior] ?? $valorAnterior;
                    $valorNuevo = $tipos[$valorNuevo] ?? $valorNuevo;
                }
                // Formatear estado
                elseif ($campo == 'estado') {
                    $estados = [
                        'planificado' => 'Planificado',
                        'en_proceso' => 'En Proceso',
                        'completado' => 'Completado',
                        'cancelado' => 'Cancelado',
                    ];
                    $valorAnterior = $estados[$valorAnterior] ?? $valorAnterior;
                    $valorNuevo = $estados[$valorNuevo] ?? $valorNuevo;
                }
                // Formatear prioridad
                elseif ($campo == 'prioridad') {
                    $prioridades = [
                        'baja' => 'Baja',
                        'media' => 'Media',
                        'alta' => 'Alta',
                        'urgente' => 'Urgente',
                    ];
                    $valorAnterior = $prioridades[$valorAnterior] ?? $valorAnterior;
                    $valorNuevo = $prioridades[$valorNuevo] ?? $valorNuevo;
                }

                $cambios[] = "{$nombreCampo}: '{$valorAnterior}' → '{$valorNuevo}'";
            }
        }

        $trabajo->update($validated);

        if (!empty($cambios)) {
            $descripcionCambios = implode(', ', $cambios);
            ActividadHelper::registrar(
                'Trabajos Realizados',
                "Trabajo actualizado: {$trabajo->titulo}. Cambios: {$descripcionCambios}",
                auth()->id()
            );
        } else {
            ActividadHelper::registrar(
                'Trabajos Realizados',
                "Trabajo actualizado: {$trabajo->titulo}",
                auth()->id()
            );
        }

        return redirect()->route('trabajos.show', $trabajo->id)
            ->with('success', 'Trabajo actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $trabajo = TrabajoRealizado::findOrFail($id);

        $trabajo->activo = 0;
        $trabajo->save();

        ActividadHelper::registrar(
            'Trabajos Realizados',
            "Trabajo eliminado: {$trabajo->titulo} - Tipo: {$trabajo->tipo_trabajo_formateado}",
            auth()->id()
        );

        return redirect()->route('trabajos.index')
            ->with('success', 'Trabajo eliminado exitosamente.');
    }
}
