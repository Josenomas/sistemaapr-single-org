<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suscripcion;

class SuscripcionController extends Controller
{
    /**
     * Página de renovación cuando la suscripción ha vencido
     */
    public function renovar()
    {
        $organizacion = auth()->user()->organizacion->load('suscripcion');

        // Obtener planes disponibles
        $planes = Suscripcion::orderBy('precio_mensual')->get();

        // Buscar o crear pago pendiente
        $pagoPendiente = \App\Models\PagoSuscripcion::where('id_organizacion', $organizacion->id)
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$pagoPendiente && $organizacion->suscripcion) {
            // Crear pago pendiente si no existe
            $pagoPendiente = \App\Models\PagoSuscripcion::create([
                'id_organizacion' => $organizacion->id,
                'id_suscripcion' => $organizacion->id_suscripcion,
                'monto' => $organizacion->suscripcion->precio_mensual,
                'estado' => 'pendiente',
                'periodo_inicio' => now(),
                'periodo_fin' => now()->addMonth(),
                'fecha_vencimiento' => now()->addDays(7),
            ]);
        }

        // Debug temporal
        \Log::info('Renovar - PagoPendiente:', [
            'existe' => $pagoPendiente ? 'SI' : 'NO',
            'id' => $pagoPendiente?->id,
            'organizacion_id' => $organizacion->id,
            'tiene_suscripcion' => $organizacion->suscripcion ? 'SI' : 'NO'
        ]);

        return view('suscripcion.renovar', compact('organizacion', 'planes', 'pagoPendiente'));
    }

    /**
     * Página de estado cuando la suscripción está suspendida/cancelada
     */
    public function estado()
    {
        $organizacion = auth()->user()->organizacion;

        return view('suscripcion.estado', compact('organizacion'));
    }

    /**
     * Enviar solicitud de soporte para reactivación
     */
    public function enviarSolicitudSoporte(Request $request)
    {
        $request->validate([
            'mensaje_adicional' => 'nullable|string|max:2000'
        ]);

        $organizacion = auth()->user()->organizacion;
        $usuario = auth()->user();

        try {
            // Detectar si viene del formulario de ayuda (con formato ASUNTO:) o del botón de reactivación
            $mensajeCompleto = $request->mensaje_adicional;
            $asunto = null;
            $descripcion = null;
            $esAyuda = false;

            if ($mensajeCompleto && strpos($mensajeCompleto, 'ASUNTO:') === 0) {
                // Es del botón de ayuda
                $esAyuda = true;
                $partes = explode("\n\n", $mensajeCompleto, 2);
                $asunto = str_replace('ASUNTO: ', '', $partes[0]);
                $descripcion = $partes[1] ?? '';
            }

            \Mail::send([], [], function ($message) use ($organizacion, $usuario, $request, $asunto, $descripcion, $mensajeCompleto, $esAyuda) {
                $subject = $esAyuda
                    ? 'Solicitud de Soporte - ' . $organizacion->nombre_apr
                    : 'Solicitud de Reactivación - ' . $organizacion->nombre_apr;

                $template = $esAyuda ? 'emails.solicitud-soporte' : 'emails.solicitud-reactivacion';

                $message->to('soportesistemaapr@gmail.com')
                    ->subject($subject)
                    ->html(view($template, [
                        'organizacion' => $organizacion,
                        'usuario' => $usuario,
                        'asunto' => $asunto,
                        'descripcion' => $descripcion,
                        'mensajeAdicional' => !$esAyuda ? $mensajeCompleto : null
                    ])->render());
            });

            return response()->json([
                'success' => true,
                'message' => 'Solicitud enviada exitosamente. Te contactaremos pronto.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error enviando solicitud de soporte: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar la solicitud. Por favor intenta nuevamente.'
            ], 500);
        }
    }
}
