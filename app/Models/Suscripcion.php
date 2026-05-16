<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    use HasFactory;

    protected $table = 'suscripciones';

    protected $fillable = [
        'nombre',
        'nombre_mostrar',
        'precio_mensual',
        'max_socios',
        'max_usuarios',
        'modulos_permitidos',
        'features',
        'permite_dominio_personalizado',
        'permite_modulo_noticias',
        'activo',
    ];

    protected $casts = [
        'precio_mensual' => 'decimal:2',
        'max_socios' => 'integer',
        'max_usuarios' => 'integer',
        'modulos_permitidos' => 'array',
        'features' => 'array',
        'permite_dominio_personalizado' => 'boolean',
        'permite_modulo_noticias' => 'boolean',
        'activo' => 'boolean',
    ];

    /**
     * Relación: Una suscripción tiene muchas organizaciones
     */
    public function organizaciones()
    {
        return $this->hasMany(Organizacion::class, 'id_suscripcion');
    }

    /**
     * Verifica si el módulo está permitido en el plan
     */
    public function permiteModulo($modulo)
    {
        return in_array($modulo, $this->modulos_permitidos);
    }

    /**
     * Verifica si tiene socios ilimitados
     */
    public function tieneSociosIlimitados()
    {
        return $this->max_socios === null;
    }

    /**
     * Verifica si tiene usuarios ilimitados
     */
    public function tieneUsuariosIlimitados()
    {
        return $this->max_usuarios === null;
    }

    /**
     * Scope para planes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Accessor para compatibilidad: limite_socios -> max_socios
     */
    public function getLimiteSociosAttribute()
    {
        return $this->max_socios;
    }

    /**
     * Accessor para compatibilidad: limite_usuarios -> max_usuarios
     */
    public function getLimiteUsuariosAttribute()
    {
        return $this->max_usuarios;
    }

    /**
     * Accessor para compatibilidad: socios_ilimitados
     */
    public function getSociosIlimitadosAttribute()
    {
        return $this->tieneSociosIlimitados();
    }

    /**
     * Accessor para compatibilidad: usuarios_ilimitados
     */
    public function getUsuariosIlimitadosAttribute()
    {
        return $this->tieneUsuariosIlimitados();
    }
}
