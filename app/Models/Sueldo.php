<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToOrganizacion;

class Sueldo extends Model
{
    use BelongsToOrganizacion;

    protected $table = 'sueldos';

    protected $fillable = [
        'id_funcionario',
        'periodo',
        'sueldo_base',
        'bonos',
        'descuentos',
        'total_liquido',
        'fecha_pago',
        'metodo_pago',
        'comprobante',
        'observaciones',
        'estado',
        'activo',
    ];

    protected $dates = [
        'fecha_pago',
        'fecha_creacion',
        'fecha_actualizacion',
    ];

    protected $casts = [
        'sueldo_base' => 'decimal:2',
        'bonos' => 'decimal:2',
        'descuentos' => 'decimal:2',
        'total_liquido' => 'decimal:2',
        'activo' => 'boolean',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    // Relaciones
    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'id_funcionario');
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

    public function scopePorFuncionario($query, $idFuncionario)
    {
        return $query->where('id_funcionario', $idFuncionario);
    }

    public function scopePorPeriodo($query, $periodo)
    {
        return $query->where('periodo', $periodo);
    }

    public function scopePorAnio($query, $anio)
    {
        return $query->where('periodo', 'LIKE', $anio . '%');
    }

    // Accessors
    public function getPeriodoFormateadoAttribute()
    {
        if (!$this->periodo) {
            return '-';
        }

        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
            '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
            '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
            '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];

        list($anio, $mes) = explode('-', $this->periodo);
        return $meses[$mes] . ' ' . $anio;
    }

    public function getSueldoBaseFormateadoAttribute()
    {
        return '$' . number_format($this->sueldo_base, 0, ',', '.');
    }

    public function getBonosFormateadoAttribute()
    {
        return '$' . number_format($this->bonos, 0, ',', '.');
    }

    public function getDescuentosFormateadoAttribute()
    {
        return '$' . number_format($this->descuentos, 0, ',', '.');
    }

    public function getTotalLiquidoFormateadoAttribute()
    {
        return '$' . number_format($this->total_liquido, 0, ',', '.');
    }

    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'pendiente' => '<span class="badge badge-warning">Pendiente</span>',
            'pagado' => '<span class="badge badge-success">Pagado</span>',
            'anulado' => '<span class="badge badge-danger">Anulado</span>',
        ];

        return $badges[$this->estado] ?? $this->estado;
    }

    public function getMetodoPagoFormateadoAttribute()
    {
        $metodos = [
            'efectivo' => 'Efectivo',
            'transferencia' => 'Transferencia',
            'cheque' => 'Cheque',
        ];

        return $metodos[$this->metodo_pago] ?? $this->metodo_pago;
    }

    // Mutators
    public function setTotalLiquidoAttribute($value)
    {
        // Calcular automáticamente si no se proporciona
        if (is_null($value)) {
            $this->attributes['total_liquido'] = ($this->sueldo_base ?? 0) + ($this->bonos ?? 0) - ($this->descuentos ?? 0);
        } else {
            $this->attributes['total_liquido'] = $value;
        }
    }

    // Métodos auxiliares
    public function calcularTotalLiquido()
    {
        return $this->sueldo_base + $this->bonos - $this->descuentos;
    }

    public function marcarComoPagado()
    {
        $this->estado = 'pagado';
        $this->save();
    }

    public function anular()
    {
        $this->estado = 'anulado';
        $this->save();
    }
}
