<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RegistroOrganizacion extends Model
{
    protected $table = 'registros_organizacion';

    protected $fillable = [
        'nombre_apr',
        'slug',
        'rut',
        'direccion',
        'comuna',
        'region',
        'telefono',
        'email_contacto',
        'admin_nombre',
        'admin_apellido',
        'admin_email',
        'admin_telefono',
        'password',
        'token_verificacion',
        'estado',
        'email_verificado_at',
        'expira_en',
        'ip_registro',
        'id_suscripcion_deseada',
        'notas',
    ];

    protected $casts = [
        'email_verificado_at' => 'datetime',
        'expira_en' => 'datetime',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Generar token de verificación único
     */
    public static function generarToken()
    {
        do {
            $token = Str::random(64);
        } while (self::where('token_verificacion', $token)->exists());

        return $token;
    }

    /**
     * Verificar si el registro ha expirado
     */
    public function haExpirado()
    {
        if (!$this->expira_en) {
            return false;
        }

        return now()->isAfter($this->expira_en);
    }

    /**
     * Marcar como verificado
     */
    public function marcarComoVerificado()
    {
        $this->update([
            'estado' => 'verificado',
            'email_verificado_at' => now(),
        ]);
    }

    /**
     * Relación con suscripción deseada
     */
    public function suscripcionDeseada()
    {
        return $this->belongsTo(Suscripcion::class, 'id_suscripcion_deseada');
    }

    /**
     * Scope para registros pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para registros verificados
     */
    public function scopeVerificados($query)
    {
        return $query->where('estado', 'verificado');
    }

    /**
     * Scope para registros no expirados
     */
    public function scopeNoExpirados($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expira_en')
              ->orWhere('expira_en', '>', now());
        });
    }
}
