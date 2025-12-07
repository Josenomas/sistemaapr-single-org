<?php

namespace App\Http\Controllers;

use App\Models\Vacacion;
use App\Models\Funcionario;
use App\Helpers\ActividadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VacacionesController extends Controller
{
    public function index(Request $request)
    {
        $query = Vacacion::with(['funcionario', 'aprobador', 'funcionarioSuplente'])
                         ->activos()
                         ->orderBy('fecha_inicio', 'desc');

        // Filtros
        if ($request->filled('funcionario')) {
            $query->where('id_funcionario', $request->funcionario);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('periodo')) {
            $query->where('periodo', $request->periodo);
        }

        $vacaciones = $query->paginate(15);

        $funcionarios = Funcionario::activos()
                                   ->where('estado', 'activo')
                                   ->orderBy('nombre')
                                   ->get();

        return view('vacaciones.index', compact('vacaciones', 'funcionarios'));
    }

    public function create()
    {
        $funcionarios = Funcionario::activos()
                                   ->where('estado', 'activo')
                                   ->orderBy('nombre')
                                   ->get();

        return view('vacaciones.create', compact('funcionarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_funcionario' => 'required|exists:funcionarios,id',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'required|date|after_or_equal:fecha_inicio',
            'dias_habiles' => 'required|integer|min:1',
            'periodo' => 'required|digits:4',
            'tipo' => 'required|in:legales,progresivas,administrativas,sin_goce',
            'estado' => 'required|in:solicitada,aprobada,rechazada,en_curso,finalizada,cancelada',
            'fecha_solicitud' => 'required|date',
            'fecha_aprobacion' => 'nullable|date',
            'id_aprobador' => 'nullable|exists:funcionarios,id',
            'motivo_rechazo' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'suplente' => 'nullable|exists:funcionarios,id'
        ]);

        $validated['activo'] = 1;

        DB::beginTransaction();
        try {
            $vacacion = Vacacion::create($validated);

            // Registrar actividad
            $funcionario = Funcionario::find($validated['id_funcionario']);
            $tipoTexto = $vacacion->tipo_texto;
            $estadoTexto = $vacacion->estado_texto;

            ActividadHelper::registrar(
                'Vacaciones',
                "Nueva vacación registrada: {$funcionario->nombre} {$funcionario->apellido_paterno} - {$tipoTexto} - {$vacacion->periodo_completo} ({$validated['dias_habiles']} días) - Estado: {$estadoTexto}",
                auth()->id()
            );

            DB::commit();
            return redirect()->route('vacaciones.index')->with('success', 'Vacación registrada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al registrar la vacación: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $vacacion = Vacacion::with(['funcionario', 'aprobador', 'funcionarioSuplente'])
                            ->where('activo', 1)
                            ->findOrFail($id);

        return view('vacaciones.show', compact('vacacion'));
    }

    public function edit($id)
    {
        $vacacion = Vacacion::where('activo', 1)->findOrFail($id);

        $funcionarios = Funcionario::activos()
                                   ->where('estado', 'activo')
                                   ->orderBy('nombre')
                                   ->get();

        return view('vacaciones.edit', compact('vacacion', 'funcionarios'));
    }

    public function update(Request $request, $id)
    {
        $vacacion = Vacacion::where('activo', 1)->findOrFail($id);

        $validated = $request->validate([
            'id_funcionario' => 'required|exists:funcionarios,id',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'required|date|after_or_equal:fecha_inicio',
            'dias_habiles' => 'required|integer|min:1',
            'periodo' => 'required|digits:4',
            'tipo' => 'required|in:legales,progresivas,administrativas,sin_goce',
            'estado' => 'required|in:solicitada,aprobada,rechazada,en_curso,finalizada,cancelada',
            'fecha_solicitud' => 'required|date',
            'fecha_aprobacion' => 'nullable|date',
            'id_aprobador' => 'nullable|exists:funcionarios,id',
            'motivo_rechazo' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'suplente' => 'nullable|exists:funcionarios,id'
        ]);

        DB::beginTransaction();
        try {
            // Capturar cambios para auditoría
            $cambios = [];
            $valoresAnteriores = $vacacion->toArray();

            // Mapeo de nombres de campos
            $nombresCampos = [
                'id_funcionario' => 'Funcionario',
                'fecha_inicio' => 'Fecha de Inicio',
                'fecha_termino' => 'Fecha de Término',
                'dias_habiles' => 'Días Hábiles',
                'periodo' => 'Periodo',
                'tipo' => 'Tipo',
                'estado' => 'Estado',
                'fecha_solicitud' => 'Fecha de Solicitud',
                'fecha_aprobacion' => 'Fecha de Aprobación',
                'id_aprobador' => 'Aprobador',
                'motivo_rechazo' => 'Motivo de Rechazo',
                'observaciones' => 'Observaciones',
                'suplente' => 'Suplente'
            ];

            foreach ($validated as $campo => $valorNuevo) {
                $valorAnterior = $valoresAnteriores[$campo] ?? null;

                // Convertir null a string vacío para comparación
                $valorAnterior = $valorAnterior ?? '';
                $valorNuevo = $valorNuevo ?? '';

                if ($valorAnterior != $valorNuevo) {
                    $nombreCampo = $nombresCampos[$campo] ?? $campo;

                    // Formatear valores según el tipo de campo
                    if ($campo == 'id_funcionario') {
                        $funcionarioAnterior = Funcionario::find($valorAnterior);
                        $funcionarioNuevo = Funcionario::find($valorNuevo);
                        $valorAnterior = $funcionarioAnterior ? "{$funcionarioAnterior->nombre} {$funcionarioAnterior->apellido_paterno}" : 'N/A';
                        $valorNuevo = $funcionarioNuevo ? "{$funcionarioNuevo->nombre} {$funcionarioNuevo->apellido_paterno}" : 'N/A';
                    } elseif ($campo == 'id_aprobador') {
                        $aprobadorAnterior = $valorAnterior ? Funcionario::find($valorAnterior) : null;
                        $aprobadorNuevo = $valorNuevo ? Funcionario::find($valorNuevo) : null;
                        $valorAnterior = $aprobadorAnterior ? "{$aprobadorAnterior->nombre} {$aprobadorAnterior->apellido_paterno}" : 'Sin asignar';
                        $valorNuevo = $aprobadorNuevo ? "{$aprobadorNuevo->nombre} {$aprobadorNuevo->apellido_paterno}" : 'Sin asignar';
                    } elseif ($campo == 'suplente') {
                        $suplenteAnterior = $valorAnterior ? Funcionario::find($valorAnterior) : null;
                        $suplenteNuevo = $valorNuevo ? Funcionario::find($valorNuevo) : null;
                        $valorAnterior = $suplenteAnterior ? "{$suplenteAnterior->nombre} {$suplenteAnterior->apellido_paterno}" : 'Sin asignar';
                        $valorNuevo = $suplenteNuevo ? "{$suplenteNuevo->nombre} {$suplenteNuevo->apellido_paterno}" : 'Sin asignar';
                    } elseif (in_array($campo, ['fecha_inicio', 'fecha_termino', 'fecha_solicitud', 'fecha_aprobacion'])) {
                        $valorAnterior = $valorAnterior ? date('d/m/Y', strtotime($valorAnterior)) : 'Sin fecha';
                        $valorNuevo = $valorNuevo ? date('d/m/Y', strtotime($valorNuevo)) : 'Sin fecha';
                    } elseif ($campo == 'tipo') {
                        $tipos = [
                            'legales' => 'Legales',
                            'progresivas' => 'Progresivas',
                            'administrativas' => 'Administrativas',
                            'sin_goce' => 'Sin Goce de Sueldo'
                        ];
                        $valorAnterior = $tipos[$valorAnterior] ?? $valorAnterior;
                        $valorNuevo = $tipos[$valorNuevo] ?? $valorNuevo;
                    } elseif ($campo == 'estado') {
                        $estados = [
                            'solicitada' => 'Solicitada',
                            'aprobada' => 'Aprobada',
                            'rechazada' => 'Rechazada',
                            'en_curso' => 'En Curso',
                            'finalizada' => 'Finalizada',
                            'cancelada' => 'Cancelada'
                        ];
                        $valorAnterior = $estados[$valorAnterior] ?? $valorAnterior;
                        $valorNuevo = $estados[$valorNuevo] ?? $valorNuevo;
                    } elseif ($campo == 'dias_habiles') {
                        $valorAnterior = $valorAnterior . ' días';
                        $valorNuevo = $valorNuevo . ' días';
                    }

                    $cambios[] = "{$nombreCampo}: '{$valorAnterior}' → '{$valorNuevo}'";
                }
            }

            $vacacion->update($validated);

            // Registrar actividad si hubo cambios
            if (!empty($cambios)) {
                $funcionario = Funcionario::find($validated['id_funcionario']);
                $descripcionCambios = implode(', ', $cambios);

                ActividadHelper::registrar(
                    'Vacaciones',
                    "Vacación actualizada: {$funcionario->nombre} {$funcionario->apellido_paterno} - {$vacacion->periodo_completo}. Cambios: {$descripcionCambios}",
                    auth()->id()
                );
            }

            DB::commit();
            return redirect()->route('vacaciones.index')->with('success', 'Vacación actualizada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al actualizar la vacación: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $vacacion = Vacacion::where('activo', 1)->findOrFail($id);

        DB::beginTransaction();
        try {
            $funcionario = $vacacion->funcionario;
            $periodo = $vacacion->periodo_completo;

            $vacacion->update(['activo' => 0]);

            ActividadHelper::registrar(
                'Vacaciones',
                "Vacación eliminada: {$funcionario->nombre} {$funcionario->apellido_paterno} - {$periodo}",
                auth()->id()
            );

            DB::commit();
            return redirect()->route('vacaciones.index')->with('success', 'Vacación eliminada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar la vacación: ' . $e->getMessage());
        }
    }
}
