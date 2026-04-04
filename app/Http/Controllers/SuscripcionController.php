<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suscripcion;

class SuscripcionController extends Controller
{
    /**
     * Página de renovación cuando la suscripción ha vencido
     */
    public function renovar()
    {
        $organizacion = auth()->user()->organizacion;

        // Obtener planes disponibles
        $planes = Suscripcion::orderBy('precio_mensual')->get();

        // Buscar o crear pago pendiente
        $pagoPendiente = \App\Models\PagoSuscripcion::where('id_organizacion', $organizacion->id)
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$pagoPendiente && $organizacion->suscripcion) {
            // Crear pago pendiente si no existe
            $pagoPendiente = \App\Models\PagoSuscripcion::create([
                'id_organizacion' => $organizacion->id,
                'id_suscripcion' => $organizacion->id_suscripcion,
                'monto' => $organizacion->suscripcion->precio_mensual,
                'estado' => 'pendiente',
                'periodo_inicio' => now(),
                'periodo_fin' => now()->addMonth(),
                'fecha_vencimiento' => now()->addDays(7),
            ]);
        }

        return view('suscripcion.renovar', compact('organizacion', 'planes', 'pagoPendiente'));
    }

    /**
     * Página de estado cuando la suscripción está suspendida/cancelada
     */
    public function estado()
    {
        $organizacion = auth()->user()->organizacion;

        return view('suscripcion.estado', compact('organizacion'));
    }
}
