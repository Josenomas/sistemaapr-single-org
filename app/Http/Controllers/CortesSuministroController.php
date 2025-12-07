<?php

namespace App\Http\Controllers;

use App\Models\CorteSuministro;
use App\Models\Socio;
use App\Models\Funcionario;
use App\Helpers\ActividadHelper;
use Illuminate\Http\Request;

class CortesSuministroController extends Controller
{
    public function index(Request $request)
    {
        $query = CorteSuministro::with(['socio', 'ejecutor'])->activos();

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
            $query->where('fecha_corte', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_corte', '<=', $request->fecha_hasta);
        }

        $cortes = $query->orderBy('fecha_corte', 'desc')->paginate(20);
        $socios = Socio::activos()->orderBy('numero_socio')->get();

        return view('cortes.index', compact('cortes', 'socios'));
    }

    public function create()
    {
        $socios = Socio::activos()->orderBy('numero_socio')->get();
        $funcionarios = Funcionario::activos()->orderBy('nombre')->get();
        return view('cortes.create', compact('socios', 'funcionarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_socio' => 'required|exists:socios,id',
            'motivo' => 'required|in:morosidad,solicitud_socio,mantenimiento,otro',
            'descripcion' => 'nullable|string',
            'fecha_corte' => 'required|date',
            'estado' => 'required|in:pendiente,ejecutado,reconectado,cancelado',
            'monto_adeudado' => 'nullable|numeric|min:0',
            'id_ejecutor' => 'nullable|exists:funcionarios,id',
            'observaciones' => 'nullable|string',
        ]);

        $corte = CorteSuministro::create($validated);
        $socio = Socio::find($validated['id_socio']);

        ActividadHelper::registrar(
            'Cortes de Suministro',
            "Corte registrado: Socio {$socio->numero_socio} - {$socio->nombre_completo} - Motivo: {$corte->motivo_formateado}",
            auth()->id()
        );

        return redirect()->route('cortes.show', $corte->id)
            ->with('success', 'Corte de suministro registrado exitosamente.');
    }

    public function show($id)
    {
        $corte = CorteSuministro::with(['socio', 'ejecutor'])->findOrFail($id);
        return view('cortes.show', compact('corte'));
    }

    public function edit($id)
    {
        $corte = CorteSuministro::findOrFail($id);
        $socios = Socio::activos()->orderBy('numero_socio')->get();
        $funcionarios = Funcionario::activos()->orderBy('nombre')->get();
        return view('cortes.edit', compact('corte', 'socios', 'funcionarios'));
    }

