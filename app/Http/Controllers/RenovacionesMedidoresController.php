<?php

namespace App\Http\Controllers;

use App\Models\RenovacionMedidor;
use App\Models\Socio;
use App\Models\Funcionario;
use App\Helpers\ActividadHelper;
use Illuminate\Http\Request;

class RenovacionesMedidoresController extends Controller
{
    public function index(Request $request)
    {
        $query = RenovacionMedidor::with(['socio', 'tecnico'])->activos();

        // Filtro por socio
        if ($request->filled('id_socio')) {
            $query->where('id_socio', $request->id_socio);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por motivo
        if ($request->filled('motivo')) {
            $query->where('motivo', $request->motivo);
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_renovacion', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_renovacion', '<=', $request->fecha_hasta);
        }

        $renovaciones = $query->orderBy('fecha_renovacion', 'desc')->paginate(20);
        $socios = Socio::activos()->orderBy('numero_socio')->get();

        return view('renovaciones.index', compact('renovaciones', 'socios'));
    }

    public function create()
    {
        $socios = Socio::activos()->orderBy('numero_socio')->get();
        $tecnicos = Funcionario::activos()->orderBy('nombre')->get();
        return view('renovaciones.create', compact('socios', 'tecnicos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_socio' => 'required|exists:socios,id',
            'medidor_anterior' => 'nullable|string|max:100',
            'lectura_anterior' => 'nullable|numeric|min:0',
            'medidor_nuevo' => 'required|string|max:100',
            'lectura_inicial' => 'required|numeric|min:0',
            'fecha_renovacion' => 'required|date',
            'motivo' => 'required|in:deterioro,falla,actualizacion,robo,otro',
            'costo_renovacion' => 'nullable|numeric|min:0',
            'id_tecnico' => 'nullable|exists:funcionarios,id',
            'observaciones' => 'nullable|string',
            'estado' => 'required|in:planificado,ejecutado,cancelado',
        ]);

        $renovacion = RenovacionMedidor::create($validated);
        $socio = Socio::find($validated['id_socio']);

        ActividadHelper::registrar(
            'Renovaciones de Medidores',
            "Renovación registrada: Socio {$socio->numero_socio} - {$socio->nombre_completo} - Medidor: {$renovacion->medidor_nuevo}",
            auth()->id()
        );

        return redirect()->route('renovaciones.show', $renovacion->id)
            ->with('success', 'Renovación de medidor registrada exitosamente.');
    }

    public function show($id)
    {
        $renovacion = RenovacionMedidor::with(['socio', 'tecnico'])->findOrFail($id);
        return view('renovaciones.show', compact('renovacion'));
    }

    public function edit($id)
    {
        $renovacion = RenovacionMedidor::findOrFail($id);
        $socios = Socio::activos()->orderBy('numero_socio')->get();
        $tecnicos = Funcionario::activos()->orderBy('nombre')->get();
        return view('renovaciones.edit', compact('renovacion', 'socios', 'tecnicos'));
    }

