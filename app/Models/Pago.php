<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'numero_recibo',
        'id_boleta',
        'id_socio',
        'fecha_pago',
        'monto_pagado',
        'metodo_pago',
        'numero_comprobante',
        'observaciones',
        'id_usuario_registro',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto_pagado' => 'decimal:2',
        'fecha_creacion' => 'datetime',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = null;

    /**
     * Relación con boleta
     */
    public function boleta()
    {
        return $this->belongsTo(Boleta::class, 'id_boleta');
    }

    /**
     * Relación con socio
     */
    public function socio()
    {
        return $this->belongsTo(Socio::class, 'id_socio');
    }

    /**
     * Relación con usuario que registró
     */
    public function usuarioRegistro()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_registro');
    }

    /**
     * Scope por fecha
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha_pago', $fecha);
    }

    /**
     * Scope por rango de fechas
     */
    public function scopePorRango($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_pago', [$desde, $hasta]);
    }

    /**
     * Scope por método de pago
     */
    public function scopePorMetodo($query, $metodo)
    {
        return $query->where('metodo_pago', $metodo);
    }

    /**
     * Accessors
     */
    public function getFechaPagoFormateadaAttribute()
    {
        return $this->fecha_pago ? $this->fecha_pago->format('d/m/Y') : '';
    }

    public function getMontoPagadoFormateadoAttribute()
    {
        return '$' . number_format($this->monto_pagado, 0, ',', '.');
    }

    public function getMetodoPagoTextoAttribute()
    {
        $metodos = [
            'efectivo' => 'Efectivo',
            'transferencia' => 'Transferencia',
            'cheque' => 'Cheque',
            'debito' => 'Débito',
            'credito' => 'Crédito'
        ];

        return $metodos[$this->metodo_pago] ?? ucfirst($this->metodo_pago);
    }

    public function getMetodoPagoBadgeAttribute()
    {
        $badges = [
            'efectivo' => '<span class="badge badge-success">Efectivo</span>',
            'transferencia' => '<span class="badge badge-info">Transferencia</span>',
            'cheque' => '<span class="badge badge-warning">Cheque</span>',
            'debito' => '<span class="badge badge-primary">Débito</span>',
            'credito' => '<span class="badge badge-secondary">Crédito</span>'
        ];

        return $badges[$this->metodo_pago] ?? '<span class="badge badge-light">' . ucfirst($this->metodo_pago) . '</span>';
    }

    /**
     * Generar número de recibo automático
     */
    public static function generarNumeroRecibo()
    {
        $ultimo = self::orderBy('id', 'desc')->first();
        $numero = $ultimo ? intval(substr($ultimo->numero_recibo, 4)) + 1 : 1;
        return 'REC-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}
