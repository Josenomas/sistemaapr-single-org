<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudCompraDominio extends Model
{
    protected $table = 'solicitudes_compra_dominio';

    protected $fillable = [
        'id_organizacion',
        'dominio_solicitado',
        'estado',
        'verificado_por',
        'fecha_verificacion',
        'monto',
        'metodo_pago',
        'comprobante_pago',
        'fecha_pago',
        'comprado_por',
        'fecha_compra_nic',
        'comprobante_nic',
        'fecha_vencimiento',
        'fecha_activacion',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_verificacion' => 'datetime',
        'fecha_pago' => 'datetime',
        'fecha_compra_nic' => 'datetime',
        'fecha_vencimiento' => 'date',
        'fecha_activacion' => 'datetime',
    ];

    /**
     * Relación con Organización
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'id_organizacion');
    }

    /**
     * Relación con usuario que verificó
     */
    public function verificador()
    {
        return $this->belongsTo(Usuario::class, 'verificado_por');
    }

    /**
     * Relación con usuario que compró
     */
    public function comprador()
    {
        return $this->belongsTo(Usuario::class, 'comprado_por');
    }

    /**
     * Scopes
     */
    public function scopeSolicitados($query)
    {
        return $query->where('estado', 'solicitado');
    }

    public function scopeVerificadosDisponibles($query)
    {
        return $query->where('estado', 'verificado_disponible');
    }

    public function scopePendientesPago($query)
    {
        return $query->where('estado', 'pendiente_pago');
    }

    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }

    /**
     * Badge de estado con colores
     */
    public function getBadgeEstadoAttribute()
    {
        $badges = [
            'solicitado' => '<span class="badge bg-info">Solicitado</span>',
            'verificado_disponible' => '<span class="badge bg-success">✓ Disponible</span>',
            'verificado_ocupado' => '<span class="badge bg-danger">✗ Ocupado</span>',
            'pendiente_pago' => '<span class="badge bg-warning text-dark">Pendiente Pago</span>',
            'pagado' => '<span class="badge bg-primary">Pagado</span>',
            'comprado' => '<span class="badge bg-purple">Comprado NIC</span>',
            'activo' => '<span class="badge bg-success">✓ Activo</span>',
            'cancelado' => '<span class="badge bg-secondary">Cancelado</span>',
        ];

        return $badges[$this->estado] ?? '<span class="badge bg-secondary">' . ucfirst($this->estado) . '</span>';
    }

    /**
     * Verificar si puede ser marcado como disponible
     */
    public function puedeVerificarDisponible()
    {
        return $this->estado === 'solicitado';
    }

    /**
     * Verificar si puede ser marcado como ocupado
     */
    public function puedeVerificarOcupado()
    {
        return $this->estado === 'solicitado';
    }

    /**
     * Verificar si puede recibir pago
     */
    public function puedeRecibirPago()
    {
        return in_array($this->estado, ['verificado_disponible', 'pendiente_pago']);
    }

    /**
     * Verificar si puede ser comprado en NIC
     */
    public function puedeComprar()
    {
        return $this->estado === 'pagado';
    }

    /**
     * Verificar si puede ser activado
     */
    public function puedeActivar()
    {
        return $this->estado === 'comprado';
    }
}
