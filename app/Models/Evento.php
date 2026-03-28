<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToOrganizacion;
use Carbon\Carbon;

class Evento extends Model
{
    use BelongsToOrganizacion;

    protected $table = 'eventos';

    protected $fillable = [
        'titulo',
        'tipo',
        'descripcion',
        'fecha_evento',
        'recurrencia',
        'dia_recurrencia',
        'icono',
        'color',
        'notificar',
        'dias_notificacion',
        'activo',
    ];

    protected $casts = [
        'fecha_evento' => 'date',
        'activo' => 'boolean',
        'notificar' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    /**
     * Scope para eventos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    /**
     * Scope para eventos próximos (futuro)
     */
    public function scopeProximos($query)
    {
        return $query->where('fecha_evento', '>=', now()->toDateString())
                     ->orderBy('fecha_evento', 'asc');
    }

    /**
     * Scope por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Calcular próxima fecha según recurrencia
     */
    public function calcularProximaFecha()
    {
        $hoy = now();
        $fechaEvento = Carbon::parse($this->fecha_evento);

        // Si el evento ya pasó y no es recurrente, retornar la fecha original
        if ($this->recurrencia === 'ninguna') {
            return $fechaEvento;
        }

        // Si la fecha no ha pasado, retornarla
        if ($fechaEvento->isFuture()) {
            return $fechaEvento;
        }

        // Calcular próxima fecha según recurrencia
        switch ($this->recurrencia) {
            case 'diaria':
                while ($fechaEvento->isPast()) {
                    $fechaEvento->addDay();
                }
                break;

            case 'semanal':
                while ($fechaEvento->isPast()) {
                    $fechaEvento->addWeek();
                }
                break;

            case 'mensual':
                if ($this->dia_recurrencia) {
                    // Usar día específico del mes
                    $proxima = $hoy->copy()->day($this->dia_recurrencia);
                    if ($proxima->isPast()) {
                        $proxima->addMonth();
                    }
                    return $proxima;
                } else {
                    while ($fechaEvento->isPast()) {
                        $fechaEvento->addMonth();
                    }
                }
                break;

            case 'anual':
                while ($fechaEvento->isPast()) {
                    $fechaEvento->addYear();
                }
                break;
        }

        return $fechaEvento;
    }

    /**
     * Obtener días hasta el evento
     */
    public function getDiasRestantesAttribute()
    {
        $proximaFecha = $this->calcularProximaFecha();
        return now()->diffInDays($proximaFecha, false);
    }

    /**
     * Obtener próxima fecha del evento
     */
    public function getProximaFechaAttribute()
    {
        return $this->calcularProximaFecha();
    }

    /**
     * Verificar si el evento es urgente (≤ 3 días)
     */
    public function getEsUrgenteAttribute()
    {
        return $this->dias_restantes <= 3 && $this->dias_restantes >= 0;
    }

    /**
     * Verificar si el evento es próximo (≤ 7 días)
     */
    public function getEsProximoAttribute()
    {
        return $this->dias_restantes > 3 && $this->dias_restantes <= 7;
    }

    /**
     * Obtener texto del tipo de recurrencia
     */
    public function getRecurrenciaTextoAttribute()
    {
        $textos = [
            'ninguna' => 'Evento único',
            'diaria' => 'Todos los días',
            'semanal' => 'Cada semana',
            'mensual' => 'Cada mes' . ($this->dia_recurrencia ? " (día {$this->dia_recurrencia})" : ''),
            'anual' => 'Cada año',
        ];

        return $textos[$this->recurrencia] ?? 'Desconocido';
    }

    /**
     * Obtener clase CSS para countdown
     */
    public function getCountdownClassAttribute()
    {
        if ($this->es_urgente) {
            return 'urgent';
        } elseif ($this->es_proximo) {
            return 'soon';
        }
        return '';
    }

    /**
     * Obtener texto del countdown
     */
    public function getCountdownTextoAttribute()
    {
        $dias = $this->dias_restantes;

        if ($dias == 0) {
            return '¡Hoy!';
        } elseif ($dias == 1) {
            return 'Mañana';
        } elseif ($dias < 0) {
            return 'Vencido';
        } else {
            return "En {$dias} días";
        }
    }
}
