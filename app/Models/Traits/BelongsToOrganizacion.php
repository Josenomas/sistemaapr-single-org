<?php

namespace App\Models\Traits;

use App\Models\Organizacion;
use App\Models\Scopes\OrganizacionScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToOrganizacion
{
    /**
     * Boot del trait - aplica el Global Scope automáticamente
     */
    protected static function bootBelongsToOrganizacion(): void
    {
        static::addGlobalScope(new OrganizacionScope);

        static::creating(function ($model) {
            if (auth()->check() && !$model->id_organizacion) {
                $model->id_organizacion = auth()->user()->id_organizacion;
            }
        });
    }

    /**
     * Relación: Pertenece a una organización
     */
    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class, 'id_organizacion');
    }
}
