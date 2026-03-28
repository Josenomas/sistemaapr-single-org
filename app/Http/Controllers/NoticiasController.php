<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Noticia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class NoticiasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Verificar que la organización tenga acceso al módulo de noticias
        $user = auth()->user();
        $organizacion = $user->organizacion;

        if (!$organizacion || !$organizacion->puedeAccederModulo('noticias')) {
            return redirect()->route('dashboard')
                ->with('error', 'Tu plan actual no incluye el módulo de noticias. Actualiza tu plan para acceder.');
        }

        $query = Noticia::query();

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por categoría
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        $noticias = $query->orderBy('fecha_publicacion', 'desc')
            ->paginate(15);

        return view('noticias.index', compact('noticias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('noticias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'resumen' => 'nullable|string|max:500',
            'contenido' => 'required|string',
            'categoria' => 'nullable|string|max:50',
            'imagen_destacada' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'estado' => 'required|in:borrador,publicada,archivada',
            'destacada' => 'nullable|boolean',
            'fecha_publicacion' => 'nullable|date',
        ]);

        // Subir imagen si existe
        if ($request->hasFile('imagen_destacada')) {
            $validated['imagen_destacada'] = $request->file('imagen_destacada')->store('noticias', 'public');
        }

        // Asignar valores por defecto
        $validated['destacada'] = $request->has('destacada');
        $validated['fecha_publicacion'] = $validated['fecha_publicacion'] ?? now();
        $validated['id_usuario_creador'] = auth()->id();

        Noticia::create($validated);

        return redirect()->route('noticias.index')
            ->with('success', 'Noticia creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $noticia = Noticia::findOrFail($id);

        // Incrementar vistas
        $noticia->increment('vistas');

        return view('noticias.show', compact('noticia'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $noticia = Noticia::findOrFail($id);

        return view('noticias.edit', compact('noticia'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $noticia = Noticia::findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'resumen' => 'nullable|string|max:500',
            'contenido' => 'required|string',
            'categoria' => 'nullable|string|max:50',
            'imagen_destacada' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'estado' => 'required|in:borrador,publicada,archivada',
            'destacada' => 'nullable|boolean',
            'fecha_publicacion' => 'nullable|date',
        ]);

        // Subir nueva imagen si existe
        if ($request->hasFile('imagen_destacada')) {
            // Eliminar imagen anterior si existe
            if ($noticia->imagen_destacada) {
                Storage::disk('public')->delete($noticia->imagen_destacada);
            }
            $validated['imagen_destacada'] = $request->file('imagen_destacada')->store('noticias', 'public');
        }

        $validated['destacada'] = $request->has('destacada');

        $noticia->update($validated);

        return redirect()->route('noticias.index')
            ->with('success', 'Noticia actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $noticia = Noticia::findOrFail($id);

        // Eliminar imagen si existe
        if ($noticia->imagen_destacada) {
            Storage::disk('public')->delete($noticia->imagen_destacada);
        }

        $noticia->delete();

        return redirect()->route('noticias.index')
            ->with('success', 'Noticia eliminada exitosamente.');
    }

    /**
     * Vista pública de noticias (para mostrar en landing)
     */
    public function publicas()
    {
        $noticias = Noticia::publicadas()
            ->orderBy('fecha_publicacion', 'desc')
            ->paginate(12);

        return view('noticias.publicas', compact('noticias'));
    }

    /**
     * Vista pública de una noticia específica
     */
    public function verPublica($slug)
    {
        $noticia = Noticia::where('slug', $slug)
            ->where('estado', 'publicada')
            ->firstOrFail();

        // Incrementar vistas
        $noticia->increment('vistas');

        return view('noticias.publica-detalle', compact('noticia'));
    }
}
