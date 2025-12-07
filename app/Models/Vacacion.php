<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacacion extends Model
{
    protected $table = 'vacaciones';

    protected $fillable = [
        'id_funcionario',
        'fecha_inicio',
        'fecha_termino',
        'dias_habiles',
        'periodo',
        'tipo',
        'estado',
        'fecha_solicitud',
        'fecha_aprobacion',
        'id_aprobador',
        'motivo_rechazo',
        'observaciones',
        'suplente',
        'activo'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_termino' => 'date',
        'fecha_solicitud' => 'date',
        'fecha_aprobacion' => 'date',
        'activo' => 'boolean'
    ];

    // Relaciones
    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'id_funcionario');
    }

    public function aprobador()
    {
        return $this->belongsTo(Funcionario::class, 'id_aprobador');
    }

    public function funcionarioSuplente()
    {
        return $this->belongsTo(Funcionario::class, 'suplente');
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
        return $query->where('tipo', $tipo);
    }

    public function scopePorPeriodo($query, $periodo)
    {
        return $query->where('periodo', $periodo);
    }

    public function scopePorFuncionario($query, $idFuncionario)
    {
        return $query->where('id_funcionario', $idFuncionario);
    }

    public function scopeEnCurso($query)
    {
        return $query->where('estado', 'en_curso')
                     ->whereDate('fecha_inicio', '<=', now())
                     ->whereDate('fecha_termino', '>=', now());
    }

    public function scopeProximas($query)
    {
        return $query->where('estado', 'aprobada')
                     ->whereDate('fecha_inicio', '>', now());
    }

    // Accessors
    public function getFechaInicioFormateadaAttribute()
    {
        return $this->fecha_inicio ? $this->fecha_inicio->format('d/m/Y') : '-';
    }

    public function getFechaTerminoFormateadaAttribute()
    {
        return $this->fecha_termino ? $this->fecha_termino->format('d/m/Y') : '-';
    }

    public function getFechaSolicitudFormateadaAttribute()
    {
        return $this->fecha_solicitud ? $this->fecha_solicitud->format('d/m/Y') : '-';
    }

    public function getFechaAprobacionFormateadaAttribute()
    {
        return $this->fecha_aprobacion ? $this->fecha_aprobacion->format('d/m/Y') : '-';
    }

    public function getTipoBadgeAttribute()
    {
        $tipos = [
            'legales' => '<span class="badge badge-primary">Legales</span>',
            'progresivas' => '<span class="badge badge-info">Progresivas</span>',
            'administrativas' => '<span class="badge badge-warning">Administrativas</span>',
            'sin_goce' => '<span class="badge badge-secondary">Sin Goce</span>'
        ];

        return $tipos[$this->tipo] ?? '<span class="badge badge-secondary">' . ucfirst($this->tipo) . '</span>';
    }

    public function getEstadoBadgeAttribute()
    {
        $estados = [
            'solicitada' => '<span class="badge badge-info">Solicitada</span>',
            'aprobada' => '<span class="badge badge-success">Aprobada</span>',
            'rechazada' => '<span class="badge badge-danger">Rechazada</span>',
            'en_curso' => '<span class="badge badge-primary">En Curso</span>',
            'finalizada' => '<span class="badge badge-secondary">Finalizada</span>',
            'cancelada' => '<span class="badge badge-dark">Cancelada</span>'
        ];

        return $estados[$this->estado] ?? '<span class="badge badge-secondary">' . ucfirst($this->estado) . '</span>';
    }

    public function getTipoTextoAttribute()
    {
        $tipos = [
            'legales' => 'Legales',
            'progresivas' => 'Progresivas',
            'administrativas' => 'Administrativas',
            'sin_goce' => 'Sin Goce de Sueldo'
        ];

        return $tipos[$this->tipo] ?? ucfirst($this->tipo);
    }

    public function getEstadoTextoAttribute()
    {
        $estados = [
            'solicitada' => 'Solicitada',
            'aprobada' => 'Aprobada',
            'rechazada' => 'Rechazada',
            'en_curso' => 'En Curso',
            'finalizada' => 'Finalizada',
            'cancelada' => 'Cancelada'
        ];

        return $estados[$this->estado] ?? ucfirst($this->estado);
    }

    public function getDiasRestantesAttribute()
    {
        if ($this->estado !== 'en_curso' && $this->estado !== 'aprobada') {
            return null;
        }

        $hoy = now()->startOfDay();

        if ($this->estado === 'aprobada' && $this->fecha_inicio > $hoy) {
            // Días para que inicien
            return $hoy->diffInDays($this->fecha_inicio, false);
        }

        if ($this->estado === 'en_curso' && $this->fecha_termino >= $hoy) {
            // Días restantes
            return $hoy->diffInDays($this->fecha_termino, false);
        }

        return 0;
    }

    public function getPeriodoCompletoAttribute()
    {
        return $this->fecha_inicio_formateada . ' - ' . $this->fecha_termino_formateada;
    }
}
