<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToOrganizacion;

class AlertaDTE extends Model
{
    use BelongsToOrganizacion;

    protected $table = 'alertas_dte';

    protected $fillable = [
        'id_organizacion',
        'tipo',
        'nivel',
        'titulo',
        'mensaje',
        'datos_adicionales',
        'leida',
        'fecha_lectura',
        'resuelta',
        'fecha_resolucion',
        'email_enviado',
        'fecha_email',
    ];

    protected $casts = [
        'datos_adicionales' => 'array',
        'leida' => 'boolean',
        'resuelta' => 'boolean',
        'email_enviado' => 'boolean',
        'fecha_lectura' => 'datetime',
        'fecha_resolucion' => 'datetime',
        'fecha_email' => 'datetime',
    ];

    /**
     * Relación con Organización
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'id_organizacion');
    }

    /**
     * Scopes
     */
    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    public function scopeNoResueltas($query)
    {
        return $query->where('resuelta', false);
    }

    public function scopeActivas($query)
    {
        return $query->where('leida', false)
                     ->where('resuelta', false);
    }

    public function scopeCriticas($query)
    {
        return $query->where('nivel', 'critico');
    }

    public function scopeAdvertencias($query)
    {
        return $query->where('nivel', 'advertencia');
    }

    public function scopeInformativas($query)
    {
        return $query->where('nivel', 'info');
    }

    /**
     * Marcar como leída
     */
    public function marcarComoLeida()
    {
        $this->update([
            'leida' => true,
            'fecha_lectura' => now(),
        ]);
    }

    /**
     * Marcar como resuelta
     */
    public function marcarComoResuelta()
    {
        $this->update([
            'resuelta' => true,
            'fecha_resolucion' => now(),
        ]);
    }

    /**
     * Obtener clase CSS según nivel
     */
    public function getClaseNivelAttribute()
    {
        return match($this->nivel) {
            'critico' => 'danger',
            'advertencia' => 'warning',
            'info' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Obtener icono según nivel
     */
    public function getIconoNivelAttribute()
    {
        return match($this->nivel) {
            'critico' => 'fa-exclamation-triangle',
            'advertencia' => 'fa-exclamation-circle',
            'info' => 'fa-info-circle',
            default => 'fa-bell',
        };
    }

    /**
     * Obtener icono según tipo
     */
    public function getIconoTipoAttribute()
    {
        return match($this->tipo) {
            'sin_folios' => 'fa-file-times',
            'folios_bajos' => 'fa-file-alt',
            'conexion_fallida' => 'fa-wifi-slash',
            'dte_rechazado' => 'fa-times-circle',
            'ambiente_certificacion' => 'fa-flask',
            'libro_ventas_pendiente' => 'fa-book',
            'configuracion_exitosa' => 'fa-check-circle',
            'hito_dtes' => 'fa-trophy',
            default => 'fa-bell',
        };
    }

    /**
     * Crear alerta de sin folios
     */
    public static function crearSinFolios($idOrganizacion)
    {
        return self::create([
            'id_organizacion' => $idOrganizacion,
            'tipo' => 'sin_folios',
            'nivel' => 'critico',
            'titulo' => 'Sin folios disponibles',
            'mensaje' => 'No hay folios disponibles para emitir DTEs. La emisión está bloqueada hasta obtener nuevos folios del SII.',
        ]);
    }

    /**
     * Crear alerta de folios bajos
     */
    public static function crearFoliosBajos($idOrganizacion, $cantidad)
    {
        return self::create([
            'id_organizacion' => $idOrganizacion,
            'tipo' => 'folios_bajos',
            'nivel' => 'advertencia',
            'titulo' => 'Folios disponibles bajos',
            'mensaje' => "Quedan {$cantidad} folios disponibles. Solicita nuevos folios al SII para evitar interrupciones.",
            'datos_adicionales' => ['folios_disponibles' => $cantidad],
        ]);
    }

    /**
     * Crear alerta de conexión fallida
     */
    public static function crearConexionFallida($idOrganizacion, $error)
    {
        return self::create([
            'id_organizacion' => $idOrganizacion,
            'tipo' => 'conexion_fallida',
            'nivel' => 'critico',
            'titulo' => 'Error de conexión con LibreDTE',
            'mensaje' => 'No se pudo conectar con el servicio de LibreDTE. Verifica tu configuración y conexión a internet.',
            'datos_adicionales' => ['error' => $error],
        ]);
    }

    /**
     * Crear alerta de DTE rechazado
     */
    public static function crearDTERechazado($idOrganizacion, $folio, $motivo)
    {
        return self::create([
            'id_organizacion' => $idOrganizacion,
            'tipo' => 'dte_rechazado',
            'nivel' => 'critico',
            'titulo' => "DTE Folio {$folio} rechazado",
            'mensaje' => "El DTE con folio {$folio} fue rechazado por el SII. Motivo: {$motivo}",
            'datos_adicionales' => ['folio' => $folio, 'motivo' => $motivo],
        ]);
    }

    /**
     * Crear alerta de ambiente en certificación
     */
    public static function crearAmbienteCertificacion($idOrganizacion)
    {
        return self::create([
            'id_organizacion' => $idOrganizacion,
            'tipo' => 'ambiente_certificacion',
            'nivel' => 'advertencia',
            'titulo' => 'Sistema en ambiente de certificación',
            'mensaje' => 'Los DTEs emitidos NO tienen validez tributaria. Cambia a ambiente de producción cuando estés listo.',
        ]);
    }
}
