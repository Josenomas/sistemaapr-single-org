<?php

namespace App\Http\Controllers;

use App\Models\Recordatorio;
use App\Models\Funcionario;
use App\Helpers\ActividadHelper;
use Illuminate\Http\Request;

class RecordatoriosController extends Controller
{
    public function index(Request $request)
    {
        $query = Recordatorio::activos()->with('asignado');

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhere('ubicacion', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo
        if ($request->filled('tipo_recordatorio')) {
            $query->porTipo($request->tipo_recordatorio);
        }

        // Filtro por prioridad
        if ($request->filled('prioridad')) {
            $query->porPrioridad($request->prioridad);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->porEstado($request->estado);
        }

        // Filtro por asignado
        if ($request->filled('id_asignado')) {
            $query->where('id_asignado', $request->id_asignado);
        }

        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_recordatorio', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_recordatorio', '<=', $request->fecha_hasta);
        }

        $recordatorios = $query->orderBy('fecha_recordatorio', 'asc')
                              ->orderBy('prioridad', 'desc')
                              ->paginate(15);

        // Actualizar recordatorios vencidos
        Recordatorio::activos()
            ->pendientes()
            ->where('fecha_recordatorio', '<', today())
            ->update(['estado' => 'vencido']);

        // Estadísticas
        $estadisticas = [
            'total_recordatorios' => Recordatorio::activos()->count(),
            'pendientes' => Recordatorio::activos()->pendientes()->count(),
            'hoy' => Recordatorio::activos()->pendientes()->hoy()->count(),
            'proximos_7_dias' => Recordatorio::activos()->pendientes()->proximos(7)->count(),
            'vencidos' => Recordatorio::activos()->vencidos()->count(),
            'completados' => Recordatorio::activos()->completados()->count()
        ];

        $funcionarios = Funcionario::activos()
                                  ->where('estado', 'activo')
                                  ->orderBy('nombre')
                                  ->get();

