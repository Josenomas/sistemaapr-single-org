<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FlowPaymentService;
use App\Models\TransaccionFlow;
use App\Models\Pago;
use App\Helpers\ActividadHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FlowController extends Controller
{
    protected $flowService;

    public function __construct(FlowPaymentService $flowService)
    {
        $this->flowService = $flowService;
    }

    /**
     * Callback de confirmación de Flow (servidor a servidor)
     */
    public function confirmar(Request $request)
    {
        try {
            $token = $request->input('token');

            if (!$token) {
                Log::error('Flow - Confirmación sin token');
                return response('Token no proporcionado', 400);
            }

            Log::info('Flow - Iniciando confirmación', ['token' => $token]);

            // Confirmar pago con Flow
            $resultado = $this->flowService->confirmarPago($token);

            if ($resultado['success']) {
                $transaccion = $resultado['transaccion'];
                $responseData = $resultado['response'];

                Log::info('Flow - Confirmación exitosa', [
                    'token' => $token,
                    'status' => $responseData['status'] ?? null,
                    'transaccion_id' => $transaccion->id ?? null,
                ]);

                // Si el pago fue exitoso, procesar según tipo de transacción
                if ($transaccion->estado === 'pagado') {
                    try {
                        // Verificar tipo de pago
                        if ($transaccion->tipo_pago === 'suscripcion') {
                            // Es un pago de suscripción
                            $this->procesarPagoSuscripcion($transaccion, $responseData);
                            Log::info('Flow - Pago de suscripción procesado', ['token' => $token]);
                        } elseif ($transaccion->tipo_pago === 'cambio_plan') {
                            // Es un cambio de plan
                            $cambioPlan = \App\Models\CambioPlan::find($transaccion->referencia_id);
                            if ($cambioPlan && $cambioPlan->aplicar()) {
                                Log::info('Flow - Cambio de plan aplicado', [
                                    'token' => $token,
                                    'cambio_plan_id' => $cambioPlan->id,
                                    'organizacion_id' => $cambioPlan->id_organizacion,
                                ]);
                            }
                        } else {
                            // Es un pago de boleta normal
                            $this->crearRegistroPago($transaccion, $responseData);
                            Log::info('Flow - Pago registrado correctamente', ['token' => $token]);
                        }
                    } catch (\Exception $e) {
                        // Aunque falle el registro, respondemos 200 a Flow
                        Log::error('Flow - Error al procesar pero transacción confirmada', [
                            'token' => $token,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                return response('CONFIRMADO', 200);
            }

            Log::error('Flow - Error en confirmación', [
                'token' => $token,
                'message' => $resultado['message'] ?? 'Error desconocido',
            ]);

            // Si es error 105 (No services available), es un error temporal de Flow
            // pero Flow SOLO llama a este callback cuando el pago fue realmente confirmado
            // por lo tanto es seguro aplicar el cambio automáticamente
            if (isset($resultado['message']) && strpos($resultado['message'], '"code":105') !== false) {
                $transaccion = \App\Models\TransaccionFlow::where('token', $token)->first();

                if ($transaccion && $transaccion->estado === 'pendiente') {
                    Log::warning('Flow - Error 105 detectado, procesando pago automáticamente', [
                        'token' => $token,
                        'transaccion_id' => $transaccion->id,
                        'flow_order' => $transaccion->flow_order,
                        'tipo_pago' => $transaccion->tipo_pago,
                        'razon' => 'Flow llamó al callback (pago confirmado) pero su API tiene error temporal'
                    ]);

                    // Marcar transacción como pagada
                    $transaccion->update([
                        'estado' => 'pagado',
                        'flow_status' => 2,
                        'payment_data' => json_encode([
                            'status' => 'paid',
                            'flow_error_105_bypass' => true,
                            'bypass_timestamp' => now()->toDateTimeString(),
                            'nota' => 'Pago confirmado por Flow callback pero API retornó error 105'
                        ]),
                    ]);

                    // Procesar según tipo de pago
                    try {
                        if ($transaccion->tipo_pago === 'suscripcion') {
                            // Procesar pago de suscripción
                            $this->procesarPagoSuscripcion($transaccion, ['status' => 2]);
                            Log::info('Flow - Pago de suscripción procesado (bypass error 105)', [
                                'transaccion_id' => $transaccion->id,
                                'referencia_id' => $transaccion->referencia_id,
                            ]);
                        } elseif ($transaccion->tipo_pago === 'cambio_plan') {
                            // Aplicar cambio de plan
                            $cambioPlan = \App\Models\CambioPlan::find($transaccion->referencia_id);
                            if ($cambioPlan && $cambioPlan->aplicar()) {
                                Log::info('Flow - Cambio de plan aplicado exitosamente (bypass error 105)', [
                                    'cambio_plan_id' => $cambioPlan->id,
                                    'organizacion_id' => $cambioPlan->id_organizacion,
                                    'plan_nuevo' => $cambioPlan->id_suscripcion_nueva,
                                ]);
                            }
                        } else {
                            // Pago de boleta
                            $this->crearRegistroPago($transaccion, ['status' => 2]);
                            Log::info('Flow - Pago de boleta procesado (bypass error 105)', [
                                'transaccion_id' => $transaccion->id,
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Flow - Error al procesar pago con bypass error 105', [
                            'transaccion_id' => $transaccion->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            // Incluso con error, devolver 200 para que Flow no reintente
            return response('CONFIRMADO', 200);

        } catch (\Exception $e) {
            Log::error('Flow - Excepción en confirmación', [
                'token' => $request->input('token') ?? 'N/A',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Devolver 200 aunque haya error para evitar reintentos de Flow
            return response('CONFIRMADO', 200);
        }
    }

    /**
     * Página de retorno después del pago
     */
    public function retorno(Request $request)
    {
        try {
            $token = $request->input('token');

            Log::info('Flow - Retorno iniciado', ['token' => $token]);

            if (!$token) {
                Log::error('Flow - Retorno sin token');
                return redirect()->route('pagos.index')
                               ->with('error', 'Token de pago no proporcionado');
            }

            // Buscar transacción
            $transaccion = TransaccionFlow::where('token', $token)
                                        ->with(['socio', 'boleta'])
                                        ->first();

            if (!$transaccion) {
                Log::error('Flow - Transacción no encontrada en retorno', ['token' => $token]);
                return redirect()->route('pagos.index')
                               ->with('error', 'Transacción no encontrada');
            }

            Log::info('Flow - Transacción encontrada', [
                'transaccion_id' => $transaccion->id,
                'estado' => $transaccion->estado,
                'flow_order' => $transaccion->flow_order,
            ]);

            // NO confirmar aquí - solo consultar estado actual
            // El pago solo debe crearse en el webhook confirmar() (servidor a servidor)

            Log::info('Flow - Consultando estado en retorno', [
                'estado_actual' => $transaccion->estado,
                'flow_order' => $transaccion->flow_order,
            ]);

            // Redirigir según estado actual de la transacción y tipo de pago
            if ($transaccion->estado === 'pagado') {
                // Pago exitoso - redirigir según tipo
                if ($transaccion->tipo_pago === 'suscripcion') {
                    // Es un pago de suscripción
                    return redirect()->route('dashboard')
                                   ->with('success', '¡Suscripción renovada exitosamente! Tu acceso ha sido extendido por un mes más.');
                } elseif ($transaccion->tipo_pago === 'cambio_plan') {
                    // Es un cambio de plan
                    return redirect()->route('organizacion.index')
                                   ->with('success', '¡Cambio de plan realizado exitosamente! Tu plan ha sido actualizado.');
                } else {
                    // Es un pago de boleta - buscar el pago registrado por el webhook
                    $pago = Pago::where('numero_comprobante', 'LIKE', '%' . $transaccion->flow_order . '%')
                               ->orderBy('id', 'desc')
                               ->first();

                    Log::info('Flow - Búsqueda de pago confirmado', [
                        'flow_order' => $transaccion->flow_order,
                        'pago_encontrado' => $pago ? 'SI' : 'NO',
                        'pago_id' => $pago->id ?? null,
                    ]);

                    if ($pago) {
                        return redirect()->route('comprobante.publico', $pago->id)
                                       ->with('success', '¡Pago realizado exitosamente!');
                    }

                    // Si el webhook aún no procesó, informar espera
                    return redirect()->route('pagos.index')
                                   ->with('success', '¡Pago realizado exitosamente! El comprobante estará disponible en unos momentos.');
                }

            } elseif ($transaccion->estado === 'rechazado') {
                // Pago rechazado
                if ($transaccion->tipo_pago === 'suscripcion') {
                    return redirect()->route('suscripcion.renovar')
                                   ->with('error', 'El pago fue rechazado. Por favor, intente nuevamente.');
                } elseif ($transaccion->tipo_pago === 'cambio_plan') {
                    return redirect()->route('organizacion.upgrade')
                                   ->with('error', 'El pago fue rechazado. Por favor, intente nuevamente.');
                } else {
                    return redirect()->route('pagos.create', ['boleta_id' => $transaccion->id_boleta])
                                   ->with('error', 'El pago fue rechazado. Por favor, intente nuevamente.');
                }
            } else {
                // Estado pendiente o anulado
                if ($transaccion->tipo_pago === 'suscripcion') {
                    return redirect()->route('suscripcion.renovar')
                                   ->with('warning', 'El pago está pendiente de confirmación. Si ya completó el pago, espere unos momentos.');
                } elseif ($transaccion->tipo_pago === 'cambio_plan') {
                    return redirect()->route('organizacion.upgrade')
                                   ->with('warning', 'El pago está pendiente de confirmación. Si ya completó el pago, espere unos momentos.');
                } else {
                    return redirect()->route('pagos.create', ['boleta_id' => $transaccion->id_boleta])
                                   ->with('warning', 'El pago está pendiente de confirmación. Si ya completó el pago, espere unos momentos.');
                }
            }

        } catch (\Exception $e) {
            Log::error('Flow - Error en retorno', [
                'token' => $request->input('token') ?? 'N/A',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('pagos.index')
                           ->with('error', 'Error al procesar el retorno del pago');
        }
    }

    /**
     * Crear registro de pago desde transacción Flow
     */
    private function crearRegistroPago($transaccion, $responseData)
    {
        DB::beginTransaction();
        try {
            // Verificar si ya existe un pago para esta transacción
            $pagoExistente = Pago::where('numero_comprobante', 'LIKE', '%FLOW-' . $transaccion->flow_order . '%')->first();

            if ($pagoExistente) {
                Log::info('Flow - Pago ya registrado para transacción', [
                    'flow_order' => $transaccion->flow_order,
                    'pago_id' => $pagoExistente->id,
                ]);
                DB::commit();
                return $pagoExistente;
            }

            // Cargar socio
            $transaccion->load('socio');
            $socio = $transaccion->socio;

            if (!$socio) {
                throw new \Exception('No se encontró el socio asociado a la transacción');
            }

            // Obtener todas las boletas a pagar
            $boletasIds = [];
            if (!empty($transaccion->boletas_ids)) {
                // Pago múltiple - decodificar IDs guardados
                $boletasIds = json_decode($transaccion->boletas_ids, true);
            } else {
                // Pago simple - solo la boleta principal
                $boletasIds = [$transaccion->id_boleta];
            }

            // Cargar boletas con sus pagos
            $boletas = \App\Models\Boleta::with('pagos')
                ->whereIn('id', $boletasIds)
                ->get();

            if ($boletas->isEmpty()) {
                throw new \Exception('No se encontraron boletas válidas para el pago');
            }

            // Calcular saldos pendientes de cada boleta
            $boletasConSaldo = [];
            $totalSaldoPendiente = 0;

            foreach ($boletas as $boleta) {
                $totalPagado = $boleta->pagos->sum('monto_pagado');
                $saldoPendiente = $boleta->total - $totalPagado;

                if ($saldoPendiente > 0) {
                    $boletasConSaldo[] = [
                        'boleta' => $boleta,
                        'saldo' => $saldoPendiente,
                    ];
                    $totalSaldoPendiente += $saldoPendiente;
                }
            }

            if (empty($boletasConSaldo)) {
                throw new \Exception('Todas las boletas ya están pagadas');
            }

            // Distribuir el monto pagado entre las boletas
            $montoPagado = $transaccion->monto;
            $montoRestante = $montoPagado;
            $pagosCreados = [];

            foreach ($boletasConSaldo as $index => $item) {
                $boleta = $item['boleta'];
                $saldoPendiente = $item['saldo'];

                // Calcular cuánto pagar de esta boleta
                if ($index === count($boletasConSaldo) - 1) {
                    // Última boleta: pagar todo lo que queda
                    $montoPagarBoleta = $montoRestante;
                } else {
                    // Pagar el saldo completo o lo que alcance
                    $montoPagarBoleta = min($saldoPendiente, $montoRestante);
                }

                if ($montoPagarBoleta <= 0) {
                    break;
                }

                // Generar número de recibo
                $numeroRecibo = Pago::generarNumeroRecibo();

                // Crear pago para esta boleta
                $pago = Pago::create([
                    'numero_recibo' => $numeroRecibo,
                    'id_boleta' => $boleta->id,
                    'id_socio' => $transaccion->id_socio,
                    'fecha_pago' => $transaccion->fecha_pago ?? now(),
                    'monto_pagado' => $montoPagarBoleta,
                    'metodo_pago' => 'credito',
                    'numero_comprobante' => 'FLOW-' . $transaccion->flow_order . ' / Token: ' . substr($transaccion->token, 0, 20),
                    'observaciones' => count($boletasConSaldo) > 1
                        ? "Pago múltiple Flow (" . ($index + 1) . " de " . count($boletasConSaldo) . "). Order: {$transaccion->flow_order}"
                        : "Pago Flow. Order: {$transaccion->flow_order}",
                    'id_usuario_registro' => null,
                ]);

                $pagosCreados[] = $pago;
                $montoRestante -= $montoPagarBoleta;

                // Actualizar estado de la boleta
                $totalPagosBoleta = Pago::where('id_boleta', $boleta->id)->sum('monto_pagado');
                if ($totalPagosBoleta >= $boleta->total) {
                    $boleta->update(['estado' => 'pagada']);
                    Log::info('Flow - Boleta marcada como pagada', [
                        'boleta_id' => $boleta->id,
                        'numero_boleta' => $boleta->numero_boleta,
                    ]);
                }

                Log::info('Flow - Pago individual creado', [
                    'boleta_id' => $boleta->id,
                    'numero_boleta' => $boleta->numero_boleta,
                    'monto' => $montoPagarBoleta,
                    'recibo' => $numeroRecibo,
                ]);
            }

            // Registrar actividad
            $montoFormateado = '$' . number_format($montoPagado, 0, ',', '.');
            $cantidadBoletas = count($pagosCreados);
            $descripcion = $cantidadBoletas > 1
                ? "Pago múltiple Flow - {$cantidadBoletas} boletas - Socio: {$socio->nombre_completo} - Total: {$montoFormateado} - Order: {$transaccion->flow_order}"
                : "Pago Flow - Recibo: {$pagosCreados[0]->numero_recibo} - Socio: {$socio->nombre_completo} - Monto: {$montoFormateado} - Order: {$transaccion->flow_order}";

            ActividadHelper::registrar('Pagos', $descripcion);

            DB::commit();

            Log::info('Flow - Pagos registrados exitosamente', [
                'flow_order' => $transaccion->flow_order,
                'cantidad_pagos' => count($pagosCreados),
                'monto_total' => $montoPagado,
            ]);

            // Retornar el primer pago (para redireccionar al comprobante)
            return $pagosCreados[0];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Flow - Error al crear pago', [
                'flow_order' => $transaccion->flow_order ?? 'N/A',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Mostrar estado de una transacción
     */
    public function verTransaccion($id)
    {
        $transaccion = TransaccionFlow::with(['socio', 'boleta'])
                                     ->findOrFail($id);

        return view('flow.transaccion', compact('transaccion'));
    }

    /**
     * Procesar pago de suscripción confirmado
     */
    private function procesarPagoSuscripcion($transaccion, $responseData)
    {
        try {
            // Buscar el pago de suscripción
            $pagoSuscripcion = \App\Models\PagoSuscripcion::find($transaccion->referencia_id);

            if (!$pagoSuscripcion) {
                Log::error('Flow - PagoSuscripcion no encontrado', [
                    'referencia_id' => $transaccion->referencia_id,
                    'transaccion_id' => $transaccion->id,
                ]);
                return;
            }

            // Verificar que no esté ya procesado
            if ($pagoSuscripcion->estado === 'pagado') {
                Log::info('Flow - PagoSuscripcion ya procesado', [
                    'pago_id' => $pagoSuscripcion->id,
                ]);
                return;
            }

            DB::beginTransaction();

            // Marcar pago como pagado
            $pagoSuscripcion->marcarComoPagado(
                $transaccion->token,
                $transaccion->flow_order
            );

            // Extender suscripción de la organización
            $organizacion = $pagoSuscripcion->organizacion;

            if ($organizacion->estado_suscripcion === 'vencida' || $organizacion->estado_suscripcion === 'suspendida') {
                // Reactivar desde vencida/suspendida
                $nuevaFechaInicio = now();
                $nuevaFechaFin = now()->addMonthNoOverflow();
            } else {
                // Extender desde activa
                $fechaActualFin = $organizacion->fecha_fin_suscripcion ?? now();
                $nuevaFechaInicio = $fechaActualFin->isPast() ? now() : $fechaActualFin;
                $nuevaFechaFin = $nuevaFechaInicio->copy()->addMonthNoOverflow();
            }

            $organizacion->update([
                'estado_suscripcion' => 'activa',
                'fecha_inicio_suscripcion' => $nuevaFechaInicio,
                'fecha_fin_suscripcion' => $nuevaFechaFin,
                'activo' => true,
                'dias_prueba_restantes' => 0,
            ]);

            DB::commit();

            Log::info('Flow - Suscripción extendida exitosamente', [
                'organizacion_id' => $organizacion->id,
                'nueva_fecha_fin' => $nuevaFechaFin->toDateString(),
                'pago_id' => $pagoSuscripcion->id,
            ]);

            // Registrar en auditoría (después del commit, no crítico)
            try {
                \App\Models\Auditoria::registrar(
                    'suscripciones',
                    'pago_procesado',
                    "Pago de suscripción procesado vía Flow. Monto: $" . number_format($pagoSuscripcion->monto, 0, ',', '.') . ". Suscripción extendida hasta " . $nuevaFechaFin->format('d/m/Y'),
                    'pagos_suscripcion',
                    $pagoSuscripcion->id,
                    $organizacion->id
                );
            } catch (\Exception $e) {
                // Si falla auditoría, solo loguear (el pago ya fue procesado correctamente)
                Log::warning('Flow - Error al registrar auditoría (pago procesado correctamente)', [
                    'error' => $e->getMessage(),
                    'pago_id' => $pagoSuscripcion->id,
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Flow - Error al procesar pago de suscripción', [
                'transaccion_id' => $transaccion->id,
                'referencia_id' => $transaccion->referencia_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
