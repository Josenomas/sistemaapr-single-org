<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organizacion;
use App\Models\Usuario;
use App\Models\Socio;
use App\Models\Boleta;
use App\Models\Pago;
use App\Models\Suscripcion;
use App\Models\TransaccionFlow;
use App\Models\RegistroOrganizacion;
use App\Models\RenovacionSuscripcion;
use App\Models\Auditoria;
use App\Models\ConfiguracionDTE;
use App\Services\ReporteDTEService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    /**
     * Dashboard del super-admin con métricas globales
     */
    public function dashboard()
    {
        // Métricas generales
        $totalOrganizaciones = Organizacion::count();
        $organizacionesActivas = Organizacion::where('activo', true)->count();
        $organizacionesSuspendidas = Organizacion::where('activo', false)->count();
        $totalUsuarios = Usuario::whereNotNull('id_organizacion')->count();
        $totalSocios = Socio::count();

        // Métricas de suscripciones
        $orgPorSuscripcion = Organizacion::select('suscripciones.nombre', DB::raw('count(*) as total'))
            ->join('suscripciones', 'organizaciones.id_suscripcion', '=', 'suscripciones.id')
            ->groupBy('suscripciones.nombre')
            ->pluck('total', 'nombre');

        // Organizaciones en período de prueba
        $enPrueba = Organizacion::where('estado_suscripcion', 'prueba')->count();

        // Registros pendientes de verificación
        $registrosPendientes = RegistroOrganizacion::where('estado', 'pendiente')->count();

        // Ingresos del mes (estimados según suscripciones activas)
        $ingresosMesActual = Organizacion::join('suscripciones', 'organizaciones.id_suscripcion', '=', 'suscripciones.id')
            ->where('organizaciones.estado_suscripcion', 'activa')
            ->sum('suscripciones.precio_mensual');

        // Organizaciones creadas en los últimos 30 días
        $nuevasOrganizaciones = Organizacion::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // Métricas de pagos Flow (últimos 30 días)
        $pagosFlow30Dias = TransaccionFlow::where('fecha_creacion', '>=', Carbon::now()->subDays(30))
            ->where('estado', 'pagado')
            ->sum('monto');

        // Últimas organizaciones registradas
        $ultimasOrganizaciones = Organizacion::with('suscripcion')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('superadmin.dashboard', compact(
            'totalOrganizaciones',
            'organizacionesActivas',
            'organizacionesSuspendidas',
            'totalUsuarios',
            'totalSocios',
            'orgPorSuscripcion',
            'enPrueba',
            'registrosPendientes',
            'ingresosMesActual',
            'nuevasOrganizaciones',
            'pagosFlow30Dias',
            'ultimasOrganizaciones'
        ));
    }

    /**
     * Listado de todas las organizaciones
     */
    public function organizaciones(Request $request)
    {
        $query = Organizacion::with('suscripcion');

        // Filtros
        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre_apr', 'LIKE', "%{$busqueda}%")
                  ->orWhere('rut', 'LIKE', "%{$busqueda}%")
                  ->orWhere('email_contacto', 'LIKE', "%{$busqueda}%");
            });
        }

        if ($request->filled('estado_suscripcion')) {
            $query->where('estado_suscripcion', $request->estado_suscripcion);
        }

        if ($request->filled('id_suscripcion')) {
            $query->where('id_suscripcion', $request->id_suscripcion);
        }

        if ($request->filled('activo')) {
            $query->where('activo', $request->activo === '1');
        }

        $organizaciones = $query->orderBy('created_at', 'desc')->paginate(20);

        // Para los filtros
        $suscripciones = \App\Models\Suscripcion::all();

        return view('superadmin.organizaciones.index', compact('organizaciones', 'suscripciones'));
    }

    /**
     * Ver detalles de una organización
     */
    public function verOrganizacion($id)
    {
        $organizacion = Organizacion::with('suscripcion')
            ->findOrFail($id);

        // Estadísticas de la organización (sin global scopes para super admin)
        $totalSocios = Socio::withoutGlobalScopes()->where('id_organizacion', $id)->count();
        $totalUsuarios = Usuario::withoutGlobalScopes()->where('id_organizacion', $id)->count();
        $totalBoletas = Boleta::withoutGlobalScopes()->where('id_organizacion', $id)->count();
        $totalPagos = Pago::withoutGlobalScopes()->whereHas('boleta', function($q) use ($id) {
            $q->withoutGlobalScopes()->where('id_organizacion', $id);
        })->sum('monto_pagado');

        // Actividad reciente (sin global scopes para super admin)
        $usuariosRecientes = Usuario::withoutGlobalScopes()
            ->where('id_organizacion', $id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $sociosRecientes = Socio::withoutGlobalScopes()
            ->where('id_organizacion', $id)
            ->orderBy('fecha_creacion', 'desc')
            ->limit(5)
            ->get();

        return view('superadmin.organizaciones.detalle', compact(
            'organizacion',
            'totalSocios',
            'totalUsuarios',
            'totalBoletas',
            'totalPagos',
            'usuariosRecientes',
            'sociosRecientes'
        ));
    }

    /**
     * Suspender una organización
     */
    public function suspenderOrganizacion($id)
    {
        $organizacion = Organizacion::findOrFail($id);

        $organizacion->update([
            'activo' => false,
            'estado_suscripcion' => 'suspendida',
        ]);

        return redirect()->back()
            ->with('success', "Organización '{$organizacion->nombre_apr}' suspendida exitosamente.");
    }

    /**
     * Activar una organización
     */
    public function activarOrganizacion($id)
    {
        $organizacion = Organizacion::findOrFail($id);

        $organizacion->update([
            'activo' => true,
            'estado_suscripcion' => 'activa',
        ]);

        return redirect()->back()
            ->with('success', "Organización '{$organizacion->nombre_apr}' activada exitosamente.");
    }

    /**
     * Cambiar plan de una organización (forzado por super-admin)
     */
    public function cambiarPlan(Request $request, $id)
    {
        $request->validate([
            'id_suscripcion' => 'required|exists:suscripciones,id',
        ]);

        $organizacion = Organizacion::findOrFail($id);
        $suscripcionAnterior = $organizacion->suscripcion;
        $suscripcionNueva = \App\Models\Suscripcion::findOrFail($request->id_suscripcion);

        $organizacion->update([
            'id_suscripcion' => $request->id_suscripcion,
        ]);

        return redirect()->back()
            ->with('success', "Plan cambiado de '{$suscripcionAnterior->nombre}' a '{$suscripcionNueva->nombre}' exitosamente.");
    }

    /**
     * Eliminar una organización (soft delete o físico según preferencia)
     */
    public function eliminarOrganizacion($id)
    {
        $organizacion = Organizacion::findOrFail($id);
        $nombre = $organizacion->nombre_apr;

        // Aquí podrías implementar soft delete o eliminación física
        // Por seguridad, mejor solo desactivar
        $organizacion->update([
            'activo' => false,
            'estado_suscripcion' => 'cancelada',
        ]);

        return redirect()->route('superadmin.organizaciones')
            ->with('success', "Organización '{$nombre}' eliminada/cancelada exitosamente.");
    }

    /**
     * Listado de registros pendientes de verificación
     */
    public function registrosPendientes()
    {
        $registros = RegistroOrganizacion::where('estado', 'pendiente')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('superadmin.registros.pendientes', compact('registros'));
    }

    /**
     * Ver detalle de un registro pendiente
     */
    public function verRegistro($id)
    {
        $registro = RegistroOrganizacion::with('suscripcionDeseada')->findOrFail($id);

        return view('superadmin.registros.detalle', compact('registro'));
    }

    /**
     * Aprobar manualmente un registro (sin verificación de email)
     */
    public function aprobarRegistro($id)
    {
        $registro = RegistroOrganizacion::findOrFail($id);

        DB::beginTransaction();
        try {
            $registro->marcarComoVerificado();

            // Crear organización
            $organizacion = Organizacion::create([
                'nombre_apr' => $registro->nombre_apr,
                'slug' => $registro->slug,
                'rut' => $registro->rut,
                'direccion' => $registro->direccion,
                'comuna' => $registro->comuna,
                'region' => $registro->region,
                'telefono' => $registro->telefono,
                'email_contacto' => $registro->email_contacto,
                'id_suscripcion' => $registro->id_suscripcion_deseada ?? 1,
                'estado_suscripcion' => 'prueba',
                'dias_prueba_restantes' => 30,
                'fecha_inicio_suscripcion' => now(),
                'activo' => true,
            ]);

            // Crear usuario admin
            Usuario::create([
                'id_organizacion' => $organizacion->id,
                'nombre_usuario' => strtolower($registro->admin_nombre . $registro->admin_apellido),
                'password' => $registro->password,
                'nombre' => $registro->admin_nombre,
                'apellido' => $registro->admin_apellido,
                'email' => $registro->admin_email,
                'telefono' => $registro->admin_telefono,
                'rol' => 'admin',
                'activo' => true,
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', "Registro aprobado y organización '{$organizacion->nombre_apr}' creada exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al aprobar el registro: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar un registro
     */
    public function rechazarRegistro($id)
    {
        $registro = RegistroOrganizacion::findOrFail($id);

        $registro->update(['estado' => 'rechazado']);

        // Enviar email de notificación al solicitante
        try {
            \Mail::send('emails.registro-rechazado', ['registro' => $registro], function($message) use ($registro) {
                $message->to($registro->email_contacto)
                        ->subject('Actualización sobre tu Solicitud de Registro - Sistema APR');
            });
        } catch (\Exception $e) {
            \Log::error('Error al enviar email de rechazo de registro: ' . $e->getMessage());
        }

        return redirect()->back()
            ->with('success', 'Registro rechazado exitosamente. Se ha enviado notificación por email.');
    }

    /**
     * Reporte financiero global
     */
    public function reporteFinanciero(Request $request)
    {
        $mesActual = $request->get('mes', now()->format('Y-m'));
        $fecha = Carbon::parse($mesActual . '-01');

        // Ingresos por suscripciones (estimados)
        $ingresosSuscripciones = Organizacion::join('suscripciones', 'organizaciones.id_suscripcion', '=', 'suscripciones.id')
            ->where('organizaciones.estado_suscripcion', 'activa')
            ->sum('suscripciones.precio_mensual');

        // Pagos reales recibidos en el mes (Flow)
        $pagosRecibidos = \App\Models\PagoSuscripcion::where('estado', 'pagado')
            ->whereYear('fecha_pago', $fecha->year)
            ->whereMonth('fecha_pago', $fecha->month)
            ->sum('monto');

        // Pagos pendientes
        $pagosPendientes = \App\Models\PagoSuscripcion::where('estado', 'pendiente')
            ->sum('monto');

        // Ingresos por plan
        $ingresosPorPlan = Organizacion::select('suscripciones.nombre', DB::raw('count(*) as organizaciones'), DB::raw('sum(suscripciones.precio_mensual) as ingresos'))
            ->join('suscripciones', 'organizaciones.id_suscripcion', '=', 'suscripciones.id')
            ->where('organizaciones.estado_suscripcion', 'activa')
            ->groupBy('suscripciones.id', 'suscripciones.nombre')
            ->get();

        // Evolución de ingresos (últimos 6 meses)
        $evolucionIngresos = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = now()->subMonths($i);
            $ingresos = \App\Models\PagoSuscripcion::where('estado', 'pagado')
                ->whereYear('fecha_pago', $mes->year)
                ->whereMonth('fecha_pago', $mes->month)
                ->sum('monto');

            $evolucionIngresos[] = [
                'mes' => $mes->format('M Y'),
                'ingresos' => $ingresos
            ];
        }

        // Tasa de conversión de prueba a pago
        $totalPrueba = Organizacion::where('estado_suscripcion', 'prueba')->count();
        $totalActivas = Organizacion::where('estado_suscripcion', 'activa')->count();
        $tasaConversion = $totalPrueba > 0 ? round(($totalActivas / ($totalActivas + $totalPrueba)) * 100, 2) : 0;

        return view('superadmin.reportes.financiero', compact(
            'ingresosSuscripciones',
            'pagosRecibidos',
            'pagosPendientes',
            'ingresosPorPlan',
            'evolucionIngresos',
            'tasaConversion',
            'mesActual'
        ));
    }

    /**
     * Reporte de uso del sistema
     */
    public function reporteUso(Request $request)
    {
        // Top 10 organizaciones por número de socios
        $topPorSocios = Organizacion::with('suscripcion')
            ->get()
            ->map(function($org) {
                $org->total_socios = Socio::where('id_organizacion', $org->id)->count();
                return $org;
            })
            ->sortByDesc('total_socios')
            ->take(10);

        // Top 10 organizaciones por número de usuarios
        $topPorUsuarios = Organizacion::with('suscripcion')
            ->get()
            ->map(function($org) {
                $org->total_usuarios = Usuario::where('id_organizacion', $org->id)->count();
                return $org;
            })
            ->sortByDesc('total_usuarios')
            ->take(10);

        // Top 10 organizaciones por actividad (boletas generadas)
        $topPorActividad = Organizacion::with('suscripcion')
            ->get()
            ->map(function($org) {
                $org->total_boletas = Boleta::where('id_organizacion', $org->id)->count();
                return $org;
            })
            ->sortByDesc('total_boletas')
            ->take(10);

        // Estadísticas generales de uso
        $promedioSociosPorOrg = round(Socio::count() / max(Organizacion::count(), 1), 2);
        $promedioUsuariosPorOrg = round(Usuario::whereNotNull('id_organizacion')->count() / max(Organizacion::count(), 1), 2);
        $promedioBoletasPorOrg = round(Boleta::count() / max(Organizacion::count(), 1), 2);

        // Uso por plan
        $usoPorPlan = Organizacion::select(
                'suscripciones.nombre',
                DB::raw('COUNT(DISTINCT organizaciones.id) as total_org'),
                DB::raw('COUNT(DISTINCT socios.id) as total_socios'),
                DB::raw('COUNT(DISTINCT usuarios.id) as total_usuarios')
            )
            ->join('suscripciones', 'organizaciones.id_suscripcion', '=', 'suscripciones.id')
            ->leftJoin('socios', 'organizaciones.id', '=', 'socios.id_organizacion')
            ->leftJoin('usuarios', 'organizaciones.id', '=', 'usuarios.id_organizacion')
            ->groupBy('suscripciones.id', 'suscripciones.nombre')
            ->get();

        return view('superadmin.reportes.uso', compact(
            'topPorSocios',
            'topPorUsuarios',
            'topPorActividad',
            'promedioSociosPorOrg',
            'promedioUsuariosPorOrg',
            'promedioBoletasPorOrg',
            'usoPorPlan'
        ));
    }

    /**
     * Reporte comparativo de organizaciones
     */
    public function reporteComparativo(Request $request)
    {
        // Obtener todos los planes disponibles
        $planes = Suscripcion::all();

        // Filtros
        $query = Organizacion::with('suscripcion');

        if ($request->filled('plan')) {
            $query->where('id_suscripcion', $request->plan);
        }

        if ($request->filled('estado')) {
            $query->where('estado_suscripcion', $request->estado);
        }

        $organizaciones = $query->get()->map(function($org) {
            $org->total_socios = Socio::where('id_organizacion', $org->id)->count();
            $org->total_usuarios = Usuario::where('id_organizacion', $org->id)->count();
            $org->total_boletas = Boleta::where('id_organizacion', $org->id)->count();
            return $org;
        });

        // Ordenar
        $orden = $request->get('orden', 'socios');
        $organizaciones = match($orden) {
            'usuarios' => $organizaciones->sortByDesc('total_usuarios'),
            'boletas' => $organizaciones->sortByDesc('total_boletas'),
            'ingresos' => $organizaciones->sortByDesc(fn($o) => $o->suscripcion->precio_mensual),
            default => $organizaciones->sortByDesc('total_socios'),
        };

        return view('superadmin.reportes.comparativo', compact('organizaciones', 'planes'));
    }

    /**
     * Vista de renovaciones y vencimientos
     */
    public function renovaciones(Request $request)
    {
        // Filtros
        $estado = $request->get('estado', 'todas');

        $query = RenovacionSuscripcion::with('organizacion.suscripcion');

        if ($estado !== 'todas') {
            $query->where('estado', $estado);
        }

        $renovaciones = $query->orderBy('fecha_vencimiento', 'asc')->get();

        // Estadísticas
        $totalPendientes = RenovacionSuscripcion::where('estado', 'pendiente')->count();
        $totalVencidas = RenovacionSuscripcion::where('estado', 'pendiente')
            ->where('fecha_vencimiento', '<', now()->toDateString())
            ->count();
        $totalProximasVencer = RenovacionSuscripcion::where('estado', 'pendiente')
            ->whereBetween('fecha_vencimiento', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->count();
        $montoTotal = RenovacionSuscripcion::where('estado', 'pendiente')->sum('monto');

        return view('superadmin.renovaciones.index', compact(
            'renovaciones',
            'estado',
            'totalPendientes',
            'totalVencidas',
            'totalProximasVencer',
            'montoTotal'
        ));
    }

    /**
     * Marcar renovación como pagada manualmente
     */
    public function pagarRenovacion(Request $request, $id)
    {
        $renovacion = RenovacionSuscripcion::findOrFail($id);

        $renovacion->marcarComoPagada('manual');

        // Extender la suscripción de la organización
        $org = $renovacion->organizacion;
        $nuevaFecha = Carbon::parse($org->fecha_fin_suscripcion)->addMonthNoOverflow();
        $org->update([
            'fecha_fin_suscripcion' => $nuevaFecha,
            'estado_suscripcion' => 'activa',
            'activo' => true,
        ]);

        return redirect()->route('superadmin.renovaciones')
            ->with('success', 'Renovación marcada como pagada. Suscripción extendida hasta ' . $nuevaFecha->format('d/m/Y'));
    }

    /**
     * Vista de auditoría y logs del sistema
     */
    public function auditoria(Request $request)
    {
        // Filtros
        $idOrganizacion = $request->get('organizacion');
        $modulo = $request->get('modulo');
        $accion = $request->get('accion');
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');

        $query = Auditoria::with(['organizacion', 'usuario']);

        if ($idOrganizacion) {
            $query->where('id_organizacion', $idOrganizacion);
        }

        if ($modulo) {
            $query->where('modulo', $modulo);
        }

        if ($accion) {
            $query->where('accion', $accion);
        }

        if ($fechaDesde) {
            $query->whereDate('created_at', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $query->whereDate('created_at', '<=', $fechaHasta);
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(50);

        // Obtener datos para filtros
        $organizaciones = Organizacion::orderBy('nombre_apr')->get();
        $modulos = Auditoria::distinct()->pluck('modulo')->sort()->values();
        $acciones = Auditoria::distinct()->pluck('accion')->sort()->values();

        // Estadísticas
        $totalAcciones = Auditoria::count();
        $accionesHoy = Auditoria::whereDate('created_at', now())->count();
        $usuariosActivos = Auditoria::whereDate('created_at', now())
            ->distinct('id_usuario')
            ->count('id_usuario');

        return view('superadmin.auditoria.index', compact(
            'logs',
            'organizaciones',
            'modulos',
            'acciones',
            'totalAcciones',
            'accionesHoy',
            'usuariosActivos',
            'idOrganizacion',
            'modulo',
            'accion',
            'fechaDesde',
            'fechaHasta'
        ));
    }

    /**
     * Lista solicitudes de dominios personalizados
     */
    public function dominiosPersonalizados()
    {
        $dominios = Organizacion::with(['suscripcion', 'aprobador'])
            ->whereNotNull('dominio_personalizado')
            ->orderByRaw("FIELD(estado_dominio_personalizado, 'verificado_dns', 'pendiente_configuracion', 'activo_aprobado', 'rechazado', 'suspendido')")
            ->orderBy('fecha_solicitud_dominio', 'desc')
            ->paginate(20);

        $stats = [
            'total' => Organizacion::whereNotNull('dominio_personalizado')->count(),
            'verificado_dns' => Organizacion::where('estado_dominio_personalizado', 'verificado_dns')->count(),
            'activo_aprobado' => Organizacion::where('estado_dominio_personalizado', 'activo_aprobado')->count(),
            'pendiente_configuracion' => Organizacion::where('estado_dominio_personalizado', 'pendiente_configuracion')->count(),
            'rechazado' => Organizacion::where('estado_dominio_personalizado', 'rechazado')->count(),
            'suspendido' => Organizacion::where('estado_dominio_personalizado', 'suspendido')->count(),
        ];

        return view('superadmin.dominios', compact('dominios', 'stats'));
    }

    /**
     * Aprobar dominio personalizado
     */
    public function aprobarDominio($id)
    {
        $organizacion = Organizacion::findOrFail($id);

        // Verificar que esté en estado verificado_dns
        if (!in_array($organizacion->estado_dominio_personalizado, ['verificado_dns', 'pendiente_configuracion'])) {
            return redirect()->back()
                ->with('error', 'El dominio no puede ser aprobado en su estado actual.');
        }

        $organizacion->update([
            'estado_dominio_personalizado' => 'activo_aprobado',
            'fecha_aprobacion_dominio' => now(),
            'aprobado_por' => auth()->id(),
        ]);

        // Registrar en auditoría
        Auditoria::registrar(
            'superadmin',
            'aprobar_dominio',
            "Aprobó dominio personalizado: {$organizacion->dominio_personalizado} para organización: {$organizacion->nombre_apr}",
            'organizaciones',
            $organizacion->id
        );

        return redirect()->back()
            ->with('success', "Dominio {$organizacion->dominio_personalizado} aprobado exitosamente.");
    }

    /**
     * Rechazar dominio personalizado
     */
    public function rechazarDominio(Request $request, $id)
    {
        $request->validate([
            'motivo' => 'required|string|max:500',
        ]);

        $organizacion = Organizacion::findOrFail($id);

        $organizacion->update([
            'estado_dominio_personalizado' => 'rechazado',
            'fecha_aprobacion_dominio' => now(),
            'aprobado_por' => auth()->id(),
            'observaciones_dominio' => 'Rechazado por Super Admin. Motivo: ' . $request->motivo,
        ]);

        // Registrar en auditoría
        Auditoria::registrar(
            'superadmin',
            'rechazar_dominio',
            "Rechazó dominio personalizado: {$organizacion->dominio_personalizado} para organización: {$organizacion->nombre_apr}. Motivo: {$request->motivo}",
            'organizaciones',
            $organizacion->id
        );

        return redirect()->back()
            ->with('success', "Dominio {$organizacion->dominio_personalizado} rechazado.");
    }

    /**
     * Suspender dominio personalizado
     */
    public function suspenderDominio(Request $request, $id)
    {
        $request->validate([
            'motivo' => 'required|string|max:500',
        ]);

        $organizacion = Organizacion::findOrFail($id);

        $organizacion->update([
            'estado_dominio_personalizado' => 'suspendido',
            'fecha_aprobacion_dominio' => now(),
            'aprobado_por' => auth()->id(),
            'observaciones_dominio' => 'Suspendido por Super Admin. Motivo: ' . $request->motivo,
        ]);

        // Registrar en auditoría
        Auditoria::registrar(
            'superadmin',
            'suspender_dominio',
            "Suspendió dominio personalizado: {$organizacion->dominio_personalizado} para organización: {$organizacion->nombre_apr}. Motivo: {$request->motivo}",
            'organizaciones',
            $organizacion->id
        );

        return redirect()->back()
            ->with('warning', "Dominio {$organizacion->dominio_personalizado} suspendido. El dominio dejará de funcionar inmediatamente.");
    }

    /**
     * Ver solicitudes de compra de dominio
     */
    public function solicitudesDominio()
    {
        $solicitudes = \App\Models\SolicitudCompraDominio::with(['organizacion', 'verificador', 'comprador'])
            ->orderByRaw("FIELD(estado, 'solicitado', 'pagado', 'comprado', 'verificado_disponible', 'pendiente_pago', 'verificado_ocupado', 'activo', 'cancelado')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => \App\Models\SolicitudCompraDominio::count(),
            'solicitados' => \App\Models\SolicitudCompraDominio::where('estado', 'solicitado')->count(),
            'pagados' => \App\Models\SolicitudCompraDominio::where('estado', 'pagado')->count(),
            'activos' => \App\Models\SolicitudCompraDominio::where('estado', 'activo')->count(),
        ];

        return view('superadmin.solicitudes-dominio', compact('solicitudes', 'stats'));
    }

    /**
     * Aprobar solicitud (marcar como disponible)
     */
    public function aprobarSolicitudDominio($id)
    {
        $solicitud = \App\Models\SolicitudCompraDominio::findOrFail($id);

        if (!$solicitud->puedeVerificarDisponible()) {
            return redirect()->back()->with('error', 'Esta solicitud no puede ser aprobada en su estado actual.');
        }

        $solicitud->update([
            'estado' => 'verificado_disponible',
            'verificado_por' => auth()->id(),
            'fecha_verificacion' => now(),
            'observaciones' => 'Dominio verificado como disponible por el administrador.',
        ]);

        // Enviar email al cliente notificando disponibilidad
        try {
            \Mail::raw(
                "¡Buenas noticias!\n\n" .
                "El dominio {$solicitud->dominio_solicitado} está DISPONIBLE.\n\n" .
                "Para proceder con la compra:\n" .
                "1. Realiza el pago de \$20.000\n" .
                "2. Puedes pagar por transferencia bancaria:\n\n" .
                "   DATOS DE TRANSFERENCIA:\n" .
                "   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
                "   Banco: Banco Bci\n" .
                "   Tipo de cuenta: Cuenta Corriente\n" .
                "   Número de cuenta: 89538463\n" .
                "   RUT: 19.762.564-3\n" .
                "   Titular: JOSE ARAVENA\n" .
                "   Email: soportesistemaapr@gmail.com\n" .
                "   Monto: \$20.000\n" .
                "   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n" .
                "   - Envía comprobante a soportesistemaapr@gmail.com\n\n" .
                "Una vez recibido el pago, compraremos y configuraremos tu dominio.\n\n" .
                "Saludos,\n" .
                "Equipo SistemaAPR",
                function($message) use ($solicitud) {
                    $message->to($solicitud->organizacion->email_contacto)
                            ->subject('✅ Dominio Disponible - ' . $solicitud->dominio_solicitado);
                }
            );
        } catch (\Exception $e) {
            \Log::error('Error enviando email disponibilidad: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Solicitud aprobada. Se notificó al cliente que el dominio está disponible.');
    }

    /**
     * Rechazar solicitud (marcar como ocupado)
     */
    public function rechazarSolicitudDominio(Request $request, $id)
    {
        $request->validate([
            'motivo' => 'nullable|string|max:500',
        ]);

        $solicitud = \App\Models\SolicitudCompraDominio::findOrFail($id);

        $motivo = $request->motivo ?? 'El dominio ya está registrado por otra persona.';

        $solicitud->update([
            'estado' => 'verificado_ocupado',
            'verificado_por' => auth()->id(),
            'fecha_verificacion' => now(),
            'observaciones' => $motivo,
        ]);

        // Enviar email al cliente
        try {
            \Mail::raw(
                "Lamentablemente el dominio {$solicitud->dominio_solicitado} NO está disponible.\n\n" .
                "Motivo: {$motivo}\n\n" .
                "¿Te gustaría que busquemos alternativas?\n" .
                "Contáctanos a soportesistemaapr@gmail.com\n\n" .
                "Saludos,\n" .
                "Equipo SistemaAPR",
                function($message) use ($solicitud) {
                    $message->to($solicitud->organizacion->email_contacto)
                            ->subject('❌ Dominio No Disponible - ' . $solicitud->dominio_solicitado);
                }
            );
        } catch (\Exception $e) {
            \Log::error('Error enviando email rechazo: ' . $e->getMessage());
        }

        return redirect()->back()->with('warning', 'Solicitud rechazada. Se notificó al cliente.');
    }

    /**
     * Marcar como pagado
     */
    public function marcarPagadoDominio(Request $request, $id)
    {
        $solicitud = \App\Models\SolicitudCompraDominio::findOrFail($id);

        if (!$solicitud->puedeRecibirPago()) {
            return redirect()->back()->with('error', 'Esta solicitud no puede recibir pago en su estado actual.');
        }

        $solicitud->update([
            'estado' => 'pagado',
            'metodo_pago' => 'transferencia',
            'fecha_pago' => now(),
        ]);

        return redirect()->back()->with('success', 'Solicitud marcada como pagada. Ya puedes comprar el dominio en NIC Chile.');
    }

    /**
     * Marcar como comprado en NIC Chile
     */
    public function marcarCompradoDominio($id)
    {
        $solicitud = \App\Models\SolicitudCompraDominio::findOrFail($id);

        if (!$solicitud->puedeComprar()) {
            return redirect()->back()->with('error', 'Esta solicitud no puede ser marcada como comprada.');
        }

        $solicitud->update([
            'estado' => 'comprado',
            'comprado_por' => auth()->id(),
            'fecha_compra_nic' => now(),
            'fecha_vencimiento' => now()->addYear(),
        ]);

        return redirect()->back()->with('success', 'Dominio marcado como comprado. Ya puedes activarlo.');
    }

    /**
     * Activar dominio (configurado y listo)
     */
    public function activarDominio($id)
    {
        $solicitud = \App\Models\SolicitudCompraDominio::findOrFail($id);

        if (!$solicitud->puedeActivar()) {
            return redirect()->back()->with('error', 'Esta solicitud no puede ser activada.');
        }

        // Activar el dominio en la organización
        $solicitud->organizacion->update([
            'dominio_personalizado' => $solicitud->dominio_solicitado,
            'estado_dominio_personalizado' => 'activo_aprobado',
            'fecha_aprobacion_dominio' => now(),
            'aprobado_por' => auth()->id(),
        ]);

        $solicitud->update([
            'estado' => 'activo',
            'fecha_activacion' => now(),
        ]);

        // Email al cliente
        try {
            \Mail::raw(
                "¡Tu dominio está listo! 🎉\n\n" .
                "Ya puedes acceder a tu sistema en:\n" .
                "https://{$solicitud->dominio_solicitado}\n\n" .
                "El dominio vence el: " . $solicitud->fecha_vencimiento->format('d/m/Y') . "\n" .
                "Te avisaremos 30 días antes para renovar.\n\n" .
                "Saludos,\n" .
                "Equipo SistemaAPR",
                function($message) use ($solicitud) {
                    $message->to($solicitud->organizacion->email_contacto)
                            ->subject('🎉 Tu Dominio está Activo - ' . $solicitud->dominio_solicitado);
                }
            );
        } catch (\Exception $e) {
            \Log::error('Error enviando email activación: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Dominio activado correctamente. El cliente fue notificado.');
    }

    /**
     * Mostrar formulario de edición del perfil del super admin
     */
    public function perfil()
    {
        $usuario = auth()->user();
        return view('superadmin.perfil', compact('usuario'));
    }

    /**
     * Actualizar perfil del super admin
     */
    public function actualizarPerfil(Request $request)
    {
        $usuario = auth()->user();

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:usuarios,email,' . $usuario->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            // Actualizar datos básicos
            $usuario->nombre = $validated['nombre'];
            $usuario->apellido = $validated['apellido'];
            $usuario->email = $validated['email'];

            // Actualizar contraseña si se proporcionó
            if ($request->filled('password')) {
                $usuario->password = bcrypt($validated['password']);
            }

            $usuario->save();

            // Registrar en auditoría
            Auditoria::registrar(
                $usuario->id,
                'actualizar_perfil_superadmin',
                'Actualizó su perfil de Super Admin',
                'usuarios',
                $usuario->id
            );

            DB::commit();

            return redirect()->back()
                ->with('success', 'Perfil actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar el perfil: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Monitoreo de DTEs de todas las organizaciones
     */
    public function monitoreoDTE()
    {
        // Obtener todas las configuraciones DTE con sus organizaciones
        $configuraciones = ConfiguracionDTE::with('organizacion')
            ->orderBy('id_organizacion', 'asc')
            ->get();

        // Estadísticas generales
        $totalOrganizaciones = Organizacion::where('activo', true)->count();
        $organizacionesConDTE = ConfiguracionDTE::where('activo', true)->distinct('id_organizacion')->count();
        $organizacionesSinDTE = $totalOrganizaciones - $organizacionesConDTE;

        // Contar por ambiente
        $enCertificacion = ConfiguracionDTE::where('activo', true)
            ->where('ambiente', 'certificacion')
            ->count();
        $enProduccion = ConfiguracionDTE::where('activo', true)
            ->where('ambiente', 'produccion')
            ->count();

        // Total de DTEs emitidos (todas las organizaciones)
        $totalDTEsEmitidos = Boleta::whereNotNull('folio_sii')->count();

        // DTEs emitidos últimos 30 días
        $dtesUltimos30Dias = Boleta::whereNotNull('folio_sii')
            ->where('fecha_emision_dte', '>=', now()->subDays(30))
            ->count();

        // Organizaciones por ID con sus métricas DTE
        $organizacionesMetricas = [];
        foreach ($configuraciones as $config) {
            $org = $config->organizacion;

            // DTEs emitidos por esta organización
            $dtesEmitidos = Boleta::where('id_organizacion', $config->id_organizacion)
                ->whereNotNull('folio_sii')
                ->count();

            // DTEs últimos 30 días
            $dtesRecientes = Boleta::where('id_organizacion', $config->id_organizacion)
                ->whereNotNull('folio_sii')
                ->where('fecha_emision_dte', '>=', now()->subDays(30))
                ->count();

            // Último DTE emitido
            $ultimoDTE = Boleta::where('id_organizacion', $config->id_organizacion)
                ->whereNotNull('folio_sii')
                ->orderBy('fecha_emision_dte', 'desc')
                ->first();

            $organizacionesMetricas[] = [
                'organizacion' => $org,
                'config' => $config,
                'dtes_emitidos' => $dtesEmitidos,
                'dtes_recientes' => $dtesRecientes,
                'ultimo_dte' => $ultimoDTE,
            ];
        }

        return view('superadmin.monitoreo-dte', compact(
            'configuraciones',
            'totalOrganizaciones',
            'organizacionesConDTE',
            'organizacionesSinDTE',
            'enCertificacion',
            'enProduccion',
            'totalDTEsEmitidos',
            'dtesUltimos30Dias',
            'organizacionesMetricas'
        ));
    }

    /**
     * Reporte avanzado de facturación DTE
     */
    public function reporteFacturacionDTE(Request $request)
    {
        $reporteService = new ReporteDTEService();

        // Filtros opcionales
        $filtros = [];
        if ($request->has('fecha_desde')) {
            $filtros['fecha_desde'] = $request->fecha_desde;
        }
        if ($request->has('fecha_hasta')) {
            $filtros['fecha_hasta'] = $request->fecha_hasta;
        }

        // Obtener todos los datos del reporte
        $resumenGeneral = $reporteService->obtenerResumenGeneral();
        $facturacionPorOrg = $reporteService->obtenerFacturacionPorOrganizacion($filtros);
        $evolucionMensual = $reporteService->obtenerEvolucionMensual();
        $distribucionTipo = $reporteService->obtenerDistribucionPorTipo($filtros);
        $analisisAdopcion = $reporteService->obtenerAnalisisAdopcion();
        $top10Organizaciones = $reporteService->obtenerTop10Organizaciones($filtros);

        return view('superadmin.reporte-facturacion-dte', compact(
            'resumenGeneral',
            'facturacionPorOrg',
            'evolucionMensual',
            'distribucionTipo',
            'analisisAdopcion',
            'top10Organizaciones',
            'filtros'
        ));
    }

    /**
     * Exportar reporte de facturación DTE a Excel
     */
    public function exportarReporteDTEExcel(Request $request)
    {
        $reporteService = new ReporteDTEService();

        // Filtros
        $filtros = [];
        if ($request->has('fecha_desde')) {
            $filtros['fecha_desde'] = $request->fecha_desde;
        }
        if ($request->has('fecha_hasta')) {
            $filtros['fecha_hasta'] = $request->fecha_hasta;
        }

        $facturacionPorOrg = $reporteService->obtenerFacturacionPorOrganizacion($filtros);
        $evolucionMensual = $reporteService->obtenerEvolucionMensual();

        // Generar CSV (compatible con Excel)
        $filename = 'reporte_facturacion_dte_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($facturacionPorOrg, $evolucionMensual) {
            $file = fopen('php://output', 'w');

            // BOM para UTF-8 en Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Sección 1: Facturación por Organización
            fputcsv($file, ['REPORTE DE FACTURACIÓN DTE POR ORGANIZACIÓN']);
            fputcsv($file, ['Generado el', now()->format('d/m/Y H:i:s')]);
            fputcsv($file, []);

            fputcsv($file, [
                'Organización',
                'Razón Social',
                'Total DTEs',
                'Boletas',
                'Facturas',
                'NC',
                'ND',
                'Ingresos Totales',
                'Último DTE'
            ]);

            foreach ($facturacionPorOrg as $org) {
                fputcsv($file, [
                    $org->nombre_organizacion,
                    $org->razon_social,
                    $org->total_dtes,
                    $org->total_boletas,
                    $org->total_facturas,
                    $org->total_nc,
                    $org->total_nd,
                    '$' . number_format($org->ingresos_totales, 0, ',', '.'),
                    $org->ultimo_dte ? Carbon::parse($org->ultimo_dte)->format('d/m/Y') : 'N/A'
                ]);
            }

            // Sección 2: Evolución Mensual
            fputcsv($file, []);
            fputcsv($file, []);
            fputcsv($file, ['EVOLUCIÓN MENSUAL DE DTEs (Últimos 12 meses)']);
            fputcsv($file, ['Mes', 'Total DTEs', 'Ingresos']);

            foreach ($evolucionMensual['data'] as $mes) {
                fputcsv($file, [
                    $mes['mes_nombre'],
                    $mes['total_dtes'],
                    '$' . number_format($mes['ingresos'], 0, ',', '.')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exportar reporte de facturación DTE a PDF
     */
    public function exportarReporteDTEPDF(Request $request)
    {
        $reporteService = new ReporteDTEService();

        // Filtros
        $filtros = [];
        if ($request->has('fecha_desde')) {
            $filtros['fecha_desde'] = $request->fecha_desde;
        }
        if ($request->has('fecha_hasta')) {
            $filtros['fecha_hasta'] = $request->fecha_hasta;
        }

        $resumenGeneral = $reporteService->obtenerResumenGeneral();
        $facturacionPorOrg = $reporteService->obtenerFacturacionPorOrganizacion($filtros);
        $top10Organizaciones = $reporteService->obtenerTop10Organizaciones($filtros);

        // Generar HTML para PDF simple
        $html = view('superadmin.pdf.reporte-facturacion-dte', compact(
            'resumenGeneral',
            'facturacionPorOrg',
            'top10Organizaciones',
            'filtros'
        ))->render();

        // Retornar HTML que se puede imprimir como PDF desde el navegador
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="reporte_dte.pdf"');
    }

    /**
     * Lista de configuraciones DTE de todas las organizaciones
     */
    public function configuracionDTE()
    {
        $organizaciones = Organizacion::with('configuracionDTE')
            ->where('activo', true)
            ->orderBy('nombre_apr', 'asc')
            ->get();

        return view('superadmin.configuracion-dte.index', compact('organizaciones'));
    }

    /**
     * Formulario de edición de configuración DTE
     */
    public function editarConfiguracionDTE($organizacionId)
    {
        $organizacion = Organizacion::findOrFail($organizacionId);
        $config = ConfiguracionDTE::where('id_organizacion', $organizacionId)->first();

        return view('superadmin.configuracion-dte.editar', compact('organizacion', 'config'));
    }

    /**
     * Guardar configuración DTE
     */
    public function guardarConfiguracionDTE(Request $request, $organizacionId)
    {
        $validated = $request->validate([
            'rut_emisor' => 'required|string|max:12',
            'razon_social' => 'required|string|max:200',
            'giro' => 'required|string|max:200',
            'direccion_casa_matriz' => 'required|string|max:255',
            'comuna' => 'required|string|max:100',
            'ciudad' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email_contacto' => 'required|email|max:150',
            'ambiente' => 'required|in:certificacion,produccion',
            'proveedor_dte' => 'required|in:simpleapi,simplefactura',
            // Credenciales SimpleAPI
            'simpleapi_token' => 'nullable|string|max:255',
            // Credenciales SimpleFactura
            'simplefactura_usuario' => 'nullable|email|max:150',
            'simplefactura_password' => 'nullable|string|max:150',
            // Certificado digital
            'certificado_digital' => 'nullable|file|mimes:pfx,p12|max:2048',
            'certificado_password' => 'nullable|string|max:100',
        ]);

        $configExistente = ConfiguracionDTE::where('id_organizacion', $organizacionId)->first();

        // Validaciones específicas por proveedor
        if ($validated['proveedor_dte'] === 'simpleapi') {
            if (!$request->hasFile('certificado_digital') && (!$configExistente || !$configExistente->certificado_digital)) {
                return redirect()->back()
                    ->withErrors(['certificado_digital' => 'SimpleAPI requiere un certificado digital'])
                    ->withInput();
            }
            if (empty($validated['simpleapi_token'])) {
                return redirect()->back()
                    ->withErrors(['simpleapi_token' => 'El token de API de SimpleAPI es obligatorio'])
                    ->withInput();
            }
        } elseif ($validated['proveedor_dte'] === 'simplefactura') {
            if (!$request->hasFile('certificado_digital') && (!$configExistente || !$configExistente->certificado_digital)) {
                return redirect()->back()
                    ->withErrors(['certificado_digital' => 'SimpleFactura requiere un certificado digital'])
                    ->withInput();
            }
            if (empty($validated['simplefactura_usuario'])) {
                return redirect()->back()
                    ->withErrors(['simplefactura_usuario' => 'El usuario de SimpleFactura es obligatorio'])
                    ->withInput();
            }
            if (empty($validated['simplefactura_password'])) {
                return redirect()->back()
                    ->withErrors(['simplefactura_password' => 'La contraseña de SimpleFactura es obligatoria'])
                    ->withInput();
            }
        }

        // Procesar certificado digital
        if ($request->hasFile('certificado_digital')) {
            $certificado = $request->file('certificado_digital');
            $certificadoBase64 = base64_encode(file_get_contents($certificado->getRealPath()));
            $validated['certificado_digital'] = $certificadoBase64;

            if (!empty($validated['certificado_password'])) {
                $validated['certificado_password'] = encrypt($validated['certificado_password']);
            }
        } else {
            unset($validated['certificado_digital']);
            if (empty($validated['certificado_password'])) {
                unset($validated['certificado_password']);
            } else {
                $validated['certificado_password'] = encrypt($validated['certificado_password']);
            }
        }

        // Encriptar contraseña de SimpleFactura
        if (!empty($validated['simplefactura_password'])) {
            $validated['simplefactura_password'] = encrypt($validated['simplefactura_password']);
        } else {
            if ($configExistente && $configExistente->simplefactura_password) {
                unset($validated['simplefactura_password']);
            }
        }

        ConfiguracionDTE::updateOrCreate(
            ['id_organizacion' => $organizacionId],
            array_merge($validated, [
                'activo' => true,
            ])
        );

        return redirect()->route('superadmin.configuracion-dte')
            ->with('success', 'Configuración DTE guardada exitosamente');
    }

    /**
     * Verificar conexión DTE de una organización
     */
    public function verificarConexionDTE($organizacionId)
    {
        try {
            $config = ConfiguracionDTE::where('id_organizacion', $organizacionId)->first();

            if (!$config) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No hay configuración DTE para esta organización'
                ], 404);
            }

            if (!$config->estaConfigurado()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'La configuración DTE está incompleta. Verifica que todos los campos requeridos estén completos.'
                ], 400);
            }

            // Obtener el servicio DTE apropiado
            if ($config->proveedor_dte === 'simpleapi') {
                $service = app(\App\Services\SimpleAPIService::class);
                $service->setOrganizacion($organizacionId);

                // SimpleAPI: Verificar obteniendo folios disponibles
                $folios = $service->obtenerFoliosDisponibles();

                if (isset($folios['error'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Error al conectar con SimpleAPI: ' . $folios['error']
                    ], 400);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => '✅ Conexión exitosa con SimpleAPI. Folios disponibles: Boleta ' . ($folios['boleta']['disponibles'] ?? 0) . ', Factura ' . ($folios['factura']['disponibles'] ?? 0),
                    'data' => $folios
                ]);

            } elseif ($config->proveedor_dte === 'simplefactura') {
                $service = app(\App\Services\SimpleFacturaService::class);
                $service->setOrganizacion($organizacionId);

                // SimpleFactura: Verificar autenticación
                try {
                    // Intentar obtener folios para verificar conexión completa
                    $folios = $service->obtenerFoliosDisponibles();

                    if (isset($folios['error'])) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Error al conectar con SimpleFactura: ' . $folios['error']
                        ], 400);
                    }

                    return response()->json([
                        'status' => 'success',
                        'message' => '✅ Conexión exitosa con SimpleFactura. Credenciales válidas.',
                        'data' => $folios
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Error al autenticar con SimpleFactura: ' . $e->getMessage()
                    ], 400);
                }

            } elseif ($config->proveedor_dte === 'libredte') {
                $service = app(\App\Services\LibreDTEService::class);
                $service->setOrganizacion($organizacionId);

                // LibreDTE: Verificar obteniendo folios
                $folios = $service->obtenerFoliosDisponibles();

                if (isset($folios['error'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Error al conectar con LibreDTE: ' . $folios['error']
                    ], 400);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => '✅ Conexión exitosa con LibreDTE. Folios disponibles: Boleta ' . ($folios['39'] ?? 0) . ', Factura ' . ($folios['33'] ?? 0),
                    'data' => $folios
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Proveedor DTE no válido: ' . $config->proveedor_dte
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al verificar conexión: ' . $e->getMessage()
            ], 500);
        }
    }
}
