<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionDTE extends Model
{
    protected $table = 'configuracion_dte';

    protected $fillable = [
        'id_organizacion',
        'rut_emisor',
        'razon_social',
        'giro',
        'direccion_casa_matriz',
        'comuna',
        'ciudad',
        'telefono',
        'email_contacto',
        'libredte_hash',
        'libredte_url',
        'ambiente',
        'certificado_digital',
        'certificado_password',
        'folio_boleta_actual',
        'folio_factura_actual',
        'activo',
        'observaciones',
        'notificar_rechazos',
        'email_notificaciones',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'notificar_rechazos' => 'boolean',
        'folio_boleta_actual' => 'integer',
        'folio_factura_actual' => 'integer',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    protected $hidden = [
        'certificado_password',
        'libredte_hash',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    /**
     * Relación con la organización
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'id_organizacion');
    }

    /**
     * Verificar si está configurado para emitir DTEs
     */
    public function estaConfigurado()
    {
        return !empty($this->libredte_hash)
            && !empty($this->rut_emisor)
            && $this->activo;
    }

    /**
     * Verificar si está en ambiente de producción
     */
    public function esProduccion()
    {
        return $this->ambiente === 'produccion';
    }

    /**
     * Obtener siguiente folio para boletas
     */
    public function obtenerSiguienteFolioBoleta()
    {
        $this->increment('folio_boleta_actual');
        return $this->folio_boleta_actual;
    }

    /**
     * Obtener siguiente folio para facturas
     */
    public function obtenerSiguienteFolioFactura()
    {
        $this->increment('folio_factura_actual');
        return $this->folio_factura_actual;
    }

    /**
     * Scope para configuraciones activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope por organización
     */
    public function scopePorOrganizacion($query, $idOrganizacion)
    {
        return $query->where('id_organizacion', $idOrganizacion);
    }
}
