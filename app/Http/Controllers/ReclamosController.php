<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reclamo;
use Illuminate\Support\Facades\Mail;

class ReclamosController extends Controller
{
    /**
     * Mostrar formulario de reclamos (público)
     */
    public function create()
    {
        return view('reclamos.create');
    }

    /**
     * Guardar reclamo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'rut' => 'required|string|max:12',
            'email' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'tipo_reclamo' => 'required|in:servicio,facturacion,soporte,funcionalidad,otro',
            'detalle_reclamo' => 'required|string|min:10',
            'solucion_solicitada' => 'nullable|string',
        ], [
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'rut.required' => 'El RUT es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser válido.',
            'tipo_reclamo.required' => 'Debes seleccionar un tipo de reclamo.',
            'detalle_reclamo.required' => 'Debes describir tu reclamo.',
            'detalle_reclamo.min' => 'El reclamo debe tener al menos 10 caracteres.',
        ]);

        // Intentar asociar con organización si está logueado
        $idOrganizacion = auth()->check() ? auth()->user()->id_organizacion : null;

        // Generar número de reclamo
        $numeroReclamo = Reclamo::generarNumeroReclamo();

        $reclamo = Reclamo::create([
            'numero_reclamo' => $numeroReclamo,
            'id_organizacion' => $idOrganizacion,
            'nombre_completo' => $validated['nombre_completo'],
            'rut' => $validated['rut'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? null,
            'direccion' => $validated['direccion'] ?? null,
            'tipo_reclamo' => $validated['tipo_reclamo'],
            'detalle_reclamo' => $validated['detalle_reclamo'],
            'solucion_solicitada' => $validated['solucion_solicitada'] ?? null,
            'estado' => 'pendiente',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Enviar email de confirmación al reclamante
        $this->enviarEmailConfirmacion($reclamo);

        // Enviar notificación al administrador
        $this->notificarAdministrador($reclamo);

        return redirect()->route('reclamos.confirmacion', $reclamo->numero_reclamo)
                       ->with('success', '¡Reclamo registrado exitosamente! Número: ' . $numeroReclamo);
    }

    /**
     * Mostrar confirmación de reclamo
     */
    public function confirmacion($numeroReclamo)
    {
        $reclamo = Reclamo::where('numero_reclamo', $numeroReclamo)->firstOrFail();
        return view('reclamos.confirmacion', compact('reclamo'));
    }

    /**
     * Listar reclamos (admin)
     */
    public function index()
    {
        $reclamos = Reclamo::orderBy('created_at', 'desc')->paginate(20);
        return view('reclamos.index', compact('reclamos'));
    }

    /**
     * Ver detalle de reclamo (admin)
     */
    public function show($id)
    {
        $reclamo = Reclamo::findOrFail($id);
        return view('reclamos.show', compact('reclamo'));
    }

    /**
     * Responder reclamo (admin)
     */
    public function responder(Request $request, $id)
    {
        $reclamo = Reclamo::findOrFail($id);

        $validated = $request->validate([
            'respuesta' => 'required|string|min:10',
            'estado' => 'required|in:resuelto,rechazado',
        ], [
            'respuesta.required' => 'La respuesta es obligatoria.',
            'respuesta.min' => 'La respuesta debe tener al menos 10 caracteres.',
            'estado.required' => 'Debes seleccionar un estado.',
        ]);

        $reclamo->update([
            'respuesta' => $validated['respuesta'],
            'estado' => $validated['estado'],
            'fecha_respuesta' => now(),
            'respondido_por' => auth()->id(),
        ]);

        // Enviar email al reclamante con la respuesta
        $this->enviarEmailRespuesta($reclamo);

        return redirect()->route('superadmin.reclamos.show', $reclamo->id)
                       ->with('success', 'Reclamo respondido exitosamente.');
    }

    /**
     * Enviar email de confirmación al reclamante
     */
    private function enviarEmailConfirmacion($reclamo)
    {
        try {
            Mail::send('emails.reclamo-confirmacion', compact('reclamo'), function($message) use ($reclamo) {
                $message->to($reclamo->email)
                       ->subject('Reclamo Recibido - ' . $reclamo->numero_reclamo . ' - Sistema APR');
            });
        } catch (\Exception $e) {
            \Log::error('Error al enviar email de confirmación de reclamo: ' . $e->getMessage());
        }
    }

    /**
     * Enviar email de respuesta al reclamante
     */
    private function enviarEmailRespuesta($reclamo)
    {
        try {
            Mail::send('emails.reclamo-respuesta', compact('reclamo'), function($message) use ($reclamo) {
                $message->to($reclamo->email)
                       ->subject('Respuesta a tu Reclamo ' . $reclamo->numero_reclamo . ' - Sistema APR');
            });
        } catch (\Exception $e) {
            \Log::error('Error al enviar email de respuesta de reclamo: ' . $e->getMessage());
        }
    }

    /**
     * Notificar al administrador sobre nuevo reclamo
     */
    private function notificarAdministrador($reclamo)
    {
        try {
            Mail::send('emails.reclamo-notificacion-admin', compact('reclamo'), function($message) use ($reclamo) {
                $message->to('soportesistemaapr@gmail.com')
                       ->subject('Nuevo Reclamo Recibido: ' . $reclamo->numero_reclamo);
            });
        } catch (\Exception $e) {
            \Log::error('Error al notificar administrador sobre reclamo: ' . $e->getMessage());
        }
    }
}
