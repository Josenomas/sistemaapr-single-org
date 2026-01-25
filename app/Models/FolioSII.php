<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Usuario;

class FolioSII extends Model
{
    protected $table = 'folios_sii';

    protected $fillable = [
        'tipo_documento',
        'folio_desde',
        'folio_hasta',
        'folio_actual',
        'fecha_autorizacion',
        'fecha_vencimiento',
        'caf_xml',
        'estado',
        'folios_disponibles',
        'id_usuario_carga',
        'observaciones',
        'activo'
    ];

    protected $casts = [
        'fecha_autorizacion' => 'date',
        'fecha_vencimiento' => 'date',
        'activo' => 'boolean',
        'folio_desde' => 'integer',
        'folio_hasta' => 'integer',
        'folio_actual' => 'integer',
        'folios_disponibles' => 'integer',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    /**
     * Relación con el usuario que cargó el folio
     */
    public function usuarioCarga()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_carga');
    }

    /**
     * Scope para folios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para folios disponibles
     */
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'activo')
                     ->where('folios_disponibles', '>', 0)
                     ->where('fecha_vencimiento', '>=', now());
    }

    /**
     * Scope por tipo de documento
     */
    public function scopeTipoDocumento($query, $tipo)
    {
        return $query->where('tipo_documento', $tipo);
    }

    /**
     * Obtener siguiente folio disponible
     */
    public function obtenerSiguienteFolio()
    {
        if ($this->estado !== 'activo' || $this->folios_disponibles <= 0) {
            return null;
        }

        if ($this->fecha_vencimiento < now()) {
            $this->update(['estado' => 'vencido']);
            return null;
        }

        if ($this->folio_actual >= $this->folio_hasta) {
            $this->update([
                'estado' => 'agotado',
                'folios_disponibles' => 0
            ]);
            return null;
        }

        $siguienteFolio = $this->folio_actual + 1;
        $foliosDisponibles = $this->folio_hasta - $siguienteFolio;

        // Determinar el estado basado en folios disponibles
        $nuevoEstado = $foliosDisponibles <= 0 ? 'agotado' : 'activo';

        $this->update([
            'folio_actual' => $siguienteFolio,
            'folios_disponibles' => $foliosDisponibles,
            'estado' => $nuevoEstado
        ]);

        return $siguienteFolio;
    }

    /**
     * Calcular folios disponibles
     */
    public function calcularDisponibles()
    {
        return $this->folio_hasta - $this->folio_actual;
    }

    /**
     * Verificar si está vencido
     */
    public function estaVencido()
    {
        return $this->fecha_vencimiento < now();
    }

    /**
     * Verificar si está agotado
     */
    public function estaAgotado()
    {
        return $this->folio_actual >= $this->folio_hasta;
    }

    /**
     * Accessor para estado con color
     */
    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'activo' => '<span class="badge badge-success">Activo</span>',
            'agotado' => '<span class="badge badge-warning">Agotado</span>',
            'vencido' => '<span class="badge badge-danger">Vencido</span>',
        ];

        return $badges[$this->estado] ?? '<span class="badge badge-secondary">' . ucfirst($this->estado) . '</span>';
    }

    /**
     * Accessor para porcentaje de uso
     */
    public function getPorcentajeUsoAttribute()
    {
        $total = $this->folio_hasta - $this->folio_desde + 1;
        $usados = $this->folio_actual - $this->folio_desde + 1;
        return round(($usados / $total) * 100, 2);
    }
}
