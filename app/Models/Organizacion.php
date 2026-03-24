<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Organizacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'organizaciones';

    protected $fillable = [
        'nombre_apr',
        'rut',
        'direccion',
        'telefono',
        'email_contacto',
        'slug',
        'dominio_personalizado',
        'id_suscripcion',
        'fecha_inicio_suscripcion',
        'fecha_fin_suscripcion',
        'estado_suscripcion',
        'dias_prueba_restantes',
        'metodo_pago',
        'proximo_pago',
        'logo',
        'color_primario',
        'color_secundario',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio_suscripcion' => 'date',
        'fecha_fin_suscripcion' => 'date',
        'proximo_pago' => 'date',
        'dias_prueba_restantes' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Relación: Una organización pertenece a una suscripción
     */
    public function suscripcion()
    {
        return $this->belongsTo(Suscripcion::class, 'id_suscripcion');
    }

    /**
     * Relación: Una organización tiene muchos usuarios
     */
    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_organizacion');
    }

    /**
     * Relación: Una organización tiene muchos socios
     */
    public function socios()
    {
        return $this->hasMany(Socio::class, 'id_organizacion');
    }

    /**
     * Relación: Una organización tiene muchas noticias
     */
    public function noticias()
    {
        return $this->hasMany(Noticia::class, 'id_organizacion');
    }

    /**
     * Verifica si la suscripción está activa
     */
    public function suscripcionActiva()
    {
        return $this->estado_suscripcion === 'activa';
    }

    /**
     * Verifica si está en período de prueba
     */
    public function enPrueba()
    {
        return $this->estado_suscripcion === 'prueba';
    }

    /**
     * Verifica si la suscripción está vencida
     */
    public function suscripcionVencida()
    {
        return $this->estado_suscripcion === 'vencida' ||
               ($this->fecha_fin_suscripcion && Carbon::parse($this->fecha_fin_suscripcion)->isPast());
    }

    /**
     * Verifica si puede acceder a un módulo
     */
    public function puedeAccederModulo($modulo)
    {
        if (!$this->suscripcionActiva() && !$this->enPrueba()) {
            return false;
        }

        return $this->suscripcion->permiteModulo($modulo);
    }

    /**
     * Verifica si puede agregar más socios
     */
    public function puedeAgregarSocio()
    {
        if ($this->suscripcion->tieneSociosIlimitados()) {
            return true;
        }

        return $this->socios()->count() < $this->suscripcion->max_socios;
    }

    /**
     * Verifica si puede agregar más usuarios
     */
    public function puedeAgregarUsuario()
    {
        if ($this->suscripcion->tieneUsuariosIlimitados()) {
            return true;
        }

        return $this->usuarios()->count() < $this->suscripcion->max_usuarios;
    }

    /**
     * Obtiene la URL del dominio (personalizado o subdominio)
     */
    public function getUrlAttribute()
    {
        if ($this->dominio_personalizado) {
            return 'https://' . $this->dominio_personalizado;
        }

        return 'https://' . $this->slug . '.sistemaapr.cl';
    }

    /**
     * Scope para organizaciones activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true)->whereIn('estado_suscripcion', ['activa', 'prueba']);
    }
}
