<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Organizacion;
use Illuminate\Support\Facades\Auth;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     * Identifica la organización basándose en:
     * 1. Dominio personalizado (www.aprnombre.cl)
     * 2. Subdominio (slug.sistemaapr.cl)
     * 3. Usuario autenticado (fallback)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $organizacion = null;

        // 1. Intentar identificar por dominio personalizado (solo si está verificado o aprobado)
        $organizacion = Organizacion::where('dominio_personalizado', $host)
                                    ->where('activo', true)
                                    ->whereIn('estado_dominio_personalizado', ['verificado_dns', 'activo_aprobado'])
                                    ->first();

        // 2. Si no se encontró, intentar por subdominio
        if (!$organizacion && $this->esSubdominio($host)) {
            $slug = $this->extraerSlug($host);

            if ($slug) {
                $organizacion = Organizacion::where('slug', $slug)
                                            ->where('activo', true)
                                            ->first();
            }
        }

        // 3. Si se identificó la organización por dominio, guardarla en la sesión
        if ($organizacion) {
            session(['tenant_id' => $organizacion->id]);

            // Si el usuario está autenticado, verificar que pertenezca a esta organización
            if (Auth::check()) {
                $user = Auth::user();

                // Super-admins pueden acceder a cualquier organización
                if (!$user->esSuperAdmin() && $user->id_organizacion !== $organizacion->id) {
                    Auth::logout();
                    return redirect()->route('login')
                                   ->with('error', 'No tienes acceso a esta organización.');
                }
            }
        }

        // 4. Si hay usuario autenticado pero no se identificó organización por dominio
        // usar la organización del usuario (modo desarrollo/localhost)
        if (!$organizacion && Auth::check()) {
            $user = Auth::user();
            if (!$user->esSuperAdmin()) {
                session(['tenant_id' => $user->id_organizacion]);
            }
        }

        return $next($request);
    }

    /**
     * Verifica si el host es un subdominio del sistema
     *
     * @param string $host
     * @return bool
     */
    private function esSubdominio($host)
    {
        // Verifica si termina en .sistemaapr.cl
        return str_ends_with($host, '.sistemaapr.cl') && $host !== 'sistemaapr.cl';
    }

    /**
     * Extrae el slug del subdominio
     *
     * @param string $host
     * @return string|null
     */
    private function extraerSlug($host)
    {
        if (str_ends_with($host, '.sistemaapr.cl')) {
            // Extraer la parte antes de .sistemaapr.cl
            return str_replace('.sistemaapr.cl', '', $host);
        }

        return null;
    }

    /**
     * Obtiene la organización identificada en la sesión
     *
     * @return \App\Models\Organizacion|null
     */
    public static function getOrganizacion()
    {
        $tenantId = session('tenant_id');

        if ($tenantId) {
            return Organizacion::find($tenantId);
        }

        // Fallback: si hay usuario autenticado, usar su organización
        if (Auth::check() && !Auth::user()->esSuperAdmin()) {
            return Auth::user()->organizacion;
        }

        return null;
    }
}
