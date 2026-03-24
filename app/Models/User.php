<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id_organizacion',
        'rol',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relación: Un usuario pertenece a una organización
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'id_organizacion');
    }

    /**
     * Verifica si el usuario es admin
     */
    public function esAdmin()
    {
        return $this->rol === 'admin';
    }

    /**
     * Verifica si el usuario puede acceder a un módulo
     */
    public function puedeAccederModulo($modulo)
    {
        if (!$this->organizacion) {
            return false;
        }

        return $this->organizacion->puedeAccederModulo($modulo);
    }

    /**
     * Verifica si el usuario tiene permiso de escritura (no es solo_lectura)
     */
    public function puedeEditar()
    {
        return $this->rol !== 'solo_lectura';
    }

    /**
     * Obtiene la suscripción de la organización
     */
    public function getSuscripcionAttribute()
    {
        return $this->organizacion ? $this->organizacion->suscripcion : null;
    }
}
