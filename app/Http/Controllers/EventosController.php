<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Helpers\ActividadHelper;

class EventosController extends Controller
{
    /**
     * Listar todos los eventos
     */
    public function index(Request $request)
    {
        $query = Evento::activos();

        // Filtros
        if ($request->filled('tipo')) {
            $query->porTipo($request->tipo);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        $eventos = $query->orderBy('fecha_evento', 'asc')->paginate(15);

        return view('eventos.index', compact('eventos'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('eventos.create');
    }

    /**
     * Guardar nuevo evento
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'fecha_evento' => 'required|date',
            'recurrencia' => 'required|in:ninguna,diaria,semanal,mensual,anual',
            'dia_recurrencia' => 'nullable|integer|min:1|max:31',
            'icono' => 'required|string|max:100',
            'color' => 'required|in:primary,success,warning,danger,info',
            'notificar' => 'nullable|boolean',
            'dias_notificacion' => 'nullable|integer|min:1|max:30',
        ]);

        try {
            $validated['activo'] = 1;
            $validated['notificar'] = $request->has('notificar') ? 1 : 0;

            // Si no se marca notificar, poner dias_notificacion en null
            if (!$validated['notificar']) {
                $validated['dias_notificacion'] = null;
            }

            $evento = Evento::create($validated);

            ActividadHelper::registrar(
                'Eventos',
                "Nuevo evento creado: {$evento->titulo} - Fecha: {$evento->fecha_evento->format('d/m/Y')}",
                auth()->id()
            );

            return redirect()->route('eventos.index')
                           ->with('success', 'Evento creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al crear el evento: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle del evento
     */
    public function show($id)
    {
        $evento = Evento::activos()->findOrFail($id);
        return view('eventos.show', compact('evento'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $evento = Evento::activos()->findOrFail($id);
        return view('eventos.edit', compact('evento'));
    }

    /**
     * Actualizar evento
     */
    public function update(Request $request, $id)
    {
        $evento = Evento::activos()->findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'fecha_evento' => 'required|date',
            'recurrencia' => 'required|in:ninguna,diaria,semanal,mensual,anual',
            'dia_recurrencia' => 'nullable|integer|min:1|max:31',
            'icono' => 'required|string|max:100',
            'color' => 'required|in:primary,success,warning,danger,info',
            'notificar' => 'nullable|boolean',
            'dias_notificacion' => 'nullable|integer|min:1|max:30',
        ]);

        try {
            $validated['notificar'] = $request->has('notificar') ? 1 : 0;

            // Si no se marca notificar, poner dias_notificacion en null
            if (!$validated['notificar']) {
                $validated['dias_notificacion'] = null;
            }

            $evento->update($validated);

            ActividadHelper::registrar(
                'Eventos',
                "Evento actualizado: {$evento->titulo}",
                auth()->id()
            );

            return redirect()->route('eventos.index')
                           ->with('success', 'Evento actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar el evento: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar evento (soft delete)
     */
    public function destroy($id)
    {
        $evento = Evento::activos()->findOrFail($id);

        try {
            $titulo = $evento->titulo;
            $evento->activo = 0;
            $evento->save();

            ActividadHelper::registrar(
                'Eventos',
                "Evento eliminado: {$titulo}",
                auth()->id()
            );

            return redirect()->route('eventos.index')
                           ->with('success', 'Evento eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error al eliminar el evento: ' . $e->getMessage());
        }
    }
}
