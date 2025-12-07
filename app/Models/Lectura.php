<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lectura extends Model
{
    protected $table = 'lecturas';

    protected $fillable = [
        'id_socio',
        'mes',
        'lectura_anterior',
        'lectura_actual',
        'consumo_m3',
        'fecha_lectura',
        'observaciones',
        'id_usuario_registro',
    ];

    protected $casts = [
        'lectura_anterior' => 'decimal:2',
        'lectura_actual' => 'decimal:2',
        'consumo_m3' => 'decimal:2',
        'fecha_lectura' => 'date',
        'fecha_creacion' => 'datetime',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = null;

    /**
     * Relación con socio
     */
    public function socio()
    {
        return $this->belongsTo(Socio::class, 'id_socio');
    }

    /**
     * Relación con usuario que registró
     */
    public function usuarioRegistro()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_registro');
    }

    /**
     * Relación con boleta
     */
    public function boleta()
    {
        return $this->hasOne(Boleta::class, 'id_lectura');
    }

    /**
     * Scope para lecturas por mes
     */
    public function scopePorMes($query, $mes)
    {
        return $query->where('mes', $mes);
    }

    /**
     * Scope para lecturas del mes actual
     */
    public function scopeMesActual($query)
    {
        return $query->where('mes', date('Y-m'));
    }

    /**
     * Calcular consumo automáticamente
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($lectura) {
            if (!$lectura->consumo_m3) {
                $lectura->consumo_m3 = $lectura->lectura_actual - $lectura->lectura_anterior;
            }
        });
    }
}
