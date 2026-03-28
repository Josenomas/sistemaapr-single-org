<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToOrganizacion;

class Directiva extends Model
{
    use HasFactory, BelongsToOrganizacion;

    protected $table = 'directiva';

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'id_socio',
        'cargo',
        'fecha_inicio',
        'fecha_termino',
        'estado',
        'periodo',
        'acta_nombramiento',
        'observaciones',
        'activo'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_termino' => 'date',
        'activo' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime'
    ];

    // Relaciones
    public function socio()
    {
        return $this->belongsTo(Socio::class, 'id_socio');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    public function scopeVigentes($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeFinalizados($query)
    {
        return $query->where('estado', 'finalizado');
    }

    public function scopePorCargo($query, $cargo)
    {
        return $query->where('cargo', $cargo);
    }

    public function scopePorPeriodo($query, $periodo)
    {
        return $query->where('periodo', $periodo);
    }

    // Accessors
    public function getFechaInicioFormateadaAttribute()
    {
        return $this->fecha_inicio ? $this->fecha_inicio->format('d/m/Y') : '';
    }

    public function getFechaTerminoFormateadaAttribute()
    {
        return $this->fecha_termino ? $this->fecha_termino->format('d/m/Y') : '-';
    }

    public function getCargoTextoAttribute()
    {
        $cargos = [
            'presidente' => 'Presidente',
            'vicepresidente' => 'Vicepresidente',
            'secretario' => 'Secretario',
            'tesorero' => 'Tesorero',
            'director' => 'Director',
            'vocal' => 'Vocal',
            'suplente' => 'Suplente'
        ];

        return $cargos[$this->cargo] ?? ucfirst($this->cargo);
    }

    public function getCargoBadgeAttribute()
    {
        $badges = [
            'presidente' => '<span class="badge badge-danger">Presidente</span>',
            'vicepresidente' => '<span class="badge badge-warning">Vicepresidente</span>',
            'secretario' => '<span class="badge badge-info">Secretario</span>',
            'tesorero' => '<span class="badge badge-success">Tesorero</span>',
            'director' => '<span class="badge badge-primary">Director</span>',
            'vocal' => '<span class="badge badge-secondary">Vocal</span>',
            'suplente' => '<span class="badge badge-light">Suplente</span>'
        ];

        return $badges[$this->cargo] ?? '<span class="badge badge-secondary">' . ucfirst($this->cargo) . '</span>';
    }

    public function getEstadoTextoAttribute()
    {
        $estados = [
            'activo' => 'Activo',
            'finalizado' => 'Finalizado',
            'renunciado' => 'Renunciado'
        ];

        return $estados[$this->estado] ?? ucfirst($this->estado);
    }

    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'activo' => '<span class="badge badge-success">Activo</span>',
            'finalizado' => '<span class="badge badge-secondary">Finalizado</span>',
            'renunciado' => '<span class="badge badge-danger">Renunciado</span>'
        ];

        return $badges[$this->estado] ?? '<span class="badge badge-secondary">' . ucfirst($this->estado) . '</span>';
    }

    public function getDuracionAttribute()
    {
        if (!$this->fecha_inicio) {
            return '-';
        }

        $fechaFin = $this->fecha_termino ?? now();
        $diff = $this->fecha_inicio->diff($fechaFin);

        $years = $diff->y;
        $months = $diff->m;

        $duracion = [];
        if ($years > 0) {
            $duracion[] = $years . ' año' . ($years > 1 ? 's' : '');
        }
        if ($months > 0) {
            $duracion[] = $months . ' mes' . ($months > 1 ? 'es' : '');
        }

        return !empty($duracion) ? implode(' y ', $duracion) : 'Menos de 1 mes';
    }
}
