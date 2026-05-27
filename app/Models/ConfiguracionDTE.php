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
        'libredte_hash_certificacion',
        'libredte_url_certificacion',
        'certificado_digital',
        'certificado_password',
        'folio_boleta_actual',
        'folio_factura_actual',
        'activo',
        'proveedor_dte', // libredte o simpleapi
        'observaciones',
        'notificar_rechazos',
        'email_notificaciones',
        // Archivos CAF
        'caf_boleta_39',
        'caf_factura_33',
        'caf_nota_credito_61',
        'caf_nota_debito_56',
        'caf_boleta_desde',
        'caf_boleta_hasta',
        'caf_boleta_vencimiento',
        'caf_factura_desde',
        'caf_factura_hasta',
        'caf_factura_vencimiento',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'notificar_rechazos' => 'boolean',
        'folio_boleta_actual' => 'integer',
        'folio_factura_actual' => 'integer',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'caf_boleta_vencimiento' => 'date',
        'caf_factura_vencimiento' => 'date',
        'caf_boleta_desde' => 'integer',
        'caf_boleta_hasta' => 'integer',
        'caf_factura_desde' => 'integer',
        'caf_factura_hasta' => 'integer',
    ];

    protected $hidden = [
        'certificado_password',
        'libredte_hash',
        'libredte_hash_certificacion',
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
        $configuracionBasica = !empty($this->rut_emisor) && $this->activo;

        if ($this->proveedor_dte === 'simpleapi') {
            // SimpleAPI requiere certificado digital configurado
            return $configuracionBasica && !empty($this->certificado_digital);
        }

        // LibreDTE requiere hash configurado
        return $configuracionBasica && !empty($this->libredte_hash);
    }

    /**
     * Verificar si usa SimpleAPI
     */
    public function usaSimpleAPI()
    {
        return $this->proveedor_dte === 'simpleapi';
    }

    /**
     * Verificar si usa LibreDTE
     */
    public function usaLibreDTE()
    {
        return $this->proveedor_dte === 'libredte' || empty($this->proveedor_dte);
    }

    /**
     * Verificar si está en ambiente de producción
     */
    public function esProduccion()
    {
        return $this->ambiente === 'produccion';
    }

    /**
     * Verificar si está en ambiente de certificación
     */
    public function esCertificacion()
    {
        return $this->ambiente === 'certificacion';
    }

    /**
     * Obtener hash según ambiente
     */
    public function getHashActivo()
    {
        return $this->esCertificacion() && $this->libredte_hash_certificacion
            ? $this->libredte_hash_certificacion
            : $this->libredte_hash;
    }

    /**
     * Obtener URL según ambiente
     */
    public function getUrlActiva()
    {
        return $this->esCertificacion() && $this->libredte_url_certificacion
            ? $this->libredte_url_certificacion
            : $this->libredte_url;
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
