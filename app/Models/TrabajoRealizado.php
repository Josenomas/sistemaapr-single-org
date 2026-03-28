<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToOrganizacion;

class TrabajoRealizado extends Model
{
    use BelongsToOrganizacion;

    protected $table = 'trabajos_realizados';

    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo_trabajo',
        'ubicacion',
        'fecha_inicio',
        'fecha_termino',
        'estado',
        'prioridad',
        'costo_estimado',
        'costo_real',
        'id_responsable',
        'materiales_utilizados',
        'observaciones',
        'activo',
    ];

    protected $dates = [
        'fecha_inicio',
        'fecha_termino',
        'fecha_creacion',
        'fecha_actualizacion',
    ];

    protected $casts = [
        'costo_estimado' => 'decimal:2',
        'costo_real' => 'decimal:2',
        'activo' => 'boolean',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    // Relaciones
    public function responsable()
    {
        return $this->belongsTo(Funcionario::class, 'id_responsable');
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

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_trabajo', $tipo);
    }

    public function scopePorPrioridad($query, $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }

    public function scopePlanificados($query)
    {
        return $query->where('estado', 'planificado');
    }

    public function scopeEnProceso($query)
    {
        return $query->where('estado', 'en_proceso');
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', 'completado');
    }

    public function scopeUrgentes($query)
    {
        return $query->where('prioridad', 'urgente');
    }

    // Accessors
    public function getTipoTrabajoFormateadoAttribute()
    {
        $tipos = [
            'mantenimiento' => 'Mantenimiento',
            'reparacion' => 'Reparación',
            'instalacion' => 'Instalación',
            'inspeccion' => 'Inspección',
            'otro' => 'Otro',
        ];

        return $tipos[$this->tipo_trabajo] ?? $this->tipo_trabajo;
    }

    public function getEstadoFormateadoAttribute()
    {
        $estados = [
            'planificado' => 'Planificado',
            'en_proceso' => 'En Proceso',
            'completado' => 'Completado',
            'cancelado' => 'Cancelado',
        ];

        return $estados[$this->estado] ?? $this->estado;
    }

    public function getPrioridadFormateadaAttribute()
    {
        $prioridades = [
            'baja' => 'Baja',
            'media' => 'Media',
            'alta' => 'Alta',
            'urgente' => 'Urgente',
        ];

        return $prioridades[$this->prioridad] ?? $this->prioridad;
    }

    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'planificado' => '<span class="badge badge-info">Planificado</span>',
            'en_proceso' => '<span class="badge badge-warning">En Proceso</span>',
            'completado' => '<span class="badge badge-success">Completado</span>',
            'cancelado' => '<span class="badge badge-secondary">Cancelado</span>',
        ];

        return $badges[$this->estado] ?? $this->estado;
    }

    public function getPrioridadBadgeAttribute()
    {
        $badges = [
            'baja' => '<span class="badge badge-info">Baja</span>',
            'media' => '<span class="badge badge-warning">Media</span>',
            'alta' => '<span class="badge badge-danger">Alta</span>',
            'urgente' => '<span class="badge badge-danger" style="animation: pulse 2s infinite;">Urgente</span>',
        ];

        return $badges[$this->prioridad] ?? $this->prioridad;
    }

    public function getTipoBadgeAttribute()
    {
        $badges = [
            'mantenimiento' => '<span class="badge badge-info">Mantenimiento</span>',
            'reparacion' => '<span class="badge badge-warning">Reparación</span>',
            'instalacion' => '<span class="badge badge-success">Instalación</span>',
            'inspeccion' => '<span class="badge badge-secondary">Inspección</span>',
            'otro' => '<span class="badge badge-secondary">Otro</span>',
        ];

        return $badges[$this->tipo_trabajo] ?? $this->tipo_trabajo;
    }

    public function getCostoEstimadoFormateadoAttribute()
    {
        if (!$this->costo_estimado) {
            return '-';
        }
        return '$' . number_format($this->costo_estimado, 0, ',', '.');
    }

    public function getCostoRealFormateadoAttribute()
    {
        if (!$this->costo_real) {
            return '-';
        }
        return '$' . number_format($this->costo_real, 0, ',', '.');
    }

    public function getDuracionDiasAttribute()
    {
        if (!$this->fecha_termino) {
            // Si no ha terminado, calcular desde fecha inicio hasta hoy
            if ($this->estado === 'en_proceso') {
                return $this->fecha_inicio->diffInDays(now());
            }
            return null;
        }
        return $this->fecha_inicio->diffInDays($this->fecha_termino);
    }

    public function getDiferenciaPresupuestoAttribute()
    {
        if (!$this->costo_estimado || !$this->costo_real) {
            return null;
        }
        return $this->costo_real - $this->costo_estimado;
    }

    public function getDiferenciaPresupuestoFormateadaAttribute()
    {
        $diferencia = $this->diferencia_presupuesto;
        if ($diferencia === null) {
            return '-';
        }

        $signo = $diferencia >= 0 ? '+' : '';
        return $signo . '$' . number_format($diferencia, 0, ',', '.');
    }

    // Métodos auxiliares
    public function iniciar()
    {
        $this->estado = 'en_proceso';
        $this->save();
    }

    public function completar($costoReal = null)
    {
        $this->estado = 'completado';
        $this->fecha_termino = now();
        if ($costoReal) {
            $this->costo_real = $costoReal;
        }
        $this->save();
    }

    public function cancelar()
    {
        $this->estado = 'cancelado';
        $this->save();
    }
}
