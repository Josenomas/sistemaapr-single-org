<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consentimiento extends Model
{
    use HasFactory;

    protected $table = 'consentimientos';

    protected $fillable = [
        'id_organizacion',
        'documento_aceptado',
        'version_documento',
        'acepto',
        'ip_address',
        'user_agent',
        'fecha_aceptacion',
    ];

    protected $casts = [
        'acepto' => 'boolean',
        'fecha_aceptacion' => 'datetime',
    ];

    /**
     * Registrar consentimiento de organización
     */
    public static function registrar($idOrganizacion, $documento, $version = '1.0')
    {
        return self::create([
            'id_organizacion' => $idOrganizacion,
            'documento_aceptado' => $documento,
            'version_documento' => $version,
            'acepto' => true,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'fecha_aceptacion' => now(),
        ]);
    }

    /**
     * Relación con organización
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'id_organizacion');
    }
}
