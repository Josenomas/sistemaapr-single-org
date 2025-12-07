<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Socio extends Model
{
    protected $table = 'socios';

    protected $fillable = [
        'numero_socio',
        'rut',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'direccion',
        'sector',
        'telefono',
        'email',
        'tipo_cliente',
        'exento_iva',
        'numero_medidor',
        'estado',
        'fecha_ingreso',
        'observaciones',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'exento_iva' => 'boolean',
        'fecha_ingreso' => 'date',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    /**
     * Relación con lecturas
     */
    public function lecturas()
    {
        return $this->hasMany(Lectura::class, 'id_socio');
    }

    /**
     * Relación con boletas
     */
    public function boletas()
    {
        return $this->hasMany(Boleta::class, 'id_socio');
    }

    /**
     * Relación con pagos
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_socio');
    }

    /**
     * Relación con incidentes reportados
     */
    public function incidentes()
    {
        return $this->hasMany(Incidente::class, 'id_socio_reporta');
    }

    /**
     * Obtener nombre completo
     */
    public function getNombreCompletoAttribute()
    {
        return trim("{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}");
    }

    /**
     * Scope para socios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', 1)->where('estado', 'activo');
    }

    /**
     * Scope para socios morosos
     */
    public function scopeMorosos($query)
    {
        return $query->where('estado', 'moroso');
    }

    /**
     * Scope por sector
     */
    public function scopePorSector($query, $sector)
    {
        return $query->where('sector', $sector);
    }
}
