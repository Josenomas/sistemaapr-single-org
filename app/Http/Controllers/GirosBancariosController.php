<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GiroBancario;
use App\Models\Funcionario;
use App\Helpers\ActividadHelper;
use Illuminate\Support\Facades\DB;

class GirosBancariosController extends Controller
{
    /**
     * Listar todos los giros bancarios
     */
    public function index(Request $request)
    {
        $query = GiroBancario::with('responsable')->where('activo', 1);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_giro', 'like', "%{$search}%")
                  ->orWhere('beneficiario', 'like', "%{$search}%")
                  ->orWhere('numero_comprobante', 'like', "%{$search}%");
            });
        }

        if ($request->filled('banco')) {
            $query->where('banco', 'like', "%{$request->banco}%");
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_emision', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_emision', '<=', $request->fecha_hasta);
        }

        if ($request->filled('beneficiario')) {
            $query->where('beneficiario', 'like', "%{$request->beneficiario}%");
        }

        // Estadísticas
        $estadisticas = [
            'total_giros' => GiroBancario::where('activo', 1)->count(),
            'emitidos' => GiroBancario::where('activo', 1)->where('estado', 'emitido')->count(),
            'pagados' => GiroBancario::where('activo', 1)->where('estado', 'pagado')->count(),
            'total_monto_emitido' => GiroBancario::where('activo', 1)->where('estado', 'emitido')->sum('monto')
        ];

        $giros = $query->orderBy('fecha_emision', 'desc')
                      ->orderBy('id', 'desc')
                      ->paginate(20);

        return view('giros-bancarios.index', compact('giros', 'estadisticas'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $funcionarios = Funcionario::activos()->orderBy('nombre')->get();
        return view('giros-bancarios.create', compact('funcionarios'));
    }

    /**
     * Guardar nuevo giro bancario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'banco' => 'required|string|max:100',
            'numero_cuenta' => 'required|string|max:50',
            'tipo_cuenta' => 'required|in:corriente,vista,ahorro',
            'beneficiario' => 'required|string|max:200',
            'rut_beneficiario' => 'nullable|string|max:12',
            'monto' => 'required|numeric|min:1',
            'fecha_emision' => 'required|date',
            'concepto' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:emitido,pagado,anulado,vencido',
            'metodo_entrega' => 'required|in:retiro_sucursal,transferencia,cheque',
            'numero_comprobante' => 'nullable|string|max:100',
            'id_responsable' => 'nullable|exists:funcionarios,id',
            'observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generar número de giro automáticamente
            $numeroGiro = GiroBancario::generarNumeroGiro();
            $validated['numero_giro'] = $numeroGiro;

            $giro = GiroBancario::create($validated);

            // Registrar actividad
            $detalles = [
                "Número: {$numeroGiro}",
                "Banco: {$validated['banco']}",
                "Beneficiario: {$validated['beneficiario']}",
                "Monto: $" . number_format($validated['monto'], 0, ',', '.'),
                "Estado: " . ucfirst($validated['estado'])
            ];

            ActividadHelper::registrar(
                'Giros Bancarios',
                'Nuevo giro bancario registrado: ' . implode(' | ', $detalles),
                auth()->id()
            );

            DB::commit();

            return redirect()->route('giros-bancarios.index')
                           ->with('success', 'Giro bancario registrado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al registrar el giro bancario: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle del giro bancario
     */
    public function show($id)
    {
        $giro = GiroBancario::with('responsable')->findOrFail($id);
        return view('giros-bancarios.show', compact('giro'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $giro = GiroBancario::findOrFail($id);
        $funcionarios = Funcionario::activos()->orderBy('nombre')->get();
        return view('giros-bancarios.edit', compact('giro', 'funcionarios'));
    }

    /**
     * Actualizar giro bancario
     */
    public function update(Request $request, $id)
    {
        $giro = GiroBancario::findOrFail($id);

        $validated = $request->validate([
            'banco' => 'required|string|max:100',
            'numero_cuenta' => 'required|string|max:50',
            'tipo_cuenta' => 'required|in:corriente,vista,ahorro',
            'beneficiario' => 'required|string|max:200',
            'rut_beneficiario' => 'nullable|string|max:12',
            'monto' => 'required|numeric|min:1',
            'fecha_emision' => 'required|date',
            'fecha_pago' => 'nullable|date',
            'concepto' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:emitido,pagado,anulado,vencido',
            'metodo_entrega' => 'required|in:retiro_sucursal,transferencia,cheque',
            'numero_comprobante' => 'nullable|string|max:100',
            'id_responsable' => 'nullable|exists:funcionarios,id',
            'observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Capturar cambios antes de actualizar
            $cambios = [];

            if ($giro->banco != $validated['banco']) {
                $cambios[] = "Banco: '{$giro->banco}' → '{$validated['banco']}'";
            }

            if ($giro->beneficiario != $validated['beneficiario']) {
                $cambios[] = "Beneficiario: '{$giro->beneficiario}' → '{$validated['beneficiario']}'";
            }

            if ($giro->monto != $validated['monto']) {
                $cambios[] = "Monto: '{$giro->monto_formateado}' → '$" . number_format($validated['monto'], 0, ',', '.') . "'";
            }

            if ($giro->estado != $validated['estado']) {
                $cambios[] = "Estado: '{$giro->estado_texto}' → '" . ucfirst($validated['estado']) . "'";
            }

            $fechaPagoAnterior = $giro->fecha_pago ? $giro->fecha_pago->format('Y-m-d') : null;
            $fechaPagoNueva = $validated['fecha_pago'] ?? null;
            if ($fechaPagoAnterior != $fechaPagoNueva) {
                $fechaAnteriorTexto = $fechaPagoAnterior ? date('d/m/Y', strtotime($fechaPagoAnterior)) : 'Sin fecha';
                $fechaNuevaTexto = $fechaPagoNueva ? date('d/m/Y', strtotime($fechaPagoNueva)) : 'Sin fecha';
                $cambios[] = "Fecha Pago: '{$fechaAnteriorTexto}' → '{$fechaNuevaTexto}'";
            }

            $giro->update($validated);

            // Registrar actividad con cambios
            if (!empty($cambios)) {
                $descripcionCambios = implode(' | ', $cambios);
                ActividadHelper::registrar(
                    'Giros Bancarios',
                    "Giro bancario actualizado [{$giro->numero_giro}]: {$descripcionCambios}",
                    auth()->id()
                );
            } else {
                ActividadHelper::registrar(
                    'Giros Bancarios',
                    "Giro bancario actualizado [{$giro->numero_giro}]",
                    auth()->id()
                );
            }

            DB::commit();

            return redirect()->route('giros-bancarios.show', $id)
                           ->with('success', 'Giro bancario actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar el giro bancario: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar giro bancario (soft delete)
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $giro = GiroBancario::findOrFail($id);
            $numeroGiro = $giro->numero_giro;
            $beneficiario = $giro->beneficiario;

            $giro->update(['activo' => 0]);

            // Registrar actividad
            ActividadHelper::registrar(
                'Giros Bancarios',
                "Giro bancario eliminado [{$numeroGiro}] - Beneficiario: {$beneficiario}",
                auth()->id()
            );

            DB::commit();

            return redirect()->route('giros-bancarios.index')
                           ->with('success', 'Giro bancario eliminado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error al eliminar el giro bancario: ' . $e->getMessage());
        }
    }
}