        return view('recordatorios.index', compact('recordatorios', 'estadisticas', 'funcionarios'));
    }

    public function create()
    {
        $funcionarios = Funcionario::activos()
                                  ->where('estado', 'activo')
                                  ->orderBy('nombre')
                                  ->get();

        return view('recordatorios.create', compact('funcionarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'required|string',
            'tipo_recordatorio' => 'required|in:reunion,pago,mantenimiento,inspeccion,vencimiento,llamada,tarea,otro',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'fecha_recordatorio' => 'required|date',
            'hora_recordatorio' => 'nullable|date_format:H:i',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_recordatorio',
            'estado' => 'required|in:pendiente,completado,cancelado,vencido',
            'id_asignado' => 'nullable|exists:funcionarios,id',
            'ubicacion' => 'nullable|string|max:255',
            'notas' => 'nullable|string'
        ]);

        try {
            $validated['activo'] = 1;
            $validated['notificado'] = 0;

            // Si se marca como completado, registrar fecha
            if ($validated['estado'] === 'completado') {
                $validated['fecha_completado'] = now();
            }

            $recordatorio = Recordatorio::create($validated);

            // Preparar detalles para actividad
            $detalles = [
                'Título: ' . $recordatorio->titulo,
                'Tipo: ' . $recordatorio->tipo_recordatorio_texto,
                'Prioridad: ' . $recordatorio->prioridad_texto,
                'Fecha: ' . $recordatorio->fecha_recordatorio_formateada,
                'Estado: ' . $recordatorio->estado_texto
            ];

            if ($recordatorio->asignado) {
                $detalles[] = 'Asignado a: ' . $recordatorio->asignado->nombre_completo;
            }

            ActividadHelper::registrar(
                'Recordatorios',
                'Nuevo recordatorio creado: ' . implode(' | ', $detalles),
                auth()->id()
            );

            return redirect()->route('recordatorios.show', $recordatorio->id)
                           ->with('success', 'Recordatorio creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al crear el recordatorio: ' . $e->getMessage());
        }
    }

    public function show(Recordatorio $recordatorio)
    {
        $recordatorio->load('asignado');
        return view('recordatorios.show', compact('recordatorio'));
    }

    public function edit(Recordatorio $recordatorio)
    {
        $funcionarios = Funcionario::activos()
                                  ->where('estado', 'activo')
                                  ->orderBy('nombre')
                                  ->get();

        return view('recordatorios.edit', compact('recordatorio', 'funcionarios'));
    }

    public function update(Request $request, Recordatorio $recordatorio)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'required|string',
            'tipo_recordatorio' => 'required|in:reunion,pago,mantenimiento,inspeccion,vencimiento,llamada,tarea,otro',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'fecha_recordatorio' => 'required|date',
            'hora_recordatorio' => 'nullable|date_format:H:i',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_recordatorio',
            'estado' => 'required|in:pendiente,completado,cancelado,vencido',
            'id_asignado' => 'nullable|exists:funcionarios,id',
            'ubicacion' => 'nullable|string|max:255',
            'notas' => 'nullable|string'
        ]);

        try {
            // Tracking de cambios
            $cambios = [];

            if ($recordatorio->titulo != $validated['titulo']) {
                $cambios[] = "Título: '{$recordatorio->titulo}' → '{$validated['titulo']}'";
            }

            if ($recordatorio->tipo_recordatorio != $validated['tipo_recordatorio']) {
                $tipoAnterior = $recordatorio->tipo_recordatorio_texto;
                $tipoNuevo = (new Recordatorio(['tipo_recordatorio' => $validated['tipo_recordatorio']]))->tipo_recordatorio_texto;
                $cambios[] = "Tipo: '{$tipoAnterior}' → '{$tipoNuevo}'";
            }

            if ($recordatorio->prioridad != $validated['prioridad']) {
                $prioridadAnterior = $recordatorio->prioridad_texto;
                $prioridadNueva = (new Recordatorio(['prioridad' => $validated['prioridad']]))->prioridad_texto;
                $cambios[] = "Prioridad: '{$prioridadAnterior}' → '{$prioridadNueva}'";
            }

            if ($recordatorio->estado != $validated['estado']) {
                $estadoAnterior = $recordatorio->estado_texto;
                $estadoNuevo = (new Recordatorio(['estado' => $validated['estado']]))->estado_texto;
                $cambios[] = "Estado: '{$estadoAnterior}' → '{$estadoNuevo}'";

                // Si cambia a completado y no tiene fecha de completado, establecerla
                if ($validated['estado'] === 'completado' && !$recordatorio->fecha_completado) {
                    $validated['fecha_completado'] = now();
                }

                // Si cambia de completado a otro estado, limpiar fecha de completado
                if ($recordatorio->estado === 'completado' && $validated['estado'] !== 'completado') {
                    $validated['fecha_completado'] = null;
                }
            }

            if ($recordatorio->fecha_recordatorio != $validated['fecha_recordatorio']) {
                $cambios[] = "Fecha: '{$recordatorio->fecha_recordatorio_formateada}' → '" . date('d/m/Y', strtotime($validated['fecha_recordatorio'])) . "'";
            }

            if ($recordatorio->id_asignado != $validated['id_asignado']) {
                $asignadoAnterior = $recordatorio->asignado ? $recordatorio->asignado->nombre_completo : 'Sin asignar';
                $funcionarioNuevo = $validated['id_asignado'] ? Funcionario::find($validated['id_asignado']) : null;
                $asignadoNuevo = $funcionarioNuevo ? $funcionarioNuevo->nombre_completo : 'Sin asignar';
                $cambios[] = "Asignado: '{$asignadoAnterior}' → '{$asignadoNuevo}'";
            }

            $recordatorio->update($validated);

            if (!empty($cambios)) {
                ActividadHelper::registrar(
                    'Recordatorios',
                    "Recordatorio actualizado [{$recordatorio->titulo}]: " . implode(' | ', $cambios),
                    auth()->id()
                );
            }

            return redirect()->route('recordatorios.show', $recordatorio->id)
                           ->with('success', 'Recordatorio actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar el recordatorio: ' . $e->getMessage());
        }
    }

    public function destroy(Recordatorio $recordatorio)
    {
        try {
            $titulo = $recordatorio->titulo;
            $recordatorio->activo = 0;
            $recordatorio->save();

            ActividadHelper::registrar(
                'Recordatorios',
                "Recordatorio eliminado: {$titulo} - {$recordatorio->fecha_recordatorio_formateada}",
                auth()->id()
            );

            return redirect()->route('recordatorios.index')
                           ->with('success', 'Recordatorio eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error al eliminar el recordatorio: ' . $e->getMessage());
        }
    }
}
