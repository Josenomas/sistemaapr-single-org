<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistroOrganizacion;
use App\Models\Organizacion;
use App\Models\Usuario;
use App\Models\Suscripcion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificacionEmail;

class RegistroController extends Controller
{
    /**
     * Mostrar formulario de registro
     */
    public function mostrarFormulario()
    {
        $planes = Suscripcion::orderBy('precio_mensual')->get();
        return view('registro.formulario', compact('planes'));
    }

    /**
     * Procesar registro y enviar email de verificación
     */
    public function registrar(Request $request)
    {
        $validated = $request->validate([
            // Datos de la organización
            'nombre_apr' => 'required|string|max:255',
            'rut' => 'required|string|max:12|unique:organizaciones,rut|unique:registros_organizacion,rut',
            'direccion' => 'nullable|string|max:255',
            'comuna' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email_contacto' => 'required|email|max:255|unique:organizaciones,email_contacto|unique:registros_organizacion,email_contacto',

            // Datos del administrador
            'admin_nombre' => 'required|string|max:100',
            'admin_apellido' => 'required|string|max:100',
            'admin_email' => 'required|email|max:255|unique:usuarios,email|unique:registros_organizacion,admin_email',
            'admin_telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',

            // Plan deseado (opcional)
            'id_suscripcion' => 'nullable|exists:suscripciones,id',

            // Aceptación de términos
            'acepta_terminos' => 'required|accepted',
        ], [
            'rut.unique' => 'Ya existe una organización registrada con este RUT.',
            'email_contacto.unique' => 'Este email ya está registrado.',
            'admin_email.unique' => 'Este email ya está en uso.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'acepta_terminos.accepted' => 'Debes aceptar los términos y condiciones.',
        ]);

        try {
            DB::beginTransaction();

            // Generar slug único
            $slug = Str::slug($validated['nombre_apr']);
            $slugOriginal = $slug;
            $contador = 1;
            while (Organizacion::where('slug', $slug)->exists() || RegistroOrganizacion::where('slug', $slug)->exists()) {
                $slug = $slugOriginal . '-' . $contador;
                $contador++;
            }

            // Crear registro pendiente
            $registro = RegistroOrganizacion::create([
                'nombre_apr' => $validated['nombre_apr'],
                'slug' => $slug,
                'rut' => $validated['rut'],
                'direccion' => $validated['direccion'] ?? null,
                'comuna' => $validated['comuna'] ?? null,
                'region' => $validated['region'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'email_contacto' => $validated['email_contacto'],
                'admin_nombre' => $validated['admin_nombre'],
                'admin_apellido' => $validated['admin_apellido'],
                'admin_email' => $validated['admin_email'],
                'admin_telefono' => $validated['admin_telefono'] ?? null,
                'password' => Hash::make($validated['password']),
                'token_verificacion' => RegistroOrganizacion::generarToken(),
                'estado' => 'pendiente',
                'expira_en' => now()->addHours(48), // Expira en 48 horas
                'ip_registro' => $request->ip(),
                'id_suscripcion_deseada' => $validated['id_suscripcion'] ?? Suscripcion::where('nombre', 'basico')->first()->id,
            ]);

            // Enviar email de verificación
            Mail::to($registro->admin_email)->send(new VerificacionEmail($registro));

            DB::commit();

            return redirect()->route('registro.confirmacion')
                ->with('success', '¡Registro exitoso! Hemos enviado un correo de verificación a ' . $registro->admin_email);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error en registro de organización: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al procesar el registro. Por favor, intenta nuevamente.');
        }
    }

    /**
     * Verificar email y activar cuenta
     */
    public function verificarEmail($token)
    {
        $registro = RegistroOrganizacion::where('token_verificacion', $token)
            ->where('estado', 'pendiente')
            ->first();

        if (!$registro) {
            return redirect()->route('login')
                ->with('error', 'El enlace de verificación no es válido o ya fue utilizado.');
        }

        if ($registro->haExpirado()) {
            return redirect()->route('login')
                ->with('error', 'El enlace de verificación ha expirado. Por favor, regístrate nuevamente.');
        }

        try {
            DB::beginTransaction();

            // Marcar como verificado
            $registro->marcarComoVerificado();

            // Crear la organización
            $organizacion = $this->crearOrganizacion($registro);

            // Crear el usuario administrador
            $usuario = $this->crearUsuarioAdmin($registro, $organizacion);

            // Autenticar automáticamente
            auth()->login($usuario);

            DB::commit();

            return redirect()->route('onboarding.bienvenida')
                ->with('success', '¡Cuenta activada exitosamente! Bienvenido a Sistema APR.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al activar cuenta: ' . $e->getMessage());

            return redirect()->route('login')
                ->with('error', 'Ocurrió un error al activar tu cuenta. Por favor, contacta a soporte.');
        }
    }

    /**
     * Crear organización desde registro
     */
    private function crearOrganizacion($registro)
    {
        return Organizacion::create([
            'nombre_apr' => $registro->nombre_apr,
            'slug' => $registro->slug,
            'rut' => $registro->rut,
            'direccion' => $registro->direccion,
            'telefono' => $registro->telefono,
            'email_contacto' => $registro->email_contacto,
            'id_suscripcion' => $registro->id_suscripcion_deseada,
            'estado_suscripcion' => 'prueba',
            'dias_prueba_restantes' => 30,
            'fecha_inicio_suscripcion' => now(),
            'activo' => true,
        ]);
    }

    /**
     * Crear usuario administrador
     */
    private function crearUsuarioAdmin($registro, $organizacion)
    {
        return Usuario::create([
            'nombre_usuario' => Str::slug($registro->admin_nombre . '-' . $registro->admin_apellido),
            'password' => $registro->password, // Ya está hasheada
            'nombre' => $registro->admin_nombre,
            'apellido' => $registro->admin_apellido,
            'email' => $registro->admin_email,
            'telefono' => $registro->admin_telefono,
            'rol' => 'admin',
            'id_organizacion' => $organizacion->id,
            'activo' => true,
        ]);
    }

    /**
     * Página de confirmación de registro
     */
    public function confirmacion()
    {
        return view('registro.confirmacion');
    }
}
