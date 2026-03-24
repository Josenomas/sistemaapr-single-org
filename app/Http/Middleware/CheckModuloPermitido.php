<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckModuloPermitido
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $modulo - Nombre del módulo a verificar
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $modulo)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $organizacion = $user->organizacion;

        // Verificar si el usuario tiene organización
        if (!$organizacion) {
            return redirect()->route('dashboard')
                           ->with('error', 'No tienes una organización asignada.');
        }

        // Verificar si la organización puede acceder al módulo
        if (!$organizacion->puedeAccederModulo($modulo)) {
            // Si es una petición AJAX, retornar JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Este módulo no está disponible en tu plan actual.',
                    'modulo' => $modulo,
                    'plan_actual' => $organizacion->suscripcion->nombre_mostrar,
                    'upgrade_required' => true
                ], 403);
            }

            // Si es petición normal, redirigir con mensaje
            return redirect()->route('dashboard')
                           ->with('error', 'Este módulo no está disponible en tu plan actual. Actualiza tu suscripción para acceder.')
                           ->with('modulo_bloqueado', $modulo)
                           ->with('plan_requerido', $this->getPlanRequerido($modulo));
        }

        return $next($request);
    }

    /**
     * Obtiene el plan mínimo requerido para un módulo
     */
    private function getPlanRequerido($modulo)
    {
        $modulosProfesional = ['trabajos', 'inventario', 'notificaciones', 'reportes', 'activos-fijos', 'compras', 'sueldos'];
        $modulosEnterprise = ['noticias'];

        if (in_array($modulo, $modulosEnterprise)) {
            return 'Enterprise';
        }

        if (in_array($modulo, $modulosProfesional)) {
            return 'Profesional';
        }

        return 'Básico';
    }
}
