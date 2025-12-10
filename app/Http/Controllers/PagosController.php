<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;
use App\Models\Boleta;
use App\Models\Socio;
use App\Helpers\ActividadHelper;
use App\Services\FlowPaymentService;
use App\Mail\LinkPagoFlowMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PagosController extends Controller
{
    /**
     * Listar todos los pagos
     */
    public function index(Request $request)
    {
        $query = Pago::with(['boleta', 'socio']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_recibo', 'like', "%{$search}%")
                  ->orWhereHas('socio', function($sq) use ($search) {
                      $sq->where('nombre', 'like', "%{$search}%")
                        ->orWhere('apellido_paterno', 'like', "%{$search}%")
                        ->orWhere('numero_socio', 'like', "%{$search}%");
                  })
                  ->orWhere('numero_comprobante', 'like', "%{$search}%");
            });
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_pago', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_pago', '<=', $request->fecha_hasta);
        }

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        if ($request->filled('socio_id')) {
            $query->where('id_socio', $request->socio_id);
        }

        // Estadísticas
        $estadisticas = [
            'total_pagos' => Pago::count(),
            'pagos_hoy' => Pago::whereDate('fecha_pago', today())->count(),
            'total_recaudado' => Pago::sum('monto_pagado'),
            'recaudado_hoy' => Pago::whereDate('fecha_pago', today())->sum('monto_pagado'),
            'recaudado_mes' => Pago::whereYear('fecha_pago', date('Y'))
                                   ->whereMonth('fecha_pago', date('m'))
                                   ->sum('monto_pagado'),
            'efectivo' => Pago::where('metodo_pago', 'efectivo')->sum('monto_pagado')
        ];

        $pagos = $query->orderBy('fecha_pago', 'desc')
                      ->orderBy('id', 'desc')
                      ->paginate(20);

        $socios = Socio::activos()->orderBy('numero_socio')->get();

        return view('pagos.index', compact('pagos', 'socios', 'estadisticas'));
    }

    /**
     * Mostrar formulario de registro de pago
     */
    public function create(Request $request)
    {
        $socioId = $request->get('socio_id');
        $boletaId = $request->get('boleta_id');

        $socios = Socio::activos()->orderBy('numero_socio')->get();
        $boleta = null;

        // Si viene un boleta_id específico, cargar esa boleta
        if ($boletaId) {
            $boleta = Boleta::with('socio')->findOrFail($boletaId);
            $boletas = collect([$boleta]);
        }
        // Si viene un socio_id, cargar solo boletas de ese socio
        elseif ($socioId) {
            $boletas = Boleta::activos()
                            ->with('socio')
                            ->where('id_socio', $socioId)
                            ->whereIn('estado', ['pendiente', 'vencida'])
                            ->orderBy('fecha_vencimiento')
                            ->get();
        }
        // Por defecto, cargar TODAS las boletas pendientes o vencidas
        else {
            $boletas = Boleta::activos()
                            ->with('socio')
                            ->whereIn('estado', ['pendiente', 'vencida'])
                            ->orderBy('fecha_vencimiento')
                            ->get();
        }

        return view('pagos.create', compact('socios', 'boletas', 'boleta'));
    }

    /**
     * Guardar nuevo pago
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_boleta' => 'required|exists:boletas,id',
            'fecha_pago' => 'required|date',
            'tipo_pago' => 'required|in:completo,parcial',
            'monto_pagado' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,transferencia,cheque,debito,credito',
            'numero_comprobante' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $boleta = Boleta::with('socio')->findOrFail($validated['id_boleta']);

            // Validar monto según tipo de pago
            if ($validated['tipo_pago'] === 'completo') {
                // En pago completo, el monto debe ser igual al total de la boleta
                $totalPendiente = $boleta->total - Pago::where('id_boleta', $boleta->id)->sum('monto_pagado');
                if (abs($validated['monto_pagado'] - $totalPendiente) > 0.01) {
                    DB::rollBack();
                    return redirect()->back()
                                   ->withInput()
                                   ->with('error', 'En pago completo, el monto debe cubrir el total pendiente de la boleta.');
                }
            } else {
                // En pago parcial, validar que no exceda el total pendiente
                $totalPendiente = $boleta->total - Pago::where('id_boleta', $boleta->id)->sum('monto_pagado');
                if ($validated['monto_pagado'] > $totalPendiente) {
                    DB::rollBack();
                    return redirect()->back()
                                   ->withInput()
                                   ->with('error', 'El monto pagado no puede exceder el total pendiente de la boleta ($' . number_format($totalPendiente, 0, ',', '.') . ').');
                }
            }

            // Generar número de recibo
            $numeroRecibo = Pago::generarNumeroRecibo();

            $pago = Pago::create([
                'numero_recibo' => $numeroRecibo,
                'id_boleta' => $validated['id_boleta'],
                'id_socio' => $boleta->id_socio,
                'fecha_pago' => $validated['fecha_pago'],
                'monto_pagado' => $validated['monto_pagado'],
                'metodo_pago' => $validated['metodo_pago'],
                'numero_comprobante' => $validated['numero_comprobante'],
                'observaciones' => $validated['observaciones'],
                'id_usuario_registro' => auth()->id(),
            ]);

            // Verificar si el pago cubre el total de la boleta
            $totalPagos = Pago::where('id_boleta', $boleta->id)->sum('monto_pagado');
            if ($totalPagos >= $boleta->total) {
                $boleta->update(['estado' => 'pagada']);
            }

            // Registrar actividad
            $detalles = [
                "Recibo: {$pago->numero_recibo}",
                "Boleta: {$boleta->numero_boleta}",
                "Socio: {$boleta->socio->nombre_completo}",
                "Monto: " . '$' . number_format($validated['monto_pagado'], 0, ',', '.'),
                "Método: " . ucfirst($validated['metodo_pago'])
            ];

            if ($request->filled('numero_comprobante')) {
                $detalles[] = "Comprobante: {$validated['numero_comprobante']}";
            }

            ActividadHelper::registrar(
                'Pagos',
                'Nuevo pago registrado: ' . implode(' | ', $detalles),
                auth()->id()
            );

            DB::commit();

            // Redirigir directamente a imprimir el comprobante
            return redirect()->route('pagos.imprimir', $pago->id)
                           ->with('success', 'Pago registrado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error al registrar el pago: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Mostrar detalle del pago
     */
    public function show($id)
    {
        $pago = Pago::with(['boleta.socio', 'boleta.pagos', 'socio', 'usuarioRegistro'])->findOrFail($id);

        // Calcular saldo de la boleta
        $totalPagado = Pago::where('id_boleta', $pago->id_boleta)->sum('monto_pagado');
        $saldo = $pago->boleta->total - $totalPagado;

        return view('pagos.show', compact('pago', 'saldo'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $pago = Pago::with(['socio', 'boleta'])->findOrFail($id);

        $boletas = Boleta::activos()
            ->with('socio')
            ->whereIn('estado', ['pendiente', 'vencida', 'pagada'])
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        $socios = Socio::activos()->orderBy('numero_socio')->get();

        return view('pagos.edit', compact('pago', 'boletas', 'socios'));
    }

    /**
     * Actualizar pago
     */
    public function update(Request $request, $id)
    {
        $pago = Pago::with('boleta')->findOrFail($id);

        $validated = $request->validate([
            'id_boleta' => 'required|exists:boletas,id',
            'fecha_pago' => 'required|date',
            'monto_pagado' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,transferencia,cheque,debito,credito',
            'numero_comprobante' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Detectar cambios
            $cambios = [];

            $boletaAnterior = $pago->boleta;
            $boletaNueva = Boleta::with('socio')->find($validated['id_boleta']);

            if ($pago->id_boleta != $validated['id_boleta']) {
                $cambios[] = "Boleta: '{$boletaAnterior->numero_boleta}' → '{$boletaNueva->numero_boleta}'";
            }

            if ($pago->monto_pagado != $validated['monto_pagado']) {
                $cambios[] = "Monto: '{$pago->monto_pagado_formateado}' → '$" . number_format($validated['monto_pagado'], 0, ',', '.') . "'";
            }

            if ($pago->metodo_pago != $validated['metodo_pago']) {
                $cambios[] = "Método: '{$pago->metodo_pago_texto}' → '" . ucfirst($validated['metodo_pago']) . "'";
            }

            if ($pago->fecha_pago->format('Y-m-d') != $validated['fecha_pago']) {
                $cambios[] = "Fecha: '{$pago->fecha_pago_formateada}' → '" . date('d/m/Y', strtotime($validated['fecha_pago'])) . "'";
            }

            // Actualizar socio si cambió la boleta
            $validated['id_socio'] = $boletaNueva->id_socio;

            $pago->update($validated);

            // Actualizar estado de boleta anterior
            $totalPagosAnterior = Pago::where('id_boleta', $boletaAnterior->id)->sum('monto_pagado');
            if ($totalPagosAnterior >= $boletaAnterior->total) {
                $boletaAnterior->update(['estado' => 'pagada']);
            } else {
                if ($boletaAnterior->estado == 'pagada') {
                    $boletaAnterior->update(['estado' => $boletaAnterior->fecha_vencimiento < today() ? 'vencida' : 'pendiente']);
                }
            }

            // Actualizar estado de boleta nueva (si cambió)
            if ($pago->id_boleta != $boletaAnterior->id) {
                $totalPagosNueva = Pago::where('id_boleta', $boletaNueva->id)->sum('monto_pagado');
                if ($totalPagosNueva >= $boletaNueva->total) {
                    $boletaNueva->update(['estado' => 'pagada']);
                }
            }

            if (!empty($cambios)) {
                ActividadHelper::registrar(
                    'Pagos',
                    "Pago actualizado [{$pago->numero_recibo}]: " . implode(' | ', $cambios),
                    auth()->id()
                );
            }

            DB::commit();

            return redirect()->route('pagos.show', $pago->id)
                           ->with('success', 'Pago actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar pago
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $pago = Pago::with('boleta', 'socio')->findOrFail($id);
            $boleta = $pago->boleta;

            // Registrar actividad antes de eliminar
            ActividadHelper::registrar(
                'Pagos',
                "Pago eliminado [{$pago->numero_recibo}] - Boleta: {$boleta->numero_boleta} - Socio: {$pago->socio->nombre_completo} - Monto: {$pago->monto_pagado_formateado}",
                auth()->id()
            );

            $pago->delete();

            // Actualizar estado de la boleta
            $totalPagos = Pago::where('id_boleta', $boleta->id)->sum('monto_pagado');
            if ($totalPagos >= $boleta->total) {
                $boleta->update(['estado' => 'pagada']);
            } else {
                if ($boleta->estado == 'pagada') {
                    $boleta->update(['estado' => $boleta->fecha_vencimiento < today() ? 'vencida' : 'pendiente']);
                }
            }

            DB::commit();

            return redirect()->route('pagos.index')
                           ->with('success', 'Pago eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error al eliminar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Imprimir recibo de pago
     */
    public function imprimir($id)
    {
        $pago = Pago::with(['boleta.socio', 'socio'])->findOrFail($id);

        // Registrar actividad
        ActividadHelper::registrar(
            'Pagos',
            "Recibo impreso/descargado [{$pago->numero_recibo}] - Socio: {$pago->socio->nombre_completo}",
            auth()->id()
        );

        return view('pagos.imprimir', compact('pago'));
    }

    /**
     * Obtener boletas pendientes de un socio (API)
     */
    public function boletasPendientes($socioId)
    {
        $boletas = Boleta::where('id_socio', $socioId)
                        ->whereIn('estado', ['pendiente', 'vencida'])
                        ->orderBy('fecha_vencimiento')
                        ->get();

        return response()->json($boletas);
    }

    /**
     * Reporte de caja diaria
     */
    public function reporteCaja(Request $request)
    {
        $fecha = $request->get('fecha', today()->toDateString());

        $pagos = Pago::with(['boleta', 'socio'])
                    ->whereDate('fecha_pago', $fecha)
                    ->orderBy('id')
                    ->get();

        // Totales por método de pago
        $totalesPorMetodo = Pago::whereDate('fecha_pago', $fecha)
                                ->select('metodo_pago', DB::raw('SUM(monto_pagado) as total'))
                                ->groupBy('metodo_pago')
                                ->get();

        $totalDia = $pagos->sum('monto_pagado');

        // Registrar actividad
        ActividadHelper::registrar(
            'Pagos',
            "Reporte de caja generado para fecha: " . date('d/m/Y', strtotime($fecha)) . " - Total: $" . number_format($totalDia, 0, ',', '.'),
            auth()->id()
        );

        return view('pagos.reporte-caja', compact('pagos', 'totalesPorMetodo', 'totalDia', 'fecha'));
    }

    /**
     * Generar link de pago Flow
     */
    public function generarLinkFlow(Request $request)
    {
        $validated = $request->validate([
            'id_boleta' => 'required|exists:boletas,id',
            'email' => 'required|email',
        ]);

        try {
            $boleta = Boleta::with('socio')->findOrFail($validated['id_boleta']);

            // Calcular monto pendiente
            $totalPagado = Pago::where('id_boleta', $boleta->id)->sum('monto_pagado');
            $montoPendiente = $boleta->total - $totalPagado;

            if ($montoPendiente <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta boleta ya está completamente pagada',
                ], 400);
            }

            // Crear pago en Flow
            $flowService = new FlowPaymentService();
            $subject = "Pago APR - Boleta {$boleta->numero_boleta} - {$boleta->socio->nombre_completo}";

            $resultado = $flowService->crearPago(
                $boleta->id_socio,
                $boleta->id,
                $montoPendiente,
                $validated['email'],
                $subject
            );

            if ($resultado['success']) {
                // Enviar correo con el link de pago
                try {
                    Mail::to($validated['email'])->send(
                        new LinkPagoFlowMail(
                            $boleta->socio,
                            $boleta,
                            $resultado['url'],
                            $montoPendiente
                        )
                    );
                } catch (\Exception $e) {
                    \Log::error('Error al enviar correo de pago Flow', [
                        'error' => $e->getMessage(),
                        'email' => $validated['email'],
                    ]);
                }

                // Registrar actividad
                ActividadHelper::registrar(
                    'Pagos',
                    "Link de pago Flow generado y enviado - Boleta: {$boleta->numero_boleta} - Socio: {$boleta->socio->nombre_completo} - Email: {$validated['email']} - Monto: $" . number_format($montoPendiente, 0, ',', '.'),
                    auth()->id()
                );

                return response()->json([
                    'success' => true,
                    'url' => $resultado['url'],
                    'message' => 'Link de pago generado y enviado exitosamente al correo ' . $validated['email'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $resultado['message'] ?? 'Error al generar el link de pago',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
