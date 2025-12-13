<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionTarifa extends Model
{
    protected $table = 'configuraciones_tarifas';

    protected $fillable = [
        'nombre',
        'tipo_cliente',
        'nombre_tarifa',
        'consumo_desde',
        'consumo_hasta',
        'monto',
        'cargo_fijo',
        'iva',
        'orden',
        'vigente_desde',
        'vigente_hasta',
        'activo'
    ];

    protected $casts = [
        'consumo_desde' => 'decimal:2',
        'consumo_hasta' => 'decimal:2',
        'monto' => 'decimal:2',
        'cargo_fijo' => 'decimal:2',
        'iva' => 'decimal:2',
        'activo' => 'boolean',
        'vigente_desde' => 'date',
        'vigente_hasta' => 'date',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $dates = [
        'fecha_creacion',
        'fecha_actualizacion',
        'vigente_desde',
        'vigente_hasta'
    ];

    /**
     * Scope para obtener solo configuraciones activas
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden', 'asc');
    }

    /**
     * Scope para filtrar por tipo de cliente
     */
    public function scopePorTipoCliente($query, $tipoCliente)
    {
        return $query->where('tipo_cliente', $tipoCliente);
    }

    /**
     * Scope para filtrar tarifas vigentes en una fecha específica
     */
    public function scopeVigentesEn($query, $fecha)
    {
        return $query->where('vigente_desde', '<=', $fecha)
                     ->where(function($q) use ($fecha) {
                         $q->whereNull('vigente_hasta')
                           ->orWhere('vigente_hasta', '>=', $fecha);
                     });
    }

    /**
     * Scope para agrupar por nombre de tarifa
     */
    public function scopePorNombreTarifa($query, $nombreTarifa)
    {
        return $query->where('nombre_tarifa', $nombreTarifa);
    }

    /**
     * Calcular el monto según el consumo (versión antigua - retrocompatibilidad)
     */
    public static function calcularMonto($consumo)
    {
        $tramo = self::activos()
                     ->ordenados()
                     ->where('consumo_desde', '<=', $consumo)
                     ->where(function($query) use ($consumo) {
                         $query->whereNull('consumo_hasta')
                               ->orWhere('consumo_hasta', '>=', $consumo);
                     })
                     ->first();

        return $tramo ? $tramo->monto : 0;
    }

    /**
     * Calcular monto por tipo de cliente, consumo y fecha
     * SISTEMA PROGRESIVO POR TRAMOS: Cada tramo cobra solo los m³ que caen dentro de su rango
     *
     * @param string $tipoCliente residencial|comercial|industrial
     * @param float $consumo Consumo en m³
     * @param string $fecha Fecha en formato Y-m-d (opcional, usa hoy si no se especifica)
     * @return array ['tramos_detalle' => array, 'monto_base' => decimal, 'iva' => decimal, 'total' => decimal, 'cargo_fijo' => decimal]
     */
    public static function calcularMontoPorConsumo($tipoCliente, $consumo, $fecha = null)
    {
        if ($fecha === null) {
            $fecha = date('Y-m-d');
        }

        // Obtener TODOS los tramos vigentes para este tipo de cliente, ordenados
        $tramos = self::activos()
                     ->porTipoCliente($tipoCliente)
                     ->vigentesEn($fecha)
                     ->ordenados()
                     ->get();

        if ($tramos->isEmpty()) {
            return [
                'tramos_detalle' => [],
                'tramo' => null,
                'monto_base' => 0,
                'cargo_fijo' => 0,
                'cargo_consumo' => 0,
                'iva' => 0,
                'total' => 0,
                'error' => 'No se encontró tarifa vigente para el tipo de cliente especificado'
            ];
        }

        $cargoConsumoTotal = 0;
        $consumoRestante = $consumo;
        $tramosDetalle = [];
        $cargoFijo = 0;
        $ivaPorcentaje = 0;
        $ultimoTramo = null;

        // Procesar cada tramo de forma progresiva
        foreach ($tramos as $tramo) {
            if ($consumo < $tramo->consumo_desde) {
                continue; // Este tramo no aplica, el consumo no llega aquí
            }

            $desde = $tramo->consumo_desde;
            $hasta = $tramo->consumo_hasta ?? PHP_INT_MAX;

            // Calcular cuántos m³ caen en este tramo
            $m3EnEsteTramo = 0;

            if ($consumo <= $hasta) {
                // El consumo cae dentro de este tramo
                $m3EnEsteTramo = $consumo - $desde;
            } else {
                // El consumo supera este tramo, tomar todo el rango
                $m3EnEsteTramo = $hasta - $desde;
            }

            if ($m3EnEsteTramo > 0) {
                $subtotalTramo = $m3EnEsteTramo * $tramo->monto;
                $cargoConsumoTotal += $subtotalTramo;

                $tramosDetalle[] = [
                    'nombre' => $tramo->nombre,
                    'rango' => $tramo->rango_descripcion,
                    'm3_en_tramo' => $m3EnEsteTramo,
                    'valor_unitario' => $tramo->monto,
                    'subtotal' => $subtotalTramo
                ];

                $ultimoTramo = $tramo;
            }

            // Si el consumo está dentro de este tramo, ya terminamos
            if ($consumo <= $hasta) {
                break;
            }
        }

        // Obtener cargo fijo e IVA del primer tramo (o del último aplicable)
        $tramoBase = $tramos->first();
        $cargoFijo = $tramoBase->cargo_fijo ?? 0;
        $ivaPorcentaje = $tramoBase->iva ?? 0;

        // Calcular totales
        $subtotal = $cargoConsumoTotal + $cargoFijo;
        $montoIva = round($subtotal * ($ivaPorcentaje / 100), 0);
        $total = $subtotal + $montoIva;

        return [
            'tramos_detalle' => $tramosDetalle,
            'tramo' => $ultimoTramo ?? $tramoBase, // Para retrocompatibilidad
            'monto_base' => $subtotal,
            'cargo_fijo' => $cargoFijo,
            'cargo_consumo' => $cargoConsumoTotal,
            'valor_unitario' => $tramoBase->monto, // Para retrocompatibilidad
            'iva_porcentaje' => $ivaPorcentaje,
            'iva' => $montoIva,
            'total' => $total
        ];
    }

    /**
     * Obtener todas las tarifas vigentes agrupadas por tipo de cliente
     */
    public static function getTarifasVigentes($fecha = null)
    {
        if ($fecha === null) {
            $fecha = date('Y-m-d');
        }

        return self::activos()
                   ->vigentesEn($fecha)
                   ->ordenados()
                   ->get()
                   ->groupBy(['tipo_cliente', 'nombre_tarifa']);
    }

    /**
     * Obtener descripción del rango
     */
    public function getRangoDescripcionAttribute()
    {
        if ($this->consumo_hasta === null) {
            return $this->consumo_desde . '+ m³';
        }
        return $this->consumo_desde . '-' . $this->consumo_hasta . ' m³';
    }

    /**
     * Obtener nombre completo de la tarifa con tipo
     */
    public function getNombreTarifaCompletoAttribute()
    {
        $tipo = ucfirst($this->tipo_cliente);
        return "{$this->nombre_tarifa} ({$tipo})";
    }

    /**
     * Obtener badge de color según tipo de cliente
     */
    public function getTipoClienteBadgeAttribute()
    {
        $badges = [
            'residencial' => 'badge-primary',
            'comercial' => 'badge-warning',
            'industrial' => 'badge-info'
        ];

        return $badges[$this->tipo_cliente] ?? 'badge-secondary';
    }

    /**
     * Verificar si la tarifa está vigente en la fecha actual
     */
    public function getEsVigenteAttribute()
    {
        $hoy = date('Y-m-d');
        return $this->vigente_desde <= $hoy &&
               ($this->vigente_hasta === null || $this->vigente_hasta >= $hoy);
    }
}
