<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToOrganizacion;

class Pago extends Model
{
    use BelongsToOrganizacion;

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
        $organizacionId = auth()->user()->id_organizacion;
        $prefijo = 'REC' . $organizacionId . '-';

        // Buscar el último número de recibo de esta organización con este prefijo
        $ultimo = self::where('numero_recibo', 'LIKE', $prefijo . '%')
            ->orderBy('numero_recibo', 'desc')
            ->lockForUpdate()
            ->first();

        $numero = 1;
        if ($ultimo && preg_match('/' . preg_quote($prefijo) . '(\d+)/', $ultimo->numero_recibo, $matches)) {
            $numero = intval($matches[1]) + 1;
        }

        // Verificar que no exista ya ese número
        do {
            $numeroRecibo = $prefijo . str_pad($numero, 6, '0', STR_PAD_LEFT);
            $existe = self::where('numero_recibo', $numeroRecibo)->exists();
            if ($existe) {
                $numero++;
            }
        } while ($existe);

        return $numeroRecibo;
    }
}
