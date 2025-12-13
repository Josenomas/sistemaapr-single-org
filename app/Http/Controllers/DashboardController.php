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
        $mesActual = date('Y-m');
        $mesPasado = date('Y-m', strtotime('-1 month'));
        $mesAnterior = date('Y-m', strtotime('-2 months'));

        // Total Clientes (socios activos)
        $totalClientes = Socio::where('estado', 'activo')
                              ->where('activo', 1)
                              ->count();
        $totalClientesMesPasado = Socio::where('estado', 'activo')
                                       ->where('activo', 1)
                                       ->where('created_at', '<', date('Y-m-01'))
                                       ->count();
        $evolucionClientes = $totalClientesMesPasado > 0
            ? round((($totalClientes - $totalClientesMesPasado) / $totalClientesMesPasado) * 100, 1)
            : 0;

        // Boletas Emitidas (mes actual vs mes pasado)
        $boletasEmitidas = Boleta::where('mes', $mesActual)->count();
        $boletasEmitidasMesPasado = Boleta::where('mes', $mesPasado)->count();
        $evolucionBoletas = $boletasEmitidasMesPasado > 0
            ? round((($boletasEmitidas - $boletasEmitidasMesPasado) / $boletasEmitidasMesPasado) * 100, 1)
            : 0;

        // Pagos del mes actual
        $boletasPagadas = Boleta::where('mes', $mesActual)
                                ->where('estado', 'pagada')
                                ->count();
        $totalBoletasMes = Boleta::where('mes', $mesActual)->count();
        $porcentajePagadas = $totalBoletasMes > 0
            ? round(($boletasPagadas / $totalBoletasMes) * 100, 1)
            : 0;

        $pagosPendientes = $totalBoletasMes - $boletasPagadas;

        // Evolución pagos pendientes
        $pagosPendientesMesPasado = Boleta::where('mes', $mesPasado)
                                          ->whereIn('estado', ['pendiente', 'vencida'])
                                          ->count();
        $evolucionPagos = $pagosPendientesMesPasado > 0
            ? round((($pagosPendientes - $pagosPendientesMesPasado) / $pagosPendientesMesPasado) * 100, 1)
            : 0;

        // Incidentes Abiertos
        $incidentesAbiertos = Incidente::whereIn('estado', ['reportado', 'en_atencion'])
                                       ->count();
        $incidentesAbiertosMesPasado = Incidente::whereIn('estado', ['reportado', 'en_atencion'])
                                                ->where('created_at', '<', date('Y-m-01'))
                                                ->count();
        $evolucionIncidentes = $incidentesAbiertosMesPasado > 0
            ? round((($incidentesAbiertos - $incidentesAbiertosMesPasado) / $incidentesAbiertosMesPasado) * 100, 1)
            : 0;

        // Datos para sparklines (últimos 7 días de actividad)
        $sparklineClientes = [];
        $sparklineBoletas = [];
        $sparklinePagos = [];
        $sparklineIncidentes = [];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));

            $sparklineClientes[] = Socio::where('estado', 'activo')
                                       ->where('activo', 1)
                                       ->where('created_at', '<=', $fecha)
                                       ->count();

            $sparklineBoletas[] = Boleta::whereDate('created_at', $fecha)->count();
            $sparklinePagos[] = Boleta::whereDate('updated_at', $fecha)
                                     ->where('estado', 'pagada')
                                     ->count();
            $sparklineIncidentes[] = Incidente::whereDate('created_at', $fecha)->count();
        }

        // Actividad Reciente con información del usuario
        $actividad = DB::table('actividad_reciente')
                       ->leftJoin('usuarios', 'actividad_reciente.id_usuario', '=', 'usuarios.id')
                       ->select('actividad_reciente.*', 'usuarios.nombre', 'usuarios.apellido', 'usuarios.nombre_usuario')
                       ->where('actividad_reciente.activo', 1)
                       ->orderBy('actividad_reciente.fecha_creacion', 'desc')
                       ->limit(5)
                       ->get();

        return view('dashboard.index', compact(
            'totalClientes',
            'boletasEmitidas',
            'pagosPendientes',
            'incidentesAbiertos',
            'actividad',
            'mesActual',
            'mesPasado',
            'evolucionClientes',
            'evolucionBoletas',
            'evolucionPagos',
            'evolucionIncidentes',
            'porcentajePagadas',
            'sparklineClientes',
            'sparklineBoletas',
            'sparklinePagos',
            'sparklineIncidentes'
        ));
    }
}
