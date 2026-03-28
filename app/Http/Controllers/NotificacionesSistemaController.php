<?php

namespace App\Http\Controllers;

use App\Models\NotificacionSistema;
use Illuminate\Http\Request;

class NotificacionesSistemaController extends Controller
{
    /**
     * Mostrar todas las notificaciones
     */
    public function index(Request $request)
    {
        $organizacion = auth()->user()->organizacion;
        $usuario = auth()->user();

        $filtro = $request->get('filtro', 'todas'); // todas, no_leidas, leidas
        $tipo = $request->get('tipo');

        $query = NotificacionSistema::query()
            ->where(function ($q) use ($organizacion, $usuario) {
                $q->where('id_organizacion', $organizacion->id)
                  ->whereNull('id_usuario')
                  ->orWhere('id_usuario', $usuario->id);
            });

        // Aplicar filtros
        if ($filtro === 'no_leidas') {
            $query->noLeidas();
        } elseif ($filtro === 'leidas') {
            $query->where('leida', true);
        }

        if ($tipo) {
            $query->tipo($tipo);
        }

        $notificaciones = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        $contadorNoLeidas = NotificacionSistema::query()
            ->where(function ($q) use ($organizacion, $usuario) {
                $q->where('id_organizacion', $organizacion->id)
                  ->whereNull('id_usuario')
                  ->orWhere('id_usuario', $usuario->id);
            })
            ->noLeidas()
            ->count();

        return view('notificaciones-sistema.index', compact('notificaciones', 'contadorNoLeidas', 'filtro', 'tipo'));
    }

    /**
     * Marcar notificación como leída
     */
    public function marcarLeida($id)
    {
        $notificacion = NotificacionSistema::findOrFail($id);

        // Verificar que el usuario tenga permiso
        $usuario = auth()->user();
        if ($notificacion->id_organizacion !== $usuario->id_organizacion) {
            abort(403);
        }

        $notificacion->marcarComoLeida();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        // Si tiene URL, redirigir ahí
        if ($notificacion->url) {
            return redirect($notificacion->url);
        }

        return redirect()->back();
    }

    /**
     * Marcar todas como leídas
     */
    public function marcarTodasLeidas()
    {
        $organizacion = auth()->user()->organizacion;
        $usuario = auth()->user();

        NotificacionSistema::query()
            ->where(function ($q) use ($organizacion, $usuario) {
                $q->where('id_organizacion', $organizacion->id)
                  ->whereNull('id_usuario')
                  ->orWhere('id_usuario', $usuario->id);
            })
            ->noLeidas()
            ->update([
                'leida' => true,
                'fecha_leida' => now(),
            ]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Todas las notificaciones han sido marcadas como leídas');
    }

    /**
     * Obtener notificaciones no leídas (para AJAX)
     */
    public function noLeidas()
    {
        $organizacion = auth()->user()->organizacion;
        $usuario = auth()->user();

        $notificaciones = NotificacionSistema::query()
            ->where(function ($q) use ($organizacion, $usuario) {
                $q->where('id_organizacion', $organizacion->id)
                  ->whereNull('id_usuario')
                  ->orWhere('id_usuario', $usuario->id);
            })
            ->noLeidas()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'notificaciones' => $notificaciones,
            'contador' => $notificaciones->count(),
        ]);
    }

    /**
     * Eliminar notificación
     */
    public function eliminar($id)
    {
        $notificacion = NotificacionSistema::findOrFail($id);

        // Verificar que el usuario tenga permiso
        $usuario = auth()->user();
        if ($notificacion->id_organizacion !== $usuario->id_organizacion) {
            abort(403);
        }

        $notificacion->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notificación eliminada');
    }
}
