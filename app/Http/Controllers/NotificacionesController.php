<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificacion;
use App\Models\Socio;
use App\Helpers\ActividadHelper;
use Illuminate\Support\Facades\DB;

class NotificacionesController extends Controller
{
    /**
     * Listar todas las notificaciones
     */
    public function index(Request $request)
    {
        $query = Notificacion::with(['socio', 'usuarioCreador']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('mensaje', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('destinatario')) {
            $query->where('destinatario', $request->destinatario);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_programada', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_programada', '<=', $request->fecha_hasta);
        }

        // Estadísticas
        $estadisticas = [
            'total_notificaciones' => Notificacion::count(),
            'enviadas_hoy' => Notificacion::whereDate('fecha_enviada', today())->count(),
            'programadas' => Notificacion::where('estado', 'programada')->count(),
            'total_destinatarios' => Notificacion::sum('total_destinatarios')
        ];

        $notificaciones = $query->orderBy('fecha_creacion', 'desc')
                              ->orderBy('id', 'desc')
                              ->paginate(20);

        return view('notificaciones.index', compact('notificaciones', 'estadisticas'));
    }

    /**
     * Mostrar formulario de creación de notificación
     */
    public function create()
    {
        $socios = Socio::activos()->orderBy('numero_socio')->get();

        return view('notificaciones.create', compact('socios'));
    }

