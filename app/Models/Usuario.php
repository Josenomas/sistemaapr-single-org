<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre_usuario',
        'password',
        'nombre',
        'apellido',
        'email',
        'telefono',
        'rol',
        'permisos',
        'activo',
        'ultimo_acceso',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    // Deshabilitar timestamps de Laravel (usamos los nuestros)
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function tienePermiso($permiso)
    {
        // El administrador tiene todos los permisos
        if ($this->rol === 'Administrador') {
            return true;
        }

        $permisos_por_rol = [
            'Administrador' => ['*'],
            'Operador' => [
                'socios.*',
                'lecturas.*',
                'boletas.*',
                'pagos.*',
                'incidentes.*',
            ],
            'Tesorero' => [
                'socios.ver',
                'boletas.*',
                'pagos.*',
                'reportes.*',
            ],
            'Secretario' => [
                'socios.*',
                'lecturas.ver',
                'boletas.ver',
                'incidentes.*',
            ],
            'usuario' => [
                'socios.ver',
                'lecturas.ver',
            ],
        ];

        $permisos = $permisos_por_rol[$this->rol] ?? [];

        if (in_array('*', $permisos)) {
            return true;
        }

        if (in_array($permiso, $permisos)) {
            return true;
        }

        list($modulo, $accion) = explode('.', $permiso);
        if (in_array($modulo . '.*', $permisos)) {
            return true;
        }

        return false;
    }

    /**
     * Verificar si es administrador
     */
    public function esAdministrador()
    {
        return $this->rol === 'Administrador';
    }

    /**
     * Obtener nombre completo
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }

    /**
     * Obtener iniciales
     */
    public function getInicialesAttribute()
    {
        return strtoupper(substr($this->nombre, 0, 1) . substr($this->apellido, 0, 1));
    }
}
