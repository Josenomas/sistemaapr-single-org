<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiroBancario extends Model
{
    use HasFactory;

    protected $table = 'giros_bancarios';

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'numero_giro',
        'banco',
        'numero_cuenta',
        'tipo_cuenta',
        'beneficiario',
        'rut_beneficiario',
        'monto',
        'fecha_emision',
        'fecha_pago',
        'concepto',
        'descripcion',
        'estado',
        'metodo_entrega',
        'numero_comprobante',
        'id_responsable',
        'observaciones',
        'activo'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_emision' => 'date',
        'fecha_pago' => 'date',
        'activo' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime'
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

    public function scopeEmitidos($query)
    {
        return $query->where('estado', 'emitido');
    }

    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }

    public function scopeAnulados($query)
    {
        return $query->where('estado', 'anulado');
    }

    public function scopeVencidos($query)
    {
        return $query->where('estado', 'vencido');
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePorBanco($query, $banco)
    {
        return $query->where('banco', $banco);
    }

    // Accessors
    public function getFechaEmisionFormateadaAttribute()
    {
        return $this->fecha_emision ? $this->fecha_emision->format('d/m/Y') : '';
    }

    public function getFechaPagoFormateadaAttribute()
    {
        return $this->fecha_pago ? $this->fecha_pago->format('d/m/Y') : '-';
    }

    public function getMontoFormateadoAttribute()
    {
        return '$' . number_format($this->monto, 0, ',', '.');
    }

    public function getEstadoTextoAttribute()
    {
        $estados = [
            'emitido' => 'Emitido',
            'pagado' => 'Pagado',
            'anulado' => 'Anulado',
            'vencido' => 'Vencido'
        ];

        return $estados[$this->estado] ?? ucfirst($this->estado);
    }

    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'emitido' => '<span class="badge badge-info">Emitido</span>',
            'pagado' => '<span class="badge badge-success">Pagado</span>',
            'anulado' => '<span class="badge badge-danger">Anulado</span>',
            'vencido' => '<span class="badge badge-warning">Vencido</span>'
        ];

        return $badges[$this->estado] ?? '<span class="badge badge-secondary">' . ucfirst($this->estado) . '</span>';
    }

    public function getTipoCuentaTextoAttribute()
    {
        $tipos = [
            'corriente' => 'Cuenta Corriente',
            'vista' => 'Cuenta Vista',
            'ahorro' => 'Cuenta de Ahorro'
        ];

        return $tipos[$this->tipo_cuenta] ?? ucfirst($this->tipo_cuenta);
    }

    public function getTipoCuentaBadgeAttribute()
    {
        $badges = [
            'corriente' => '<span class="badge badge-primary">Corriente</span>',
            'vista' => '<span class="badge badge-info">Vista</span>',
            'ahorro' => '<span class="badge badge-success">Ahorro</span>'
        ];

        return $badges[$this->tipo_cuenta] ?? '<span class="badge badge-secondary">' . ucfirst($this->tipo_cuenta) . '</span>';
    }

    public function getMetodoEntregaTextoAttribute()
    {
        $metodos = [
            'retiro_sucursal' => 'Retiro en Sucursal',
            'transferencia' => 'Transferencia Bancaria',
            'cheque' => 'Cheque'
        ];

        return $metodos[$this->metodo_entrega] ?? ucfirst($this->metodo_entrega);
    }

    // Métodos estáticos
    public static function generarNumeroGiro()
    {
        $ultimoGiro = self::orderBy('id', 'desc')->first();

        if (!$ultimoGiro) {
            return 'GIRO-' . date('Y') . '-00001';
        }

        $ultimoNumero = intval(substr($ultimoGiro->numero_giro, -5));
        $nuevoNumero = $ultimoNumero + 1;

        return 'GIRO-' . date('Y') . '-' . str_pad($nuevoNumero, 5, '0', STR_PAD_LEFT);
    }
}
