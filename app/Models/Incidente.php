<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToOrganizacion;

class Incidente extends Model
{
    use BelongsToOrganizacion;

    protected $table = 'incidentes';

    protected $fillable = [
        'tipo',
        'descripcion',
        'ubicacion',
        'sector',
        'id_socio_reporta',
        'prioridad',
        'estado',
        'fecha_reporte',
        'fecha_atencion',
        'fecha_resolucion',
        'solucion',
        'observaciones',
        'id_usuario_asignado',
    ];

    protected $casts = [
        'fecha_reporte' => 'datetime',
        'fecha_atencion' => 'datetime',
        'fecha_resolucion' => 'datetime',
        'fecha_creacion' => 'datetime',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = null;

    /**
     * Relación con socio que reporta
     */
    public function socioReporta()
    {
        return $this->belongsTo(Socio::class, 'id_socio_reporta');
    }

    /**
     * Relación con usuario asignado
     */
    public function usuarioAsignado()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_asignado');
    }

    /**
     * Scope para incidentes activos
     */
    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['reportado', 'en_atencion']);
    }

    /**
     * Scope por prioridad
     */
    public function scopePorPrioridad($query, $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }

    /**
     * Scope críticos
     */
    public function scopeCriticos($query)
    {
        return $query->where('prioridad', 'critica');
    }
}
