<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudPagoManual extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_pago_manual';

    protected $fillable = [
        'id_pago_suscripcion',
        'id_organizacion',
        'comprobante_path',
        'numero_operacion',
        'banco_origen',
        'fecha_transferencia',
        'monto',
        'estado',
        'revisado_por',
        'motivo_rechazo',
        'notas',
        'fecha_revision',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_transferencia' => 'date',
        'fecha_revision' => 'datetime',
    ];

    /**
     * Relación con PagoSuscripcion
     */
    public function pagoSuscripcion()
    {
        return $this->belongsTo(PagoSuscripcion::class, 'id_pago_suscripcion');
    }

    /**
     * Relación con Organización
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'id_organizacion');
    }

    /**
     * Relación con el usuario que revisó (super admin)
     */
    public function revisor()
    {
        return $this->belongsTo(Usuario::class, 'revisado_por');
    }

    /**
     * Scope para solicitudes pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para solicitudes aprobadas
     */
    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    /**
     * Scope para solicitudes rechazadas
     */
    public function scopeRechazadas($query)
    {
        return $query->where('estado', 'rechazada');
    }

    /**
     * Marcar como aprobada
     */
    public function aprobar($idRevisor)
    {
        $this->update([
            'estado' => 'aprobada',
            'revisado_por' => $idRevisor,
            'fecha_revision' => now(),
        ]);

        // Marcar el pago de suscripción como pagado
        $this->pagoSuscripcion->update([
            'estado' => 'pagado',
            'metodo_pago' => 'manual',
            'fecha_pago' => now(),
            'notas' => 'Pago manual aprobado. Operación: ' . $this->numero_operacion,
        ]);

        // Actualizar la suscripción de la organización
        $organizacion = $this->organizacion;
        $organizacion->update([
            'estado_suscripcion' => 'activa',
            'fecha_inicio_suscripcion' => now(),
            'fecha_fin_suscripcion' => now()->addMonthNoOverflow(),
            'metodo_pago' => 'transferencia',
            'proximo_pago' => now()->addMonthNoOverflow(),
        ]);
    }

    /**
     * Marcar como rechazada
     */
    public function rechazar($idRevisor, $motivo)
    {
        $this->update([
            'estado' => 'rechazada',
            'revisado_por' => $idRevisor,
            'motivo_rechazo' => $motivo,
            'fecha_revision' => now(),
        ]);
    }
}
