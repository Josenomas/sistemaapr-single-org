<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lectura;
use App\Models\Socio;
use App\Helpers\ActividadHelper;

class LecturasController extends Controller
{
    /**
     * Listar todas las lecturas
     */
    public function index(Request $request)
    {
        $query = Lectura::with('socio');

        // Filtrar por mes si se proporciona
        if ($request->has('mes') && $request->mes) {
            $query->where('mes', $request->mes);
        }

        // Filtrar por socio
        if ($request->has('socio_id') && $request->socio_id) {
            $query->where('id_socio', $request->socio_id);
        }

        $lecturas = $query->orderBy('fecha_lectura', 'desc')->paginate(20);
        $socios = Socio::activos()->orderBy('numero_socio')->get();

        return view('lecturas.index', compact('lecturas', 'socios'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $socios = Socio::activos()->orderBy('numero_socio')->get();
        return view('lecturas.create', compact('socios'));
    }

    /**
     * Guardar nueva lectura
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_socio' => 'required|exists:socios,id',
            'mes' => 'required|string|size:7',
            'lectura_actual' => 'required|numeric|min:0',
            'fecha_lectura' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        // Obtener lectura anterior
        $lecturaAnterior = Lectura::where('id_socio', $validated['id_socio'])
                                  ->where('mes', '<', $validated['mes'])
                                  ->orderBy('mes', 'desc')
                                  ->first();

        $validated['lectura_anterior'] = $lecturaAnterior ? $lecturaAnterior->lectura_actual : 0;
        $validated['consumo_m3'] = $validated['lectura_actual'] - $validated['lectura_anterior'];
        $validated['id_usuario_registro'] = auth()->id();

        $lectura = Lectura::create($validated);

        // Obtener información del socio para la actividad
        $socio = Socio::find($validated['id_socio']);
        $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $fecha = explode('-', $validated['mes']);
        $mesTexto = $meses[(int)$fecha[1]] . ' ' . $fecha[0];

        ActividadHelper::registrar(
            'Lecturas',
            "Nueva lectura registrada: {$socio->numero_socio} - {$socio->nombre_completo} ({$mesTexto}) - Consumo: {$validated['consumo_m3']} m³",
            auth()->id()
        );

        return redirect()->route('lecturas.index')
                        ->with('success', 'Lectura registrada exitosamente');
    }

    /**
     * Mostrar detalle de lectura
     */
    public function show($id)
    {
        $lectura = Lectura::with(['socio', 'usuarioRegistro', 'boleta'])->findOrFail($id);
        return view('lecturas.show', compact('lectura'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $lectura = Lectura::findOrFail($id);
        $socios = Socio::activos()->orderBy('numero_socio')->get();
        return view('lecturas.edit', compact('lectura', 'socios'));
    }

    /**
     * Actualizar lectura
     */
    public function update(Request $request, $id)
    {
        $lectura = Lectura::findOrFail($id);

        $validated = $request->validate([
            'lectura_actual' => 'required|numeric|min:0',
            'fecha_lectura' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        $validated['consumo_m3'] = $validated['lectura_actual'] - $lectura->lectura_anterior;

        // Capturar cambios antes de actualizar
        $cambios = [];
        $camposTraducidos = [
            'lectura_actual' => 'Lectura Actual',
            'consumo_m3' => 'Consumo',
            'fecha_lectura' => 'Fecha Lectura',
            'observaciones' => 'Observaciones',
        ];

        foreach ($validated as $campo => $valorNuevo) {
            $valorAnterior = $lectura->$campo;
            if ($valorAnterior != $valorNuevo) {
                $nombreCampo = $camposTraducidos[$campo] ?? $campo;

                // Formatear valores según el tipo
                if ($campo == 'lectura_actual' || $campo == 'consumo_m3') {
                    $valorAnterior = number_format($valorAnterior, 2) . ' m³';
                    $valorNuevo = number_format($valorNuevo, 2) . ' m³';
                } elseif ($campo == 'fecha_lectura') {
                    $valorAnterior = date('d/m/Y', strtotime($valorAnterior));
                    $valorNuevo = date('d/m/Y', strtotime($valorNuevo));
                }

                $cambios[] = "{$nombreCampo}: '{$valorAnterior}' → '{$valorNuevo}'";
            }
        }

        $lectura->update($validated);

        // Registrar actividad con cambios
        $socio = $lectura->socio;
        $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $fecha = explode('-', $lectura->mes);
        $mesTexto = $meses[(int)$fecha[1]] . ' ' . $fecha[0];

        if (!empty($cambios)) {
            $descripcionCambios = implode(', ', $cambios);
            ActividadHelper::registrar(
                'Lecturas',
                "Lectura actualizada: {$socio->numero_socio} - {$socio->nombre_completo} ({$mesTexto}). Cambios: {$descripcionCambios}",
                auth()->id()
            );
        } else {
            ActividadHelper::registrar(
                'Lecturas',
                "Lectura actualizada: {$socio->numero_socio} - {$socio->nombre_completo} ({$mesTexto}) - Consumo: {$validated['consumo_m3']} m³",
                auth()->id()
            );
        }

        return redirect()->route('lecturas.show', $id)
                        ->with('success', 'Lectura actualizada exitosamente');
    }

    /**
     * Eliminar lectura
     */
    public function destroy($id)
    {
        $lectura = Lectura::findOrFail($id);

        // Verificar que no tenga boleta asociada
        if ($lectura->boleta) {
            return redirect()->route('lecturas.index')
                           ->with('error', 'No se puede eliminar la lectura porque tiene una boleta asociada');
        }

        // Guardar información antes de eliminar
        $socio = $lectura->socio;
        $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $fecha = explode('-', $lectura->mes);
        $mesTexto = $meses[(int)$fecha[1]] . ' ' . $fecha[0];
        $consumo = $lectura->consumo;

        $lectura->delete();

        ActividadHelper::registrar(
            'Lecturas',
            "Lectura eliminada: {$socio->numero_socio} - {$socio->nombre_completo} ({$mesTexto}) - Consumo: {$consumo} m³",
            auth()->id()
        );

        return redirect()->route('lecturas.index')
                        ->with('success', 'Lectura eliminada exitosamente');
    }

    /**
     * Registrar lecturas masivas
     */
    public function masivo()
    {
        $mesActual = date('Y-m');
        $socios = Socio::activos()
                      ->orderBy('numero_socio')
                      ->get();

        // Obtener sectores únicos
        $sectores = Socio::activos()
                        ->whereNotNull('sector')
                        ->distinct()
                        ->pluck('sector')
                        ->toArray();

        return view('lecturas.masivo', compact('socios', 'sectores'));
    }

    /**
     * Guardar lecturas masivas
     */
    public function storeMasivo(Request $request)
    {
        // DEBUG: Log request data
        \Log::info('INICIO storeMasivo', [
            'mes' => $request->mes,
            'fecha_lectura' => $request->fecha_lectura,
            'total_lecturas' => count($request->lecturas ?? [])
        ]);

        $validated = $request->validate([
            'mes' => 'required|string|size:7',
            'fecha_lectura' => 'required|date',
            'lecturas' => 'required|array',
            'lecturas.*.id_socio' => 'required|exists:socios,id',
            'lecturas.*.lectura_actual' => 'nullable|numeric|min:0',
        ]);

        \Log::info('VALIDACION OK', ['validated_count' => count($validated['lecturas'])]);

        $registradas = 0;
        $errores = [];
        foreach ($validated['lecturas'] as $index => $lecturaData) {
            // Solo procesar si tiene lectura actual (permitir 0 o mayor)
            if (!isset($lecturaData['lectura_actual']) || $lecturaData['lectura_actual'] === '' || $lecturaData['lectura_actual'] === null) {
                \Log::info("SKIP lectura #{$index}", ['razon' => 'sin lectura_actual']);
                continue;
            }

            // Obtener lectura anterior
            $lecturaAnterior = Lectura::where('id_socio', $lecturaData['id_socio'])
                                      ->where('mes', '<', $validated['mes'])
                                      ->orderBy('mes', 'desc')
                                      ->first();

            $lecturaAnteriorValor = isset($lecturaData['lectura_anterior']) && $lecturaData['lectura_anterior'] >= 0
                                  ? $lecturaData['lectura_anterior']
                                  : ($lecturaAnterior ? $lecturaAnterior->lectura_actual : 0);

            $consumo = $lecturaData['lectura_actual'] - $lecturaAnteriorValor;

            try {
                $lectura = Lectura::create([
                    'id_socio' => $lecturaData['id_socio'],
                    'mes' => $validated['mes'],
                    'lectura_anterior' => $lecturaAnteriorValor,
                    'lectura_actual' => $lecturaData['lectura_actual'],
                    'consumo_m3' => $consumo,
                    'fecha_lectura' => $validated['fecha_lectura'],
                    'observaciones' => $lecturaData['observaciones'] ?? null,
                    'id_usuario_registro' => auth()->id(),
                ]);

                \Log::info("GUARDADA lectura #{$index}", [
                    'id' => $lectura->id,
                    'id_socio' => $lectura->id_socio,
                    'lectura_actual' => $lectura->lectura_actual
                ]);

                $registradas++;
            } catch (\Exception $e) {
                \Log::error("ERROR guardando lectura #{$index}", [
                    'error' => $e->getMessage(),
                    'data' => $lecturaData
                ]);
                $errores[] = "Socio {$lecturaData['id_socio']}: {$e->getMessage()}";
            }
        }

        // Registrar actividad masiva
        $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $fecha = explode('-', $validated['mes']);
        $mesTexto = $meses[(int)$fecha[1]] . ' ' . $fecha[0];

        \Log::info('FIN storeMasivo', [
            'registradas' => $registradas,
            'errores_count' => count($errores)
        ]);

        ActividadHelper::registrar(
            'Lecturas',
            "Registro masivo de lecturas: {$registradas} lecturas registradas para {$mesTexto}",
            auth()->id()
        );

        if (count($errores) > 0) {
            return redirect()->route('lecturas.index')
                            ->with('warning', "Se registraron {$registradas} lecturas. Errores: " . implode(', ', $errores));
        }

        return redirect()->route('lecturas.index')
                        ->with('success', "Se registraron {$registradas} lecturas exitosamente");
    }
}
