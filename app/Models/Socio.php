<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\OrganizacionScope;

class Socio extends Model
{
    protected $table = 'socios';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizacionScope);

        // Auto-asignar id_organizacion al crear
        static::creating(function ($socio) {
            if (auth()->check() && !$socio->id_organizacion) {
                $socio->id_organizacion = auth()->user()->id_organizacion;
            }
        });
    }

    protected $fillable = [
        'id_organizacion',
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
        'subsidio_porcentaje',
        'descuento_monto',
        'observaciones_subsidio',
        'numero_medidor',
        'estado',
        'fecha_ingreso',
        'observaciones',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'exento_iva' => 'boolean',
        'subsidio_porcentaje' => 'decimal:2',
        'descuento_monto' => 'decimal:2',
        'fecha_ingreso' => 'date',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    /**
     * Relación: Un socio pertenece a una organización
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'id_organizacion');
    }

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
