<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoSuscripcion extends Model
{
    use HasFactory;

    protected $table = 'pagos_suscripcion';

    protected $fillable = [
        'id_organizacion',
        'id_suscripcion',
        'monto',
        'metodo_pago',
        'estado',
        'periodo_inicio',
        'periodo_fin',
        'token_flow',
        'id_transaccion_flow',
        'orden_compra',
        'notas',
        'fecha_pago',
        'fecha_vencimiento',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'periodo_inicio' => 'date',
        'periodo_fin' => 'date',
    ];

    /**
     * Relación con Organización
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'id_organizacion');
    }

    /**
     * Relación con Suscripción
     */
    public function suscripcion()
    {
        return $this->belongsTo(Suscripcion::class, 'id_suscripcion');
    }

    /**
     * Verificar si el pago está vencido
     */
    public function estaVencido()
    {
        if (!$this->fecha_vencimiento) {
            return false;
        }
        return now()->isAfter($this->fecha_vencimiento) && $this->estado === 'pendiente';
    }

    /**
     * Marcar como pagado
     */
    public function marcarComoPagado($tokenFlow = null, $idTransaccion = null)
    {
        $this->update([
            'estado' => 'pagado',
            'fecha_pago' => now(),
            'token_flow' => $tokenFlow,
            'id_transaccion_flow' => $idTransaccion,
        ]);
    }

    /**
     * Marcar como fallido
     */
    public function marcarComoFallido($nota = null)
    {
        $this->update([
            'estado' => 'fallido',
            'notas' => $nota,
        ]);
    }

    /**
     * Scope para pagos pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para pagos vencidos
     */
    public function scopeVencidos($query)
    {
        return $query->where('estado', 'pendiente')
                     ->where('fecha_vencimiento', '<', now());
    }

    /**
     * Scope para pagos por vencer
     */
    public function scopePorVencer($query, $dias = 7)
    {
        return $query->where('estado', 'pendiente')
                     ->whereBetween('fecha_vencimiento', [now(), now()->addDays($dias)]);
    }
}
