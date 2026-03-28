<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'id_organizacion',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Usar timestamps estándar de Laravel
    public $timestamps = true;

    /**
     * Relación: Un usuario pertenece a una organización
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'id_organizacion');
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function tienePermiso($permiso)
    {
        // El super-admin y admin tienen todos los permisos
        if ($this->esSuperAdmin() || $this->esAdministrador()) {
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
     * Verificar si es super-administrador
     */
    public function esSuperAdmin()
    {
        return $this->rol === 'superadmin';
    }

    /**
     * Verificar si es administrador
     */
    public function esAdministrador()
    {
        return $this->rol === 'admin';
    }

    /**
     * Verificar si es administrador (admin o superadmin)
     */
    public function esAdminOSuperAdmin()
    {
        return in_array($this->rol, ['admin', 'superadmin']);
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
