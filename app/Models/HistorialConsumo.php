<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialConsumo extends Model
{
    protected $table = 'historial_consumo';

    protected $fillable = [
        'id_socio',
        'id_lectura',
        'periodo',
        'lectura_anterior',
        'lectura_actual',
        'consumo_m3',
        'monto_consumo',
        'promedio_diario',
        'anomalia',
        'observaciones',
        'activo'
    ];

    protected $casts = [
        'lectura_anterior' => 'decimal:2',
        'lectura_actual' => 'decimal:2',
        'consumo_m3' => 'decimal:2',
        'monto_consumo' => 'decimal:2',
        'promedio_diario' => 'decimal:2',
    ];

    // Laravel uses created_at and updated_at by default, but our table uses fecha_creacion and fecha_actualizacion
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $dates = ['fecha_creacion', 'fecha_actualizacion'];

    // Relaciones
    public function socio()
    {
        return $this->belongsTo(Socio::class, 'id_socio');
    }

    public function lectura()
    {
        return $this->belongsTo(Lectura::class, 'id_lectura');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    public function scopeConAnomalias($query)
    {
        return $query->whereIn('anomalia', ['alto', 'bajo', 'cero']);
    }

    public function scopeConsumoAlto($query)
    {
        return $query->where('anomalia', 'alto');
    }

    public function scopeConsumoBajo($query)
    {
        return $query->where('anomalia', 'bajo');
    }

    public function scopeConsumoCero($query)
    {
        return $query->where('anomalia', 'cero');
    }

    public function scopePorPeriodo($query, $periodo)
    {
        return $query->where('periodo', $periodo);
    }

    // Accessors
    public function getAnomaliaBadgeAttribute()
    {
        $badges = [
            'normal' => '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Normal</span>',
            'alto' => '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Consumo Alto</span>',
            'bajo' => '<span class="badge badge-info"><i class="fas fa-info-circle"></i> Consumo Bajo</span>',
            'cero' => '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Sin Consumo</span>'
        ];
        return $badges[$this->anomalia] ?? '<span class="badge badge-secondary">' . ucfirst($this->anomalia) . '</span>';
    }

    public function getConsumoFormateadoAttribute()
    {
        return number_format($this->consumo_m3, 2, ',', '.') . ' m³';
    }

    public function getMontoFormateadoAttribute()
    {
        return '$' . number_format($this->monto_consumo, 0, ',', '.');
    }

    public function getPromedioDiarioFormateadoAttribute()
    {
        if (!$this->promedio_diario) {
            return '-';
        }
        return number_format($this->promedio_diario, 2, ',', '.') . ' m³/día';
    }

    public function getPeriodoFormateadoAttribute()
    {
        if (!$this->periodo) {
            return '-';
        }

        // Convertir YYYY-MM a formato "Mes YYYY"
        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
            '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
            '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
            '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];

        $partes = explode('-', $this->periodo);
        if (count($partes) == 2) {
            $anio = $partes[0];
            $mes = $partes[1];
            return $meses[$mes] . ' ' . $anio;
        }

        return $this->periodo;
    }

    public function getVariacionConsumoAttribute()
    {
        // Obtener el consumo del período anterior del mismo socio
        $periodoAnterior = static::where('id_socio', $this->id_socio)
                                ->where('periodo', '<', $this->periodo)
                                ->orderBy('periodo', 'desc')
                                ->first();

        if (!$periodoAnterior || $periodoAnterior->consumo_m3 == 0) {
            return null;
        }

        $variacion = (($this->consumo_m3 - $periodoAnterior->consumo_m3) / $periodoAnterior->consumo_m3) * 100;
        return round($variacion, 2);
    }

    public function getVariacionBadgeAttribute()
    {
        $variacion = $this->variacion_consumo;

        if ($variacion === null) {
            return '<span class="badge badge-secondary"><i class="fas fa-minus"></i> N/A</span>';
        }

        if ($variacion > 20) {
            return '<span class="badge badge-danger"><i class="fas fa-arrow-up"></i> +' . number_format($variacion, 1) . '%</span>';
        } elseif ($variacion > 10) {
            return '<span class="badge badge-warning"><i class="fas fa-arrow-up"></i> +' . number_format($variacion, 1) . '%</span>';
        } elseif ($variacion < -20) {
            return '<span class="badge badge-info"><i class="fas fa-arrow-down"></i> ' . number_format($variacion, 1) . '%</span>';
        } elseif ($variacion < -10) {
            return '<span class="badge badge-info"><i class="fas fa-arrow-down"></i> ' . number_format($variacion, 1) . '%</span>';
        } else {
            return '<span class="badge badge-success"><i class="fas fa-minus"></i> ' . number_format($variacion, 1) . '%</span>';
        }
    }

    // Métodos estáticos
    public static function calcularPromedioPorSocio($idSocio, $meses = 6)
    {
        $historiales = static::where('id_socio', $idSocio)
                             ->where('activo', 1)
                             ->orderBy('periodo', 'desc')
                             ->limit($meses)
                             ->get();

        if ($historiales->isEmpty()) {
            return 0;
        }

        return $historiales->avg('consumo_m3');
    }

    public static function obtenerTendenciaPorSocio($idSocio, $meses = 12)
    {
        return static::where('id_socio', $idSocio)
                     ->where('activo', 1)
                     ->orderBy('periodo', 'desc')
                     ->limit($meses)
                     ->get(['periodo', 'consumo_m3']);
    }

    public static function detectarAnomalia($consumoActual, $promedioHistorico)
    {
        if ($consumoActual == 0) {
            return 'cero';
        }

        if ($promedioHistorico == 0) {
            return 'normal';
        }

        $variacion = (($consumoActual - $promedioHistorico) / $promedioHistorico) * 100;

        if ($variacion > 50) {
            return 'alto';
        } elseif ($variacion < -50) {
            return 'bajo';
        }

        return 'normal';
    }
}
