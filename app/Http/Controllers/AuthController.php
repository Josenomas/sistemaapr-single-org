<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'El usuario o email es obligatorio',
            'password.required' => 'La contraseña es obligatoria',
        ]);

        // Buscar usuario por nombre_usuario o email
        $usuario = Usuario::where(function($query) use ($request) {
                            $query->where('nombre_usuario', $request->username)
                                  ->orWhere('email', $request->username);
                        })
                        ->where('activo', 1)
                        ->first();

        if ($usuario && Hash::check($request->password, $usuario->password)) {
            Auth::login($usuario, $request->has('remember'));

            // Actualizar último acceso
            $usuario->update(['ultimo_acceso' => now()]);

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'username' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('username');
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
