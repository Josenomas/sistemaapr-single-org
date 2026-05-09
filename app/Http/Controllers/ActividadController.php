<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActividadController extends Controller
{
    /**
     * Mostrar listado completo de actividades recientes
     */
    public function index()
    {
        // Obtener ID de organización del usuario autenticado (multi-tenancy)
        $idOrganizacion = auth()->user()->id_organizacion;

        // Consultar actividades filtradas por organización
        $actividades = DB::table('actividad_reciente')
            ->leftJoin('usuarios', 'actividad_reciente.id_usuario', '=', 'usuarios.id')
            ->select(
                'actividad_reciente.*',
                'usuarios.nombre',
                'usuarios.apellido',
                'usuarios.nombre_usuario'
            )
            ->where('usuarios.id_organizacion', $idOrganizacion)
            ->where('actividad_reciente.activo', 1)
            ->orderBy('actividad_reciente.fecha_creacion', 'desc')
            ->paginate(20);

        return view('actividades.index', compact('actividades'));
    }
}
