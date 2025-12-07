<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Helpers\ActividadHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuariosController extends Controller
{
    /**
     * Listar todos los usuarios
     */
    public function index(Request $request)
    {
        $query = Usuario::query();

        // Filtrar por rol si se proporciona
        if ($request->has('rol') && $request->rol) {
            $query->where('rol', $request->rol);
        }

        // Filtrar por estado
        if ($request->has('estado') && $request->estado !== '') {
            $query->where('activo', $request->estado);
        }

        $usuarios = $query->orderBy('nombre_usuario')->paginate(20);

        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('usuarios.create');
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_usuario' => 'required|string|max:50|unique:usuarios,nombre_usuario',
            'email' => 'nullable|email|max:100|unique:usuarios,email',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'password' => 'required|string|min:6|confirmed',
            'rol' => 'required|in:admin,tesorero,operador,lecturista',
            'activo' => 'required|boolean',
            'permisos' => 'nullable|array',
            'permisos.*' => 'string',
        ]);

        // Encriptar contraseña
        $validated['password'] = Hash::make($validated['password']);

        // Convertir permisos a JSON
        $validated['permisos'] = json_encode($request->permisos ?? []);

        $usuario = Usuario::create($validated);

        // Registrar actividad
        ActividadHelper::registrar(
            'Usuarios',
            "Nuevo usuario creado: {$usuario->nombre_usuario} - {$usuario->nombre_completo} (Rol: " . ucfirst($usuario->rol) . ")",
            auth()->id()
        );

        return redirect()->route('usuarios.index')
                        ->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Mostrar detalle de usuario
     */
    public function show($id)
    {
        $usuario = Usuario::findOrFail($id);
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $validated = $request->validate([
            'nombre_usuario' => [
                'required',
                'string',
                'max:50',
                Rule::unique('usuarios')->ignore($usuario->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('usuarios')->ignore($usuario->id),
            ],
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'password' => 'nullable|string|min:6|confirmed',
            'rol' => 'required|in:admin,tesorero,operador,lecturista',
            'activo' => 'required|boolean',
            'permisos' => 'nullable|array',
            'permisos.*' => 'string',
        ]);

        // Capturar cambios antes de actualizar
        $cambios = [];
        $camposTraducidos = [
            'nombre_usuario' => 'Usuario',
            'email' => 'Email',
            'nombre' => 'Nombre',
            'apellido' => 'Apellido',
            'rol' => 'Rol',
            'activo' => 'Estado',
            'permisos' => 'Permisos',
        ];

        // Comparar valores antes de actualizar
        foreach ($validated as $campo => $valorNuevo) {
            if ($campo == 'password') continue; // No mostrar contraseñas

            $valorAnterior = $usuario->$campo;

            // Manejar comparación de permisos
            if ($campo == 'permisos') {
                $permisosAnteriores = $usuario->permisos ? json_decode($usuario->permisos, true) : [];
                $permisosNuevos = $request->permisos ?? [];

                if (json_encode($permisosAnteriores) != json_encode($permisosNuevos)) {
                    $valorAnterior = !empty($permisosAnteriores) ? implode(', ', $permisosAnteriores) : 'ninguno';
                    $valorNuevo = !empty($permisosNuevos) ? implode(', ', $permisosNuevos) : 'ninguno';
                    $cambios[] = "Permisos: '{$valorAnterior}' → '{$valorNuevo}'";
                }
                continue;
            }

            if ($valorAnterior != $valorNuevo) {
                $nombreCampo = $camposTraducidos[$campo] ?? $campo;

                // Formatear valores según el tipo
                if ($campo == 'activo') {
                    $valorAnterior = $valorAnterior ? 'Activo' : 'Inactivo';
                    $valorNuevo = $valorNuevo ? 'Activo' : 'Inactivo';
                } elseif ($campo == 'rol') {
                    $valorAnterior = ucfirst($valorAnterior);
                    $valorNuevo = ucfirst($valorNuevo);
                }

                $cambios[] = "{$nombreCampo}: '{$valorAnterior}' → '{$valorNuevo}'";
            }
        }

        // Solo actualizar contraseña si se proporcionó
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
            $cambios[] = "Contraseña: actualizada";
        } else {
            unset($validated['password']);
        }

        // Convertir permisos a JSON
        $validated['permisos'] = json_encode($request->permisos ?? []);

        $usuario->update($validated);

        // Registrar actividad con cambios
        if (!empty($cambios)) {
            $descripcionCambios = implode(', ', $cambios);
            ActividadHelper::registrar(
                'Usuarios',
                "Usuario actualizado: {$usuario->nombre_usuario} - {$usuario->nombre_completo}. Cambios: {$descripcionCambios}",
                auth()->id()
            );
        } else {
            ActividadHelper::registrar(
                'Usuarios',
                "Usuario actualizado: {$usuario->nombre_usuario} - {$usuario->nombre_completo} (Rol: " . ucfirst($usuario->rol) . ")",
                auth()->id()
            );
        }

        return redirect()->route('usuarios.show', $id)
                        ->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Eliminar usuario
     */
    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);

        // No permitir eliminar el propio usuario
        if ($usuario->id == auth()->id()) {
            return redirect()->route('usuarios.index')
                           ->with('error', 'No puedes eliminar tu propio usuario');
        }

        // Guardar información antes de eliminar
        $nombreUsuario = $usuario->nombre_usuario;
        $nombreCompleto = $usuario->nombre_completo;

        $usuario->delete();

        // Registrar actividad
        ActividadHelper::registrar(
            'Usuarios',
            "Usuario eliminado: {$nombreUsuario} - {$nombreCompleto}",
            auth()->id()
        );

        return redirect()->route('usuarios.index')
                        ->with('success', 'Usuario eliminado exitosamente');
    }
}