    public function update(Request $request, $id)
    {
        $renovacion = RenovacionMedidor::findOrFail($id);

        $validated = $request->validate([
            'id_socio' => 'required|exists:socios,id',
            'medidor_anterior' => 'nullable|string|max:100',
            'lectura_anterior' => 'nullable|numeric|min:0',
            'medidor_nuevo' => 'required|string|max:100',
            'lectura_inicial' => 'required|numeric|min:0',
            'fecha_renovacion' => 'required|date',
            'motivo' => 'required|in:deterioro,falla,actualizacion,robo,otro',
            'costo_renovacion' => 'nullable|numeric|min:0',
            'id_tecnico' => 'nullable|exists:funcionarios,id',
            'observaciones' => 'nullable|string',
            'estado' => 'required|in:planificado,ejecutado,cancelado',
        ]);

        // Capturar cambios antes de actualizar
        $cambios = [];
        $camposTraducidos = [
            'id_socio' => 'Socio',
            'medidor_anterior' => 'Medidor Anterior',
            'lectura_anterior' => 'Lectura Anterior',
            'medidor_nuevo' => 'Medidor Nuevo',
            'lectura_inicial' => 'Lectura Inicial',
            'fecha_renovacion' => 'Fecha de Renovación',
            'motivo' => 'Motivo',
            'costo_renovacion' => 'Costo de Renovación',
            'id_tecnico' => 'Técnico',
            'observaciones' => 'Observaciones',
            'estado' => 'Estado',
        ];

        foreach ($validated as $campo => $valorNuevo) {
            $valorAnterior = $renovacion->$campo;

            if ($valorAnterior != $valorNuevo) {
                $nombreCampo = $camposTraducidos[$campo] ?? $campo;

                // Formatear valores monetarios
                if ($campo == 'costo_renovacion') {
                    $valorAnterior = $valorAnterior ? '$' . number_format($valorAnterior, 0, ',', '.') : 'Sin costo';
                    $valorNuevo = $valorNuevo ? '$' . number_format($valorNuevo, 0, ',', '.') : 'Sin costo';
                }
                // Formatear lecturas
                elseif (in_array($campo, ['lectura_anterior', 'lectura_inicial'])) {
                    $valorAnterior = $valorAnterior ? number_format($valorAnterior, 2, ',', '.') . ' m³' : 'Sin lectura';
                    $valorNuevo = $valorNuevo ? number_format($valorNuevo, 2, ',', '.') . ' m³' : 'Sin lectura';
                }
                // Formatear fechas
                elseif ($campo == 'fecha_renovacion') {
                    $valorAnterior = date('d/m/Y', strtotime($valorAnterior));
                    $valorNuevo = date('d/m/Y', strtotime($valorNuevo));
                }
                // Formatear socio
                elseif ($campo == 'id_socio') {
                    $socioAnterior = Socio::find($valorAnterior);
                    $socioNuevo = Socio::find($valorNuevo);
                    $valorAnterior = $socioAnterior ? "{$socioAnterior->numero_socio} - {$socioAnterior->nombre_completo}" : $valorAnterior;
                    $valorNuevo = $socioNuevo ? "{$socioNuevo->numero_socio} - {$socioNuevo->nombre_completo}" : $valorNuevo;
                }
                // Formatear técnico
                elseif ($campo == 'id_tecnico') {
                    $tecnicoAnterior = Funcionario::find($valorAnterior);
                    $tecnicoNuevo = Funcionario::find($valorNuevo);
                    $valorAnterior = $tecnicoAnterior ? $tecnicoAnterior->nombre_completo : 'Sin asignar';
                    $valorNuevo = $tecnicoNuevo ? $tecnicoNuevo->nombre_completo : 'Sin asignar';
                }
                // Formatear motivo
                elseif ($campo == 'motivo') {
                    $motivos = [
                        'deterioro' => 'Deterioro',
                        'falla' => 'Falla',
                        'actualizacion' => 'Actualización',
                        'robo' => 'Robo',
                        'otro' => 'Otro',
                    ];
                    $valorAnterior = $motivos[$valorAnterior] ?? $valorAnterior;
                    $valorNuevo = $motivos[$valorNuevo] ?? $valorNuevo;
                }
                // Formatear estado
                elseif ($campo == 'estado') {
                    $estados = [
                        'planificado' => 'Planificado',
                        'ejecutado' => 'Ejecutado',
                        'cancelado' => 'Cancelado',
                    ];
                    $valorAnterior = $estados[$valorAnterior] ?? $valorAnterior;
                    $valorNuevo = $estados[$valorNuevo] ?? $valorNuevo;
                }

                $cambios[] = "{$nombreCampo}: '{$valorAnterior}' → '{$valorNuevo}'";
            }
        }

        $renovacion->update($validated);
        $socio = Socio::find($validated['id_socio']);

        if (!empty($cambios)) {
            $descripcionCambios = implode(', ', $cambios);
            ActividadHelper::registrar(
                'Renovaciones de Medidores',
                "Renovación actualizada: Socio {$socio->numero_socio} - {$socio->nombre_completo}. Cambios: {$descripcionCambios}",
                auth()->id()
            );
        } else {
            ActividadHelper::registrar(
                'Renovaciones de Medidores',
                "Renovación actualizada: Socio {$socio->numero_socio} - {$socio->nombre_completo}",
                auth()->id()
            );
        }

        return redirect()->route('renovaciones.show', $renovacion->id)
            ->with('success', 'Renovación de medidor actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $renovacion = RenovacionMedidor::findOrFail($id);
        $socio = $renovacion->socio;

        $renovacion->activo = 0;
        $renovacion->save();

        ActividadHelper::registrar(
            'Renovaciones de Medidores',
            "Renovación eliminada: Socio {$socio->numero_socio} - {$socio->nombre_completo} - Medidor: {$renovacion->medidor_nuevo}",
            auth()->id()
        );

        return redirect()->route('renovaciones.index')
            ->with('success', 'Renovación de medidor eliminada exitosamente.');
    }
}
