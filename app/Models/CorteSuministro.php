<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorteSuministro extends Model
{
    protected $table = 'cortes_suministro';

    protected $fillable = [
        'id_socio',
        'motivo',
        'descripcion',
        'fecha_corte',
        'fecha_reconexion',
        'estado',
        'monto_adeudado',
        'monto_reconexion',
        'id_ejecutor',
        'observaciones',
        'activo',
    ];

    protected $dates = [
        'fecha_corte',
        'fecha_reconexion',
        'fecha_creacion',
        'fecha_actualizacion',
    ];

    protected $casts = [
        'monto_adeudado' => 'decimal:2',
        'monto_reconexion' => 'decimal:2',
        'activo' => 'boolean',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    // Relaciones
    public function socio()
    {
        return $this->belongsTo(Socio::class, 'id_socio');
    }

    public function ejecutor()
    {
        return $this->belongsTo(Funcionario::class, 'id_ejecutor');
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

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeEjecutados($query)
    {
        return $query->where('estado', 'ejecutado');
    }

    public function scopeReconectados($query)
    {
        return $query->where('estado', 'reconectado');
    }

    // Accessors
    public function getMotivoFormateadoAttribute()
    {
        $motivos = [
            'morosidad' => 'Morosidad',
            'solicitud_socio' => 'Solicitud del Socio',
            'mantenimiento' => 'Mantenimiento',
            'otro' => 'Otro',
        ];

        return $motivos[$this->motivo] ?? $this->motivo;
    }

    public function getEstadoFormateadoAttribute()
    {
        $estados = [
            'pendiente' => 'Pendiente',
            'ejecutado' => 'Ejecutado',
            'reconectado' => 'Reconectado',
            'cancelado' => 'Cancelado',
        ];

        return $estados[$this->estado] ?? $this->estado;
    }

    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'pendiente' => '<span class="badge badge-warning">Pendiente</span>',
            'ejecutado' => '<span class="badge badge-danger">Ejecutado</span>',
            'reconectado' => '<span class="badge badge-success">Reconectado</span>',
            'cancelado' => '<span class="badge badge-secondary">Cancelado</span>',
        ];

        return $badges[$this->estado] ?? $this->estado;
    }

    public function getMotivoBadgeAttribute()
    {
        $badges = [
            'morosidad' => '<span class="badge badge-danger">Morosidad</span>',
            'solicitud_socio' => '<span class="badge badge-info">Solicitud del Socio</span>',
            'mantenimiento' => '<span class="badge badge-warning">Mantenimiento</span>',
            'otro' => '<span class="badge badge-secondary">Otro</span>',
        ];

        return $badges[$this->motivo] ?? $this->motivo;
    }

    public function getMontoAdeudadoFormateadoAttribute()
    {
        if (!$this->monto_adeudado) {
            return '-';
        }
        return '$' . number_format($this->monto_adeudado, 0, ',', '.');
    }

    public function getMontoReconexionFormateadoAttribute()
    {
        if (!$this->monto_reconexion) {
            return '-';
        }
        return '$' . number_format($this->monto_reconexion, 0, ',', '.');
    }

    public function getDiasCorteAttribute()
    {
        if (!$this->fecha_reconexion) {
            // Si no hay reconexión, calcular desde fecha de corte hasta hoy
            if ($this->estado === 'ejecutado') {
                return $this->fecha_corte->diffInDays(now());
            }
            return null;
        }
        return $this->fecha_corte->diffInDays($this->fecha_reconexion);
    }

    // Métodos auxiliares
    public function marcarComoEjecutado()
    {
        $this->estado = 'ejecutado';
        $this->save();
    }

    public function marcarComoReconectado($fecha = null, $monto = null)
    {
        $this->estado = 'reconectado';
        $this->fecha_reconexion = $fecha ?? now();
        if ($monto) {
            $this->monto_reconexion = $monto;
        }
        $this->save();
    }

    public function cancelar()
    {
        $this->estado = 'cancelado';
        $this->save();
    }
}
