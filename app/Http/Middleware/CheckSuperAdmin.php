<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSuperAdmin
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
        // Verificar que el usuario esté autenticado
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Debe iniciar sesión para acceder a esta sección.');
        }

        // Verificar que sea super-admin
        if (!auth()->user()->esSuperAdmin()) {
            abort(403, 'Acceso no autorizado. Solo super-administradores pueden acceder a esta sección.');
        }

        return $next($request);
    }
}
