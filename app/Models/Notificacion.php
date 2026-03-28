<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToOrganizacion;

class Notificacion extends Model
{
    use BelongsToOrganizacion;

    protected $table = 'notificaciones';

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'titulo', 'mensaje', 'tipo', 'destinatario', 'id_socio', 'sector',
        'estado', 'fecha_programada', 'fecha_enviada', 'canal',
        'enviado_email', 'enviado_sms', 'enviado_whatsapp',
        'total_destinatarios', 'total_enviados', 'total_leidos', 'total_errores',
        'id_usuario_creador', 'observaciones', 'activo'
    ];

    protected $casts = [
        'fecha_programada' => 'date',
        'fecha_enviada' => 'datetime',
        'enviado_email' => 'boolean',
        'enviado_sms' => 'boolean',
        'enviado_whatsapp' => 'boolean',
        'activo' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime'
    ];

    public function socio() { return $this->belongsTo(Socio::class, 'id_socio'); }
    public function usuarioCreador() { return $this->belongsTo(Usuario::class, 'id_usuario_creador'); }

    public function scopeActivos($query) { return $query->where('activo', 1); }

    public function getTipoBadgeAttribute()
    {
        $badges = [
            'informativa' => '<span class="badge badge-info">Informativa</span>',
            'importante' => '<span class="badge badge-warning">Importante</span>',
            'urgente' => '<span class="badge badge-danger">Urgente</span>',
            'recordatorio' => '<span class="badge badge-primary">Recordatorio</span>',
            'aviso_pago' => '<span class="badge badge-success">Aviso de Pago</span>',
            'corte_servicio' => '<span class="badge badge-danger">Corte de Servicio</span>',
            'corte' => '<span class="badge badge-danger">Corte</span>',
            'reunion' => '<span class="badge badge-primary">Reunión</span>',
        ];
        return $badges[$this->tipo] ?? '<span class="badge badge-light">' . ucfirst($this->tipo) . '</span>';
    }

    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'borrador' => '<span class="badge badge-secondary">Borrador</span>',
            'programada' => '<span class="badge badge-info">Programada</span>',
            'enviada' => '<span class="badge badge-success">Enviada</span>',
            'cancelada' => '<span class="badge badge-danger">Cancelada</span>',
        ];
        return $badges[$this->estado] ?? '<span class="badge badge-light">' . ucfirst($this->estado) . '</span>';
    }

    public function getDestinatarioTextoAttribute()
    {
        $textos = [
            'todos' => 'Todos los Socios',
            'morosos' => 'Socios Morosos',
            'activos' => 'Socios Activos',
            'sector' => 'Sector: ' . ($this->sector ?? 'No especificado'),
            'individual' => $this->socio ? $this->socio->nombre_completo : 'Socio Individual',
            'socio' => $this->socio ? $this->socio->nombre_completo : 'Socio Específico',
        ];
        return $textos[$this->destinatario] ?? ucfirst($this->destinatario);
    }

    public function getFechaProgramadaFormateadaAttribute()
    {
        return $this->fecha_programada ? $this->fecha_programada->format('d/m/Y') : '-';
    }
}
