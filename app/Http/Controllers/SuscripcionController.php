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

        return view('suscripcion.renovar', compact('organizacion', 'planes'));
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
