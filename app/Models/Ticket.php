<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'numero_ticket',
        'id_socio',
        'titulo',
        'descripcion',
        'tipo_ticket',
        'prioridad',
        'estado',
        'id_asignado',
        'fecha_reporte',
        'fecha_asignacion',
        'fecha_resolucion',
        'fecha_cierre',
        'tiempo_respuesta',
        'tiempo_resolucion',
        'solucion',
        'costo_reparacion',
        'satisfaccion',
        'comentario_cierre',
        'ubicacion',
        'contacto_nombre',
        'contacto_telefono',
        'contacto_email',
        'observaciones',
        'activo'
    ];

    protected $casts = [
        'fecha_reporte' => 'date',
        'fecha_asignacion' => 'date',
        'fecha_resolucion' => 'date',
        'fecha_cierre' => 'date',
        'costo_reparacion' => 'decimal:2',
        'activo' => 'boolean'
    ];

    // Relaciones
    public function socio()
    {
        return $this->belongsTo(Socio::class, 'id_socio');
    }

    public function asignado()
    {
        return $this->belongsTo(Funcionario::class, 'id_asignado');
    }

    public function respuestas()
    {
        return $this->hasMany(TicketRespuesta::class, 'id_ticket');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_ticket', $tipo);
    }

    public function scopePorPrioridad($query, $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeAbiertos($query)
    {
        return $query->whereIn('estado', ['abierto', 'en_proceso', 'pendiente']);
    }

    public function scopeCerrados($query)
    {
        return $query->whereIn('estado', ['resuelto', 'cerrado']);
    }

    public function scopeUrgentes($query)
    {
        return $query->where('prioridad', 'urgente');
    }

    public function scopeSinAsignar($query)
    {
        return $query->whereNull('id_asignado')
                     ->whereIn('estado', ['abierto', 'en_proceso']);
    }

    // Accessors
    public function getFechaReporteFormateadaAttribute()
    {
        return $this->fecha_reporte ? $this->fecha_reporte->format('d/m/Y') : '-';
    }

    public function getFechaAsignacionFormateadaAttribute()
    {
        return $this->fecha_asignacion ? $this->fecha_asignacion->format('d/m/Y') : '-';
    }

    public function getFechaResolucionFormateadaAttribute()
    {
        return $this->fecha_resolucion ? $this->fecha_resolucion->format('d/m/Y') : '-';
    }

    public function getFechaCierreFormateadaAttribute()
    {
        return $this->fecha_cierre ? $this->fecha_cierre->format('d/m/Y') : '-';
    }

    public function getFechaCreacionFormateadaAttribute()
    {
        return $this->fecha_creacion ? $this->fecha_creacion->format('d/m/Y H:i') : '-';
    }

    public function getFechaActualizacionFormateadaAttribute()
    {
        return $this->fecha_actualizacion ? $this->fecha_actualizacion->format('d/m/Y H:i') : '-';
    }

    public function getCostoReparacionFormateadoAttribute()
    {
        if (!$this->costo_reparacion) return '-';
        return '$' . number_format($this->costo_reparacion, 0, ',', '.');
    }

    public function getTipoTicketBadgeAttribute()
    {
        $tipos = [
            'consulta' => '<span class="badge badge-info">Consulta</span>',
            'reclamo' => '<span class="badge badge-warning">Reclamo</span>',
            'solicitud' => '<span class="badge badge-primary">Solicitud</span>',
            'averia' => '<span class="badge badge-danger">Avería</span>',
            'fuga' => '<span class="badge badge-danger">Fuga</span>',
            'corte' => '<span class="badge badge-warning">Corte</span>',
            'reconexion' => '<span class="badge badge-success">Reconexión</span>',
            'lectura' => '<span class="badge badge-secondary">Lectura</span>',
            'otro' => '<span class="badge badge-dark">Otro</span>'
        ];

        return $tipos[$this->tipo_ticket] ?? '<span class="badge badge-secondary">' . ucfirst($this->tipo_ticket) . '</span>';
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
            'abierto' => '<span class="badge badge-info">Abierto</span>',
            'en_proceso' => '<span class="badge badge-warning">En Proceso</span>',
            'pendiente' => '<span class="badge badge-secondary">Pendiente</span>',
            'resuelto' => '<span class="badge badge-success">Resuelto</span>',
            'cerrado' => '<span class="badge badge-dark">Cerrado</span>',
            'cancelado' => '<span class="badge badge-danger">Cancelado</span>'
        ];

        return $estados[$this->estado] ?? '<span class="badge badge-secondary">' . ucfirst($this->estado) . '</span>';
    }

    public function getSatisfaccionBadgeAttribute()
    {
        if (!$this->satisfaccion) return '-';

        $satisfacciones = [
            'muy_insatisfecho' => '<span class="badge badge-danger">Muy Insatisfecho</span>',
            'insatisfecho' => '<span class="badge badge-warning">Insatisfecho</span>',
            'neutral' => '<span class="badge badge-secondary">Neutral</span>',
            'satisfecho' => '<span class="badge badge-success">Satisfecho</span>',
            'muy_satisfecho' => '<span class="badge badge-success">Muy Satisfecho</span>'
        ];

        return $satisfacciones[$this->satisfaccion] ?? '<span class="badge badge-secondary">' . ucfirst($this->satisfaccion) . '</span>';
    }

    public function getTipoTicketTextoAttribute()
    {
        $tipos = [
            'consulta' => 'Consulta',
            'reclamo' => 'Reclamo',
            'solicitud' => 'Solicitud',
            'averia' => 'Avería',
            'fuga' => 'Fuga',
            'corte' => 'Corte',
            'reconexion' => 'Reconexión',
            'lectura' => 'Lectura',
            'otro' => 'Otro'
        ];

        return $tipos[$this->tipo_ticket] ?? ucfirst($this->tipo_ticket);
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
            'abierto' => 'Abierto',
            'en_proceso' => 'En Proceso',
            'pendiente' => 'Pendiente',
            'resuelto' => 'Resuelto',
            'cerrado' => 'Cerrado',
            'cancelado' => 'Cancelado'
        ];

        return $estados[$this->estado] ?? ucfirst($this->estado);
    }

    public function getTiempoRespuestaFormateadoAttribute()
    {
        if (!$this->tiempo_respuesta) return '-';

        $horas = floor($this->tiempo_respuesta / 60);
        $minutos = $this->tiempo_respuesta % 60;

        if ($horas > 0) {
            return $horas . 'h ' . $minutos . 'm';
        }
        return $minutos . 'm';
    }

    public function getTiempoResolucionFormateadoAttribute()
    {
        if (!$this->tiempo_resolucion) return '-';

        $horas = floor($this->tiempo_resolucion / 60);
        $minutos = $this->tiempo_resolucion % 60;

        if ($horas > 0) {
            return $horas . 'h ' . $minutos . 'm';
        }
        return $minutos . 'm';
    }

    // Métodos auxiliares
    public static function generarNumeroTicket()
    {
        $ultimoTicket = self::orderBy('id', 'desc')->first();
        $numero = $ultimoTicket ? intval(substr($ultimoTicket->numero_ticket, 4)) + 1 : 1;
        return 'TKT-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    public function estaAbierto()
    {
        return in_array($this->estado, ['abierto', 'en_proceso', 'pendiente']);
    }

    public function estaCerrado()
    {
        return in_array($this->estado, ['resuelto', 'cerrado']);
    }

    public function esUrgente()
    {
        return $this->prioridad === 'urgente';
    }
}
