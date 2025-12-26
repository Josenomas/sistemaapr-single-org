<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boleta;
use App\Models\Socio;
use App\Models\Lectura;
use App\Models\ConfiguracionTarifa;
use App\Helpers\ActividadHelper;
use Illuminate\Support\Facades\DB;

class BoletasController extends Controller
{
    /**
     * Listar todas las boletas
     */
    public function index(Request $request)
    {
        $query = Boleta::activos()->with('socio');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_boleta', 'like', "%{$search}%")
                  ->orWhereHas('socio', function($sq) use ($search) {
                      $sq->where('nombre', 'like', "%{$search}%")
                        ->orWhere('apellido_paterno', 'like', "%{$search}%")
                        ->orWhere('numero_socio', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('mes')) {
            $query->porMes($request->mes);
        }

        if ($request->filled('estado')) {
            $query->porEstado($request->estado);
        }

        if ($request->filled('id_socio')) {
            $query->where('id_socio', $request->id_socio);
        }

        // Actualizar boletas vencidas
        Boleta::activos()
            ->pendientes()
            ->where('fecha_vencimiento', '<', today())
            ->update(['estado' => 'vencida']);

        $boletas = $query->orderBy('fecha_emision', 'desc')->paginate(15);

        // Estadísticas
        $estadisticas = [
            'total_boletas' => Boleta::activos()->count(),
            'pendientes' => Boleta::activos()->pendientes()->count(),
            'vencidas' => Boleta::activos()->vencidas()->count(),
            'pagadas' => Boleta::activos()->pagadas()->count(),
            'total_pendiente' => Boleta::activos()->pendientes()->sum('total'),
            'total_mes_actual' => Boleta::activos()->porMes(date('Y-m'))->sum('total')
        ];

        $socios = Socio::activos()
                      ->where('estado', 'activo')
                      ->orderBy('numero_socio')
                      ->get();

        return view('boletas.index', compact('boletas', 'socios', 'estadisticas'));
    }

    /**
     * Crear boleta manualmente
     */
    public function create()
    {
        $socios = Socio::activos()
                      ->where('estado', 'activo')
                      ->orderBy('numero_socio')
                      ->get();

        return view('boletas.create', compact('socios'));
    }

    /**
     * Guardar nueva boleta
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_socio' => 'required|exists:socios,id',
            'mes' => 'required|string|size:7',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_emision',
            'consumo_m3' => 'required|numeric|min:0',
            'cargo_fijo' => 'required|numeric|min:0',
            'cargo_consumo' => 'required|numeric|min:0',
            'otros_cargos' => 'nullable|numeric|min:0',
            'descuentos' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string'
        ]);

        try {
            // Generar número de boleta
            $validated['numero_boleta'] = Boleta::generarNumeroBoleta();
            $validated['activo'] = 1;
            $validated['estado'] = 'pendiente';
            $validated['otros_cargos'] = $validated['otros_cargos'] ?? 0;
            $validated['descuentos'] = $validated['descuentos'] ?? 0;

            // Calcular total
            $validated['total'] = ($validated['cargo_fijo'] + $validated['cargo_consumo'] + $validated['otros_cargos']) - $validated['descuentos'];

            $boleta = Boleta::create($validated);

            // Registrar actividad
            $socio = Socio::find($validated['id_socio']);
            $detalles = [
                'Número: ' . $boleta->numero_boleta,
                'Socio: ' . $socio->nombre_completo,
                'Mes: ' . $boleta->mes_texto,
                'Total: ' . $boleta->total_formateado
            ];

            ActividadHelper::registrar(
                'Boletas',
                'Nueva boleta creada: ' . implode(' | ', $detalles),
                auth()->id()
            );

            return redirect()->route('boletas.show', $boleta->id)
                           ->with('success', 'Boleta creada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al crear la boleta: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle de boleta
     */
    public function show($id)
    {
        $boleta = Boleta::activos()->with(['socio', 'lectura', 'pagos'])->findOrFail($id);
        return view('boletas.show', compact('boleta'));
    }

    /**
     * Editar boleta
     */
    public function edit($id)
    {
        $boleta = Boleta::activos()->findOrFail($id);

        // No permitir editar boletas pagadas
        if ($boleta->estado === 'pagada') {
            return redirect()->route('boletas.show', $id)
                           ->with('error', 'No se puede editar una boleta pagada.');
        }

        $socios = Socio::activos()
                      ->where('estado', 'activo')
                      ->orderBy('numero_socio')
                      ->get();

        return view('boletas.edit', compact('boleta', 'socios'));
    }

    /**
     * Actualizar boleta
     */
    public function update(Request $request, $id)
    {
        $boleta = Boleta::activos()->findOrFail($id);

        // No permitir actualizar boletas pagadas
        if ($boleta->estado === 'pagada') {
            return redirect()->route('boletas.show', $id)
                           ->with('error', 'No se puede actualizar una boleta pagada.');
        }

        $validated = $request->validate([
            'id_socio' => 'required|exists:socios,id',
            'mes' => 'required|string|size:7',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_emision',
            'consumo_m3' => 'required|numeric|min:0',
            'cargo_fijo' => 'required|numeric|min:0',
            'cargo_consumo' => 'required|numeric|min:0',
            'otros_cargos' => 'nullable|numeric|min:0',
            'descuentos' => 'nullable|numeric|min:0',
            'estado' => 'required|in:pendiente,vencida,anulada',
            'observaciones' => 'nullable|string'
        ]);

        try {
            // Tracking de cambios
            $cambios = [];

            if ($boleta->id_socio != $validated['id_socio']) {
                $socioAnterior = $boleta->socio->nombre_completo;
                $socioNuevo = Socio::find($validated['id_socio'])->nombre_completo;
                $cambios[] = "Socio: '{$socioAnterior}' → '{$socioNuevo}'";
            }

            if ($boleta->mes != $validated['mes']) {
                $cambios[] = "Mes: '{$boleta->mes_texto}' → '" . $this->getMesTexto($validated['mes']) . "'";
            }

            if ($boleta->consumo_m3 != $validated['consumo_m3']) {
                $cambios[] = "Consumo: '{$boleta->consumo_m3} m³' → '{$validated['consumo_m3']} m³'";
            }

            $validated['otros_cargos'] = $validated['otros_cargos'] ?? 0;
            $validated['descuentos'] = $validated['descuentos'] ?? 0;

            // Calcular total
            $totalNuevo = ($validated['cargo_fijo'] + $validated['cargo_consumo'] + $validated['otros_cargos']) - $validated['descuentos'];

            if ($boleta->total != $totalNuevo) {
                $cambios[] = "Total: '{$boleta->total_formateado}' → '$" . number_format($totalNuevo, 0, ',', '.') . "'";
            }

            $validated['total'] = $totalNuevo;

            if ($boleta->estado != $validated['estado']) {
                $cambios[] = "Estado: '{$boleta->estado_texto}' → '" . ucfirst($validated['estado']) . "'";
            }

            $boleta->update($validated);

            if (!empty($cambios)) {
                ActividadHelper::registrar(
                    'Boletas',
                    "Boleta actualizada [{$boleta->numero_boleta}]: " . implode(' | ', $cambios),
                    auth()->id()
                );
            }

            return redirect()->route('boletas.show', $boleta->id)
                           ->with('success', 'Boleta actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar la boleta: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar boleta (soft delete)
     */
    public function destroy($id)
    {
        $boleta = Boleta::activos()->findOrFail($id);

        // Verificar que no tenga pagos
        if ($boleta->pagos()->count() > 0) {
            return redirect()->route('boletas.show', $id)
                           ->with('error', 'No se puede eliminar una boleta con pagos registrados.');
        }

        try {
            $numeroBoleta = $boleta->numero_boleta;
            $socio = $boleta->socio->nombre_completo;

            $boleta->activo = 0;
            $boleta->save();

            ActividadHelper::registrar(
                'Boletas',
                "Boleta eliminada: {$numeroBoleta} - Socio: {$socio}",
                auth()->id()
            );

            return redirect()->route('boletas.index')
                           ->with('success', 'Boleta eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error al eliminar la boleta: ' . $e->getMessage());
        }
    }

    /**
     * Generar boletas del mes
     */
    public function generar()
    {
        $mesActual = date('Y-m');

        // Verificar si ya existen boletas para TODOS los socios activos
        $sociosActivos = Socio::where('activo', 1)->count();
        $boletasExistentes = Boleta::activos()->where('mes', $mesActual)->count();

        if ($boletasExistentes >= $sociosActivos) {
            return redirect()->route('boletas.index')
                           ->with('error', "Ya se generaron boletas para todos los socios del mes {$mesActual}");
        }

        return view('boletas.generar', compact('mesActual'));
    }

    /**
     * Procesar generación de boletas
     */
    public function storeGenerar(Request $request)
    {
        $validated = $request->validate([
            'mes' => 'required|string|size:7',
        ]);

        $mes = $validated['mes'];

        DB::beginTransaction();
        try {
            // Llamar al procedimiento almacenado
            DB::statement('CALL sp_generar_boletas_mes(?)', [$mes]);

            $boletasGeneradas = Boleta::activos()->where('mes', $mes)->count();

            ActividadHelper::registrar(
                'Boletas',
                "Generación masiva de boletas para {$mes}: {$boletasGeneradas} boletas creadas",
                auth()->id()
            );

            DB::commit();

            return redirect()->route('boletas.index')
                           ->with('success', "Se generaron {$boletasGeneradas} boletas para el mes {$mes}");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('boletas.generar')
                           ->with('error', 'Error al generar boletas: ' . $e->getMessage());
        }
    }

    /**
     * Anular boleta
     */
    public function anular($id)
    {
        $boleta = Boleta::activos()->findOrFail($id);

        // Verificar que no tenga pagos
        if ($boleta->pagos()->count() > 0) {
            return redirect()->route('boletas.show', $id)
                           ->with('error', 'No se puede anular una boleta con pagos registrados.');
        }

        try {
            $estadoAnterior = $boleta->estado_texto;
            $boleta->update(['estado' => 'anulada']);

            ActividadHelper::registrar(
                'Boletas',
                "Boleta anulada [{$boleta->numero_boleta}]: Estado: '{$estadoAnterior}' → 'Anulada'",
                auth()->id()
            );

            return redirect()->route('boletas.index')
                            ->with('success', 'Boleta anulada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error al anular la boleta: ' . $e->getMessage());
        }
    }

    /**
     * Imprimir boleta (PDF)
     */
    public function imprimir($id)
    {
        $boleta = Boleta::activos()->with(['socio', 'lectura'])->findOrFail($id);

        // Obtener historial de consumo de los últimos 12 meses
        $historialConsumo = Boleta::activos()
            ->where('id_socio', $boleta->id_socio)
            ->where('mes', '<=', $boleta->mes)
            ->orderBy('mes', 'desc')
            ->limit(12)
            ->get()
            ->reverse()
            ->map(function($b) {
                return [
                    'mes' => $b->mes,
                    'mes_texto' => $b->mes_texto,
                    'consumo' => $b->consumo_m3
                ];
            });

        // Obtener último pago realizado
        $ultimoPago = DB::table('pagos')
            ->where('id_socio', $boleta->id_socio)
            ->orderBy('fecha_pago', 'desc')
            ->first();

        // Obtener boletas pendientes/vencidas (deuda)
        $boletasPendientes = Boleta::activos()
            ->where('id_socio', $boleta->id_socio)
            ->whereIn('estado', ['pendiente', 'vencida'])
            ->orderBy('mes', 'asc')
            ->get();

        $totalAdeudado = $boletasPendientes->sum('total');
        $mesesAdeudados = $boletasPendientes->count();

        // Generar PDF
        $pdf = \PDF::loadView('boletas.pdf', compact('boleta', 'historialConsumo', 'ultimoPago', 'boletasPendientes', 'totalAdeudado', 'mesesAdeudados'));

        // Configurar orientación y tamaño
        $pdf->setPaper('letter', 'portrait');

        // Registrar actividad
        ActividadHelper::registrar(
            'Boletas',
            "Boleta impresa/descargada [{$boleta->numero_boleta}] - Socio: {$boleta->socio->nombre_completo}",
            auth()->id()
        );

        // Retornar PDF para descarga
        return $pdf->download('Boleta-' . $boleta->numero_boleta . '.pdf');
    }

    /**
     * Boletas vencidas
     */
    public function vencidas()
    {
        $boletas = Boleta::activos()
                        ->with('socio')
                        ->vencidas()
                        ->orderBy('fecha_vencimiento')
                        ->paginate(20);

        return view('boletas.vencidas', compact('boletas'));
    }

    /**
     * Enviar recordatorio de pago
     */
    public function enviarRecordatorio($id)
    {
        $boleta = Boleta::activos()->with('socio')->findOrFail($id);

        // Aquí implementarías el envío de email/SMS
        // Por ahora solo registramos la acción

        ActividadHelper::registrar(
            'Boletas',
            "Recordatorio de pago enviado [{$boleta->numero_boleta}] - Socio: {$boleta->socio->nombre_completo}",
            auth()->id()
        );

        return redirect()->route('boletas.show', $id)
                        ->with('success', 'Recordatorio enviado exitosamente.');
    }

    /**
     * Enviar boleta por email
     */
    public function enviarEmail($id)
    {
        DB::beginTransaction();
        try {
            $boleta = Boleta::activos()->with('socio')->findOrFail($id);

            // Verificar que el socio tenga email
            if (!$boleta->socio || !$boleta->socio->email) {
                return redirect()->back()
                    ->with('error', 'El socio no tiene un email registrado.');
            }

            // Despachar el job para enviar el email
            \App\Jobs\EnviarBoletaEmail::dispatch($boleta);

            // Registrar actividad
            ActividadHelper::registrar(
                'Boletas',
                "Boleta enviada por email [{$boleta->numero_boleta}] - Socio: {$boleta->socio->nombre_completo} - Email: {$boleta->socio->email}",
                auth()->id()
            );

            DB::commit();

            return redirect()->back()
                ->with('success', 'Boleta enviada por correo electrónico a ' . $boleta->socio->email);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al enviar la boleta: ' . $e->getMessage());
        }
    }

    /**
     * Helper para obtener texto del mes
     */
    private function getMesTexto($mes)
    {
        [$anio, $mesNum] = explode('-', $mes);
        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];

        return $meses[$mesNum] . ' ' . $anio;
    }

    /**
     * Calcular boleta usando sistema de tramos por tipo de cliente
     *
     * @param Socio $socio
     * @param float $consumo
     * @param string $mes (formato: YYYY-MM)
     * @return array
     */
    private function calcularBoletaPorTramos(Socio $socio, $consumo, $mes)
    {
        // Calcular fecha de emisión (último día del mes)
        $fechaEmision = date('Y-m-d', strtotime("last day of $mes"));

        // Obtener tipo de cliente del socio
        $tipoCliente = $socio->tipo_cliente ?? 'residencial';

        // Calcular usando el modelo de ConfiguracionTarifa
        $calculo = ConfiguracionTarifa::calcularMontoPorConsumo($tipoCliente, $consumo, $fechaEmision);

        if (isset($calculo['error'])) {
            throw new \Exception($calculo['error']);
        }

        return [
            'cargo_fijo' => $calculo['cargo_fijo'],
            'cargo_consumo' => $calculo['cargo_consumo'],
            'monto_base' => $calculo['monto_base'],
            'iva_porcentaje' => $calculo['iva_porcentaje'],
            'monto_iva' => $calculo['iva'],
            'total' => $calculo['total'],
            'tramo' => $calculo['tramo'],
            'tipo_cliente' => $tipoCliente
        ];
    }

    /**
     * AJAX: Calcular montos automáticamente cuando se ingresa consumo
     */
    public function calcularMontos(Request $request)
    {
        $validated = $request->validate([
            'id_socio' => 'required|exists:socios,id',
            'consumo_m3' => 'required|numeric|min:0',
            'mes' => 'required|string|size:7',
        ]);

        try {
            $socio = Socio::findOrFail($validated['id_socio']);
            $resultado = $this->calcularBoletaPorTramos(
                $socio,
                $validated['consumo_m3'],
                $validated['mes']
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'tipo_cliente' => ucfirst($resultado['tipo_cliente']),
                    'tramo' => $resultado['tramo']->nombre,
                    'nombre_tarifa' => $resultado['tramo']->nombre_tarifa,
                    'rango' => $resultado['tramo']->rango_descripcion,
                    'cargo_fijo' => $resultado['cargo_fijo'],
                    'cargo_fijo_formateado' => '$' . number_format($resultado['cargo_fijo'], 0, ',', '.'),
                    'cargo_consumo' => $resultado['cargo_consumo'],
                    'cargo_consumo_formateado' => '$' . number_format($resultado['cargo_consumo'], 0, ',', '.'),
                    'monto_base' => $resultado['monto_base'],
                    'monto_base_formateado' => '$' . number_format($resultado['monto_base'], 0, ',', '.'),
                    'iva_porcentaje' => $resultado['iva_porcentaje'],
                    'monto_iva' => $resultado['monto_iva'],
                    'monto_iva_formateado' => '$' . number_format($resultado['monto_iva'], 0, ',', '.'),
                    'total' => $resultado['total'],
                    'total_formateado' => '$' . number_format($resultado['total'], 0, ',', '.'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular montos: ' . $e->getMessage()
            ], 400);
        }
    }
}
