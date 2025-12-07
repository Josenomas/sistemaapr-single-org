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
            $emailDestino = env('MAIL_CONTACT', 'aravenanacho890@gmail.com');

            // Enviar email
            Mail::send('emails.contacto', ['datos' => $validated], function ($message) use ($validated, $emailDestino) {
                $message->to($emailDestino)
                        ->subject('Nueva Consulta desde Sistema APR - ' . $validated['apr'])
                        ->replyTo($validated['email'], $validated['nombre']);
            });

            return redirect()->route('landing')
                ->with('success', '¡Gracias por tu consulta! Te contactaremos pronto.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Hubo un error al enviar tu mensaje. Por favor intenta nuevamente.')
                ->withInput();
        }
    }
}
