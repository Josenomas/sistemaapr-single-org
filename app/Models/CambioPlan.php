<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToOrganizacion;

class CambioPlan extends Model
{
    use BelongsToOrganizacion;

    protected $table = 'cambios_plan';

    protected $fillable = [
        'id_organizacion',
        'id_suscripcion_anterior',
        'id_suscripcion_nueva',
        'tipo',
        'estado',
        'monto_anterior',
        'monto_nuevo',
        'monto_diferencia',
        'token_flow',
        'id_transaccion_flow',
        'fecha_solicitud',
        'fecha_aplicacion',
        'observaciones',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'monto_anterior' => 'decimal:2',
        'monto_nuevo' => 'decimal:2',
        'monto_diferencia' => 'decimal:2',
        'fecha_solicitud' => 'datetime',
        'fecha_aplicacion' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Relación con organización
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'id_organizacion');
    }

    /**
     * Relación con suscripción anterior
     */
    public function suscripcionAnterior()
    {
        return $this->belongsTo(Suscripcion::class, 'id_suscripcion_anterior');
    }

    /**
     * Relación con suscripción nueva
     */
    public function suscripcionNueva()
    {
        return $this->belongsTo(Suscripcion::class, 'id_suscripcion_nueva');
    }

    /**
     * Relación con transacción Flow
     */
    public function transaccionFlow()
    {
        return $this->belongsTo(TransaccionFlow::class, 'id_transaccion_flow');
    }

    /**
     * Scope para cambios pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente')->where('activo', 1);
    }

    /**
     * Scope para cambios completados
     */
    public function scopeCompletados($query)
    {
        return $query->where('estado', 'completado')->where('activo', 1);
    }

    /**
     * Verificar si es upgrade
     */
    public function esUpgrade()
    {
        return $this->tipo === 'upgrade';
    }

    /**
     * Verificar si es downgrade
     */
    public function esDowngrade()
    {
        return $this->tipo === 'downgrade';
    }

    /**
     * Aplicar el cambio de plan
     */
    public function aplicar()
    {
        if ($this->estado !== 'pendiente' && $this->estado !== 'procesando') {
            return false;
        }

        // Si la suscripción está vencida, reactivarla con el nuevo plan
        if ($this->organizacion->suscripcionVencida()) {
            $this->organizacion->update([
                'id_suscripcion' => $this->id_suscripcion_nueva,
                'estado_suscripcion' => 'activa',
                'fecha_inicio_suscripcion' => now(),
                'fecha_fin_suscripcion' => now()->addMonthNoOverflow(),
                'activo' => true,
                'dias_prueba_restantes' => 0,
            ]);

            // Cancelar pagos pendientes antiguos del plan anterior
            \App\Models\PagoSuscripcion::where('id_organizacion', $this->id_organizacion)
                ->where('estado', 'pendiente')
                ->update(['estado' => 'cancelado']);

            // Crear registro de pago para el cambio de plan
            \App\Models\PagoSuscripcion::create([
                'id_organizacion' => $this->id_organizacion,
                'id_suscripcion' => $this->id_suscripcion_nueva,
                'monto' => $this->monto_diferencia,
                'estado' => 'pagado',
                'periodo_inicio' => now(),
                'periodo_fin' => now()->addMonthNoOverflow(),
                'fecha_pago' => now(),
                'metodo_pago' => 'flow',
                'token_flow' => $this->token_flow,
            ]);
        } else {
            // Si está activa, solo cambiar el plan (el cambio se aplicará en la renovación)
            $this->organizacion->update([
                'id_suscripcion' => $this->id_suscripcion_nueva,
            ]);
        }

        $this->update([
            'estado' => 'completado',
            'fecha_aplicacion' => now(),
        ]);

        return true;
    }
}
