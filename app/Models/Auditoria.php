<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    use HasFactory;

    protected $table = 'auditoria';

    protected $fillable = [
        'id_organizacion',
        'id_usuario',
        'modulo',
        'accion',
        'tabla_afectada',
        'id_registro',
        'descripcion',
        'datos_anteriores',
        'datos_nuevos',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
    ];

    /**
     * Relación con Organización
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'id_organizacion');
    }

    /**
     * Relación con Usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    /**
     * Registrar acción en auditoría
     */
    public static function registrar($modulo, $accion, $descripcion, $tablaAfectada = null, $idRegistro = null, $datosAnteriores = null, $datosNuevos = null)
    {
        $usuario = auth()->user();

        return static::create([
            'id_organizacion' => $usuario->id_organizacion ?? null,
            'id_usuario' => $usuario->id ?? null,
            'modulo' => $modulo,
            'accion' => $accion,
            'tabla_afectada' => $tablaAfectada,
            'id_registro' => $idRegistro,
            'descripcion' => $descripcion,
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos' => $datosNuevos,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Obtener icono según el módulo
     */
    public function getIconoAttribute()
    {
        return match($this->modulo) {
            'socios' => 'fa-users',
            'boletas' => 'fa-file-invoice',
            'pagos' => 'fa-dollar-sign',
            'lecturas' => 'fa-tachometer-alt',
            'usuarios' => 'fa-user',
            'suscripcion' => 'fa-crown',
            'organizacion' => 'fa-building',
            'sistema' => 'fa-cog',
            'auth' => 'fa-sign-in-alt',
            default => 'fa-circle',
        };
    }

    /**
     * Obtener color según la acción
     */
    public function getColorAccionAttribute()
    {
        return match($this->accion) {
            'crear' => 'success',
            'editar', 'actualizar' => 'info',
            'eliminar' => 'danger',
            'login' => 'primary',
            'logout' => 'secondary',
            default => 'dark',
        };
    }
}
