<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Socio;
use App\Models\Funcionario;
use App\Helpers\ActividadHelper;
use App\Jobs\EnviarTicketEmail;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::activos()->with(['socio', 'asignado']);

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_ticket', 'like', "%{$search}%")
                  ->orWhere('titulo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhereHas('socio', function($sq) use ($search) {
                      $sq->where('nombre', 'like', "%{$search}%")
                        ->orWhere('apellido_paterno', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por tipo
        if ($request->filled('tipo_ticket')) {
            $query->porTipo($request->tipo_ticket);
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
            if ($request->id_asignado === 'sin_asignar') {
                $query->whereNull('id_asignado');
            } else {
                $query->where('id_asignado', $request->id_asignado);
            }
        }

        $tickets = $query->orderBy('fecha_reporte', 'desc')
                        ->orderBy('prioridad', 'desc')
                        ->paginate(15);

        // Estadísticas
        $estadisticas = [
            'total_tickets' => Ticket::activos()->count(),
            'abiertos' => Ticket::activos()->abiertos()->count(),
            'cerrados' => Ticket::activos()->cerrados()->count(),
            'urgentes' => Ticket::activos()->urgentes()->count(),
            'sin_asignar' => Ticket::activos()->sinAsignar()->count()
        ];

        $funcionarios = Funcionario::activos()
                                  ->where('estado', 'activo')
                                  ->orderBy('nombre')
                                  ->get();

        return view('tickets.index', compact('tickets', 'estadisticas', 'funcionarios'));
    }

    public function create()
    {
        $socios = Socio::activos()
                      ->where('estado', 'activo')
                      ->orderBy('nombre')
                      ->get();

        $funcionarios = Funcionario::activos()
                                  ->where('estado', 'activo')
                                  ->orderBy('nombre')
                                  ->get();

        return view('tickets.create', compact('socios', 'funcionarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_socio' => 'nullable|exists:socios,id',
            'titulo' => 'required|string|max:200',
            'descripcion' => 'required|string',
            'tipo_ticket' => 'required|in:consulta,reclamo,solicitud,averia,fuga,corte,reconexion,lectura,otro',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'estado' => 'required|in:abierto,en_proceso,pendiente,resuelto,cerrado,cancelado',
            'id_asignado' => 'nullable|exists:funcionarios,id',
            'fecha_reporte' => 'required|date',
            'ubicacion' => 'nullable|string|max:255',
            'contacto_nombre' => 'nullable|string|max:100',
            'contacto_telefono' => 'nullable|string|max:20',
            'contacto_email' => 'nullable|email|max:150',
            'observaciones' => 'nullable|string'
        ]);

        try {
            // Generar número de ticket automático
            $validated['numero_ticket'] = Ticket::generarNumeroTicket();
            $validated['activo'] = 1;

            // Si se asigna, registrar fecha de asignación
            if (!empty($validated['id_asignado'])) {
                $validated['fecha_asignacion'] = now();
            }

            $ticket = Ticket::create($validated);

            // Cargar relaciones necesarias para el email
            $ticket->load('socio');

            // Preparar detalles para actividad
            $detalles = [
                'Número: ' . $ticket->numero_ticket,
                'Título: ' . $ticket->titulo,
                'Tipo: ' . $ticket->tipo_ticket_texto,
                'Prioridad: ' . $ticket->prioridad_texto,
                'Estado: ' . $ticket->estado_texto
            ];

            if ($ticket->socio) {
                $detalles[] = 'Socio: ' . $ticket->socio->nombre_completo;
            }

            if ($ticket->asignado) {
                $detalles[] = 'Asignado a: ' . $ticket->asignado->nombre_completo;
            }

            ActividadHelper::registrar(
                'Tickets',
                'Nuevo ticket creado: ' . implode(' | ', $detalles),
                auth()->id()
            );

            // Enviar email al socio si tiene uno asignado y tiene email
            if ($ticket->socio && $ticket->socio->email) {
                EnviarTicketEmail::dispatch($ticket, $ticket->socio);
            }

            return redirect()->route('tickets.show', $ticket->id)
                           ->with('success', 'Ticket creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al crear el ticket: ' . $e->getMessage());
        }
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['socio', 'asignado', 'respuestas.usuario', 'respuestas.socio']);

        // Ordenar respuestas cronológicamente
        $respuestas = $ticket->respuestas()
                            ->activos()
                            ->ordenCronologico()
                            ->get();

        return view('tickets.show', compact('ticket', 'respuestas'));
    }

    public function edit(Ticket $ticket)
    {
        $socios = Socio::activos()
                      ->where('estado', 'activo')
                      ->orderBy('nombre')
                      ->get();

        $funcionarios = Funcionario::activos()
                                  ->where('estado', 'activo')
                                  ->orderBy('nombre')
                                  ->get();

        return view('tickets.edit', compact('ticket', 'socios', 'funcionarios'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'id_socio' => 'nullable|exists:socios,id',
            'titulo' => 'required|string|max:200',
            'descripcion' => 'required|string',
            'tipo_ticket' => 'required|in:consulta,reclamo,solicitud,averia,fuga,corte,reconexion,lectura,otro',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'estado' => 'required|in:abierto,en_proceso,pendiente,resuelto,cerrado,cancelado',
            'id_asignado' => 'nullable|exists:funcionarios,id',
            'fecha_reporte' => 'required|date',
            'fecha_resolucion' => 'nullable|date',
            'fecha_cierre' => 'nullable|date',
            'solucion' => 'nullable|string',
            'costo_reparacion' => 'nullable|numeric|min:0',
            'satisfaccion' => 'nullable|in:muy_insatisfecho,insatisfecho,neutral,satisfecho,muy_satisfecho',
            'comentario_cierre' => 'nullable|string',
            'ubicacion' => 'nullable|string|max:255',
            'contacto_nombre' => 'nullable|string|max:100',
            'contacto_telefono' => 'nullable|string|max:20',
            'contacto_email' => 'nullable|email|max:150',
            'observaciones' => 'nullable|string'
        ]);

        try {
            // Tracking de cambios
            $cambios = [];

            if ($ticket->titulo != $validated['titulo']) {
                $cambios[] = "Título: '{$ticket->titulo}' → '{$validated['titulo']}'";
            }

            if ($ticket->tipo_ticket != $validated['tipo_ticket']) {
                $tipoAnterior = $ticket->tipo_ticket_texto;
                $tipoNuevo = (new Ticket(['tipo_ticket' => $validated['tipo_ticket']]))->tipo_ticket_texto;
                $cambios[] = "Tipo: '{$tipoAnterior}' → '{$tipoNuevo}'";
            }

            if ($ticket->prioridad != $validated['prioridad']) {
                $prioridadAnterior = $ticket->prioridad_texto;
                $prioridadNueva = (new Ticket(['prioridad' => $validated['prioridad']]))->prioridad_texto;
                $cambios[] = "Prioridad: '{$prioridadAnterior}' → '{$prioridadNueva}'";
            }

            if ($ticket->estado != $validated['estado']) {
                $estadoAnterior = $ticket->estado_texto;
                $estadoNuevo = (new Ticket(['estado' => $validated['estado']]))->estado_texto;
                $cambios[] = "Estado: '{$estadoAnterior}' → '{$estadoNuevo}'";

                // Si cambia a resuelto y no tiene fecha de resolución, establecerla
                if ($validated['estado'] === 'resuelto' && !$ticket->fecha_resolucion) {
                    $validated['fecha_resolucion'] = now();
                }

                // Si cambia a cerrado y no tiene fecha de cierre, establecerla
                if ($validated['estado'] === 'cerrado' && !$ticket->fecha_cierre) {
                    $validated['fecha_cierre'] = now();
                }
            }

            if ($ticket->id_asignado != $validated['id_asignado']) {
                $asignadoAnterior = $ticket->asignado ? $ticket->asignado->nombre_completo : 'Sin asignar';
                $funcionarioNuevo = $validated['id_asignado'] ? Funcionario::find($validated['id_asignado']) : null;
                $asignadoNuevo = $funcionarioNuevo ? $funcionarioNuevo->nombre_completo : 'Sin asignar';
                $cambios[] = "Asignado: '{$asignadoAnterior}' → '{$asignadoNuevo}'";

                // Si se asigna por primera vez, registrar fecha de asignación
                if (!$ticket->id_asignado && $validated['id_asignado']) {
                    $validated['fecha_asignacion'] = now();
                }
            }

            if ($ticket->id_socio != $validated['id_socio']) {
                $socioAnterior = $ticket->socio ? $ticket->socio->nombre_completo : 'Sin socio';
                $socioNuevo = $validated['id_socio'] ? Socio::find($validated['id_socio']) : null;
                $socioNuevo = $socioNuevo ? $socioNuevo->nombre_completo : 'Sin socio';
                $cambios[] = "Socio: '{$socioAnterior}' → '{$socioNuevo}'";
            }

            if ($ticket->ubicacion != $validated['ubicacion']) {
                $cambios[] = "Ubicación: '{$ticket->ubicacion}' → '{$validated['ubicacion']}'";
            }

            if (!empty($validated['costo_reparacion']) && $ticket->costo_reparacion != $validated['costo_reparacion']) {
                $cambios[] = "Costo reparación: '{$ticket->costo_reparacion_formateado}' → '$" . number_format($validated['costo_reparacion'], 0, ',', '.') . "'";
            }

            $ticket->update($validated);

            if (!empty($cambios)) {
                ActividadHelper::registrar(
                    'Tickets',
                    "Ticket actualizado [{$ticket->numero_ticket}]: " . implode(' | ', $cambios),
                    auth()->id()
                );
            }

            return redirect()->route('tickets.show', $ticket->id)
                           ->with('success', 'Ticket actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar el ticket: ' . $e->getMessage());
        }
    }

    public function destroy(Ticket $ticket)
    {
        try {
            $numeroTicket = $ticket->numero_ticket;
            $ticket->activo = 0;
            $ticket->save();

            ActividadHelper::registrar(
                'Tickets',
                "Ticket eliminado: {$numeroTicket} - {$ticket->titulo}",
                auth()->id()
            );

            return redirect()->route('tickets.index')
                           ->with('success', 'Ticket eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error al eliminar el ticket: ' . $e->getMessage());
        }
    }

    /**
     * Agregar respuesta a un ticket
     */
    public function agregarRespuesta(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'mensaje' => 'required|string|min:10',
            'visible_socio' => 'sometimes|boolean'
        ]);

        try {
            // Crear la respuesta
            $respuesta = \App\Models\TicketRespuesta::create([
                'id_ticket' => $ticket->id,
                'id_usuario' => auth()->id(),
                'mensaje' => $validated['mensaje'],
                'tipo_autor' => 'funcionario',
                'visible_socio' => $validated['visible_socio'] ?? true,
                'activo' => 1
            ]);

            // Cargar relaciones necesarias
            $respuesta->load(['usuario', 'ticket', 'ticket.socio']);

            // Registrar actividad
            ActividadHelper::registrar(
                'Tickets',
                "Respuesta agregada al ticket {$ticket->numero_ticket} por " . auth()->user()->name,
                auth()->id()
            );

            // Enviar email al socio si corresponde y tiene email
            if ($respuesta->visible_socio && $ticket->socio && $ticket->socio->email) {
                \App\Jobs\EnviarRespuestaTicketEmail::dispatch($respuesta, $ticket->socio->email);
            }

            return redirect()->route('tickets.show', $ticket->id)
                           ->with('success', 'Respuesta agregada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al agregar la respuesta: ' . $e->getMessage());
        }
    }
}
