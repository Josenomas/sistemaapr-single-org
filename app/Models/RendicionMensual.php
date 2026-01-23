<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendicionMensual extends Model
{
    use HasFactory;

    protected $table = 'rendiciones_mensuales';

    protected $fillable = [
        'codigo_rendicion',
        'periodo',
        'mes',
        'anio',
        'saldo_anterior',
        'total_ingresos',
        'total_egresos',
        'saldo_final',
        'ingresos_consumo_agua',
        'ingresos_subsidios',
        'ingresos_aportes_socios',
        'ingresos_multas',
        'ingresos_incorporaciones',
        'ingresos_otros',
        'egresos_energia_electrica',
        'egresos_productos_quimicos',
        'egresos_reparaciones',
        'egresos_remuneraciones',
        'egresos_gastos_administrativos',
        'egresos_otros',
        'estado',
        'fecha_cierre',
        'id_usuario_cierre',
        'observaciones',
        'notas_cierre',
        'id_responsable',
        'activo'
    ];

    protected $casts = [
        'mes' => 'integer',
        'anio' => 'integer',
        'saldo_anterior' => 'decimal:2',
        'total_ingresos' => 'decimal:2',
        'total_egresos' => 'decimal:2',
        'saldo_final' => 'decimal:2',
        'ingresos_consumo_agua' => 'decimal:2',
        'ingresos_subsidios' => 'decimal:2',
        'ingresos_aportes_socios' => 'decimal:2',
        'ingresos_multas' => 'decimal:2',
        'ingresos_incorporaciones' => 'decimal:2',
        'ingresos_otros' => 'decimal:2',
        'egresos_energia_electrica' => 'decimal:2',
        'egresos_productos_quimicos' => 'decimal:2',
        'egresos_reparaciones' => 'decimal:2',
        'egresos_remuneraciones' => 'decimal:2',
        'egresos_gastos_administrativos' => 'decimal:2',
        'egresos_otros' => 'decimal:2',
        'fecha_cierre' => 'datetime',
        'activo' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime'
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    /**
     * Relación con usuario que cerró la rendición
     */
    public function usuarioCierre()
    {
        return $this->belongsTo(User::class, 'id_usuario_cierre');
    }

    /**
     * Relación con usuario responsable
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'id_responsable');
    }

    /**
     * Generar código único de rendición
     */
    public static function generarCodigoRendicion()
    {
        $ultimoCodigo = self::orderByRaw('CAST(SUBSTRING(codigo_rendicion, 5) AS UNSIGNED) DESC')->value('codigo_rendicion');
        $numero = $ultimoCodigo ? intval(substr($ultimoCodigo, 4)) + 1 : 1;
        return 'REN-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Calcular totales automáticamente
     */
    public function calcularTotales()
    {
        // Calcular total de ingresos
        $this->total_ingresos =
            $this->ingresos_consumo_agua +
            $this->ingresos_subsidios +
            $this->ingresos_aportes_socios +
            $this->ingresos_multas +
            $this->ingresos_incorporaciones +
            $this->ingresos_otros;

        // Calcular total de egresos
        $this->total_egresos =
            $this->egresos_energia_electrica +
            $this->egresos_productos_quimicos +
            $this->egresos_reparaciones +
            $this->egresos_remuneraciones +
            $this->egresos_gastos_administrativos +
            $this->egresos_otros;

        // Calcular saldo final
        $this->saldo_final = ($this->saldo_anterior + $this->total_ingresos) - $this->total_egresos;
    }

    /**
     * Cerrar rendición
     */
    public function cerrar($usuarioId, $notasCierre = null)
    {
        $this->estado = 'cerrado';
        $this->fecha_cierre = now();
        $this->id_usuario_cierre = $usuarioId;
        $this->notas_cierre = $notasCierre;
        $this->save();

        // Registrar actividad
        \App\Helpers\ActividadHelper::registrar(
            'Rendición Mensual',
            'Rendición ' . $this->codigo_rendicion . ' del periodo ' . $this->periodo_texto . ' cerrada'
        );
    }

    /**
     * Reabrir rendición
     */
    public function reabrir($usuarioId)
    {
        $this->estado = 'abierto';
        $this->fecha_cierre = null;
        $this->id_usuario_cierre = null;
        $this->save();

        // Registrar actividad
        \App\Helpers\ActividadHelper::registrar(
            'Rendición Mensual',
            'Rendición ' . $this->codigo_rendicion . ' del periodo ' . $this->periodo_texto . ' reabierta'
        );
    }

    /**
     * Scope para rendiciones abiertas
     */
    public function scopeAbiertas($query)
    {
        return $query->where('estado', 'abierto');
    }

    /**
     * Scope para rendiciones cerradas
     */
    public function scopeCerradas($query)
    {
        return $query->where('estado', 'cerrado');
    }

    /**
     * Scope por periodo
     */
    public function scopePorPeriodo($query, $periodo)
    {
        return $query->where('periodo', $periodo);
    }

    /**
     * Scope por año
     */
    public function scopePorAnio($query, $anio)
    {
        return $query->where('anio', $anio);
    }

    /**
     * Accessors para formateo
     */
    public function getPeriodoTextoAttribute()
    {
        if (!$this->mes || !$this->anio) return '-';

        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        return $meses[$this->mes] . ' ' . $this->anio;
    }

    public function getSaldoAnteriorFormateadoAttribute()
    {
        return '$' . number_format($this->saldo_anterior, 0, ',', '.');
    }

    public function getTotalIngresosFormateadoAttribute()
    {
        return '$' . number_format($this->total_ingresos, 0, ',', '.');
    }

    public function getTotalEgresosFormateadoAttribute()
    {
        return '$' . number_format($this->total_egresos, 0, ',', '.');
    }

    public function getSaldoFinalFormateadoAttribute()
    {
        return '$' . number_format($this->saldo_final, 0, ',', '.');
    }

    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'abierto' => '<span class="badge badge-info">Abierto</span>',
            'cerrado' => '<span class="badge badge-success">Cerrado</span>'
        ];

        return $badges[$this->estado] ?? '<span class="badge badge-secondary">' . ucfirst($this->estado) . '</span>';
    }

    public function getEstadoTextoAttribute()
    {
        $estados = [
            'abierto' => 'Abierto',
            'cerrado' => 'Cerrado'
        ];

        return $estados[$this->estado] ?? ucfirst($this->estado);
    }

    public function getFechaCierreFormateadaAttribute()
    {
        return $this->fecha_cierre ? $this->fecha_cierre->format('d/m/Y H:i') : '-';
    }

    public function getFechaCreacionFormateadaAttribute()
    {
        return $this->fecha_creacion ? $this->fecha_creacion->format('d/m/Y H:i') : '-';
    }

    /**
     * Verificar si tiene déficit
     */
    public function getEsDeficitAttribute()
    {
        return $this->saldo_final < 0;
    }

    /**
     * Verificar si tiene superávit
     */
    public function getEsSuperavitAttribute()
    {
        return $this->saldo_final > 0;
    }

    /**
     * Porcentaje de cada categoría de egreso
     */
    public function getPorcentajeEgresosAttribute()
    {
        if ($this->total_egresos == 0) {
            return [
                'energia' => 0,
                'quimicos' => 0,
                'reparaciones' => 0,
                'remuneraciones' => 0,
                'administrativos' => 0,
                'otros' => 0
            ];
        }

        return [
            'energia' => round(($this->egresos_energia_electrica / $this->total_egresos) * 100, 1),
            'quimicos' => round(($this->egresos_productos_quimicos / $this->total_egresos) * 100, 1),
            'reparaciones' => round(($this->egresos_reparaciones / $this->total_egresos) * 100, 1),
            'remuneraciones' => round(($this->egresos_remuneraciones / $this->total_egresos) * 100, 1),
            'administrativos' => round(($this->egresos_gastos_administrativos / $this->total_egresos) * 100, 1),
            'otros' => round(($this->egresos_otros / $this->total_egresos) * 100, 1)
        ];
    }

    /**
     * Porcentaje de cada categoría de ingreso
     */
    public function getPorcentajeIngresosAttribute()
    {
        if ($this->total_ingresos == 0) {
            return [
                'consumo_agua' => 0,
                'subsidios' => 0,
                'aportes' => 0,
                'multas' => 0,
                'incorporaciones' => 0,
                'otros' => 0
            ];
        }

        return [
            'consumo_agua' => round(($this->ingresos_consumo_agua / $this->total_ingresos) * 100, 1),
            'subsidios' => round(($this->ingresos_subsidios / $this->total_ingresos) * 100, 1),
            'aportes' => round(($this->ingresos_aportes_socios / $this->total_ingresos) * 100, 1),
            'multas' => round(($this->ingresos_multas / $this->total_ingresos) * 100, 1),
            'incorporaciones' => round(($this->ingresos_incorporaciones / $this->total_ingresos) * 100, 1),
            'otros' => round(($this->ingresos_otros / $this->total_ingresos) * 100, 1)
        ];
    }
}
