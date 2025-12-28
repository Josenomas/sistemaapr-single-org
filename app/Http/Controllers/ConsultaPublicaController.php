<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Socio;
use App\Models\Boleta;
use App\Services\FlowPaymentService;
use Illuminate\Support\Facades\Log;

class ConsultaPublicaController extends Controller
{
    protected $flowService;

    public function __construct(FlowPaymentService $flowService)
    {
        $this->flowService = $flowService;
    }

    /**
     * Mostrar formulario de consulta
     */
    public function mostrarFormulario()
    {
        return view('consulta-pago');
    }

    /**
     * Buscar socio por RUT y mostrar sus boletas pendientes
     */
    public function buscarPorRut(Request $request)
    {
        $validated = $request->validate([
            'rut' => 'required|string|max:12',
        ]);

        try {
            // Limpiar RUT (quitar puntos y guiones para buscar)
            $rutLimpio = $this->limpiarRut($validated['rut']);

            // Buscar socio por RUT (buscamos tanto con formato como sin formato)
            $socio = Socio::where('activo', 1)
                ->where(function($query) use ($validated, $rutLimpio) {
                    $query->where('rut', $validated['rut'])
                          ->orWhere('rut', $rutLimpio)
                          ->orWhere('rut', 'LIKE', '%' . $rutLimpio . '%');
                })
                ->first();

            if (!$socio) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'No se encontró ningún socio con el RUT ingresado.');
            }

            // Obtener boletas pendientes o vencidas del socio
            $boletas = Boleta::where('id_socio', $socio->id)
                ->where('activo', 1)
                ->whereIn('estado', ['pendiente', 'vencida'])
                ->with('pagos')
                ->orderBy('mes', 'asc')
                ->get();

            // Calcular total de deuda considerando pagos parciales
            $totalDeuda = 0;
            foreach ($boletas as $boleta) {
                $totalPagado = $boleta->pagos->sum('monto_pagado');
                $saldoPendiente = $boleta->total - $totalPagado;
                $totalDeuda += $saldoPendiente;
            }

            return view('resultado-consulta', compact('socio', 'boletas', 'totalDeuda'));

        } catch (\Exception $e) {
            Log::error('Error en consulta pública', [
                'rut' => $validated['rut'],
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al procesar la consulta. Por favor, intente nuevamente.');
        }
    }

    /**
     * Generar pago en Flow para las boletas seleccionadas
     */
    public function generarPago(Request $request)
    {
        $validated = $request->validate([
            'id_socio' => 'required|exists:socios,id',
            'boletas' => 'required|string',
            'monto_total' => 'required|numeric|min:1',
        ]);

        try {
            $socio = Socio::findOrFail($validated['id_socio']);

            // Obtener IDs de boletas
            $boletasIds = explode(',', $validated['boletas']);
            $boletas = Boleta::whereIn('id', $boletasIds)
                ->where('id_socio', $socio->id)
                ->where('activo', 1)
                ->whereIn('estado', ['pendiente', 'vencida'])
                ->get();

            if ($boletas->isEmpty()) {
                return redirect()->route('consulta.pago')
                    ->with('error', 'No se encontraron boletas válidas para pagar.');
            }

            // Calcular monto total
            $montoTotal = $boletas->sum('total');

            // Validar que el monto coincida
            if (abs($montoTotal - $validated['monto_total']) > 0.01) {
                return redirect()->route('consulta.pago')
                    ->with('error', 'El monto a pagar no coincide con las boletas seleccionadas.');
            }

            // Email del socio (o usar uno genérico si no tiene)
            $email = $socio->email ?: env('MAIL_FROM_ADDRESS', 'sistemaapr@gmail.com');

            // Subject del pago
            $cantidadBoletas = $boletas->count();
            $subject = "Pago APR - {$cantidadBoletas} " . ($cantidadBoletas == 1 ? 'boleta' : 'boletas');

            // Crear pago en Flow (usamos la primera boleta como referencia)
            $primeraBoleta = $boletas->first();
            $resultado = $this->flowService->crearPago(
                $socio->id,
                $primeraBoleta->id,
                $montoTotal,
                $email,
                $subject
            );

            if ($resultado['success']) {
                // Guardar en la transacción las boletas adicionales en observaciones
                if ($cantidadBoletas > 1) {
                    $transaccion = $resultado['transaccion'];
                    $boletasTexto = $boletas->pluck('numero_boleta')->implode(', ');
                    $transaccion->update([
                        'observaciones' => "Pago múltiple - Boletas: {$boletasTexto}"
                    ]);
                }

                // Redirigir a Flow
                return redirect($resultado['url']);
            }

            return redirect()->route('consulta.pago')
                ->with('error', 'Error al generar el pago: ' . ($resultado['message'] ?? 'Error desconocido'));

        } catch (\Exception $e) {
            Log::error('Error al generar pago público', [
                'id_socio' => $validated['id_socio'],
                'error' => $e->getMessage()
            ]);

            return redirect()->route('consulta.pago')
                ->with('error', 'Ocurrió un error al generar el pago. Por favor, intente nuevamente.');
        }
    }

    /**
     * Limpiar RUT (quitar puntos y guiones)
     */
    private function limpiarRut($rut)
    {
        return preg_replace('/[^0-9kK]/', '', $rut);
    }
}
