<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RenovacionSuscripcion extends Model
{
    use HasFactory;

    protected $table = 'renovaciones_suscripcion';

    protected $fillable = [
        'id_organizacion',
        'fecha_vencimiento',
        'fecha_procesada',
        'monto',
        'estado',
        'metodo_pago',
        'token_flow',
        'respuesta_flow',
        'intentos_notificacion',
        'notificado_7dias',
        'notificado_3dias',
        'notificado_1dia',
        'notificado_vencido',
        'notas',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_procesada' => 'date',
        'notificado_7dias' => 'datetime',
        'notificado_3dias' => 'datetime',
        'notificado_1dia' => 'datetime',
        'notificado_vencido' => 'datetime',
        'monto' => 'decimal:2',
    ];

    /**
     * Relación con Organización
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'id_organizacion');
    }

    /**
     * Verificar si está vencida
     */
    public function estaVencida()
    {
        return $this->fecha_vencimiento < now()->toDateString() && $this->estado === 'pendiente';
    }

    /**
     * Verificar si necesita notificación de 7 días
     */
    public function necesitaNotificacion7Dias()
    {
        $diasRestantes = now()->diffInDays($this->fecha_vencimiento, false);
        return $diasRestantes <= 7 && $diasRestantes > 3 && !$this->notificado_7dias;
    }

    /**
     * Verificar si necesita notificación de 3 días
     */
    public function necesitaNotificacion3Dias()
    {
        $diasRestantes = now()->diffInDays($this->fecha_vencimiento, false);
        return $diasRestantes <= 3 && $diasRestantes > 1 && !$this->notificado_3dias;
    }

    /**
     * Verificar si necesita notificación de 1 día
     */
    public function necesitaNotificacion1Dia()
    {
        $diasRestantes = now()->diffInDays($this->fecha_vencimiento, false);
        return $diasRestantes <= 1 && $diasRestantes >= 0 && !$this->notificado_1dia;
    }

    /**
     * Marcar notificación como enviada
     */
    public function marcarNotificacion($tipo)
    {
        $campo = "notificado_{$tipo}";
        $this->update([
            $campo => now(),
            'intentos_notificacion' => $this->intentos_notificacion + 1,
        ]);
    }

    /**
     * Marcar como pagada
     */
    public function marcarComoPagada($metodoPago, $respuestaFlow = null)
    {
        $this->update([
            'estado' => 'pagado',
            'fecha_procesada' => now(),
            'metodo_pago' => $metodoPago,
            'respuesta_flow' => $respuestaFlow,
        ]);
    }

    /**
     * Marcar como fallida
     */
    public function marcarComoFallida($notas = null)
    {
        $this->update([
            'estado' => 'fallido',
            'fecha_procesada' => now(),
            'notas' => $notas,
        ]);
    }
}
