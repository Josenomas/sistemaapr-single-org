<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boleta;
use App\Models\Socio;
use App\Models\Lectura;
use App\Models\ConfiguracionTarifa;
use App\Models\Auditoria;
use App\Helpers\ActividadHelper;
use Illuminate\Support\Facades\DB;
use App\Services\PdfExportService;
use App\Services\ExcelExportService;

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
        ], [
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la fecha de emisión.'
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

            // Intentar asignar folio SII si hay disponibles
            $folioAsignado = $boleta->asignarFolioSII('boleta');
            if ($folioAsignado) {
                $boleta->save();
            }

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

            // Registrar en auditoría
            Auditoria::registrar(
                'boletas',
                'crear',
                "Generó boleta #{$boleta->numero_boleta} para {$socio->nombre_completo} - Mes: {$boleta->mes_texto} - Total: {$boleta->total_formateado}",
                'boletas',
                $boleta->id,
                null,
                $boleta->toArray()
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
        ], [
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la fecha de emisión.'
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

        // Verificar si ya existen boletas para TODOS los socios activos (excluyendo desconectados)
        $sociosActivos = Socio::where('activo', 1)
                              ->where('estado', '!=', 'desconectado')
                              ->count();
        $boletasExistentes = Boleta::activos()->where('mes', $mesActual)->count();

        // DEBUG: Ver los números
        \Log::info('Generación de boletas', [
            'mes' => $mesActual,
            'socios_activos' => $sociosActivos,
            'boletas_existentes' => $boletasExistentes,
            'puede_generar' => ($boletasExistentes < $sociosActivos)
        ]);

        if ($boletasExistentes >= $sociosActivos) {
            return redirect()->route('boletas.index')
                           ->with('error', "Ya se generaron boletas para todos los socios del mes {$mesActual} (Socios: {$sociosActivos}, Boletas: {$boletasExistentes})");
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

        // VALIDACIÓN PREVIA: Verificar lecturas faltantes
        $totalSocios = Socio::where('activo', 1)
                            ->where('estado', '!=', 'desconectado')
                            ->count();

        $totalLecturas = Lectura::whereHas('socio', function($q) {
                                    $q->where('activo', 1)
                                      ->where('estado', '!=', 'desconectado');
                                })
                                ->where('mes', $mes)
                                ->distinct('id_socio')
                                ->count('id_socio');

        \Log::info('Validación lecturas', [
            'mes' => $mes,
            'total_socios' => $totalSocios,
            'total_lecturas' => $totalLecturas
        ]);

        if ($totalLecturas < $totalSocios) {
            $sociosSinLectura = Socio::leftJoin('lecturas', function($join) use ($mes) {
                                        $join->on('socios.id', '=', 'lecturas.id_socio')
                                             ->where('lecturas.mes', '=', $mes);
                                    })
                                    ->whereNull('lecturas.id')
                                    ->where('socios.activo', 1)
                                    ->where('socios.estado', '!=', 'desconectado')
                                    ->limit(10)
                                    ->get(['socios.numero_socio', 'socios.nombre', 'socios.apellido_paterno'])
                                    ->map(function($s) {
                                        return $s->numero_socio . ' - ' . $s->nombre . ' ' . $s->apellido_paterno;
                                    })
                                    ->implode(', ');

            $faltantes = $totalSocios - $totalLecturas;
            return redirect()->route('boletas.generar')
                           ->with('warning', "⚠️ FALTAN LECTURAS: {$faltantes} socios sin lectura de {$mes}. Primeros 10: {$sociosSinLectura}");
        }

        DB::beginTransaction();
        try {
            // Llamar al procedimiento almacenado
            DB::statement('CALL sp_generar_boletas_mes(?)', [$mes]);

            // Obtener boletas recién generadas
            $boletasGeneradas = Boleta::activos()
                                      ->where('mes', $mes)
                                      ->whereNull('folio_sii') // Solo las que no tienen folio
                                      ->get();

            $totalGeneradas = $boletasGeneradas->count();
            $foliosAsignados = 0;

            // Intentar asignar folios SII a cada boleta generada
            foreach ($boletasGeneradas as $boleta) {
                $folioAsignado = $boleta->asignarFolioSII('boleta');
                if ($folioAsignado) {
                    $boleta->save();
                    $foliosAsignados++;
                }
            }

            ActividadHelper::registrar(
                'Boletas',
                "Generación masiva de boletas para {$mes}: {$totalGeneradas} boletas creadas" .
                ($foliosAsignados > 0 ? " | {$foliosAsignados} folios SII asignados" : ""),
                auth()->id()
            );

            DB::commit();

            $mensaje = "Se generaron {$totalGeneradas} boletas para el mes {$mes}";
            if ($foliosAsignados > 0) {
                $mensaje .= " ({$foliosAsignados} con folio SII asignado)";
            }

            return redirect()->route('boletas.index')
                           ->with('success', $mensaje);
        } catch (\Exception $e) {
            DB::rollBack();

            // Capturar error de lecturas faltantes
            $errorMsg = $e->getMessage();
            if (strpos($errorMsg, 'FALTAN LECTURAS:') !== false) {
                return redirect()->route('boletas.generar')
                               ->with('warning', $errorMsg);
            }

            return redirect()->route('boletas.generar')
                           ->with('error', 'Error al generar boletas: ' . $errorMsg);
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
            $datosAnteriores = $boleta->toArray();

            $boleta->update(['estado' => 'anulada']);

            ActividadHelper::registrar(
                'Boletas',
                "Boleta anulada [{$boleta->numero_boleta}]: Estado: '{$estadoAnterior}' → 'Anulada'",
                auth()->id()
            );

            // Registrar en auditoría
            Auditoria::registrar(
                'boletas',
                'anular',
                "Anuló boleta #{$boleta->numero_boleta} - Socio: {$boleta->socio->nombre_completo}",
                'boletas',
                $boleta->id,
                $datosAnteriores,
                ['estado' => 'anulada']
            );

            return redirect()->route('boletas.index')
                            ->with('success', 'Boleta anulada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error al anular la boleta: ' . $e->getMessage());
        }
    }

    /**
     * Imprimir boleta (PDF) - NUEVA VERSION CON WKHTMLTOPDF
     */
    public function imprimirV2($id)
    {
        // LOG DE DEBUG - CONFIRMAR QUE ESTE METODO SE EJECUTA
        \Log::info('===== EJECUTANDO imprimirV2() - NUEVO CODIGO =====', ['boleta_id' => $id]);

        $boleta = Boleta::activos()->with(['socio.organizacion', 'lectura'])->findOrFail($id);

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
            ->with('pagos')
            ->orderBy('mes', 'asc')
            ->get();

        // Calcular total adeudado considerando pagos parciales
        $totalAdeudado = 0;
        foreach ($boletasPendientes as $boletaPendiente) {
            $totalPagado = $boletaPendiente->pagos->sum('monto_pagado');
            $saldoPendiente = $boletaPendiente->total - $totalPagado;
            $totalAdeudado += $saldoPendiente;
        }
        $mesesAdeudados = $boletasPendientes->count();

        // Generar PDF con wkhtmltopdf
        $html = view('boletas.pdf_new', compact('boleta', 'historialConsumo', 'ultimoPago', 'boletasPendientes', 'totalAdeudado', 'mesesAdeudados'))->render();

        // Guardar HTML temporal
        $tempHtmlPath = public_path('temp_boleta_' . $boleta->id . '.html');
        file_put_contents($tempHtmlPath, $html);

        // Generar PDF
        $pdfPath = storage_path('app/temp_boleta_' . $boleta->id . '.pdf');
        $wkhtmltopdfPath = '"C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe"';

        // Convertir path a formato file:// con forward slashes
        $fileUrl = 'file:///' . str_replace('\\', '/', $tempHtmlPath);

        $command = $wkhtmltopdfPath . ' --enable-local-file-access --page-size Letter "' . $fileUrl . '" "' . $pdfPath . '" 2>&1';
        exec($command, $output, $returnCode);

        // Leer el PDF generado
        if (!file_exists($pdfPath)) {
            throw new \Exception('Error al generar PDF: ' . implode("\n", $output));
        }

        $pdfContent = file_get_contents($pdfPath);

        // Eliminar archivos temporales
        @unlink($tempHtmlPath);
        @unlink($pdfPath);

        // Registrar actividad
        ActividadHelper::registrar(
            'Boletas',
            "Boleta impresa/descargada [{$boleta->numero_boleta}] - Socio: {$boleta->socio->nombre_completo}",
            auth()->id()
        );

        // Retornar PDF para descarga SIN CACHÉ
        $nombreArchivo = 'Boleta-' . $boleta->numero_boleta . '-' . time() . '.pdf';
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Boletas vencidas
     */
    public function vencidas(Request $request)
    {
        // Obtener todas las boletas vencidas con sus socios
        $query = Boleta::activos()
                        ->with('socio')
                        ->vencidas();

        // Filtro por socio
        if ($request->filled('id_socio')) {
            $query->where('id_socio', $request->id_socio);
        }

        $boletas = $query->orderBy('fecha_vencimiento')->get();

        // Calcular resumen por socio
        $resumenPorSocio = $boletas->groupBy('id_socio')->map(function ($boletasSocio) {
            $socio = $boletasSocio->first()->socio;
            $cantidadBoletas = $boletasSocio->count();
            $montoTotal = $boletasSocio->sum('total');
            $diasMaxAtraso = $boletasSocio->max('dias_atraso');

            // Determinar nivel de riesgo y recomendación
            if ($cantidadBoletas >= 4) {
                $nivelRiesgo = 'critico';
                $recomendacion = 'CORTE DE SUMINISTRO INMEDIATO';
                $accionDetalle = 'Se recomienda realizar el corte de suministro por deuda acumulada superior a 4 meses.';
            } elseif ($cantidadBoletas === 3) {
                $nivelRiesgo = 'alto';
                $recomendacion = 'NOTIFICACIÓN DE CORTE DE SUMINISTRO';
                $accionDetalle = 'Enviar carta certificada notificando corte de suministro en 15 días si no regulariza.';
            } elseif ($cantidadBoletas === 2) {
                $nivelRiesgo = 'medio';
                $recomendacion = 'ENVIAR CARTA DE NO PAGO';
                $accionDetalle = 'Enviar carta formal solicitando regularización de deuda en 30 días.';
            } else {
                $nivelRiesgo = 'bajo';
                $recomendacion = 'ENVIAR RECORDATORIO';
                $accionDetalle = 'Enviar recordatorio de pago vía email o llamada telefónica.';
            }

            return [
                'socio' => $socio,
                'cantidad_boletas' => $cantidadBoletas,
                'monto_total' => $montoTotal,
                'dias_max_atraso' => $diasMaxAtraso,
                'nivel_riesgo' => $nivelRiesgo,
                'recomendacion' => $recomendacion,
                'accion_detalle' => $accionDetalle,
                'boletas' => $boletasSocio->sortByDesc('fecha_vencimiento')
            ];
        })->sortByDesc('cantidad_boletas');

        // Estadísticas generales
        $estadisticas = [
            'total_vencidas' => $boletas->count(),
            'monto_total' => $boletas->sum('total'),
            'socios_afectados' => $boletas->unique('id_socio')->count(),
            'criticos' => $resumenPorSocio->where('nivel_riesgo', 'critico')->count(),
            'alto_riesgo' => $resumenPorSocio->where('nivel_riesgo', 'alto')->count(),
            'medio_riesgo' => $resumenPorSocio->where('nivel_riesgo', 'medio')->count(),
            'bajo_riesgo' => $resumenPorSocio->where('nivel_riesgo', 'bajo')->count(),
        ];

        // Lista de socios para el filtro
        $socios = Socio::activos()
                      ->whereHas('boletas', function($q) {
                          $q->vencidas();
                      })
                      ->orderBy('nombre')
                      ->orderBy('apellido_paterno')
                      ->get();

        return view('boletas.vencidas', compact('boletas', 'resumenPorSocio', 'estadisticas', 'socios'));
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

    /**
     * Exportar boletas a PDF
     */
    public function exportarPDF(Request $request, PdfExportService $pdfService)
    {
        $organizacion = auth()->user()->organizacion;

        // Aplicar filtros si existen
        $query = Boleta::with('socio');

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }
        if ($request->has('periodo') && $request->periodo != '') {
            $query->where('periodo', $request->periodo);
        }
        if ($request->has('numero_boleta') && $request->numero_boleta != '') {
            $query->where('numero_boleta', 'like', '%' . $request->numero_boleta . '%');
        }

        $boletas = $query->orderBy('fecha_emision', 'desc')->get();
        $periodo = $request->periodo ?? 'Todos';

        return $pdfService->listadoBoletas($boletas, $organizacion, compact('periodo'))->download('boletas_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exportar boletas a Excel
     */
    public function exportarExcel(Request $request, ExcelExportService $excelService)
    {
        // Aplicar filtros si existen
        $query = Boleta::with('socio');

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }
        if ($request->has('periodo') && $request->periodo != '') {
            $query->where('periodo', $request->periodo);
        }
        if ($request->has('numero_boleta') && $request->numero_boleta != '') {
            $query->where('numero_boleta', 'like', '%' . $request->numero_boleta . '%');
        }

        $boletas = $query->orderBy('fecha_emision', 'desc')->get();

        return $excelService->exportarBoletas($boletas);
    }
}
