<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToOrganizacion;

class TransaccionFlow extends Model
{
    use BelongsToOrganizacion;

    protected $table = 'transacciones_flow';

    protected $fillable = [
        'flow_order',
        'token',
        'id_socio',
        'id_boleta',
        'id_organizacion',
        'monto',
        'email',
        'subject',
        'url_confirmacion',
        'url_retorno',
        'estado',
        'tipo_pago',
        'referencia_id',
        'flow_status',
        'payment_data',
        'fecha_pago',
        'observaciones',
        'boletas_ids',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'monto' => 'decimal:2',
        'flow_order' => 'integer',
        'flow_status' => 'integer',
        'fecha_creacion' => 'datetime',
        'fecha_pago' => 'datetime',
        'fecha_actualizacion' => 'datetime',
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
     * Relación con boleta
     */
    public function boleta()
    {
        return $this->belongsTo(Boleta::class, 'id_boleta');
    }

    /**
     * Scope para transacciones pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente')->where('activo', 1);
    }

    /**
     * Scope para transacciones pagadas
     */
    public function scopePagadas($query)
    {
        return $query->where('estado', 'pagado')->where('activo', 1);
    }

    /**
     * Obtener estado formateado
     */
    public function getEstadoFormateadoAttribute()
    {
        $estados = [
            'pendiente' => 'Pendiente',
            'pagado' => 'Pagado',
            'rechazado' => 'Rechazado',
            'anulado' => 'Anulado',
            'expirado' => 'Expirado',
        ];

        return $estados[$this->estado] ?? ucfirst($this->estado);
    }

    /**
     * Obtener monto formateado
     */
    public function getMontoFormateadoAttribute()
    {
        return '$' . number_format($this->monto, 0, ',', '.');
    }

    /**
     * Obtener datos de pago decodificados
     */
    public function getPaymentDataDecodedAttribute()
    {
        return $this->payment_data ? json_decode($this->payment_data, true) : null;
    }
}
