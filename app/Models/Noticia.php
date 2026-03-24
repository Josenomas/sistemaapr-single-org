<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Scopes\OrganizacionScope;

class Noticia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'noticias';

    protected $fillable = [
        'id_organizacion',
        'titulo',
        'slug',
        'resumen',
        'contenido',
        'imagen_destacada',
        'categoria',
        'estado',
        'destacada',
        'fecha_publicacion',
        'id_usuario_creador',
        'vistas',
    ];

    protected $casts = [
        'fecha_publicacion' => 'datetime',
        'destacada' => 'boolean',
        'vistas' => 'integer',
    ];

    /**
     * Boot del modelo para generar slug automático y agregar scope
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OrganizacionScope);

        static::creating(function ($noticia) {
            // Auto-generar slug
            if (empty($noticia->slug)) {
                $noticia->slug = Str::slug($noticia->titulo) . '-' . time();
            }

            // Auto-asignar id_organizacion
            if (auth()->check() && !$noticia->id_organizacion) {
                $noticia->id_organizacion = auth()->user()->id_organizacion;
            }

            // Auto-asignar creador
            if (auth()->check() && !$noticia->id_usuario_creador) {
                $noticia->id_usuario_creador = auth()->id();
            }
        });
    }

    /**
     * Relación: Una noticia pertenece a una organización
     */
    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class, 'id_organizacion');
    }

    /**
     * Relación: Una noticia tiene un creador (usuario)
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'id_usuario_creador');
    }

    /**
     * Scope para noticias publicadas
     */
    public function scopePublicadas($query)
    {
        return $query->where('estado', 'publicada')
                     ->whereNotNull('fecha_publicacion')
                     ->where('fecha_publicacion', '<=', now());
    }

    /**
     * Scope para noticias destacadas
     */
    public function scopeDestacadas($query)
    {
        return $query->where('destacada', true);
    }

    /**
     * Scope por categoría
     */
    public function scopeCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Incrementar contador de vistas
     */
    public function incrementarVistas()
    {
        $this->increment('vistas');
    }

    /**
     * Verifica si está publicada
     */
    public function estaPublicada()
    {
        return $this->estado === 'publicada' &&
               $this->fecha_publicacion &&
               $this->fecha_publicacion->isPast();
    }

    /**
     * Obtiene la URL de la noticia
     */
    public function getUrlAttribute()
    {
        return route('noticias.public.show', ['slug' => $this->slug]);
    }
}
