<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Usuario;
use App\Models\Auditoria;
use App\Mail\RecuperarPasswordMail;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        if (Auth::check()) {
            // Redirigir a super-admin dashboard si es super-admin
            if (Auth::user()->esSuperAdmin()) {
                return redirect()->route('superadmin.dashboard');
            }
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

        // Generar clave para rate limiting (IP + username)
        $throttleKey = 'login_attempts:' . strtolower($request->input('username')) . '|' . $request->ip();
        $maxAttempts = 10;
        $decayMinutes = 15;
        $decaySeconds = $decayMinutes * 60;

        // Obtener intentos actuales y tiempo de expiración
        $attempts = (int) Cache::get($throttleKey . ':count', 0);
        $lockUntil = Cache::get($throttleKey . ':lock');

        // Verificar si está bloqueado
        if ($attempts >= $maxAttempts && $lockUntil && now()->lt($lockUntil)) {
            $seconds = now()->diffInSeconds($lockUntil);
            $minutes = ceil($seconds / 60);

            // Registrar intento bloqueado en auditoría (solo una vez cada 5 minutos para no spam)
            $logKey = $throttleKey . ':logged';
            if (!Cache::has($logKey)) {
                Cache::put($logKey, true, 300); // Log cada 5 minutos
                Auditoria::create([
                    'id_organizacion' => null,
                    'id_usuario' => null,
                    'modulo' => 'auth',
                    'accion' => 'login_bloqueado',
                    'descripcion' => "Intento de login bloqueado por demasiados intentos fallidos - Usuario: {$request->username}",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            throw ValidationException::withMessages([
                'username' => "Demasiados intentos de inicio de sesión. Por favor, intenta de nuevo en {$minutes} minuto(s).",
            ]);
        }

        // Buscar usuario por nombre_usuario o email
        $usuario = Usuario::where(function($query) use ($request) {
                            $query->where('nombre_usuario', $request->username)
                                  ->orWhere('email', $request->username);
                        })
                        ->where('activo', 1)
                        ->first();

        if ($usuario && Hash::check($request->password, $usuario->password)) {
            // Login exitoso - limpiar intentos fallidos
            Cache::forget($throttleKey . ':count');
            Cache::forget($throttleKey . ':lock');

            Auth::login($usuario, $request->has('remember'));

            // Actualizar último acceso
            $usuario->update(['ultimo_acceso' => now()]);

            // Registrar login en auditoría
            Auditoria::create([
                'id_organizacion' => $usuario->id_organizacion,
                'id_usuario' => $usuario->id,
                'modulo' => 'auth',
                'accion' => 'login',
                'descripcion' => "Inicio de sesión exitoso",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $request->session()->regenerate();

            // Redirigir a super-admin dashboard si es super-admin
            if ($usuario->esSuperAdmin()) {
                return redirect()->intended(route('superadmin.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        // Login fallido - incrementar contador de intentos
        // Si es el primer intento, establecer el tiempo de bloqueo
        if ($attempts === 0) {
            $lockUntil = now()->addSeconds($decaySeconds);
            Cache::put($throttleKey . ':lock', $lockUntil, $decaySeconds);
        }

        // Incrementar contador (sin cambiar el tiempo de expiración del lock)
        $newAttempts = $attempts + 1;
        Cache::put($throttleKey . ':count', $newAttempts, $decaySeconds);

        // Registrar intento fallido en auditoría
        Auditoria::create([
            'id_organizacion' => null,
            'id_usuario' => null,
            'modulo' => 'auth',
            'accion' => 'login_fallido',
            'descripcion' => "Intento de login fallido - Usuario: {$request->username} (Intento {$newAttempts}/{$maxAttempts})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->withErrors([
            'username' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('username');
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        // Si es GET, validar que venga desde la aplicación (prevenir CSRF externo)
        if ($request->isMethod('get')) {
            $referer = $request->headers->get('referer');
            $appUrl = config('app.url');

            // Solo permitir GET si viene del mismo dominio
            if (!$referer || !str_starts_with($referer, $appUrl)) {
                abort(403, 'Acceso no autorizado');
            }
        }

        $usuario = Auth::user();

        // Registrar logout en auditoría
        if ($usuario) {
            Auditoria::create([
                'id_organizacion' => $usuario->id_organizacion,
                'id_usuario' => $usuario->id,
                'modulo' => 'auth',
                'accion' => 'logout',
                'descripcion' => "Cierre de sesión",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }

    /**
     * Mostrar formulario de recuperación de contraseña
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Enviar enlace de recuperación de contraseña por email
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,email',
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email no es válido',
            'email.exists' => 'No existe un usuario con este email',
        ]);

        // Buscar usuario activo
        $usuario = Usuario::where('email', $request->email)
                         ->where('activo', 1)
                         ->first();

        if (!$usuario) {
            return back()->withErrors([
                'email' => 'El usuario con este email no está activo.',
            ])->onlyInput('email');
        }

        // Generar token único
        $token = Str::random(64);

        // Guardar token en la tabla password_resets
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        try {
            // Enviar email con el enlace de recuperación
            Mail::to($request->email)->send(new RecuperarPasswordMail($token, $request->email));

            // Registrar en auditoría
            Auditoria::create([
                'id_organizacion' => $usuario->id_organizacion,
                'id_usuario' => $usuario->id,
                'modulo' => 'auth',
                'accion' => 'recuperar_password',
                'descripcion' => "Solicitó recuperación de contraseña. Email enviado a: {$request->email}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->with('success', 'Se ha enviado un enlace de recuperación a tu email.');
        } catch (\Exception $e) {
            \Log::error('Error al enviar email de recuperación de contraseña', [
                'error' => $e->getMessage(),
                'email' => $request->email
            ]);

            return back()->withErrors([
                'email' => 'Hubo un error al enviar el email. Por favor, intenta nuevamente.',
            ])->onlyInput('email');
        }
    }

    /**
     * Mostrar formulario de cambio de contraseña
     */
    public function showResetPasswordForm($token, Request $request)
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('login')->with('error', 'Enlace inválido.');
        }

        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Procesar cambio de contraseña
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:usuarios,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email no es válido',
            'email.exists' => 'No existe un usuario con este email',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        // Verificar si existe el token
        $passwordReset = DB::table('password_resets')
                          ->where('email', $request->email)
                          ->first();

        if (!$passwordReset) {
            return back()->withErrors([
                'email' => 'Este enlace de recuperación no es válido.',
            ])->onlyInput('email');
        }

        // Verificar si el token coincide
        if (!Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors([
                'email' => 'El token de recuperación no es válido.',
            ])->onlyInput('email');
        }

        // Verificar si el token ha expirado (60 minutos)
        $created = new \DateTime($passwordReset->created_at);
        $now = new \DateTime();
        $diff = $created->diff($now);
        $minutesDiff = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;

        if ($minutesDiff > 60) {
            // Eliminar token expirado
            DB::table('password_resets')->where('email', $request->email)->delete();

            return back()->withErrors([
                'email' => 'El enlace de recuperación ha expirado. Por favor, solicita uno nuevo.',
            ])->onlyInput('email');
        }

        // Buscar usuario
        $usuario = Usuario::where('email', $request->email)
                         ->where('activo', 1)
                         ->first();

        if (!$usuario) {
            return back()->withErrors([
                'email' => 'El usuario con este email no está activo.',
            ])->onlyInput('email');
        }

        // Actualizar contraseña
        $usuario->update([
            'password' => Hash::make($request->password),
        ]);

        // Eliminar el token usado
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Registrar en auditoría
        Auditoria::create([
            'id_organizacion' => $usuario->id_organizacion,
            'id_usuario' => $usuario->id,
            'modulo' => 'auth',
            'accion' => 'cambiar_password',
            'descripcion' => "Cambió su contraseña mediante recuperación por email",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('login')->with('success', 'Contraseña actualizada exitosamente. Ahora puedes iniciar sesión.');
    }
}
