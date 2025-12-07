<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketRespuesta extends Model
{
    protected $table = 'ticket_respuestas';

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'id_ticket',
        'id_usuario',
        'id_socio',
        'mensaje',
        'tipo_autor',
        'visible_socio',
        'notificado',
        'activo'
    ];

    protected $casts = [
        'visible_socio' => 'boolean',
        'notificado' => 'boolean',
        'activo' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime'
    ];

    // Relaciones
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'id_ticket');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function socio()
    {
        return $this->belongsTo(Socio::class, 'id_socio');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    public function scopeVisiblesSocio($query)
    {
        return $query->where('visible_socio', 1);
    }

    public function scopeOrdenCronologico($query)
    {
        return $query->orderBy('fecha_creacion', 'asc');
    }

    // Accessors
    public function getFechaCreacionFormateadaAttribute()
    {
        return $this->fecha_creacion ? $this->fecha_creacion->format('d/m/Y H:i') : '-';
    }

    public function getAutorNombreAttribute()
    {
        if ($this->tipo_autor === 'sistema') {
            return 'Sistema';
        } elseif ($this->tipo_autor === 'socio' && $this->socio) {
            return $this->socio->nombre_completo;
        } elseif ($this->tipo_autor === 'funcionario' && $this->usuario) {
            return $this->usuario->name;
        }
        return 'Desconocido';
    }
}
