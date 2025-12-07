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

            // Confirmar pago con Flow
            $resultado = $this->flowService->confirmarPago($token);

            if ($resultado['success']) {
                $transaccion = $resultado['transaccion'];
                $responseData = $resultado['response'];

                Log::info('Flow - Confirmación exitosa', [
                    'token' => $token,
                    'status' => $responseData['status'] ?? null,
                ]);

                // Si el pago fue exitoso, crear registro de pago
                if ($transaccion->estado === 'pagado') {
                    $this->crearRegistroPago($transaccion, $responseData);
                }

                return response('CONFIRMADO', 200);
            }

            Log::error('Flow - Error en confirmación', [
                'token' => $token,
                'message' => $resultado['message'] ?? 'Error desconocido',
            ]);

            return response('ERROR', 500);

        } catch (\Exception $e) {
            Log::error('Flow - Excepción en confirmación', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response('ERROR: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Página de retorno después del pago
     */
    public function retorno(Request $request)
    {
        try {
            $token = $request->input('token');

            if (!$token) {
                return redirect()->route('pagos.index')
                               ->with('error', 'Token de pago no proporcionado');
            }

            // Buscar transacción
            $transaccion = TransaccionFlow::where('token', $token)
                                        ->with(['socio', 'boleta'])
                                        ->first();

            if (!$transaccion) {
                return redirect()->route('pagos.index')
                               ->with('error', 'Transacción no encontrada');
            }

            // Confirmar estado actualizado con Flow
            $resultado = $this->flowService->confirmarPago($token);

            if ($resultado['success']) {
                $transaccion->refresh();

                // Redirigir según estado
                if ($transaccion->estado === 'pagado') {
                    // Buscar el pago registrado
                    $pago = Pago::where('numero_comprobante', 'LIKE', '%' . $transaccion->flow_order . '%')
                               ->orderBy('id', 'desc')
                               ->first();

                    if ($pago) {
                        return redirect()->route('pagos.imprimir', $pago->id)
                                       ->with('success', '¡Pago realizado exitosamente!');
                    }

                    return redirect()->route('pagos.index')
                                   ->with('success', '¡Pago realizado exitosamente!');
                } elseif ($transaccion->estado === 'rechazado') {
                    return redirect()->route('pagos.create', ['boleta_id' => $transaccion->id_boleta])
                                   ->with('error', 'El pago fue rechazado. Por favor, intente nuevamente.');
                } else {
                    return redirect()->route('pagos.create', ['boleta_id' => $transaccion->id_boleta])
                                   ->with('warning', 'El pago está pendiente de confirmación.');
                }
            }

            return redirect()->route('pagos.index')
                           ->with('error', 'Error al verificar el estado del pago');

        } catch (\Exception $e) {
            Log::error('Flow - Error en retorno', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('pagos.index')
                           ->with('error', 'Error al procesar el retorno del pago: ' . $e->getMessage());
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

            $boleta = $transaccion->boleta;

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
            ActividadHelper::registrar(
                'Pagos',
                "Pago automático Flow - Recibo: {$numeroRecibo} - Socio: {$transaccion->socio->nombre_completo} - Monto: " . $transaccion->monto_formateado . " - Order: {$transaccion->flow_order}"
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
                'flow_order' => $transaccion->flow_order,
                'message' => $e->getMessage(),
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
