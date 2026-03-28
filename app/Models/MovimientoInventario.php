<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToOrganizacion;

class MovimientoInventario extends Model
{
    use HasFactory, BelongsToOrganizacion;

    protected $table = 'movimientos_inventario';

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'numero_movimiento',
        'id_producto',
        'tipo_movimiento',
        'cantidad',
        'cantidad_anterior',
        'cantidad_nueva',
        'motivo',
        'descripcion',
        'id_responsable',
        'destino',
        'documento_referencia',
        'fecha_movimiento',
        'observaciones',
        'activo'
    ];

    protected $casts = [
        'fecha_movimiento' => 'date',
        'cantidad' => 'decimal:2',
        'cantidad_anterior' => 'decimal:2',
        'cantidad_nueva' => 'decimal:2',
        'activo' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime'
    ];

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Inventario::class, 'id_producto');
    }

    public function responsable()
    {
        return $this->belongsTo(Funcionario::class, 'id_responsable');
    }

    public function detalles()
    {
        return $this->hasMany(MovimientoInventarioDetalle::class, 'id_movimiento');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    public function scopeEntradas($query)
    {
        return $query->where('tipo_movimiento', 'entrada');
    }

    public function scopeSalidas($query)
    {
        return $query->where('tipo_movimiento', 'salida');
    }

    public function scopeAjustes($query)
    {
        return $query->where('tipo_movimiento', 'ajuste');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_movimiento', $tipo);
    }

    public function scopePorProducto($query, $productoId)
    {
        return $query->where('id_producto', $productoId);
    }

    // Accessors
    public function getFechaMovimientoFormateadaAttribute()
    {
        return $this->fecha_movimiento ? $this->fecha_movimiento->format('d/m/Y') : '';
    }

    public function getTipoMovimientoTextoAttribute()
    {
        $tipos = [
            'entrada' => 'Entrada',
            'salida' => 'Salida',
            'ajuste' => 'Ajuste'
        ];

        return $tipos[$this->tipo_movimiento] ?? ucfirst($this->tipo_movimiento);
    }

    public function getTipoMovimientoBadgeAttribute()
    {
        $badges = [
            'entrada' => '<span class="badge badge-success">Entrada</span>',
            'salida' => '<span class="badge badge-warning">Salida</span>',
            'ajuste' => '<span class="badge badge-info">Ajuste</span>'
        ];

        return $badges[$this->tipo_movimiento] ?? '<span class="badge badge-secondary">' . ucfirst($this->tipo_movimiento) . '</span>';
    }

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

    // Métodos estáticos
    public static function generarNumeroMovimiento()
    {
        $ultimoMovimiento = self::orderBy('id', 'desc')->first();

        if (!$ultimoMovimiento) {
            return 'MOV-00001';
        }

        $ultimoNumero = intval(substr($ultimoMovimiento->numero_movimiento, 4));
        $nuevoNumero = $ultimoNumero + 1;

        return 'MOV-' . str_pad($nuevoNumero, 5, '0', STR_PAD_LEFT);
    }
}