    /**
     * Guardar nueva notificación
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:200',
            'mensaje' => 'required|string',
            'tipo' => 'required|in:informativa,importante,urgente,recordatorio,aviso_pago,corte_servicio,corte,reunion',
            'destinatario' => 'required|in:todos,morosos,activos,sector,socio',
            'id_socio' => 'required_if:destinatario,socio|nullable|exists:socios,id',
            'sector' => 'required_if:destinatario,sector|nullable|string|max:100',
            'estado' => 'required|in:borrador,programada,enviada,cancelada',
            'fecha_programada' => 'nullable|date',
            'canal' => 'required|array|min:1',
            'canal.*' => 'in:email,sms,whatsapp,sistema',
            'observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Calcular total de destinatarios según el tipo
            $totalDestinatarios = 0;
            switch ($validated['destinatario']) {
                case 'todos':
                    $totalDestinatarios = Socio::activos()->count();
                    break;
                case 'morosos':
                    $totalDestinatarios = Socio::activos()->where('estado_cuenta', 'moroso')->count();
                    break;
                case 'activos':
                    $totalDestinatarios = Socio::activos()->count();
                    break;
                case 'sector':
                    $totalDestinatarios = Socio::activos()->where('sector', $validated['sector'])->count();
                    break;
                case 'socio':
                    $totalDestinatarios = 1;
                    break;
            }

            // Convertir array de canales a string separado por comas
            $canalString = implode(',', $validated['canal']);

            $notificacion = Notificacion::create([
                'titulo' => $validated['titulo'],
                'mensaje' => $validated['mensaje'],
                'tipo' => $validated['tipo'],
                'destinatario' => $validated['destinatario'],
                'id_socio' => $validated['id_socio'] ?? null,
                'sector' => $validated['sector'] ?? null,
                'estado' => $validated['estado'],
                'fecha_programada' => $validated['fecha_programada'] ?? null,
                'canal' => $canalString,
                'total_destinatarios' => $totalDestinatarios,
                'total_enviados' => 0,
                'total_leidos' => 0,
                'total_errores' => 0,
                'enviado_email' => false,
                'enviado_sms' => false,
                'enviado_whatsapp' => false,
                'id_usuario_creador' => auth()->id(),
                'observaciones' => $validated['observaciones'],
                'activo' => true,
            ]);

            // Registrar actividad
            $detalles = [
                "Título: {$notificacion->titulo}",
                "Tipo: " . ucfirst($validated['tipo']),
                "Destinatario: {$notificacion->destinatario_texto}",
                "Total destinatarios: {$totalDestinatarios}",
                "Estado: " . ucfirst($validated['estado'])
            ];

            if ($request->filled('fecha_programada')) {
                $detalles[] = "Fecha programada: " . date('d/m/Y', strtotime($validated['fecha_programada']));
            }

            ActividadHelper::registrar(
                'Notificaciones',
                'Nueva notificación creada: ' . implode(' | ', $detalles),
                auth()->id()
            );

            DB::commit();

            // Si el estado es "enviada", enviar inmediatamente
            if ($validated['estado'] === 'enviada') {
                return $this->enviar($notificacion->id);
            }

            return redirect()->route('notificaciones.show', $notificacion->id)
                           ->with('success', 'Notificación creada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error al crear la notificación: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Mostrar detalle de la notificación
     */
    public function show($id)
    {
        $notificacion = Notificacion::with(['socio', 'usuarioCreador'])->findOrFail($id);

        return view('notificaciones.show', compact('notificacion'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $notificacion = Notificacion::with(['socio'])->findOrFail($id);
        $socios = Socio::activos()->orderBy('numero_socio')->get();

        return view('notificaciones.edit', compact('notificacion', 'socios'));
    }

    /**
     * Actualizar notificación
     */
    public function update(Request $request, $id)
    {
        $notificacion = Notificacion::with(['socio'])->findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'required|string|max:200',
            'mensaje' => 'required|string',
            'tipo' => 'required|in:informativa,importante,urgente,recordatorio,aviso_pago,corte_servicio,corte,reunion',
            'destinatario' => 'required|in:todos,morosos,activos,sector,socio',
            'id_socio' => 'required_if:destinatario,socio|nullable|exists:socios,id',
            'sector' => 'required_if:destinatario,sector|nullable|string|max:100',
            'estado' => 'required|in:borrador,programada,enviada,cancelada',
            'fecha_programada' => 'nullable|date',
            'canal' => 'required|array|min:1',
            'canal.*' => 'in:email,sms,whatsapp,sistema',
            'observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Convertir array de canales a string separado por comas
            $canalString = implode(',', $validated['canal']);

            // Detectar cambios
            $cambios = [];

            if ($notificacion->titulo != $validated['titulo']) {
                $cambios[] = "Título: '{$notificacion->titulo}' → '{$validated['titulo']}'";
            }

            if ($notificacion->tipo != $validated['tipo']) {
                $cambios[] = "Tipo: '" . ucfirst($notificacion->tipo) . "' → '" . ucfirst($validated['tipo']) . "'";
            }

            if ($notificacion->destinatario != $validated['destinatario']) {
                $destinatarioAnterior = $notificacion->destinatario_texto;
                $cambios[] = "Destinatario: '{$destinatarioAnterior}' → '" . ucfirst($validated['destinatario']) . "'";
            }

            if ($notificacion->estado != $validated['estado']) {
                $cambios[] = "Estado: '" . ucfirst($notificacion->estado) . "' → '" . ucfirst($validated['estado']) . "'";
            }

            if ($notificacion->canal != $canalString) {
                $cambios[] = "Canal: '" . ucfirst($notificacion->canal) . "' → '" . ucfirst($canalString) . "'";
            }

            $fechaProgramadaAnterior = $notificacion->fecha_programada ? $notificacion->fecha_programada->format('Y-m-d') : null;
            $fechaProgramadaNueva = $validated['fecha_programada'] ?? null;

            if ($fechaProgramadaAnterior != $fechaProgramadaNueva) {
                $fechaAnteriorTexto = $fechaProgramadaAnterior ? date('d/m/Y', strtotime($fechaProgramadaAnterior)) : 'Sin fecha';
                $fechaNuevaTexto = $fechaProgramadaNueva ? date('d/m/Y', strtotime($fechaProgramadaNueva)) : 'Sin fecha';
                $cambios[] = "Fecha programada: '{$fechaAnteriorTexto}' → '{$fechaNuevaTexto}'";
            }

            // Recalcular total de destinatarios si cambió el destinatario
            $totalDestinatarios = $notificacion->total_destinatarios;
            if ($notificacion->destinatario != $validated['destinatario'] ||
                $notificacion->id_socio != ($validated['id_socio'] ?? null) ||
                $notificacion->sector != ($validated['sector'] ?? null)) {

                switch ($validated['destinatario']) {
                    case 'todos':
                        $totalDestinatarios = Socio::activos()->count();
                        break;
                    case 'morosos':
                        $totalDestinatarios = Socio::activos()->where('estado_cuenta', 'moroso')->count();
                        break;
                    case 'activos':
                        $totalDestinatarios = Socio::activos()->count();
                        break;
                    case 'sector':
                        $totalDestinatarios = Socio::activos()->where('sector', $validated['sector'])->count();
                        break;
                    case 'socio':
                        $totalDestinatarios = 1;
                        break;
                }

                if ($notificacion->total_destinatarios != $totalDestinatarios) {
                    $cambios[] = "Total destinatarios: '{$notificacion->total_destinatarios}' → '{$totalDestinatarios}'";
                }
            }

            $notificacion->update([
                'titulo' => $validated['titulo'],
                'mensaje' => $validated['mensaje'],
                'tipo' => $validated['tipo'],
                'destinatario' => $validated['destinatario'],
                'id_socio' => $validated['id_socio'] ?? null,
                'sector' => $validated['sector'] ?? null,
                'estado' => $validated['estado'],
                'fecha_programada' => $validated['fecha_programada'] ?? null,
                'canal' => $canalString,
                'total_destinatarios' => $totalDestinatarios,
                'observaciones' => $validated['observaciones'],
            ]);

            if (!empty($cambios)) {
                ActividadHelper::registrar(
                    'Notificaciones',
                    "Notificación actualizada [ID: {$notificacion->id}]: " . implode(' | ', $cambios),
                    auth()->id()
                );
            }

            DB::commit();

            return redirect()->route('notificaciones.show', $notificacion->id)
                           ->with('success', 'Notificación actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar la notificación: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar notificación
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $notificacion = Notificacion::with(['socio'])->findOrFail($id);

            // Registrar actividad antes de eliminar
            $detalles = [
                "ID: {$notificacion->id}",
                "Título: {$notificacion->titulo}",
                "Tipo: " . ucfirst($notificacion->tipo),
                "Destinatario: {$notificacion->destinatario_texto}",
                "Estado: " . ucfirst($notificacion->estado)
            ];

            ActividadHelper::registrar(
                'Notificaciones',
                "Notificación eliminada - " . implode(' | ', $detalles),
                auth()->id()
            );

            $notificacion->delete();

            DB::commit();

            return redirect()->route('notificaciones.index')
                           ->with('success', 'Notificación eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error al eliminar la notificación: ' . $e->getMessage());
        }
    }

    /**
     * Enviar notificación a los destinatarios
     */
    public function enviar($id)
    {
        DB::beginTransaction();
        try {
            $notificacion = Notificacion::with('socio')->findOrFail($id);

            // Solo validar si ya fue enviada Y ya tiene jobs procesados
            if ($notificacion->estado === 'enviada' && $notificacion->total_enviados > 0) {
                return redirect()->back()->with('warning', 'Esta notificación ya fue enviada.');
            }

            $socios = $this->obtenerDestinatarios($notificacion);

            if ($socios->isEmpty()) {
                return redirect()->back()->with('error', 'No se encontraron destinatarios.');
            }

            // Actualizar solo si no estaba enviada
            if ($notificacion->estado !== 'enviada') {
                $notificacion->update([
                    'total_destinatarios' => $socios->count(),
                    'estado' => 'enviada',
                    'fecha_enviada' => now()
                ]);
            }

            // Verificar si el canal incluye email
            $canales = explode(',', $notificacion->canal);
            if (in_array('email', $canales)) {
                foreach ($socios as $socio) {
                    \App\Jobs\EnviarNotificacionEmail::dispatch($notificacion, $socio);
                }
            }

            ActividadHelper::registrar(
                'Notificaciones',
                "Notificación enviada - Título: {$notificacion->titulo} | Destinatarios: {$socios->count()}",
                auth()->id()
            );

            DB::commit();

            return redirect()->route('notificaciones.show', $notificacion->id)
                           ->with('success', "Notificación enviada a {$socios->count()} destinatarios.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    private function obtenerDestinatarios($notificacion)
    {
        $query = Socio::activos();
        switch ($notificacion->destinatario) {
            case 'todos': return $query->get();
            case 'morosos': return $query->where('estado', 'moroso')->get();
            case 'activos': return $query->where('estado', 'activo')->get();
            case 'sector': return $query->where('sector', $notificacion->sector)->get();
            case 'individual':
            case 'socio':
                return $notificacion->socio ? collect([$notificacion->socio]) : collect([]);
            default: return collect([]);
        }
    }
}
