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

        $this->organizacion->update([
            'id_suscripcion' => $this->id_suscripcion_nueva,
        ]);

        $this->update([
            'estado' => 'completado',
            'fecha_aplicacion' => now(),
        ]);

        return true;
    }
}
