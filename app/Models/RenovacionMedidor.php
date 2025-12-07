<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RenovacionMedidor extends Model
{
    protected $table = 'renovaciones_medidores';

    protected $fillable = [
        'id_socio',
        'medidor_anterior',
        'lectura_anterior',
        'medidor_nuevo',
        'lectura_inicial',
        'fecha_renovacion',
        'motivo',
        'costo_renovacion',
        'id_tecnico',
        'observaciones',
        'estado',
        'activo',
    ];

    protected $dates = [
        'fecha_renovacion',
        'fecha_creacion',
        'fecha_actualizacion',
    ];

    protected $casts = [
        'lectura_anterior' => 'decimal:2',
        'lectura_inicial' => 'decimal:2',
        'costo_renovacion' => 'decimal:2',
        'activo' => 'boolean',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    // Relaciones
    public function socio()
    {
        return $this->belongsTo(Socio::class, 'id_socio');
    }

    public function tecnico()
    {
        return $this->belongsTo(Funcionario::class, 'id_tecnico');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePorMotivo($query, $motivo)
    {
        return $query->where('motivo', $motivo);
    }

    public function scopePorSocio($query, $idSocio)
    {
        return $query->where('id_socio', $idSocio);
    }

    public function scopePlanificados($query)
    {
        return $query->where('estado', 'planificado');
    }

    public function scopeEjecutados($query)
    {
        return $query->where('estado', 'ejecutado');
    }

    // Accessors
    public function getMotivoFormateadoAttribute()
    {
        $motivos = [
            'deterioro' => 'Deterioro',
            'falla' => 'Falla',
            'actualizacion' => 'Actualización',
            'robo' => 'Robo',
            'otro' => 'Otro',
        ];

        return $motivos[$this->motivo] ?? $this->motivo;
    }

    public function getEstadoFormateadoAttribute()
    {
        $estados = [
            'planificado' => 'Planificado',
            'ejecutado' => 'Ejecutado',
            'cancelado' => 'Cancelado',
        ];

        return $estados[$this->estado] ?? $this->estado;
    }

    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'planificado' => '<span class="badge badge-warning">Planificado</span>',
            'ejecutado' => '<span class="badge badge-success">Ejecutado</span>',
            'cancelado' => '<span class="badge badge-secondary">Cancelado</span>',
        ];

        return $badges[$this->estado] ?? $this->estado;
    }

    public function getMotivoBadgeAttribute()
    {
        $badges = [
            'deterioro' => '<span class="badge badge-warning">Deterioro</span>',
            'falla' => '<span class="badge badge-danger">Falla</span>',
            'actualizacion' => '<span class="badge badge-info">Actualización</span>',
            'robo' => '<span class="badge badge-danger">Robo</span>',
            'otro' => '<span class="badge badge-secondary">Otro</span>',
        ];

        return $badges[$this->motivo] ?? $this->motivo;
    }

    public function getLecturaAnteriorFormateadaAttribute()
    {
        if (!$this->lectura_anterior) {
            return '-';
        }
        return number_format($this->lectura_anterior, 2, ',', '.') . ' m³';
    }

    public function getLecturaInicialFormateadaAttribute()
    {
        return number_format($this->lectura_inicial, 2, ',', '.') . ' m³';
    }

    public function getCostoRenovacionFormateadoAttribute()
    {
        if (!$this->costo_renovacion) {
            return '-';
        }
        return '$' . number_format($this->costo_renovacion, 0, ',', '.');
    }

    // Métodos auxiliares
    public function marcarComoEjecutado()
    {
        $this->estado = 'ejecutado';
        $this->save();
    }

    public function cancelar()
    {
        $this->estado = 'cancelado';
        $this->save();
    }
}
