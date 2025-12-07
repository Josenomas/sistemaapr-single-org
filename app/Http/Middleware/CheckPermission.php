<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        // Verificar que el usuario esté autenticado
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder.');
        }

        $user = auth()->user();

        // Los administradores tienen acceso total
        if ($user->rol === 'admin') {
            return $next($request);
        }

        // Verificar si el usuario tiene el permiso específico
        if (!$this->tienePermiso($user, $permission)) {
            return redirect()->route('dashboard')
                           ->with('error', 'No tienes permisos para acceder a este módulo.');
        }

        return $next($request);
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    private function tienePermiso($user, $permission)
    {
        // Decodificar permisos del usuario
        $permisos = $user->permisos ? json_decode($user->permisos, true) : [];

        // Si no es un array, convertirlo
        if (!is_array($permisos)) {
            $permisos = [];
        }

        // Verificar si tiene el permiso exacto
        if (in_array($permission, $permisos)) {
            return true;
        }

        return false;
    }
}
