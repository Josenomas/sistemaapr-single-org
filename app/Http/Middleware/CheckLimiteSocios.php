<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLimiteSocios
{
    /**
     * Handle an incoming request.
     * Solo aplica en rutas de creación de socios (POST)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Solo verificar en peticiones POST (creación)
        if (!$request->isMethod('post')) {
            return $next($request);
        }

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $organizacion = auth()->user()->organizacion;

        if (!$organizacion) {
            return redirect()->back()
                           ->with('error', 'No tienes una organización asignada.');
        }

        // Verificar si puede agregar más socios
        if (!$organizacion->puedeAgregarSocio()) {
            $maxSocios = $organizacion->suscripcion->max_socios ?? 'ilimitados';
            $sociosActuales = $organizacion->socios()->count();

            // Si es AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => "Has alcanzado el límite de socios de tu plan ({$maxSocios}).",
                    'limite' => $maxSocios,
                    'actual' => $sociosActuales,
                    'upgrade_required' => true,
                    'plan_actual' => $organizacion->suscripcion->nombre_mostrar
                ], 403);
            }

            // Si es petición normal
            return redirect()->back()
                           ->with('error', "Has alcanzado el límite de {$maxSocios} socios de tu plan {$organizacion->suscripcion->nombre_mostrar}. Actualiza tu suscripción para agregar más socios.")
                           ->with('limite_alcanzado', 'socios')
                           ->withInput();
        }

        return $next($request);
    }
}