    public function update(Request $request, $id)
    {
        $corte = CorteSuministro::findOrFail($id);

        $validated = $request->validate([
            'id_socio' => 'required|exists:socios,id',
            'motivo' => 'required|in:morosidad,solicitud_socio,mantenimiento,otro',
            'descripcion' => 'nullable|string',
            'fecha_corte' => 'required|date',
            'fecha_reconexion' => 'nullable|date',
            'estado' => 'required|in:pendiente,ejecutado,reconectado,cancelado',
            'monto_adeudado' => 'nullable|numeric|min:0',
            'monto_reconexion' => 'nullable|numeric|min:0',
            'id_ejecutor' => 'nullable|exists:funcionarios,id',
            'observaciones' => 'nullable|string',
        ]);

        // Capturar cambios antes de actualizar
        $cambios = [];
        $camposTraducidos = [
            'id_socio' => 'Socio',
            'motivo' => 'Motivo',
            'descripcion' => 'Descripción',
            'fecha_corte' => 'Fecha de Corte',
            'fecha_reconexion' => 'Fecha de Reconexión',
            'estado' => 'Estado',
            'monto_adeudado' => 'Monto Adeudado',
            'monto_reconexion' => 'Monto de Reconexión',
            'id_ejecutor' => 'Ejecutor',
            'observaciones' => 'Observaciones',
        ];

        foreach ($validated as $campo => $valorNuevo) {
            $valorAnterior = $corte->$campo;

            if ($valorAnterior != $valorNuevo) {
                $nombreCampo = $camposTraducidos[$campo] ?? $campo;

                // Formatear valores monetarios
                if (in_array($campo, ['monto_adeudado', 'monto_reconexion'])) {
                    $valorAnterior = $valorAnterior ? '$' . number_format($valorAnterior, 0, ',', '.') : 'Sin monto';
                    $valorNuevo = $valorNuevo ? '$' . number_format($valorNuevo, 0, ',', '.') : 'Sin monto';
                }
                // Formatear fechas
                elseif (in_array($campo, ['fecha_corte', 'fecha_reconexion'])) {
                    $valorAnterior = $valorAnterior ? date('d/m/Y', strtotime($valorAnterior)) : 'Sin fecha';
                    $valorNuevo = $valorNuevo ? date('d/m/Y', strtotime($valorNuevo)) : 'Sin fecha';
                }
                // Formatear socio
                elseif ($campo == 'id_socio') {
                    $socioAnterior = Socio::find($valorAnterior);
                    $socioNuevo = Socio::find($valorNuevo);
                    $valorAnterior = $socioAnterior ? "{$socioAnterior->numero_socio} - {$socioAnterior->nombre_completo}" : $valorAnterior;
                    $valorNuevo = $socioNuevo ? "{$socioNuevo->numero_socio} - {$socioNuevo->nombre_completo}" : $valorNuevo;
                }
                // Formatear ejecutor
                elseif ($campo == 'id_ejecutor') {
                    $ejecutorAnterior = Funcionario::find($valorAnterior);
                    $ejecutorNuevo = Funcionario::find($valorNuevo);
                    $valorAnterior = $ejecutorAnterior ? $ejecutorAnterior->nombre_completo : 'Sin asignar';
                    $valorNuevo = $ejecutorNuevo ? $ejecutorNuevo->nombre_completo : 'Sin asignar';
                }
                // Formatear motivo
                elseif ($campo == 'motivo') {
                    $motivos = [
                        'morosidad' => 'Morosidad',
                        'solicitud_socio' => 'Solicitud del Socio',
                        'mantenimiento' => 'Mantenimiento',
                        'otro' => 'Otro',
                    ];
                    $valorAnterior = $motivos[$valorAnterior] ?? $valorAnterior;
                    $valorNuevo = $motivos[$valorNuevo] ?? $valorNuevo;
                }
                // Formatear estado
                elseif ($campo == 'estado') {
                    $estados = [
                        'pendiente' => 'Pendiente',
                        'ejecutado' => 'Ejecutado',
                        'reconectado' => 'Reconectado',
                        'cancelado' => 'Cancelado',
                    ];
                    $valorAnterior = $estados[$valorAnterior] ?? $valorAnterior;
                    $valorNuevo = $estados[$valorNuevo] ?? $valorNuevo;
                }

                $cambios[] = "{$nombreCampo}: '{$valorAnterior}' → '{$valorNuevo}'";
            }
        }

        $corte->update($validated);
        $socio = Socio::find($validated['id_socio']);

        if (!empty($cambios)) {
            $descripcionCambios = implode(', ', $cambios);
            ActividadHelper::registrar(
                'Cortes de Suministro',
                "Corte actualizado: Socio {$socio->numero_socio} - {$socio->nombre_completo}. Cambios: {$descripcionCambios}",
                auth()->id()
            );
        } else {
            ActividadHelper::registrar(
                'Cortes de Suministro',
                "Corte actualizado: Socio {$socio->numero_socio} - {$socio->nombre_completo}",
                auth()->id()
            );
        }

        return redirect()->route('cortes.show', $corte->id)
            ->with('success', 'Corte de suministro actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $corte = CorteSuministro::findOrFail($id);
        $socio = $corte->socio;

        $corte->activo = 0;
        $corte->save();

        ActividadHelper::registrar(
            'Cortes de Suministro',
            "Corte eliminado: Socio {$socio->numero_socio} - {$socio->nombre_completo} - Motivo: {$corte->motivo_formateado}",
            auth()->id()
        );

        return redirect()->route('cortes.index')
            ->with('success', 'Corte de suministro eliminado exitosamente.');
    }
}
