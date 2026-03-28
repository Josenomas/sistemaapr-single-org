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

        // Aquí integraríamos con Flow para procesar el pago
        // Por ahora redirigimos al upgrade que ya tiene Flow integrado
        return redirect()->route('organizacion.upgrade')
            ->with('info', 'Contacta al administrador para procesar el pago de suscripción');
    }
}
