<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Recordatorio extends Model
{
    protected $table = 'recordatorios';

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo_recordatorio',
        'prioridad',
        'fecha_recordatorio',
        'hora_recordatorio',
        'fecha_vencimiento',
        'estado',
        'id_asignado',
        'id_relacionado',
        'tipo_relacionado',
        'ubicacion',
        'notas',
        'fecha_completado',
        'notificado',
        'activo'
    ];

    protected $casts = [
        'fecha_recordatorio' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_completado' => 'date',
        'notificado' => 'boolean',
        'activo' => 'boolean'
    ];

    // Relaciones
    public function asignado()
    {
        return $this->belongsTo(Funcionario::class, 'id_asignado');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_recordatorio', $tipo);
    }

    public function scopePorPrioridad($query, $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', 'completado');
    }

    public function scopeVencidos($query)
    {
        return $query->where('estado', 'vencido')
                     ->orWhere(function($q) {
                         $q->where('estado', 'pendiente')
                           ->where('fecha_recordatorio', '<', now());
                     });
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_recordatorio', today());
    }

    public function scopeProximos($query, $dias = 7)
    {
        return $query->whereBetween('fecha_recordatorio', [today(), today()->addDays($dias)]);
    }

    public function scopeUrgentes($query)
    {
        return $query->where('prioridad', 'urgente');
    }

    public function scopeSinNotificar($query)
    {
        return $query->where('notificado', 0)
                     ->where('estado', 'pendiente')
                     ->where('fecha_recordatorio', '<=', now());
    }

    // Accessors
    public function getFechaRecordatorioFormateadaAttribute()
    {
        return $this->fecha_recordatorio ? $this->fecha_recordatorio->format('d/m/Y') : '-';
    }

    public function getFechaVencimientoFormateadaAttribute()
    {
        return $this->fecha_vencimiento ? $this->fecha_vencimiento->format('d/m/Y') : '-';
    }

    public function getFechaCompletadoFormateadaAttribute()
    {
        return $this->fecha_completado ? $this->fecha_completado->format('d/m/Y') : '-';
    }

    public function getFechaCreacionFormateadaAttribute()
    {
        return $this->fecha_creacion ? $this->fecha_creacion->format('d/m/Y H:i') : '-';
    }

    public function getFechaActualizacionFormateadaAttribute()
    {
        return $this->fecha_actualizacion ? $this->fecha_actualizacion->format('d/m/Y H:i') : '-';
    }

    public function getHoraRecordatorioFormateadaAttribute()
    {
        if (!$this->hora_recordatorio) return '-';
        return date('H:i', strtotime($this->hora_recordatorio));
    }

    public function getTipoRecordatorioBadgeAttribute()
    {
        $tipos = [
            'reunion' => '<span class="badge badge-primary">Reunión</span>',
            'pago' => '<span class="badge badge-success">Pago</span>',
            'mantenimiento' => '<span class="badge badge-warning">Mantenimiento</span>',
            'inspeccion' => '<span class="badge badge-info">Inspección</span>',
            'vencimiento' => '<span class="badge badge-danger">Vencimiento</span>',
            'llamada' => '<span class="badge badge-secondary">Llamada</span>',
            'tarea' => '<span class="badge badge-primary">Tarea</span>',
            'otro' => '<span class="badge badge-dark">Otro</span>'
        ];

        return $tipos[$this->tipo_recordatorio] ?? '<span class="badge badge-secondary">' . ucfirst($this->tipo_recordatorio) . '</span>';
    }

    public function getPrioridadBadgeAttribute()
    {
        $prioridades = [
            'baja' => '<span class="badge badge-secondary">Baja</span>',
            'media' => '<span class="badge badge-info">Media</span>',
            'alta' => '<span class="badge badge-warning">Alta</span>',
            'urgente' => '<span class="badge badge-danger">Urgente</span>'
        ];

        return $prioridades[$this->prioridad] ?? '<span class="badge badge-secondary">' . ucfirst($this->prioridad) . '</span>';
    }

    public function getEstadoBadgeAttribute()
    {
        $estados = [
            'pendiente' => '<span class="badge badge-warning">Pendiente</span>',
            'completado' => '<span class="badge badge-success">Completado</span>',
            'cancelado' => '<span class="badge badge-secondary">Cancelado</span>',
            'vencido' => '<span class="badge badge-danger">Vencido</span>'
        ];

        return $estados[$this->estado] ?? '<span class="badge badge-secondary">' . ucfirst($this->estado) . '</span>';
    }

    public function getTipoRecordatorioTextoAttribute()
    {
        $tipos = [
            'reunion' => 'Reunión',
            'pago' => 'Pago',
            'mantenimiento' => 'Mantenimiento',
            'inspeccion' => 'Inspección',
            'vencimiento' => 'Vencimiento',
            'llamada' => 'Llamada',
            'tarea' => 'Tarea',
            'otro' => 'Otro'
        ];

        return $tipos[$this->tipo_recordatorio] ?? ucfirst($this->tipo_recordatorio);
    }

    public function getPrioridadTextoAttribute()
    {
        $prioridades = [
            'baja' => 'Baja',
            'media' => 'Media',
            'alta' => 'Alta',
            'urgente' => 'Urgente'
        ];

        return $prioridades[$this->prioridad] ?? ucfirst($this->prioridad);
    }

    public function getEstadoTextoAttribute()
    {
        $estados = [
            'pendiente' => 'Pendiente',
            'completado' => 'Completado',
            'cancelado' => 'Cancelado',
            'vencido' => 'Vencido'
        ];

        return $estados[$this->estado] ?? ucfirst($this->estado);
    }

    public function getDiasRestantesAttribute()
    {
        if (!$this->fecha_recordatorio || $this->estado !== 'pendiente') {
            return null;
        }

        $dias = today()->diffInDays($this->fecha_recordatorio, false);

        if ($dias < 0) {
            return 'Vencido hace ' . abs($dias) . ' día(s)';
        } elseif ($dias == 0) {
            return 'Hoy';
        } elseif ($dias == 1) {
            return 'Mañana';
        } else {
            return 'En ' . $dias . ' día(s)';
        }
    }

    public function getDiasRestantesColorAttribute()
    {
        if (!$this->fecha_recordatorio || $this->estado !== 'pendiente') {
            return '';
        }

        $dias = today()->diffInDays($this->fecha_recordatorio, false);

        if ($dias < 0) {
            return 'text-danger';
        } elseif ($dias == 0) {
            return 'text-warning';
        } elseif ($dias <= 3) {
            return 'text-warning';
        } else {
            return 'text-success';
        }
    }

    // Métodos auxiliares
    public function estaVencido()
    {
        if ($this->estado !== 'pendiente') {
            return false;
        }

        return $this->fecha_recordatorio < today();
    }

    public function esHoy()
    {
        return $this->fecha_recordatorio && $this->fecha_recordatorio->isToday();
    }

    public function estaProximo($dias = 3)
    {
        if (!$this->fecha_recordatorio || $this->estado !== 'pendiente') {
            return false;
        }

        return $this->fecha_recordatorio->between(today(), today()->addDays($dias));
    }

    public function marcarComoCompletado()
    {
        $this->estado = 'completado';
        $this->fecha_completado = now();
        $this->save();
    }

    public function marcarComoVencido()
    {
        if ($this->estado === 'pendiente' && $this->estaVencido()) {
            $this->estado = 'vencido';
            $this->save();
        }
    }
}
