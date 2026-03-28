<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacionSistema extends Model
{
    use HasFactory;

    protected $table = 'notificaciones_sistema';

    protected $fillable = [
        'id_organizacion',
        'id_usuario',
        'tipo',
        'prioridad',
        'titulo',
        'mensaje',
        'icono',
        'color',
        'url',
        'texto_accion',
        'leida',
        'fecha_leida',
        'metadata',
    ];

    protected $casts = [
        'leida' => 'boolean',
        'fecha_leida' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relación: Una notificación pertenece a una organización
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'id_organizacion');
    }

    /**
     * Relación: Una notificación pertenece a un usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Marcar como leída
     */
    public function marcarComoLeida()
    {
        $this->update([
            'leida' => true,
            'fecha_leida' => now(),
        ]);
    }

    /**
     * Scope: Notificaciones no leídas
     */
    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    /**
     * Scope: Notificaciones de una organización
     */
    public function scopeDeOrganizacion($query, $idOrganizacion)
    {
        return $query->where('id_organizacion', $idOrganizacion);
    }

    /**
     * Scope: Notificaciones de un usuario
     */
    public function scopeDeUsuario($query, $idUsuario)
    {
        return $query->where('id_usuario', $idUsuario);
    }

    /**
     * Scope: Por tipo
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope: Por prioridad
     */
    public function scopePrioridad($query, $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }

    /**
     * Crear notificación para toda una organización
     */
    public static function crearParaOrganizacion($idOrganizacion, $datos)
    {
        return self::create(array_merge($datos, [
            'id_organizacion' => $idOrganizacion,
            'id_usuario' => null,
        ]));
    }

    /**
     * Crear notificación para un usuario específico
     */
    public static function crearParaUsuario($idUsuario, $datos)
    {
        $usuario = User::find($idUsuario);

        return self::create(array_merge($datos, [
            'id_organizacion' => $usuario->id_organizacion,
            'id_usuario' => $idUsuario,
        ]));
    }

    /**
     * Crear notificación de pago pendiente
     */
    public static function notificarPagoPendiente($organizacion, $pago, $diasRestantes)
    {
        $urgencia = $diasRestantes <= 1 ? '¡URGENTE! ' : '';
        $color = $diasRestantes <= 3 ? 'danger' : 'warning';

        return self::crearParaOrganizacion($organizacion->id, [
            'tipo' => 'pago_pendiente',
            'prioridad' => $diasRestantes <= 3 ? 'urgente' : 'alta',
            'titulo' => "{$urgencia}Pago de suscripción pendiente",
            'mensaje' => "Tu suscripción vence en {$diasRestantes} día(s). Monto: $" . number_format($pago->monto, 0, ',', '.'),
            'icono' => 'fa-exclamation-triangle',
            'color' => $color,
            'url' => route('organizacion.pagos-suscripcion'),
            'texto_accion' => 'Pagar ahora',
            'metadata' => [
                'id_pago' => $pago->id,
                'monto' => $pago->monto,
                'dias_restantes' => $diasRestantes,
            ],
        ]);
    }

    /**
     * Crear notificación de cuenta suspendida
     */
    public static function notificarCuentaSuspendida($organizacion, $pago)
    {
        return self::crearParaOrganizacion($organizacion->id, [
            'tipo' => 'cuenta_suspendida',
            'prioridad' => 'urgente',
            'titulo' => '⚠️ Cuenta suspendida por falta de pago',
            'mensaje' => 'Tu cuenta ha sido suspendida. Realiza el pago pendiente para reactivar el acceso.',
            'icono' => 'fa-ban',
            'color' => 'danger',
            'url' => route('organizacion.pagos-suscripcion'),
            'texto_accion' => 'Reactivar cuenta',
            'metadata' => [
                'id_pago' => $pago->id,
                'monto' => $pago->monto,
            ],
        ]);
    }

    /**
     * Crear notificación de bienvenida
     */
    public static function notificarBienvenida($organizacion)
    {
        return self::crearParaOrganizacion($organizacion->id, [
            'tipo' => 'bienvenida',
            'prioridad' => 'normal',
            'titulo' => '¡Bienvenido a Sistema APR!',
            'mensaje' => 'Tu cuenta ha sido activada exitosamente. Comienza a gestionar tu APR de forma eficiente.',
            'icono' => 'fa-check-circle',
            'color' => 'success',
            'url' => route('dashboard'),
            'texto_accion' => 'Ir al Dashboard',
        ]);
    }

    /**
     * Crear notificación de límite alcanzado
     */
    public static function notificarLimiteAlcanzado($organizacion, $tipo, $limite)
    {
        $mensajes = [
            'socios' => "Has alcanzado el límite de {$limite} socios de tu plan actual.",
            'usuarios' => "Has alcanzado el límite de {$limite} usuarios de tu plan actual.",
        ];

        return self::crearParaOrganizacion($organizacion->id, [
            'tipo' => $tipo === 'socios' ? 'limite_socios' : 'limite_usuarios',
            'prioridad' => 'alta',
            'titulo' => 'Límite de plan alcanzado',
            'mensaje' => $mensajes[$tipo] ?? 'Has alcanzado un límite de tu plan.',
            'icono' => 'fa-exclamation-circle',
            'color' => 'warning',
            'url' => route('organizacion.upgrade'),
            'texto_accion' => 'Mejorar plan',
            'metadata' => [
                'tipo_limite' => $tipo,
                'limite' => $limite,
            ],
        ]);
    }

    /**
     * Crear notificación de cambio de plan
     */
    public static function notificarCambioPlan($organizacion, $planAnterior, $planNuevo)
    {
        return self::crearParaOrganizacion($organizacion->id, [
            'tipo' => 'cambio_plan',
            'prioridad' => 'normal',
            'titulo' => 'Plan actualizado exitosamente',
            'mensaje' => "Tu plan ha sido actualizado de {$planAnterior} a {$planNuevo}.",
            'icono' => 'fa-arrow-up',
            'color' => 'success',
            'url' => route('organizacion.index'),
            'texto_accion' => 'Ver detalles',
            'metadata' => [
                'plan_anterior' => $planAnterior,
                'plan_nuevo' => $planNuevo,
            ],
        ]);
    }
}
