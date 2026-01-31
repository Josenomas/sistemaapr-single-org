<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boleta extends Model
{
    protected $table = 'boletas';

    protected $fillable = [
        'numero_boleta',
        'id_folio_sii',
        'folio_sii',
        'timbre_electronico',
        'fecha_timbraje',
        'id_socio',
        'id_lectura',
        'mes',
        'fecha_emision',
        'fecha_vencimiento',
        'consumo_m3',
        'cargo_fijo',
        'cargo_consumo',
        'otros_cargos',
        'descuentos',
        'subsidio',
        'total',
        'estado',
        'observaciones',
        'activo'
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_timbraje' => 'datetime',
        'consumo_m3' => 'decimal:2',
        'cargo_fijo' => 'decimal:2',
        'cargo_consumo' => 'decimal:2',
        'otros_cargos' => 'decimal:2',
        'descuentos' => 'decimal:2',
        'subsidio' => 'decimal:2',
        'total' => 'decimal:2',
        'activo' => 'boolean'
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    /**
     * Relación con socio
     */
    public function socio()
    {
        return $this->belongsTo(Socio::class, 'id_socio');
    }

    /**
     * Relación con lectura
     */
    public function lectura()
    {
        return $this->belongsTo(Lectura::class, 'id_lectura');
    }

    /**
     * Relación con folio SII
     */
    public function folioSII()
    {
        return $this->belongsTo(FolioSII::class, 'id_folio_sii');
    }

    /**
     * Relación con pagos
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_boleta');
    }

    /**
     * Scope para boletas pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para boletas vencidas
     */
    public function scopeVencidas($query)
    {
        return $query->where('estado', 'vencida')
                     ->orWhere(function($q) {
                         $q->where('estado', 'pendiente')
                           ->where('fecha_vencimiento', '<', now());
                     });
    }

    /**
     * Scope para boletas pagadas
     */
    public function scopePagadas($query)
    {
        return $query->where('estado', 'pagada');
    }

    /**
     * Scope para boletas activas
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    /**
     * Scope por mes
     */
    public function scopePorMes($query, $mes)
    {
        return $query->where('mes', $mes);
    }

    /**
     * Scope por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Verificar si está vencida
     */
    public function getEstaVencidaAttribute()
    {
        return $this->estado === 'pendiente' && $this->fecha_vencimiento < now();
    }

    /**
     * Obtener días de atraso
     */
    public function getDiasAtrasoAttribute()
    {
        // Solo calcular atraso para boletas pendientes o vencidas (no pagadas ni anuladas)
        if (!in_array($this->estado, ['pendiente', 'vencida'])) {
            return 0;
        }

        // Si la fecha de vencimiento es futura, no hay atraso
        if ($this->fecha_vencimiento >= now()->startOfDay()) {
            return 0;
        }

        // Calcular días de atraso desde la fecha de vencimiento
        return now()->startOfDay()->diffInDays($this->fecha_vencimiento->startOfDay());
    }

    /**
     * Obtener monto pendiente
     */
    public function getMontoPendienteAttribute()
    {
        $totalPagado = $this->pagos()->sum('monto_pagado');
        return $this->total - $totalPagado;
    }

    // Accessors para formateo
    public function getFechaEmisionFormateadaAttribute()
    {
        return $this->fecha_emision ? $this->fecha_emision->format('d/m/Y') : '-';
    }

    public function getFechaVencimientoFormateadaAttribute()
    {
        return $this->fecha_vencimiento ? $this->fecha_vencimiento->format('d/m/Y') : '-';
    }

    public function getFechaCreacionFormateadaAttribute()
    {
        return $this->fecha_creacion ? $this->fecha_creacion->format('d/m/Y H:i') : '-';
    }

    public function getFechaActualizacionFormateadaAttribute()
    {
        return $this->fecha_actualizacion ? $this->fecha_actualizacion->format('d/m/Y H:i') : '-';
    }

    public function getTotalFormateadoAttribute()
    {
        return '$' . number_format($this->total, 0, ',', '.');
    }

    public function getCargoFijoFormateadoAttribute()
    {
        return '$' . number_format($this->cargo_fijo, 0, ',', '.');
    }

    public function getCargoConsumoFormateadoAttribute()
    {
        return '$' . number_format($this->cargo_consumo, 0, ',', '.');
    }

    public function getOtrosCargosFormateadoAttribute()
    {
        return '$' . number_format($this->otros_cargos, 0, ',', '.');
    }

    public function getDescuentosFormateadoAttribute()
    {
        return '$' . number_format($this->descuentos, 0, ',', '.');
    }

    public function getSubsidioFormateadoAttribute()
    {
        return '$' . number_format($this->subsidio, 0, ',', '.');
    }

    public function getMontoPendienteFormateadoAttribute()
    {
        return '$' . number_format($this->monto_pendiente, 0, ',', '.');
    }

    public function getEstadoBadgeAttribute()
    {
        $estados = [
            'pendiente' => '<span class="badge badge-warning">Pendiente</span>',
            'pagada' => '<span class="badge badge-success">Pagada</span>',
            'vencida' => '<span class="badge badge-danger">Vencida</span>',
            'anulada' => '<span class="badge badge-secondary">Anulada</span>'
        ];

        return $estados[$this->estado] ?? '<span class="badge badge-secondary">' . ucfirst($this->estado) . '</span>';
    }

    public function getEstadoTextoAttribute()
    {
        $estados = [
            'pendiente' => 'Pendiente',
            'pagada' => 'Pagada',
            'vencida' => 'Vencida',
            'anulada' => 'Anulada'
        ];

        return $estados[$this->estado] ?? ucfirst($this->estado);
    }

    public function getMesTextoAttribute()
    {
        if (!$this->mes) return '-';

        [$anio, $mes] = explode('-', $this->mes);
        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];

        return $meses[$mes] . ' ' . $anio;
    }

    // Métodos auxiliares
    public static function generarNumeroBoleta()
    {
        // Buscar el número más alto de boleta, sin importar si está activa o no
        $ultimaBoleta = self::orderByRaw('CAST(SUBSTRING(numero_boleta, 5) AS UNSIGNED) DESC')->first();
        $numero = $ultimaBoleta ? intval(substr($ultimaBoleta->numero_boleta, 4)) + 1 : 1;
        return 'BOL-' . str_pad($numero, 8, '0', STR_PAD_LEFT);
    }

    public function calcularTotal()
    {
        $this->total = ($this->cargo_fijo + $this->cargo_consumo + $this->otros_cargos) - $this->descuentos;
        return $this->total;
    }

    /**
     * Asignar folio SII automáticamente si hay disponibles
     */
    public function asignarFolioSII($tipoDocumento = 'boleta')
    {
        // Buscar folio disponible
        $folioSII = FolioSII::disponibles()
                           ->tipoDocumento($tipoDocumento)
                           ->orderBy('fecha_creacion', 'asc')
                           ->first();

        if (!$folioSII) {
            // No hay folios disponibles, continuar sin folio (modo normal)
            return false;
        }

        // Obtener siguiente folio
        $numeroFolio = $folioSII->obtenerSiguienteFolio();

        if (!$numeroFolio) {
            // No se pudo obtener folio, continuar sin folio
            return false;
        }

        // Asignar folio a la boleta
        $this->id_folio_sii = $folioSII->id;
        $this->folio_sii = $numeroFolio;
        $this->fecha_timbraje = now();

        // Generar timbre electrónico si hay CAF disponible
        if ($folioSII->caf_xml) {
            $this->timbre_electronico = $this->generarTimbre($folioSII->caf_xml, $numeroFolio);
        }

        return true;
    }

    /**
     * Generar timbre electrónico (placeholder para futura implementación)
     */
    private function generarTimbre($cafXml, $numeroFolio)
    {
        // Por ahora solo guardamos el CAF
        // En el futuro aquí se generaría el timbre PDF417 con firma digital
        return $cafXml;
    }

    /**
     * Verificar si tiene folio SII asignado
     */
    public function tieneFolioSII()
    {
        return !is_null($this->folio_sii);
    }
}
