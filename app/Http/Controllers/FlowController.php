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

                // Si el pago fue exitoso, crear registro de pago
                if ($transaccion->estado === 'pagado') {
                    try {
                        $this->crearRegistroPago($transaccion, $responseData);
                        Log::info('Flow - Pago registrado correctamente', ['token' => $token]);
                    } catch (\Exception $e) {
                        // Aunque falle el registro, respondemos 200 a Flow
                        Log::error('Flow - Error al crear pago pero transacción confirmada', [
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

            // Confirmar estado actualizado con Flow
            $resultado = $this->flowService->confirmarPago($token);

            if ($resultado['success']) {
                $transaccion->refresh();

                Log::info('Flow - Estado confirmado', [
                    'estado' => $transaccion->estado,
                    'flow_order' => $transaccion->flow_order,
                ]);

                // Redirigir según estado
                if ($transaccion->estado === 'pagado') {
                    // Buscar el pago registrado
                    $pago = Pago::where('numero_comprobante', 'LIKE', '%' . $transaccion->flow_order . '%')
                               ->orderBy('id', 'desc')
                               ->first();

                    Log::info('Flow - Búsqueda de pago', [
                        'flow_order' => $transaccion->flow_order,
                        'pago_encontrado' => $pago ? 'SI' : 'NO',
                        'pago_id' => $pago->id ?? null,
                    ]);

                    if ($pago) {
                        return redirect()->route('pagos.imprimir', $pago->id)
                                       ->with('success', '¡Pago realizado exitosamente!');
                    }

                    // Si no encontró el pago, intentar crearlo ahora
                    Log::warning('Flow - Pago no encontrado, intentando crear', [
                        'flow_order' => $transaccion->flow_order,
                    ]);

                    try {
                        $responseData = $resultado['response'] ?? [];
                        $pagoCreado = $this->crearRegistroPago($transaccion, $responseData);

                        if ($pagoCreado) {
                            Log::info('Flow - Pago creado en retorno', ['pago_id' => $pagoCreado->id]);
                            return redirect()->route('pagos.imprimir', $pagoCreado->id)
                                           ->with('success', '¡Pago realizado exitosamente!');
                        }
                    } catch (\Exception $e) {
                        Log::error('Flow - Error al crear pago en retorno', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }

                    return redirect()->route('pagos.index')
                                   ->with('success', '¡Pago realizado exitosamente! El comprobante estará disponible en unos momentos.');
                } elseif ($transaccion->estado === 'rechazado') {
                    return redirect()->route('pagos.create', ['boleta_id' => $transaccion->id_boleta])
                                   ->with('error', 'El pago fue rechazado. Por favor, intente nuevamente.');
                } else {
                    return redirect()->route('pagos.create', ['boleta_id' => $transaccion->id_boleta])
                                   ->with('warning', 'El pago está pendiente de confirmación.');
                }
            }

            Log::error('Flow - Error al confirmar pago', [
                'resultado' => $resultado,
            ]);

            return redirect()->route('pagos.index')
                           ->with('error', 'Error al verificar el estado del pago');

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

            // Cargar relaciones necesarias
            $transaccion->load(['boleta', 'socio']);
            $boleta = $transaccion->boleta;
            $socio = $transaccion->socio;

            if (!$boleta || !$socio) {
                throw new \Exception('No se encontró la boleta o socio asociado a la transacción');
            }

            // Generar número de recibo
            $numeroRecibo = Pago::generarNumeroRecibo();

            // Crear pago
            $pago = Pago::create([
                'numero_recibo' => $numeroRecibo,
                'id_boleta' => $transaccion->id_boleta,
                'id_socio' => $transaccion->id_socio,
                'fecha_pago' => $transaccion->fecha_pago ?? now(),
                'monto_pagado' => $transaccion->monto,
                'metodo_pago' => 'credito', // o 'debito' según preferencia
                'numero_comprobante' => 'FLOW-' . $transaccion->flow_order . ' / Token: ' . substr($transaccion->token, 0, 20),
                'observaciones' => 'Pago realizado mediante Flow. Order: ' . $transaccion->flow_order,
                'id_usuario_registro' => null, // Pago automático
            ]);

            // Actualizar estado de la boleta
            $totalPagos = Pago::where('id_boleta', $boleta->id)->sum('monto_pagado');
            if ($totalPagos >= $boleta->total) {
                $boleta->update(['estado' => 'pagada']);
            }

            // Registrar actividad
            $montoFormateado = '$' . number_format($transaccion->monto, 0, ',', '.');
            ActividadHelper::registrar(
                'Pagos',
                "Pago automático Flow - Recibo: {$numeroRecibo} - Socio: {$socio->nombre_completo} - Monto: {$montoFormateado} - Order: {$transaccion->flow_order}"
            );

            DB::commit();

            Log::info('Flow - Pago registrado exitosamente', [
                'flow_order' => $transaccion->flow_order,
                'pago_id' => $pago->id,
                'recibo' => $numeroRecibo,
            ]);

            return $pago;

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
