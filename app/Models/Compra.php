<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras';

    protected $fillable = [
        'numero_compra',
        'fecha_compra',
        'proveedor',
        'rut_proveedor',
        'tipo_compra',
        'descripcion',
        'cantidad',
        'unidad_medida',
        'precio_unitario',
        'subtotal',
        'iva',
        'total',
        'metodo_pago',
        'numero_factura',
        'fecha_pago',
        'estado',
        'id_responsable',
        'observaciones',
        'activo'
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'fecha_pago' => 'date',
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
        'activo' => 'boolean'
    ];

    // Relaciones
    public function responsable()
    {
        return $this->belongsTo(Funcionario::class, 'id_responsable');
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
        return $query->where('tipo_compra', $tipo);
    }

    public function scopePorProveedor($query, $proveedor)
    {
        return $query->where('proveedor', 'LIKE', "%{$proveedor}%");
    }

    public function scopePorFecha($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_compra', [$fechaInicio, $fechaFin]);
    }

    // Accessors
    public function getFechaCompraFormateadaAttribute()
    {
        return $this->fecha_compra ? $this->fecha_compra->format('d/m/Y') : '-';
    }

    public function getFechaPagoFormateadaAttribute()
    {
        return $this->fecha_pago ? $this->fecha_pago->format('d/m/Y') : '-';
    }

    public function getSubtotalFormateadoAttribute()
    {
        return '$' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getIvaFormateadoAttribute()
    {
        return '$' . number_format($this->iva, 0, ',', '.');
    }

    public function getTotalFormateadoAttribute()
    {
        return '$' . number_format($this->total, 0, ',', '.');
    }

    public function getPrecioUnitarioFormateadoAttribute()
    {
        return '$' . number_format($this->precio_unitario, 0, ',', '.');
    }

    public function getCantidadFormateadaAttribute()
    {
        return number_format($this->cantidad, 2, ',', '.') . ' ' . ($this->unidad_medida ?? 'unidades');
    }

    public function getTipoCompraBadgeAttribute()
    {
        $tipos = [
            'materiales' => '<span class="badge badge-primary">Materiales</span>',
            'equipos' => '<span class="badge badge-info">Equipos</span>',
            'herramientas' => '<span class="badge badge-warning">Herramientas</span>',
            'insumos' => '<span class="badge badge-success">Insumos</span>',
            'servicios' => '<span class="badge badge-secondary">Servicios</span>',
            'otro' => '<span class="badge badge-dark">Otro</span>'
        ];

        return $tipos[$this->tipo_compra] ?? '<span class="badge badge-secondary">' . ucfirst($this->tipo_compra) . '</span>';
    }

    public function getEstadoBadgeAttribute()
    {
        $estados = [
            'pendiente' => '<span class="badge badge-warning">Pendiente</span>',
            'pagada' => '<span class="badge badge-success">Pagada</span>',
            'anulada' => '<span class="badge badge-danger">Anulada</span>'
        ];

        return $estados[$this->estado] ?? '<span class="badge badge-secondary">' . ucfirst($this->estado) . '</span>';
    }

    public function getMetodoPagoTextoAttribute()
    {
        $metodos = [
            'efectivo' => 'Efectivo',
            'transferencia' => 'Transferencia',
            'cheque' => 'Cheque',
            'credito' => 'Crédito'
        ];

        return $metodos[$this->metodo_pago] ?? ucfirst($this->metodo_pago);
    }

    public function getTipoCompraTextoAttribute()
    {
        $tipos = [
            'materiales' => 'Materiales',
            'equipos' => 'Equipos',
            'herramientas' => 'Herramientas',
            'insumos' => 'Insumos',
            'servicios' => 'Servicios',
            'otro' => 'Otro'
        ];

        return $tipos[$this->tipo_compra] ?? ucfirst($this->tipo_compra);
    }

    public function getEstadoTextoAttribute()
    {
        $estados = [
            'pendiente' => 'Pendiente',
            'pagada' => 'Pagada',
            'anulada' => 'Anulada'
        ];

        return $estados[$this->estado] ?? ucfirst($this->estado);
    }

    // Mutators
    public function setSubtotalAttribute($value)
    {
        $this->attributes['subtotal'] = $this->cantidad * $this->precio_unitario;
    }

    public function setTotalAttribute($value)
    {
        $this->attributes['total'] = $this->subtotal + $this->iva;
    }

    // Métodos auxiliares
    public static function generarNumeroCompra()
    {
        $ultimaCompra = self::orderBy('id', 'desc')->first();
        $numero = $ultimaCompra ? intval(substr($ultimaCompra->numero_compra, 4)) + 1 : 1;
        return 'COM-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}
