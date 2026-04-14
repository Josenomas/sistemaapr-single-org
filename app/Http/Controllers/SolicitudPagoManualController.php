<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudPagoManual;
use App\Models\PagoSuscripcion;
use App\Models\Auditoria;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SolicitudPagoManualController extends Controller
{
    /**
     * Mostrar formulario para crear solicitud de pago manual
     */
    public function create($idPago)
    {
        $pago = PagoSuscripcion::with(['organizacion', 'suscripcion'])
            ->where('id_organizacion', auth()->user()->id_organizacion)
            ->findOrFail($idPago);

        // Verificar que el pago esté pendiente
        if ($pago->estado !== 'pendiente') {
            return redirect()->route('organizacion.pagos-suscripcion')
                ->with('error', 'Este pago ya fue procesado.');
        }

        // Verificar si ya existe una solicitud pendiente
        $solicitudExistente = SolicitudPagoManual::where('id_pago_suscripcion', $idPago)
            ->where('estado', 'pendiente')
            ->first();

        if ($solicitudExistente) {
            return redirect()->route('organizacion.pagos-suscripcion')
                ->with('warning', 'Ya existe una solicitud de pago manual pendiente para este pago.');
        }

        return view('organizacion.solicitud-pago-manual', compact('pago'));
    }

    /**
     * Guardar solicitud de pago manual
     */
    public function store(Request $request, $idPago)
    {
        $pago = PagoSuscripcion::where('id_organizacion', auth()->user()->id_organizacion)
            ->findOrFail($idPago);

        $validated = $request->validate([
            'comprobante' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'numero_operacion' => 'required|string|max:100',
            'banco_origen' => 'required|string|max:100',
            'fecha_transferencia' => 'required|date|before_or_equal:today',
            'monto' => 'required|numeric|min:0',
            'notas' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Subir comprobante
            $comprobantePath = null;
            if ($request->hasFile('comprobante')) {
                $comprobantePath = $request->file('comprobante')->store('comprobantes-pago', 'public');
            }

            // Crear solicitud
            $solicitud = SolicitudPagoManual::create([
                'id_pago_suscripcion' => $idPago,
                'id_organizacion' => auth()->user()->id_organizacion,
                'comprobante_path' => $comprobantePath,
                'numero_operacion' => $validated['numero_operacion'],
                'banco_origen' => $validated['banco_origen'],
                'fecha_transferencia' => $validated['fecha_transferencia'],
                'monto' => $validated['monto'],
                'notas' => $validated['notas'] ?? null,
                'estado' => 'pendiente',
            ]);

            // Registrar en auditoría
            Auditoria::registrar(
                auth()->user()->id,
                'crear_solicitud_pago_manual',
                "Creó solicitud de pago manual para pago #{$idPago}. Monto: $" . number_format($validated['monto'], 0, ',', '.'),
                'solicitudes_pago_manual',
                $solicitud->id
            );

            DB::commit();

            return redirect()->route('organizacion.pagos-suscripcion')
                ->with('success', 'Solicitud de pago manual enviada exitosamente. Será revisada por el administrador.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al enviar la solicitud: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Ver estado de una solicitud (para admin de organización)
     */
    public function show($id)
    {
        $solicitud = SolicitudPagoManual::with(['pagoSuscripcion.suscripcion', 'revisor'])
            ->where('id_organizacion', auth()->user()->id_organizacion)
            ->findOrFail($id);

        return view('organizacion.solicitud-pago-detalle', compact('solicitud'));
    }

    /**
     * Listado de solicitudes para Super Admin
     */
    public function index(Request $request)
    {
        $query = SolicitudPagoManual::with(['organizacion', 'pagoSuscripcion.suscripcion', 'revisor']);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('organizacion')) {
            $query->where('id_organizacion', $request->organizacion);
        }

        $solicitudes = $query->orderBy('created_at', 'desc')->paginate(20);

        // Estadísticas
        $stats = [
            'pendientes' => SolicitudPagoManual::where('estado', 'pendiente')->count(),
            'aprobadas' => SolicitudPagoManual::where('estado', 'aprobada')->count(),
            'rechazadas' => SolicitudPagoManual::where('estado', 'rechazada')->count(),
            'total' => SolicitudPagoManual::count(),
        ];

        $organizaciones = \App\Models\Organizacion::orderBy('nombre_apr')->get();

        return view('superadmin.solicitudes-pago.index', compact('solicitudes', 'stats', 'organizaciones'));
    }

    /**
     * Ver detalle de solicitud (Super Admin)
     */
    public function showSuperAdmin($id)
    {
        $solicitud = SolicitudPagoManual::with(['organizacion', 'pagoSuscripcion.suscripcion', 'revisor'])
            ->findOrFail($id);

        return view('superadmin.solicitudes-pago.detalle', compact('solicitud'));
    }

    /**
     * Aprobar solicitud (Super Admin)
     */
    public function aprobar($id)
    {
        $solicitud = SolicitudPagoManual::with('pagoSuscripcion')->findOrFail($id);

        if ($solicitud->estado !== 'pendiente') {
            return redirect()->back()
                ->with('error', 'Esta solicitud ya fue procesada.');
        }

        DB::beginTransaction();
        try {
            // Aprobar solicitud y marcar pago como pagado
            $solicitud->aprobar(auth()->id());

            // Registrar en auditoría
            Auditoria::registrar(
                auth()->id(),
                'aprobar_solicitud_pago_manual',
                "Aprobó solicitud de pago manual #{$solicitud->id} de {$solicitud->organizacion->nombre_apr}. Monto: $" . number_format($solicitud->monto, 0, ',', '.'),
                'solicitudes_pago_manual',
                $solicitud->id
            );

            DB::commit();

            return redirect()->route('superadmin.solicitudes-pago.index')
                ->with('success', 'Solicitud aprobada exitosamente. El pago ha sido registrado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al aprobar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar solicitud (Super Admin)
     */
    public function rechazar(Request $request, $id)
    {
        $solicitud = SolicitudPagoManual::findOrFail($id);

        if ($solicitud->estado !== 'pendiente') {
            return redirect()->back()
                ->with('error', 'Esta solicitud ya fue procesada.');
        }

        $validated = $request->validate([
            'motivo_rechazo' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Rechazar solicitud
            $solicitud->rechazar(auth()->id(), $validated['motivo_rechazo']);

            // Registrar en auditoría
            Auditoria::registrar(
                auth()->id(),
                'rechazar_solicitud_pago_manual',
                "Rechazó solicitud de pago manual #{$solicitud->id} de {$solicitud->organizacion->nombre_apr}. Motivo: {$validated['motivo_rechazo']}",
                'solicitudes_pago_manual',
                $solicitud->id
            );

            DB::commit();

            return redirect()->route('superadmin.solicitudes-pago.index')
                ->with('success', 'Solicitud rechazada. La organización será notificada.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al rechazar la solicitud: ' . $e->getMessage());
        }
    }
}
