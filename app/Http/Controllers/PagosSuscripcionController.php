<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PagoSuscripcion;

class PagosSuscripcionController extends Controller
{
    /**
     * Mostrar historial de pagos de la organización
     */
    public function index()
    {
        $organizacion = auth()->user()->organizacion;

        $pagos = PagoSuscripcion::where('id_organizacion', $organizacion->id)
            ->with('suscripcion')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Pago pendiente actual
        $pagoPendiente = PagoSuscripcion::where('id_organizacion', $organizacion->id)
            ->where('estado', 'pendiente')
            ->orderBy('fecha_vencimiento', 'asc')
            ->first();

        // Estadísticas
        $totalPagado = PagoSuscripcion::where('id_organizacion', $organizacion->id)
            ->where('estado', 'pagado')
            ->sum('monto');

        $pagosPendientes = PagoSuscripcion::where('id_organizacion', $organizacion->id)
            ->where('estado', 'pendiente')
            ->count();

        return view('organizacion.pagos-suscripcion', compact(
            'pagos',
            'pagoPendiente',
            'totalPagado',
            'pagosPendientes',
            'organizacion'
        ));
    }

    /**
     * Iniciar pago de una suscripción pendiente
     */
    public function pagar($id)
    {
        $pago = PagoSuscripcion::findOrFail($id);

        // Verificar que pertenece a la organización del usuario
        if ($pago->id_organizacion !== auth()->user()->id_organizacion) {
            abort(403, 'No autorizado');
        }

        // Verificar que esté pendiente
        if ($pago->estado !== 'pendiente') {
            return redirect()->route('organizacion.pagos-suscripcion')
                ->with('error', 'Este pago ya fue procesado');
        }

        try {
            // Crear pago con Flow
            $flowService = app(\App\Services\FlowPaymentService::class);

            $resultado = $flowService->crearPago(
                null, // No es pago de socio
                null, // No es pago de boleta
                $pago->monto,
                auth()->user()->email,
                "Renovación suscripción - {$pago->organizacion->nombre_apr}"
            );

            if ($resultado['success']) {
                // Guardar token de Flow en el pago
                $pago->update([
                    'token_flow' => $resultado['token'],
                    'orden_compra' => $resultado['transaccion']->flow_order,
                ]);

                // Marcar transacción como pago de suscripción
                $resultado['transaccion']->update([
                    'id_organizacion' => $pago->id_organizacion,
                    'tipo_pago' => 'suscripcion',
                    'referencia_id' => $pago->id,
                ]);

                // Redirigir a Flow
                return redirect($resultado['url']);
            }

            return redirect()->route('organizacion.pagos-suscripcion')
                ->with('error', $resultado['message'] ?? 'Error al iniciar el pago');

        } catch (\Exception $e) {
            \Log::error('Error al crear pago de suscripción', [
                'pago_id' => $pago->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('organizacion.pagos-suscripcion')
                ->with('error', 'Error al procesar el pago. Por favor, intenta nuevamente.');
        }
    }
}
