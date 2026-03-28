<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSuscripcionActiva
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Si no está autenticado, dejar que el middleware de autenticación lo maneje
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Si es super-admin, permitir acceso sin restricciones
        if ($user->esSuperAdmin()) {
            return $next($request);
        }

        // Si el usuario no tiene organización, redirigir a una página de configuración
        if (!$user->id_organizacion) {
            return redirect()->route('organizacion.setup')
                           ->with('warning', 'Debes configurar tu organización primero.');
        }

        $organizacion = $user->organizacion;

        // Verificar si la organización existe
        if (!$organizacion) {
            auth()->logout();
            return redirect()->route('login')
                           ->with('error', 'Tu organización no existe. Contacta al administrador.');
        }

        // Verificar si la suscripción está vencida
        if ($organizacion->suscripcionVencida()) {
            return redirect()->route('suscripcion.renovar')
                           ->with('error', 'Tu suscripción ha vencido. Por favor renueva para continuar.');
        }

        // Verificar si la suscripción está suspendida o cancelada
        if (in_array($organizacion->estado_suscripcion, ['suspendida', 'cancelada'])) {
            return redirect()->route('suscripcion.estado')
                           ->with('error', 'Tu suscripción está ' . $organizacion->estado_suscripcion . '. Contacta al administrador.');
        }

        // Si está en prueba, mostrar alerta pero dejar pasar
        if ($organizacion->enPrueba() && $organizacion->dias_prueba_restantes <= 3) {
            session()->flash('warning', "Tu período de prueba termina en {$organizacion->dias_prueba_restantes} días. Contrata un plan para continuar.");
        }

        return $next($request);
    }
}
