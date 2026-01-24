<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FolioSII;
use App\Helpers\ActividadHelper;
use Illuminate\Support\Facades\DB;

class FolioSIIController extends Controller
{
    /**
     * Listar todos los folios
     */
    public function index(Request $request)
    {
        $query = FolioSII::with('usuarioCarga');

        // Filtros
        if ($request->filled('tipo_documento')) {
            $query->where('tipo_documento', $request->tipo_documento);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Estadísticas
        $estadisticas = [
            'total_folios' => FolioSII::activos()->count(),
            'folios_activos' => FolioSII::where('estado', 'activo')->count(),
            'folios_disponibles' => FolioSII::where('estado', 'activo')->sum('folios_disponibles'),
            'folios_vencidos' => FolioSII::where('estado', 'vencido')->count(),
        ];

        $folios = $query->orderBy('fecha_creacion', 'desc')->paginate(20);

        return view('folios-sii.index', compact('folios', 'estadisticas'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('folios-sii.create');
    }

    /**
     * Guardar nuevo folio
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_documento' => 'required|in:boleta,factura,nota_credito,nota_debito',
            'folio_desde' => 'required|integer|min:1',
            'folio_hasta' => 'required|integer|min:1|gte:folio_desde',
            'fecha_autorizacion' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_autorizacion',
            'caf_xml' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Calcular folios disponibles
            $foliosDisponibles = $validated['folio_hasta'] - $validated['folio_desde'] + 1;

            $folio = FolioSII::create([
                'tipo_documento' => $validated['tipo_documento'],
                'folio_desde' => $validated['folio_desde'],
                'folio_hasta' => $validated['folio_hasta'],
                'folio_actual' => $validated['folio_desde'] - 1, // Inicia antes del primer folio
                'fecha_autorizacion' => $validated['fecha_autorizacion'],
                'fecha_vencimiento' => $validated['fecha_vencimiento'],
                'caf_xml' => $validated['caf_xml'] ?? null,
                'estado' => 'activo',
                'folios_disponibles' => $foliosDisponibles,
                'id_usuario_carga' => auth()->id(),
                'observaciones' => $validated['observaciones'],
                'activo' => true,
            ]);

            // Registrar actividad
            ActividadHelper::registrar(
                'Folios SII',
                "Nuevo rango de folios cargado: {$folio->tipo_documento} desde {$folio->folio_desde} hasta {$folio->folio_hasta} ({$foliosDisponibles} folios)"
            );

            DB::commit();

            return redirect()->route('folios-sii.index')
                           ->with('success', 'Folio creado exitosamente. Total: ' . $foliosDisponibles . ' folios disponibles.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error al crear el folio: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Mostrar detalle del folio
     */
    public function show($id)
    {
        $folio = FolioSII::with('usuarioCarga')->findOrFail($id);

        return view('folios-sii.show', compact('folio'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $folio = FolioSII::findOrFail($id);

        return view('folios-sii.edit', compact('folio'));
    }

    /**
     * Actualizar folio
     */
    public function update(Request $request, $id)
    {
        $folio = FolioSII::findOrFail($id);

        $validated = $request->validate([
            'fecha_vencimiento' => 'required|date',
            'estado' => 'required|in:activo,agotado,vencido',
            'observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $cambios = [];

            if ($folio->fecha_vencimiento != $validated['fecha_vencimiento']) {
                $cambios[] = "Fecha vencimiento: " . $folio->fecha_vencimiento->format('d/m/Y') . " → " . date('d/m/Y', strtotime($validated['fecha_vencimiento']));
            }

            if ($folio->estado != $validated['estado']) {
                $cambios[] = "Estado: " . ucfirst($folio->estado) . " → " . ucfirst($validated['estado']);
            }

            $folio->update([
                'fecha_vencimiento' => $validated['fecha_vencimiento'],
                'estado' => $validated['estado'],
                'observaciones' => $validated['observaciones'],
            ]);

            if (!empty($cambios)) {
                ActividadHelper::registrar(
                    'Folios SII',
                    "Folio actualizado [ID: {$folio->id}]: " . implode(' | ', $cambios)
                );
            }

            DB::commit();

            return redirect()->route('folios-sii.show', $folio->id)
                           ->with('success', 'Folio actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar el folio: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar folio (soft delete)
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $folio = FolioSII::findOrFail($id);

            // Solo permitir eliminar si no se han usado folios
            if ($folio->folio_actual >= $folio->folio_desde) {
                return redirect()->back()
                               ->with('error', 'No se puede eliminar un folio que ya ha sido utilizado.');
            }

            ActividadHelper::registrar(
                'Folios SII',
                "Folio eliminado: {$folio->tipo_documento} desde {$folio->folio_desde} hasta {$folio->folio_hasta}"
            );

            $folio->update(['activo' => false]);

            DB::commit();

            return redirect()->route('folios-sii.index')
                           ->with('success', 'Folio eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error al eliminar el folio: ' . $e->getMessage());
        }
    }

    /**
     * Obtener siguiente folio disponible (API)
     */
    public function obtenerSiguiente(Request $request)
    {
        $tipoDocumento = $request->input('tipo_documento', 'boleta');

        $folio = FolioSII::disponibles()
                         ->tipoDocumento($tipoDocumento)
                         ->orderBy('fecha_creacion', 'asc')
                         ->first();

        if (!$folio) {
            return response()->json([
                'success' => false,
                'message' => 'No hay folios disponibles para ' . $tipoDocumento
            ], 404);
        }

        $siguienteFolio = $folio->obtenerSiguienteFolio();

        if (!$siguienteFolio) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el siguiente folio'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'folio' => $siguienteFolio,
            'folio_sii_id' => $folio->id,
            'folios_restantes' => $folio->folios_disponibles
        ]);
    }
}
