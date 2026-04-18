<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reclamo extends Model
{
    use HasFactory;

    protected $table = 'reclamos';

    protected $fillable = [
        'numero_reclamo',
        'id_organizacion',
        'nombre_completo',
        'rut',
        'email',
        'telefono',
        'direccion',
        'tipo_reclamo',
        'detalle_reclamo',
        'solucion_solicitada',
        'estado',
        'respuesta',
        'fecha_respuesta',
        'respondido_por',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'fecha_respuesta' => 'datetime',
    ];

    /**
     * Generar número de reclamo correlativo
     */
    public static function generarNumeroReclamo()
    {
        $ultimoReclamo = self::orderBy('id', 'desc')->first();

        if ($ultimoReclamo) {
            $ultimoNumero = (int) str_replace('REC-', '', $ultimoReclamo->numero_reclamo);
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            $nuevoNumero = 1;
        }

        return 'REC-' . str_pad($nuevoNumero, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Relación con organización
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'id_organizacion');
    }

    /**
     * Relación con usuario que respondió
     */
    public function respondidoPor()
    {
        return $this->belongsTo(Usuario::class, 'respondido_por');
    }

    /**
     * Obtener nombre del tipo de reclamo
     */
    public function getTipoReclamoNombreAttribute()
    {
        $tipos = [
            'servicio' => 'Servicio',
            'facturacion' => 'Facturación',
            'soporte' => 'Soporte Técnico',
            'funcionalidad' => 'Funcionalidad del Sistema',
            'otro' => 'Otro',
        ];

        return $tipos[$this->tipo_reclamo] ?? $this->tipo_reclamo;
    }

    /**
     * Verificar si está dentro del plazo de 5 días hábiles
     */
    public function getDentroDelPlazoAttribute()
    {
        if ($this->estado === 'resuelto' || $this->estado === 'rechazado') {
            return true;
        }

        $diasTranscurridos = $this->created_at->diffInDays(now());
        return $diasTranscurridos <= 5;
    }
}
