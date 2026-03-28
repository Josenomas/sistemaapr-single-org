<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Funcionario;
use App\Models\Auditoria;
use App\Helpers\ActividadHelper;

class FuncionariosController extends Controller
{
    /**
     * Listar todos los funcionarios
     */
    public function index(Request $request)
    {
        $query = Funcionario::query();

        // Filtrar por estado
        if ($request->has('estado') && $request->estado) {
            $query->where('estado', $request->estado);
        }

        // Filtrar por cargo
        if ($request->has('cargo') && $request->cargo) {
            $query->where('cargo', $request->cargo);
        }

        $funcionarios = $query->where('activo', 1)
                              ->orderBy('nombre')
                              ->paginate(20);

        // Obtener cargos únicos para el filtro
        $cargos = Funcionario::where('activo', 1)
                             ->distinct()
                             ->pluck('cargo')
                             ->toArray();

        return view('funcionarios.index', compact('funcionarios', 'cargos'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('funcionarios.create');
    }

    /**
     * Guardar nuevo funcionario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rut' => 'required|string|max:12|unique:funcionarios,rut',
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'cargo' => 'required|string|max:100',
            'email' => 'nullable|email|max:150',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'fecha_ingreso' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        $funcionario = Funcionario::create($validated);

        // Registrar actividad
        ActividadHelper::registrar(
            'Funcionarios',
            "Nuevo funcionario registrado: {$funcionario->nombre_completo} - Cargo: {$funcionario->cargo}",
            auth()->id()
        );

        // Registrar en auditoría
        Auditoria::registrar(
            'funcionarios',
            'crear',
            "Creó funcionario: {$funcionario->nombre_completo} - Cargo: {$funcionario->cargo}",
            'funcionarios',
            $funcionario->id,
            null,
            $funcionario->toArray()
        );

        return redirect()->route('funcionarios.index')
                        ->with('success', 'Funcionario creado exitosamente');
    }

    /**
     * Mostrar detalle de funcionario
     */
    public function show($id)
    {
        $funcionario = Funcionario::findOrFail($id);
        return view('funcionarios.show', compact('funcionario'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $funcionario = Funcionario::findOrFail($id);
        return view('funcionarios.edit', compact('funcionario'));
    }

    /**
     * Actualizar funcionario
     */
    public function update(Request $request, $id)
    {
        $funcionario = Funcionario::findOrFail($id);

        $validated = $request->validate([
            'rut' => 'required|string|max:12|unique:funcionarios,rut,' . $id,
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'cargo' => 'required|string|max:100',
            'email' => 'nullable|email|max:150',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'fecha_ingreso' => 'required|date',
            'fecha_termino' => 'nullable|date',
            'estado' => 'required|in:activo,inactivo,licencia',
            'observaciones' => 'nullable|string',
        ]);

        // Capturar datos antes de actualizar para auditoría
        $datosAnteriores = $funcionario->toArray();

        // Capturar cambios antes de actualizar
        $cambios = [];
        $camposTraducidos = [
            'rut' => 'RUT',
            'nombre' => 'Nombre',
            'apellido_paterno' => 'Apellido Paterno',
            'apellido_materno' => 'Apellido Materno',
            'cargo' => 'Cargo',
            'email' => 'Email',
            'telefono' => 'Teléfono',
            'direccion' => 'Dirección',
            'fecha_ingreso' => 'Fecha Ingreso',
            'fecha_termino' => 'Fecha Término',
            'estado' => 'Estado',
            'observaciones' => 'Observaciones',
        ];

        foreach ($validated as $campo => $valorNuevo) {
            $valorAnterior = $funcionario->$campo;
            if ($valorAnterior != $valorNuevo) {
                $nombreCampo = $camposTraducidos[$campo] ?? $campo;

                // Formatear valores según el tipo
                if ($campo == 'fecha_ingreso' || $campo == 'fecha_termino') {
                    if ($valorAnterior) {
                        $valorAnterior = date('d/m/Y', strtotime($valorAnterior));
                    }
                    if ($valorNuevo) {
                        $valorNuevo = date('d/m/Y', strtotime($valorNuevo));
                    }
                } elseif ($campo == 'estado') {
                    $valorAnterior = ucfirst($valorAnterior);
                    $valorNuevo = ucfirst($valorNuevo);
                }

                $cambios[] = "{$nombreCampo}: '{$valorAnterior}' → '{$valorNuevo}'";
            }
        }

        $funcionario->update($validated);

        // Registrar actividad con cambios
        if (!empty($cambios)) {
            $descripcionCambios = implode(', ', $cambios);
            ActividadHelper::registrar(
                'Funcionarios',
                "Funcionario actualizado: {$funcionario->nombre_completo}. Cambios: {$descripcionCambios}",
                auth()->id()
            );

            // Registrar en auditoría
            Auditoria::registrar(
                'funcionarios',
                'editar',
                "Editó funcionario: {$funcionario->nombre_completo}. Cambios: {$descripcionCambios}",
                'funcionarios',
                $funcionario->id,
                $datosAnteriores,
                $funcionario->fresh()->toArray()
            );
        } else {
            ActividadHelper::registrar(
                'Funcionarios',
                "Funcionario actualizado: {$funcionario->nombre_completo} - Cargo: {$funcionario->cargo}",
                auth()->id()
            );
        }

        return redirect()->route('funcionarios.show', $id)
                        ->with('success', 'Funcionario actualizado exitosamente');
    }

    /**
     * Eliminar funcionario (soft delete)
     */
    public function destroy($id)
    {
        $funcionario = Funcionario::findOrFail($id);
        $nombreCompleto = $funcionario->nombre_completo;
        $cargo = $funcionario->cargo;
        $datosAnteriores = $funcionario->toArray();

        $funcionario->update(['activo' => 0]);

        // Registrar actividad
        ActividadHelper::registrar(
            'Funcionarios',
            "Funcionario eliminado: {$nombreCompleto} - Cargo: {$cargo}",
            auth()->id()
        );

        // Registrar en auditoría
        Auditoria::registrar(
            'funcionarios',
            'eliminar',
            "Eliminó funcionario: {$nombreCompleto} - Cargo: {$cargo}",
            'funcionarios',
            null,
            $datosAnteriores,
            null
        );

        return redirect()->route('funcionarios.index')
                        ->with('success', 'Funcionario eliminado exitosamente');
    }
}
