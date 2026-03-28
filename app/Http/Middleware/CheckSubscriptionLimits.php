<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Organizacion;
use App\Models\Socio;
use App\Models\Usuario;

class CheckSubscriptionLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $limitType - Tipo de límite a verificar (socios o usuarios)
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $limitType = null)
    {
        $user = auth()->user();

        if (!$user || !$user->id_organizacion) {
            return $next($request);
        }

        $organizacion = Organizacion::with('suscripcion')->find($user->id_organizacion);

        if (!$organizacion || !$organizacion->suscripcion) {
            return $next($request);
        }

        // Verificar límite de socios
        if ($limitType === 'socios') {
            if (!$organizacion->suscripcion->tieneSociosIlimitados()) {
                $sociosTotales = Socio::count();
                $maxSocios = $organizacion->suscripcion->max_socios;

                if ($sociosTotales >= $maxSocios) {
                    return redirect()->back()->with('error',
                        "Has alcanzado el límite de socios de tu plan ({$maxSocios} socios). " .
                        "Por favor, actualiza tu plan para agregar más socios."
                    )->with('upgrade_required', true);
                }
            }
        }

        // Verificar límite de usuarios
        if ($limitType === 'usuarios') {
            if (!$organizacion->suscripcion->tieneUsuariosIlimitados()) {
                $usuariosTotales = Usuario::where('id_organizacion', $organizacion->id)->count();
                $maxUsuarios = $organizacion->suscripcion->max_usuarios;

                if ($usuariosTotales >= $maxUsuarios) {
                    return redirect()->back()->with('error',
                        "Has alcanzado el límite de usuarios de tu plan ({$maxUsuarios} usuarios). " .
                        "Por favor, actualiza tu plan para agregar más usuarios."
                    )->with('upgrade_required', true);
                }
            }
        }

        return $next($request);
    }
}
