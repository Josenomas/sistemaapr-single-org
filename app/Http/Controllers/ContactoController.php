<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactoController extends Controller
{
    /**
     * Procesar formulario de contacto
     */
    public function enviar(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email',
            'telefono' => 'required|string|max:20',
            'apr' => 'required|string|max:255',
            'mensaje' => 'required|string',
        ]);

        try {
            // Email del administrador del sistema (cámbialo por tu email)
            $emailDestino = env('MAIL_CONTACT', 'soportesistemaapr@gmail.com');

            // 1. Enviar email al admin (notificación de nueva consulta)
            Mail::send('emails.contacto', ['datos' => $validated], function ($message) use ($validated, $emailDestino) {
                $message->to($emailDestino)
                        ->subject('Nueva Consulta desde Sistema APR - ' . $validated['apr'])
                        ->replyTo($validated['email'], $validated['nombre']);
            });

            // 2. Enviar email de confirmación al usuario
            Mail::send('emails.contacto-confirmacion', ['datos' => $validated], function ($message) use ($validated) {
                $message->to($validated['email'])
                        ->subject('Confirmación de Solicitud Recibida - Sistema APR');
            });

            return redirect()->route('landing')
                ->with('success', '¡Gracias por tu consulta! Hemos enviado una confirmación a tu email.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Hubo un error al enviar tu mensaje. Por favor intenta nuevamente.')
                ->withInput();
        }
    }
}
