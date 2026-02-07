<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoInventarioDetalle extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario_detalle';

    protected $fillable = [
        'id_movimiento',
        'id_producto',
        'cantidad',
        'cantidad_anterior',
        'cantidad_nueva'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'cantidad_anterior' => 'decimal:2',
        'cantidad_nueva' => 'decimal:2'
    ];

    // Relación con movimiento
    public function movimiento()
    {
        return $this->belongsTo(MovimientoInventario::class, 'id_movimiento');
    }

    // Relación con producto
    public function producto()
    {
        return $this->belongsTo(Inventario::class, 'id_producto');
    }

    // Accessor para cantidad formateada
    public function getCantidadFormateadaAttribute()
    {
        return number_format($this->cantidad, 2, ',', '.');
    }

    public function getCantidadAnteriorFormateadaAttribute()
    {
        return number_format($this->cantidad_anterior, 2, ',', '.');
    }

    public function getCantidadNuevaFormateadaAttribute()
    {
        return number_format($this->cantidad_nueva, 2, ',', '.');
    }
}
