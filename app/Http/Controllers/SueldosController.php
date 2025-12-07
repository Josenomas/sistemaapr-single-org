<?php

namespace App\Http\Controllers;

use App\Models\Sueldo;
use App\Models\Funcionario;
use App\Helpers\ActividadHelper;
use Illuminate\Http\Request;

class SueldosController extends Controller
{
    public function index(Request $request)
    {
        $query = Sueldo::with('funcionario')->activos();

        // Filtro por funcionario
        if ($request->filled('id_funcionario')) {
            $query->where('id_funcionario', $request->id_funcionario);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por período
        if ($request->filled('periodo')) {
            $query->where('periodo', $request->periodo);
        }

        // Filtro por año
        if ($request->filled('anio')) {
            $query->porAnio($request->anio);
        }

        $sueldos = $query->orderBy('fecha_pago', 'desc')->paginate(20);
        $funcionarios = Funcionario::activos()->orderBy('nombre')->get();

        // Obtener años disponibles para filtro
        $anios = Sueldo::activos()
            ->selectRaw('DISTINCT SUBSTRING(periodo, 1, 4) as anio')
            ->orderBy('anio', 'desc')
            ->pluck('anio');

        return view('sueldos.index', compact('sueldos', 'funcionarios', 'anios'));
    }

    public function create()
    {
        $funcionarios = Funcionario::activos()->orderBy('nombre')->get();
        return view('sueldos.create', compact('funcionarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_funcionario' => 'required|exists:funcionarios,id',
            'periodo' => 'required|regex:/^\d{4}-\d{2}$/',
            'sueldo_base' => 'required|numeric|min:0',
            'bonos' => 'nullable|numeric|min:0',
            'descuentos' => 'nullable|numeric|min:0',
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|in:efectivo,transferencia,cheque',
            'comprobante' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'estado' => 'required|in:pendiente,pagado,anulado',
        ]);

        // Calcular total líquido
        $validated['bonos'] = $validated['bonos'] ?? 0;
        $validated['descuentos'] = $validated['descuentos'] ?? 0;
        $validated['total_liquido'] = $validated['sueldo_base'] + $validated['bonos'] - $validated['descuentos'];

        // Verificar si ya existe un sueldo para este funcionario en este período
        $existe = Sueldo::where('id_funcionario', $validated['id_funcionario'])
            ->where('periodo', $validated['periodo'])
            ->where('activo', 1)
            ->exists();

        if ($existe) {
            return redirect()->back()
                ->withErrors(['periodo' => 'Ya existe un registro de sueldo para este funcionario en el período seleccionado.'])
                ->withInput();
        }

        $sueldo = Sueldo::create($validated);
        $funcionario = Funcionario::find($validated['id_funcionario']);

        ActividadHelper::registrar(
            'Sueldos',
            "Sueldo registrado: {$funcionario->nombre_completo} - {$sueldo->periodo_formateado} - {$sueldo->total_liquido_formateado}",
            auth()->id()
        );

        return redirect()->route('sueldos.show', $sueldo->id)
            ->with('success', 'Sueldo registrado exitosamente.');
    }

    public function show($id)
    {
        $sueldo = Sueldo::with('funcionario')->findOrFail($id);
        return view('sueldos.show', compact('sueldo'));
    }

    public function edit($id)
    {
        $sueldo = Sueldo::findOrFail($id);
        $funcionarios = Funcionario::activos()->orderBy('nombre')->get();
        return view('sueldos.edit', compact('sueldo', 'funcionarios'));
    }

    public function update(Request $request, $id)
    {
        $sueldo = Sueldo::findOrFail($id);

        $validated = $request->validate([
            'id_funcionario' => 'required|exists:funcionarios,id',
            'periodo' => 'required|regex:/^\d{4}-\d{2}$/',
            'sueldo_base' => 'required|numeric|min:0',
            'bonos' => 'nullable|numeric|min:0',
            'descuentos' => 'nullable|numeric|min:0',
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|in:efectivo,transferencia,cheque',
            'comprobante' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'estado' => 'required|in:pendiente,pagado,anulado',
        ]);

        // Calcular total líquido
        $validated['bonos'] = $validated['bonos'] ?? 0;
        $validated['descuentos'] = $validated['descuentos'] ?? 0;
        $validated['total_liquido'] = $validated['sueldo_base'] + $validated['bonos'] - $validated['descuentos'];

        // Verificar si ya existe otro sueldo para este funcionario en este período
        $existe = Sueldo::where('id_funcionario', $validated['id_funcionario'])
            ->where('periodo', $validated['periodo'])
            ->where('id', '!=', $id)
            ->where('activo', 1)
            ->exists();

        if ($existe) {
            return redirect()->back()
                ->withErrors(['periodo' => 'Ya existe un registro de sueldo para este funcionario en el período seleccionado.'])
                ->withInput();
        }

        // Capturar cambios antes de actualizar
        $cambios = [];
        $camposTraducidos = [
            'id_funcionario' => 'Funcionario',
            'periodo' => 'Período',
            'sueldo_base' => 'Sueldo Base',
            'bonos' => 'Bonos',
            'descuentos' => 'Descuentos',
            'total_liquido' => 'Total Líquido',
            'fecha_pago' => 'Fecha de Pago',
            'metodo_pago' => 'Método de Pago',
            'comprobante' => 'Comprobante',
            'observaciones' => 'Observaciones',
            'estado' => 'Estado',
        ];

        foreach ($validated as $campo => $valorNuevo) {
            $valorAnterior = $sueldo->$campo;

            if ($valorAnterior != $valorNuevo) {
                $nombreCampo = $camposTraducidos[$campo] ?? $campo;

                // Formatear valores monetarios
                if (in_array($campo, ['sueldo_base', 'bonos', 'descuentos', 'total_liquido'])) {
                    $valorAnterior = '$' . number_format($valorAnterior, 0, ',', '.');
                    $valorNuevo = '$' . number_format($valorNuevo, 0, ',', '.');
                }
                // Formatear fechas
                elseif ($campo == 'fecha_pago') {
                    $valorAnterior = date('d/m/Y', strtotime($valorAnterior));
                    $valorNuevo = date('d/m/Y', strtotime($valorNuevo));
                }
                // Formatear funcionario
                elseif ($campo == 'id_funcionario') {
                    $funcionarioAnterior = Funcionario::find($valorAnterior);
                    $funcionarioNuevo = Funcionario::find($valorNuevo);
                    $valorAnterior = $funcionarioAnterior ? $funcionarioAnterior->nombre_completo : $valorAnterior;
                    $valorNuevo = $funcionarioNuevo ? $funcionarioNuevo->nombre_completo : $valorNuevo;
                }
                // Formatear método de pago
                elseif ($campo == 'metodo_pago') {
                    $metodos = [
                        'efectivo' => 'Efectivo',
                        'transferencia' => 'Transferencia',
                        'cheque' => 'Cheque',
                    ];
                    $valorAnterior = $metodos[$valorAnterior] ?? $valorAnterior;
                    $valorNuevo = $metodos[$valorNuevo] ?? $valorNuevo;
                }
                // Formatear estado
                elseif ($campo == 'estado') {
                    $estados = [
                        'pendiente' => 'Pendiente',
                        'pagado' => 'Pagado',
                        'anulado' => 'Anulado',
                    ];
                    $valorAnterior = $estados[$valorAnterior] ?? $valorAnterior;
                    $valorNuevo = $estados[$valorNuevo] ?? $valorNuevo;
                }

                $cambios[] = "{$nombreCampo}: '{$valorAnterior}' → '{$valorNuevo}'";
            }
        }

        $sueldo->update($validated);
        $funcionario = Funcionario::find($validated['id_funcionario']);

        if (!empty($cambios)) {
            $descripcionCambios = implode(', ', $cambios);
            ActividadHelper::registrar(
                'Sueldos',
                "Sueldo actualizado: {$funcionario->nombre_completo} - {$sueldo->periodo_formateado}. Cambios: {$descripcionCambios}",
                auth()->id()
            );
        } else {
            ActividadHelper::registrar(
                'Sueldos',
                "Sueldo actualizado: {$funcionario->nombre_completo} - {$sueldo->periodo_formateado}",
                auth()->id()
            );
        }

        return redirect()->route('sueldos.show', $sueldo->id)
            ->with('success', 'Sueldo actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $sueldo = Sueldo::findOrFail($id);
        $funcionario = $sueldo->funcionario;

        $sueldo->activo = 0;
        $sueldo->save();

        ActividadHelper::registrar(
            'Sueldos',
            "Sueldo eliminado: {$funcionario->nombre_completo} - {$sueldo->periodo_formateado} - {$sueldo->total_liquido_formateado}",
            auth()->id()
        );

        return redirect()->route('sueldos.index')
            ->with('success', 'Sueldo eliminado exitosamente.');
    }
}
