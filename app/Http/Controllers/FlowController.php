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
                        // Verificar si es un cambio de plan
                        $cambioPlan = \App\Models\CambioPlan::where('token_flow', $token)->first();

                        if ($cambioPlan) {
                            // Es un cambio de plan - aplicar el cambio
                            if ($cambioPlan->aplicar()) {
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

            // Verificar si es un cambio de plan
            $cambioPlan = \App\Models\CambioPlan::where('token_flow', $token)->first();

            // Redirigir según estado actual de la transacción
            if ($transaccion->estado === 'pagado') {
                if ($cambioPlan) {
                    // Es un cambio de plan
                    return redirect()->route('organizacion.index')
                                   ->with('success', '¡Cambio de plan realizado exitosamente! Tu plan ha sido actualizado.');
                }

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

            } elseif ($transaccion->estado === 'rechazado') {
                if ($cambioPlan) {
                    // Cambio de plan rechazado
                    return redirect()->route('organizacion.upgrade')
                                   ->with('error', 'El pago fue rechazado. Por favor, intente nuevamente.');
                }

                return redirect()->route('pagos.create', ['boleta_id' => $transaccion->id_boleta])
                               ->with('error', 'El pago fue rechazado. Por favor, intente nuevamente.');
            } else {
                // Estado pendiente o anulado
                if ($cambioPlan) {
                    return redirect()->route('organizacion.upgrade')
                                   ->with('warning', 'El pago está pendiente de confirmación. Si ya completó el pago, espere unos momentos.');
                }

                return redirect()->route('pagos.create', ['boleta_id' => $transaccion->id_boleta])
                               ->with('warning', 'El pago está pendiente de confirmación. Si ya completó el pago, espere unos momentos.');
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
}
