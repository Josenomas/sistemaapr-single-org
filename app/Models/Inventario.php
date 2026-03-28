<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToOrganizacion;

class Inventario extends Model
{
    use BelongsToOrganizacion;

    protected $table = 'inventario';

    // Especificar los nombres de las columnas de timestamp personalizadas
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'codigo_producto',
        'nombre',
        'categoria',
        'descripcion',
        'unidad_medida',
        'cantidad_actual',
        'cantidad_minima',
        'cantidad_maxima',
        'precio_unitario',
        'ubicacion',
        'proveedor',
        'fecha_ultima_compra',
        'fecha_ultimo_movimiento',
        'estado',
        'observaciones',
        'activo'
    ];

    protected $casts = [
        'cantidad_actual' => 'decimal:2',
        'cantidad_minima' => 'decimal:2',
        'cantidad_maxima' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'fecha_ultima_compra' => 'date',
        'fecha_ultimo_movimiento' => 'date',
        'activo' => 'boolean'
    ];

    // Relaciones
    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class, 'id_producto');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeBajoStock($query)
    {
        return $query->whereRaw('cantidad_actual <= cantidad_minima');
    }

    public function scopeAgotados($query)
    {
        return $query->where('cantidad_actual', '<=', 0);
    }

    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible')
                     ->where('cantidad_actual', '>', 0);
    }

    // Accessors
    public function getCantidadActualFormateadaAttribute()
    {
        return number_format($this->cantidad_actual, 2, ',', '.') . ' ' . $this->unidad_medida;
    }

    public function getCantidadMinimaFormateadaAttribute()
    {
        return number_format($this->cantidad_minima, 2, ',', '.') . ' ' . $this->unidad_medida;
    }

    public function getCantidadMaximaFormateadaAttribute()
    {
        if (!$this->cantidad_maxima) return '-';
        return number_format($this->cantidad_maxima, 2, ',', '.') . ' ' . $this->unidad_medida;
    }

    public function getPrecioUnitarioFormateadoAttribute()
    {
        if (!$this->precio_unitario) return '-';
        return '$' . number_format($this->precio_unitario, 0, ',', '.');
    }

    public function getValorTotalAttribute()
    {
        if (!$this->precio_unitario) return 0;
        return $this->cantidad_actual * $this->precio_unitario;
    }

    public function getValorTotalFormateadoAttribute()
    {
        return '$' . number_format($this->valor_total, 0, ',', '.');
    }

    public function getFechaUltimaCompraFormateadaAttribute()
    {
        return $this->fecha_ultima_compra ? $this->fecha_ultima_compra->format('d/m/Y') : '-';
    }

    public function getFechaUltimoMovimientoFormateadaAttribute()
    {
        return $this->fecha_ultimo_movimiento ? $this->fecha_ultimo_movimiento->format('d/m/Y') : '-';
    }

    public function getFechaCreacionFormateadaAttribute()
    {
        return $this->fecha_creacion ? $this->fecha_creacion->format('d/m/Y H:i') : '-';
    }

    public function getFechaActualizacionFormateadaAttribute()
    {
        return $this->fecha_actualizacion ? $this->fecha_actualizacion->format('d/m/Y H:i') : '-';
    }

    public function getCategoriaBadgeAttribute()
    {
        $categorias = [
            'materiales' => '<span class="badge badge-primary">Materiales</span>',
            'equipos' => '<span class="badge badge-info">Equipos</span>',
            'herramientas' => '<span class="badge badge-warning">Herramientas</span>',
            'insumos' => '<span class="badge badge-success">Insumos</span>',
            'quimicos' => '<span class="badge badge-danger">Químicos</span>',
            'repuestos' => '<span class="badge badge-secondary">Repuestos</span>',
            'otro' => '<span class="badge badge-dark">Otro</span>'
        ];

        return $categorias[$this->categoria] ?? '<span class="badge badge-secondary">' . ucfirst($this->categoria) . '</span>';
    }

    public function getEstadoBadgeAttribute()
    {
        $estados = [
            'disponible' => '<span class="badge badge-success">Disponible</span>',
            'agotado' => '<span class="badge badge-danger">Agotado</span>',
            'bajo_stock' => '<span class="badge badge-warning">Bajo Stock</span>',
            'descontinuado' => '<span class="badge badge-secondary">Descontinuado</span>'
        ];

        return $estados[$this->estado] ?? '<span class="badge badge-secondary">' . ucfirst($this->estado) . '</span>';
    }

    public function getCategoriaTextoAttribute()
    {
        $categorias = [
            'materiales' => 'Materiales',
            'equipos' => 'Equipos',
            'herramientas' => 'Herramientas',
            'insumos' => 'Insumos',
            'quimicos' => 'Químicos',
            'repuestos' => 'Repuestos',
            'otro' => 'Otro'
        ];

        return $categorias[$this->categoria] ?? ucfirst($this->categoria);
    }

    public function getEstadoTextoAttribute()
    {
        $estados = [
            'disponible' => 'Disponible',
            'agotado' => 'Agotado',
            'bajo_stock' => 'Bajo Stock',
            'descontinuado' => 'Descontinuado'
        ];

        return $estados[$this->estado] ?? ucfirst($this->estado);
    }

    public function getPorcentajeStockAttribute()
    {
        if ($this->cantidad_minima <= 0) return 100;
        return ($this->cantidad_actual / $this->cantidad_minima) * 100;
    }

    public function getAlertaStockAttribute()
    {
        if ($this->cantidad_actual <= 0) {
            return ['nivel' => 'critico', 'mensaje' => 'Sin stock'];
        } elseif ($this->cantidad_actual <= $this->cantidad_minima) {
            return ['nivel' => 'bajo', 'mensaje' => 'Stock bajo'];
        } elseif ($this->cantidad_maxima && $this->cantidad_actual >= $this->cantidad_maxima) {
            return ['nivel' => 'alto', 'mensaje' => 'Stock alto'];
        }
        return ['nivel' => 'normal', 'mensaje' => 'Stock normal'];
    }

    // Métodos auxiliares
    public static function generarCodigoProducto()
    {
        $ultimoProducto = self::orderBy('id', 'desc')->first();
        $numero = $ultimoProducto ? intval(substr($ultimoProducto->codigo_producto, 4)) + 1 : 1;
        return 'PROD-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    public function actualizarEstado()
    {
        if ($this->cantidad_actual <= 0) {
            $this->estado = 'agotado';
        } elseif ($this->cantidad_actual <= $this->cantidad_minima) {
            $this->estado = 'bajo_stock';
        } else {
            $this->estado = 'disponible';
        }
        $this->save();
    }
}
