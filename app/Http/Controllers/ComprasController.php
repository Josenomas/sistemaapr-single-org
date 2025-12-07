<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Funcionario;
use App\Helpers\ActividadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComprasController extends Controller
{
    public function index(Request $request)
    {
        $query = Compra::with('responsable')
                       ->activos()
                       ->orderBy('fecha_compra', 'desc');

        // Filtros
        if ($request->filled('proveedor')) {
            $query->porProveedor($request->proveedor);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo_compra', $request->tipo);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
            $query->porFecha($request->fecha_desde, $request->fecha_hasta);
        }

        $compras = $query->paginate(15);

        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        $funcionarios = Funcionario::activos()
                                   ->where('estado', 'activo')
                                   ->orderBy('nombre')
                                   ->get();

        $numeroCompra = Compra::generarNumeroCompra();

        return view('compras.create', compact('funcionarios', 'numeroCompra'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_compra' => 'required|string|max:50|unique:compras,numero_compra',
            'fecha_compra' => 'required|date',
            'proveedor' => 'required|string|max:200',
            'rut_proveedor' => 'nullable|string|max:12',
            'tipo_compra' => 'required|in:materiales,equipos,herramientas,insumos,servicios,otro',
            'descripcion' => 'required|string',
            'cantidad' => 'required|numeric|min:0.01',
            'unidad_medida' => 'nullable|string|max:50',
            'precio_unitario' => 'required|numeric|min:0',
            'iva' => 'nullable|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,transferencia,cheque,credito',
            'numero_factura' => 'nullable|string|max:50',
            'fecha_pago' => 'nullable|date',
            'estado' => 'required|in:pendiente,pagada,anulada',
            'id_responsable' => 'nullable|exists:funcionarios,id',
            'observaciones' => 'nullable|string'
        ]);

        // Calcular subtotal y total
        $validated['subtotal'] = $validated['cantidad'] * $validated['precio_unitario'];
        $validated['iva'] = $validated['iva'] ?? 0;
        $validated['total'] = $validated['subtotal'] + $validated['iva'];
        $validated['activo'] = 1;

        DB::beginTransaction();
        try {
            $compra = Compra::create($validated);

            // Registrar actividad
            $tipoTexto = $compra->tipo_compra_texto;
            $estadoTexto = $compra->estado_texto;

            ActividadHelper::registrar(
                'Compras',
                "Nueva compra registrada: {$compra->numero_compra} - {$compra->proveedor} - {$tipoTexto} - Total: {$compra->total_formateado} - Estado: {$estadoTexto}",
                auth()->id()
            );

            DB::commit();
            return redirect()->route('compras.index')->with('success', 'Compra registrada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al registrar la compra: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $compra = Compra::with('responsable')
                        ->where('activo', 1)
                        ->findOrFail($id);

        return view('compras.show', compact('compra'));
    }

    public function edit($id)
    {
        $compra = Compra::where('activo', 1)->findOrFail($id);

        $funcionarios = Funcionario::activos()
                                   ->where('estado', 'activo')
                                   ->orderBy('nombre')
                                   ->get();

        return view('compras.edit', compact('compra', 'funcionarios'));
    }

    public function update(Request $request, $id)
    {
        $compra = Compra::where('activo', 1)->findOrFail($id);

        $validated = $request->validate([
            'numero_compra' => 'required|string|max:50|unique:compras,numero_compra,' . $id,
            'fecha_compra' => 'required|date',
            'proveedor' => 'required|string|max:200',
            'rut_proveedor' => 'nullable|string|max:12',
            'tipo_compra' => 'required|in:materiales,equipos,herramientas,insumos,servicios,otro',
            'descripcion' => 'required|string',
            'cantidad' => 'required|numeric|min:0.01',
            'unidad_medida' => 'nullable|string|max:50',
            'precio_unitario' => 'required|numeric|min:0',
            'iva' => 'nullable|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,transferencia,cheque,credito',
            'numero_factura' => 'nullable|string|max:50',
            'fecha_pago' => 'nullable|date',
            'estado' => 'required|in:pendiente,pagada,anulada',
            'id_responsable' => 'nullable|exists:funcionarios,id',
            'observaciones' => 'nullable|string'
        ]);

        // Calcular subtotal y total
        $validated['subtotal'] = $validated['cantidad'] * $validated['precio_unitario'];
        $validated['iva'] = $validated['iva'] ?? 0;
        $validated['total'] = $validated['subtotal'] + $validated['iva'];

        DB::beginTransaction();
        try {
            // Capturar cambios para auditoría
            $cambios = [];
            $valoresAnteriores = $compra->toArray();

            // Mapeo de nombres de campos
            $nombresCampos = [
                'numero_compra' => 'Número de Compra',
                'fecha_compra' => 'Fecha de Compra',
                'proveedor' => 'Proveedor',
                'rut_proveedor' => 'RUT Proveedor',
                'tipo_compra' => 'Tipo de Compra',
                'descripcion' => 'Descripción',
                'cantidad' => 'Cantidad',
                'unidad_medida' => 'Unidad de Medida',
                'precio_unitario' => 'Precio Unitario',
                'iva' => 'IVA',
                'metodo_pago' => 'Método de Pago',
                'numero_factura' => 'Número de Factura',
                'fecha_pago' => 'Fecha de Pago',
                'estado' => 'Estado',
                'id_responsable' => 'Responsable',
                'observaciones' => 'Observaciones'
            ];

            foreach ($validated as $campo => $valorNuevo) {
                if ($campo == 'subtotal' || $campo == 'total') continue;

                $valorAnterior = $valoresAnteriores[$campo] ?? null;

                // Convertir null a string vacío para comparación
                $valorAnterior = $valorAnterior ?? '';
                $valorNuevo = $valorNuevo ?? '';

                if ($valorAnterior != $valorNuevo) {
                    $nombreCampo = $nombresCampos[$campo] ?? $campo;

                    // Formatear valores según el tipo de campo
                    if ($campo == 'id_responsable') {
                        $responsableAnterior = $valorAnterior ? Funcionario::find($valorAnterior) : null;
                        $responsableNuevo = $valorNuevo ? Funcionario::find($valorNuevo) : null;
                        $valorAnterior = $responsableAnterior ? "{$responsableAnterior->nombre} {$responsableAnterior->apellido_paterno}" : 'Sin asignar';
                        $valorNuevo = $responsableNuevo ? "{$responsableNuevo->nombre} {$responsableNuevo->apellido_paterno}" : 'Sin asignar';
                    } elseif (in_array($campo, ['fecha_compra', 'fecha_pago'])) {
                        $valorAnterior = $valorAnterior ? date('d/m/Y', strtotime($valorAnterior)) : 'Sin fecha';
                        $valorNuevo = $valorNuevo ? date('d/m/Y', strtotime($valorNuevo)) : 'Sin fecha';
                    } elseif (in_array($campo, ['precio_unitario', 'iva'])) {
                        $valorAnterior = '$' . number_format($valorAnterior, 0, ',', '.');
                        $valorNuevo = '$' . number_format($valorNuevo, 0, ',', '.');
                    } elseif ($campo == 'cantidad') {
                        $valorAnterior = number_format($valorAnterior, 2, ',', '.');
                        $valorNuevo = number_format($valorNuevo, 2, ',', '.');
                    } elseif ($campo == 'tipo_compra') {
                        $tipos = [
                            'materiales' => 'Materiales',
                            'equipos' => 'Equipos',
                            'herramientas' => 'Herramientas',
                            'insumos' => 'Insumos',
                            'servicios' => 'Servicios',
                            'otro' => 'Otro'
                        ];
                        $valorAnterior = $tipos[$valorAnterior] ?? $valorAnterior;
                        $valorNuevo = $tipos[$valorNuevo] ?? $valorNuevo;
                    } elseif ($campo == 'estado') {
                        $estados = [
                            'pendiente' => 'Pendiente',
                            'pagada' => 'Pagada',
                            'anulada' => 'Anulada'
                        ];
                        $valorAnterior = $estados[$valorAnterior] ?? $valorAnterior;
                        $valorNuevo = $estados[$valorNuevo] ?? $valorNuevo;
                    } elseif ($campo == 'metodo_pago') {
                        $metodos = [
                            'efectivo' => 'Efectivo',
                            'transferencia' => 'Transferencia',
                            'cheque' => 'Cheque',
                            'credito' => 'Crédito'
                        ];
                        $valorAnterior = $metodos[$valorAnterior] ?? $valorAnterior;
                        $valorNuevo = $metodos[$valorNuevo] ?? $valorNuevo;
                    }

                    $cambios[] = "{$nombreCampo}: '{$valorAnterior}' → '{$valorNuevo}'";
                }
            }

            $compra->update($validated);

            // Registrar actividad si hubo cambios
            if (!empty($cambios)) {
                $descripcionCambios = implode(', ', $cambios);

                ActividadHelper::registrar(
                    'Compras',
                    "Compra actualizada: {$compra->numero_compra} - {$compra->proveedor}. Cambios: {$descripcionCambios}",
                    auth()->id()
                );
            }

            DB::commit();
            return redirect()->route('compras.index')->with('success', 'Compra actualizada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al actualizar la compra: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $compra = Compra::where('activo', 1)->findOrFail($id);

        DB::beginTransaction();
        try {
            $numeroCompra = $compra->numero_compra;
            $proveedor = $compra->proveedor;

            $compra->update(['activo' => 0]);

            ActividadHelper::registrar(
                'Compras',
                "Compra eliminada: {$numeroCompra} - {$proveedor}",
                auth()->id()
            );

            DB::commit();
            return redirect()->route('compras.index')->with('success', 'Compra eliminada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar la compra: ' . $e->getMessage());
        }
    }
}
