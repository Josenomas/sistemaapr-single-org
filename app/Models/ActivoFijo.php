<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivoFijo extends Model
{
    protected $table = 'activos_fijos';

    protected $fillable = [
        'codigo_activo',
        'nombre',
        'categoria',
        'descripcion',
        'marca',
        'modelo',
        'numero_serie',
        'fecha_adquisicion',
        'valor_adquisicion',
        'valor_actual',
        'proveedor',
        'ubicacion',
        'estado',
        'id_responsable',
        'observaciones',
        'foto',
        'vida_util_anos',
        'fecha_ultimo_mantenimiento',
        'proxima_revision',
        'activo'
    ];

    protected $casts = [
        'fecha_adquisicion' => 'date',
        'valor_adquisicion' => 'decimal:2',
        'valor_actual' => 'decimal:2',
        'fecha_ultimo_mantenimiento' => 'date',
        'proxima_revision' => 'date',
        'activo' => 'boolean'
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    /**
     * Relación con usuario responsable
     */
    public function responsable()
    {
        return $this->belongsTo(Usuario::class, 'id_responsable');
    }

    /**
     * Scope para activos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    /**
     * Scope por categoría
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Generar código de activo único
     */
    public static function generarCodigoActivo()
    {
        $ultimoActivo = self::orderBy('id', 'desc')->first();
        $numero = $ultimoActivo ? $ultimoActivo->id + 1 : 1;
        return 'ACT-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Accessors
     */
    public function getCategoriaNombreAttribute()
    {
        $categorias = [
            'mobiliario' => 'Mobiliario',
            'equipos_computo' => 'Equipos de Cómputo',
            'equipos_oficina' => 'Equipos de Oficina',
            'herramientas' => 'Herramientas',
            'vehiculos' => 'Vehículos',
            'equipamiento_tecnico' => 'Equipamiento Técnico',
            'otros' => 'Otros'
        ];

        return $categorias[$this->categoria] ?? ucfirst($this->categoria);
    }

    public function getEstadoNombreAttribute()
    {
        $estados = [
            'excelente' => 'Excelente',
            'bueno' => 'Bueno',
            'regular' => 'Regular',
            'malo' => 'Malo',
            'en_reparacion' => 'En Reparación',
            'dado_de_baja' => 'Dado de Baja'
        ];

        return $estados[$this->estado] ?? ucfirst($this->estado);
    }

    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'excelente' => '<span class="badge badge-success">Excelente</span>',
            'bueno' => '<span class="badge badge-success">Bueno</span>',
            'regular' => '<span class="badge badge-warning">Regular</span>',
            'malo' => '<span class="badge badge-danger">Malo</span>',
            'en_reparacion' => '<span class="badge badge-info">En Reparación</span>',
            'dado_de_baja' => '<span class="badge badge-secondary">Dado de Baja</span>'
        ];

        return $badges[$this->estado] ?? '<span class="badge badge-secondary">' . ucfirst($this->estado) . '</span>';
    }

    public function getValorAdquisicionFormateadoAttribute()
    {
        return '$' . number_format($this->valor_adquisicion, 0, ',', '.');
    }

    public function getValorActualFormateadoAttribute()
    {
        return '$' . number_format($this->valor_actual ?? 0, 0, ',', '.');
    }

    public function getFechaAdquisicionFormateadaAttribute()
    {
        return $this->fecha_adquisicion ? $this->fecha_adquisicion->format('d/m/Y') : '-';
    }

    public function getFechaUltimoMantenimientoFormateadaAttribute()
    {
        return $this->fecha_ultimo_mantenimiento ? $this->fecha_ultimo_mantenimiento->format('d/m/Y') : '-';
    }

    public function getProximaRevisionFormateadaAttribute()
    {
        return $this->proxima_revision ? $this->proxima_revision->format('d/m/Y') : '-';
    }

    public function getDepreciacionAttribute()
    {
        if (!$this->vida_util_anos || !$this->valor_adquisicion) {
            return 0;
        }

        $anosTranscurridos = now()->diffInYears($this->fecha_adquisicion);
        $depreciacionAnual = $this->valor_adquisicion / $this->vida_util_anos;
        $depreciacionTotal = $depreciacionAnual * $anosTranscurridos;

        return min($depreciacionTotal, $this->valor_adquisicion);
    }

    public function getValorDepreciadoAttribute()
    {
        return max(0, $this->valor_adquisicion - $this->depreciacion);
    }
}
