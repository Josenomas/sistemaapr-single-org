<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Socio;
use App\Helpers\ActividadHelper;

class SociosController extends Controller
{
    /**
     * Listar todos los socios
     */
    public function index()
    {
        $socios = Socio::where('activo', 1)
                      ->orderBy('numero_socio')
                      ->paginate(20);

        return view('socios.index', compact('socios'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('socios.create');
    }

    /**
     * Guardar nuevo socio
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rut' => 'required|unique:socios,rut',
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'direccion' => 'required|string|max:255',
            'sector' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'tipo_cliente' => 'required|in:residencial,comercial,industrial',
            'numero_medidor' => 'nullable|string|max:50',
            'fecha_ingreso' => 'required|date',
        ]);

        // Generar número de socio automático
        $ultimoSocio = Socio::orderBy('id', 'desc')->first();
        $numeroSocio = 'SOC-' . str_pad(($ultimoSocio ? $ultimoSocio->id + 1 : 1), 4, '0', STR_PAD_LEFT);

        $validated['numero_socio'] = $numeroSocio;
        $validated['estado'] = 'activo';

        $socio = Socio::create($validated);

        // Registrar actividad
        ActividadHelper::registrar(
            'Socios',
            "Nuevo socio registrado: {$numeroSocio} - {$validated['nombre']} {$validated['apellido_paterno']}"
        );

        return redirect()->route('socios.index')
                        ->with('success', 'Socio registrado exitosamente');
    }

    /**
     * Mostrar detalle del socio
     */
    public function show($id)
    {
        $socio = Socio::with(['lecturas', 'boletas', 'pagos', 'incidentes'])
                     ->findOrFail($id);

        return view('socios.show', compact('socio'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $socio = Socio::findOrFail($id);
        return view('socios.edit', compact('socio'));
    }

    /**
     * Actualizar socio
     */
    public function update(Request $request, $id)
    {
        $socio = Socio::findOrFail($id);

        $validated = $request->validate([
            'rut' => 'required|unique:socios,rut,' . $id,
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'direccion' => 'required|string|max:255',
            'sector' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'tipo_cliente' => 'required|in:residencial,comercial,industrial',
            'numero_medidor' => 'nullable|string|max:50',
            'estado' => 'required|in:activo,suspendido,moroso,desconectado',
            'subsidio_porcentaje' => 'nullable|numeric|min:0|max:100',
            'descuento_monto' => 'nullable|numeric|min:0',
            'observaciones_subsidio' => 'nullable|string|max:255',
        ]);

        // Capturar cambios antes de actualizar
        $cambios = [];
        $camposTraducidos = [
            'rut' => 'RUT',
            'nombre' => 'Nombre',
            'apellido_paterno' => 'Apellido Paterno',
            'apellido_materno' => 'Apellido Materno',
            'direccion' => 'Dirección',
            'sector' => 'Sector',
            'telefono' => 'Teléfono',
            'email' => 'Email',
            'tipo_cliente' => 'Tipo de Cliente',
            'numero_medidor' => 'N° Medidor',
            'estado' => 'Estado',
            'subsidio_porcentaje' => 'Subsidio (%)',
            'descuento_monto' => 'Descuento ($)',
            'observaciones_subsidio' => 'Descripción Subsidio',
        ];

        foreach ($validated as $campo => $valorNuevo) {
            $valorAnterior = $socio->$campo;
            if ($valorAnterior != $valorNuevo) {
                $nombreCampo = $camposTraducidos[$campo] ?? $campo;
                $cambios[] = "{$nombreCampo}: '{$valorAnterior}' → '{$valorNuevo}'";
            }
        }

        $socio->update($validated);

        // Registrar actividad con cambios
        if (!empty($cambios)) {
            $descripcionCambios = implode(', ', $cambios);
            ActividadHelper::registrar(
                'Socios',
                "Socio actualizado: {$socio->numero_socio} - {$socio->nombre_completo}. Cambios: {$descripcionCambios}",
                auth()->id()
            );
        } else {
            ActividadHelper::registrar(
                'Socios',
                "Socio actualizado: {$socio->numero_socio} - {$socio->nombre_completo}",
                auth()->id()
            );
        }

        return redirect()->route('socios.show', $id)
                        ->with('success', 'Socio actualizado exitosamente');
    }

    /**
     * Eliminar socio (soft delete)
     */
    public function destroy($id)
    {
        $socio = Socio::findOrFail($id);
        $numeroSocio = $socio->numero_socio;
        $nombreCompleto = $socio->nombre_completo;

        $socio->update(['activo' => 0]);

        // Registrar actividad
        ActividadHelper::registrar(
            'Socios',
            "Socio eliminado: {$numeroSocio} - {$nombreCompleto}"
        );

        return redirect()->route('socios.index')
                        ->with('success', 'Socio eliminado exitosamente');
    }

    /**
     * Toggle exención de IVA
     */
    public function toggleExentoIva($id)
    {
        $socio = Socio::findOrFail($id);

        // Cambiar estado de exención
        $nuevoEstado = !$socio->exento_iva;
        $socio->update(['exento_iva' => $nuevoEstado]);

        // Registrar actividad
        $accion = $nuevoEstado ? 'Exento de IVA' : 'Exención de IVA removida';
        ActividadHelper::registrar(
            'Socios',
            "{$accion}: {$socio->numero_socio} - {$socio->nombre_completo}",
            auth()->id()
        );

        $mensaje = $nuevoEstado
            ? 'Socio marcado como exento de IVA'
            : 'Exención de IVA removida del socio';

        return redirect()->back()->with('success', $mensaje);
    }
}
