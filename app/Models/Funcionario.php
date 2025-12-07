<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    protected $table = 'funcionarios';

    protected $fillable = [
        'rut',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'cargo',
        'email',
        'telefono',
        'direccion',
        'fecha_ingreso',
        'fecha_termino',
        'estado',
        'observaciones',
        'activo',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_termino' => 'date',
        'activo' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    /**
     * Scope para funcionarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    /**
     * Scope para funcionarios por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para funcionarios por cargo
     */
    public function scopePorCargo($query, $cargo)
    {
        return $query->where('cargo', $cargo);
    }

    /**
     * Obtener nombre completo
     */
    public function getNombreCompletoAttribute()
    {
        $nombre = $this->nombre . ' ' . $this->apellido_paterno;
        if ($this->apellido_materno) {
            $nombre .= ' ' . $this->apellido_materno;
        }
        return $nombre;
    }

    /**
     * Obtener iniciales
     */
    public function getInicialesAttribute()
    {
        $iniciales = strtoupper(substr($this->nombre, 0, 1) . substr($this->apellido_paterno, 0, 1));
        return $iniciales;
    }

    /**
     * Obtener años de servicio
     */
    public function getAniosServicioAttribute()
    {
        $fechaFin = $this->fecha_termino ?? now();
        return $this->fecha_ingreso->diffInYears($fechaFin);
    }
}
