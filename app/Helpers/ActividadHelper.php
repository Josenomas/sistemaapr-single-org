<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ActividadHelper
{
    /**
     * Registrar una actividad reciente
     *
     * @param string $modulo - Nombre del módulo (Socios, Lecturas, Boletas, etc.)
     * @param string $descripcion - Descripción de la acción realizada
     * @param int|null $id_usuario - ID del usuario (si es null, toma el usuario autenticado)
     * @return void
     */
    public static function registrar($modulo, $descripcion, $id_usuario = null)
    {
        try {
            // Si no se proporciona usuario, usar el autenticado
            if ($id_usuario === null && Auth::check()) {
                $id_usuario = Auth::id();
            }

            DB::table('actividad_reciente')->insert([
                'modulo' => $modulo,
                'descripcion' => $descripcion,
                'id_usuario' => $id_usuario,
                'fecha_creacion' => now(),
                'activo' => 1,
            ]);

            // Limpiar actividades antiguas (mantener solo las últimas 100)
            self::limpiarActividadesAntiguas();

        } catch (\Exception $e) {
            // Log error pero no interrumpir el flujo
            \Log::error('Error al registrar actividad: ' . $e->getMessage());
        }
    }

    /**
     * Limpiar actividades antiguas (mantener solo las últimas 100)
     *
     * @return void
     */
    private static function limpiarActividadesAntiguas()
    {
        try {
            $totalActividades = DB::table('actividad_reciente')->count();

            if ($totalActividades > 100) {
                // Obtener el ID de la actividad número 100 (desde la más reciente)
                $idLimite = DB::table('actividad_reciente')
                              ->orderBy('fecha_creacion', 'desc')
                              ->skip(99)
                              ->take(1)
                              ->value('id');

                // Eliminar actividades más antiguas
                if ($idLimite) {
                    DB::table('actividad_reciente')
                      ->where('id', '<', $idLimite)
                      ->delete();
                }
            }
        } catch (\Exception $e) {
            // Log error pero no interrumpir
            \Log::error('Error al limpiar actividades antiguas: ' . $e->getMessage());
        }
    }

    /**
     * Obtener las últimas N actividades
     *
     * @param int $limite
     * @return \Illuminate\Support\Collection
     */
    public static function obtenerUltimas($limite = 10)
    {
        return DB::table('actividad_reciente')
                 ->where('activo', 1)
                 ->orderBy('fecha_creacion', 'desc')
                 ->limit($limite)
                 ->get();
    }

    /**
     * Obtener actividades por módulo
     *
     * @param string $modulo
     * @param int $limite
     * @return \Illuminate\Support\Collection
     */
    public static function obtenerPorModulo($modulo, $limite = 10)
    {
        return DB::table('actividad_reciente')
                 ->where('modulo', $modulo)
                 ->where('activo', 1)
                 ->orderBy('fecha_creacion', 'desc')
                 ->limit($limite)
                 ->get();
    }

    /**
     * Obtener actividades por usuario
     *
     * @param int $id_usuario
     * @param int $limite
     * @return \Illuminate\Support\Collection
     */
    public static function obtenerPorUsuario($id_usuario, $limite = 10)
    {
        return DB::table('actividad_reciente')
                 ->where('id_usuario', $id_usuario)
                 ->where('activo', 1)
                 ->orderBy('fecha_creacion', 'desc')
                 ->limit($limite)
                 ->get();
    }
}
