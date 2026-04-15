<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Socio;
use App\Models\Boleta;
use App\Models\Incidente;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Mostrar dashboard principal
     */
    public function index()
    {
        $mesPasado = date('Y-m', strtotime('-1 month'));

        // Total Clientes (socios activos)
        $totalClientes = Socio::where('estado', 'activo')
                              ->where('activo', 1)
                              ->count();

        // Boletas Emitidas (mes pasado)
        $boletasEmitidas = Boleta::where('mes', $mesPasado)->count();

        // Pagos Pendientes (boletas impagas del mes pasado)
        $pagosPendientes = Boleta::where('mes', $mesPasado)
                                 ->whereIn('estado', ['pendiente', 'vencida'])
                                 ->count();

        // Incidentes Abiertos
        $incidentesAbiertos = Incidente::whereIn('estado', ['reportado', 'en_atencion'])
                                       ->count();

        // Actividad Reciente con información del usuario (filtrada por organización)
        $idOrganizacion = auth()->user()->id_organizacion ?? null;

        $actividad = collect(); // Colección vacía por defecto

        if ($idOrganizacion) {
            $actividad = DB::table('actividad_reciente')
                           ->leftJoin('usuarios', 'actividad_reciente.id_usuario', '=', 'usuarios.id')
                           ->select('actividad_reciente.*', 'usuarios.nombre', 'usuarios.apellido', 'usuarios.nombre_usuario')
                           ->where('actividad_reciente.id_organizacion', $idOrganizacion)
                           ->where('actividad_reciente.activo', 1)
                           ->orderBy('actividad_reciente.fecha_creacion', 'desc')
                           ->limit(5)
                           ->get();
        }

        return view('dashboard.index', compact(
            'totalClientes',
            'boletasEmitidas',
            'pagosPendientes',
            'incidentesAbiertos',
            'actividad',
            'mesPasado'
        ));
    }
}
