<?php

namespace App\Http\Controllers;

use App\Models\Boleta;
use App\Models\ConfiguracionDTE;
use App\Services\LibreDTEService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DTEController extends Controller
{
    protected $libredteService;

    public function __construct(LibreDTEService $libredteService)
    {
        $this->libredteService = $libredteService;
    }

    /**
     * Dashboard de estadísticas DTE
     */
    public function dashboard()
    {
        $idOrganizacion = auth()->user()->id_organizacion;

        // Total de DTEs emitidos
        $totalDTEsEmitidos = Boleta::where('id_organizacion', $idOrganizacion)
            ->whereNotNull('folio_sii')
            ->count();

        // DTEs por estado
        $dtesPorEstado = Boleta::where('id_organizacion', $idOrganizacion)
            ->whereNotNull('folio_sii')
            ->select('estado_dte', DB::raw('count(*) as total'))
            ->groupBy('estado_dte')
            ->get()
            ->pluck('total', 'estado_dte');

        // DTEs emitidos por mes (últimos 12 meses)
        $dtesPorMes = Boleta::where('id_organizacion', $idOrganizacion)
            ->whereNotNull('folio_sii')
            ->where('fecha_emision_dte', '>=', now()->subMonths(12))
            ->select(
                DB::raw('DATE_FORMAT(fecha_emision_dte, "%Y-%m") as mes'),
                DB::raw('count(*) as total')
            )
            ->groupBy(DB::raw('DATE_FORMAT(fecha_emision_dte, "%Y-%m")'))
            ->orderBy('mes', 'asc')
            ->get();

        // Monto total facturado electrónicamente
        $montoTotalFacturado = Boleta::where('id_organizacion', $idOrganizacion)
            ->whereNotNull('folio_sii')
            ->sum('total');

        // Últimos 10 DTEs emitidos
        $ultimosDTEs = Boleta::where('id_organizacion', $idOrganizacion)
            ->whereNotNull('folio_sii')
            ->with('socio')
            ->orderBy('fecha_emision_dte', 'desc')
            ->limit(10)
            ->get();

        // Boletas pendientes de emitir DTE (sin folio_sii)
        $totalBoletasPendientes = Boleta::where('id_organizacion', $idOrganizacion)
            ->whereNull('folio_sii')
            ->count();

        // Últimas 20 boletas SIN DTE para emisión masiva
        $boletasSinDTE = Boleta::where('id_organizacion', $idOrganizacion)
            ->whereNull('folio_sii')
            ->with('socio')
            ->orderBy('fecha_emision', 'desc')
            ->limit(20)
            ->get();

        // Estado de conexión con LibreDTE
        $conexionLibreDTE = false;
        try {
            $this->libredteService->setOrganizacion($idOrganizacion);
            $conexionLibreDTE = $this->libredteService->verificarConexion();
        } catch (\Exception $e) {
            // Conexión fallida
        }

        // Configuración DTE
        $config = ConfiguracionDTE::where('id_organizacion', $idOrganizacion)->first();

        return view('dte.dashboard', compact(
            'totalDTEsEmitidos',
            'dtesPorEstado',
            'dtesPorMes',
            'montoTotalFacturado',
            'ultimosDTEs',
            'totalBoletasPendientes',
            'boletasSinDTE',
            'conexionLibreDTE',
            'config'
        ));
    }

    /**
     * Mostrar configuración DTE de la organización
     */
    public function configuracion()
    {
        $idOrganizacion = auth()->user()->id_organizacion;
        $config = ConfiguracionDTE::where('id_organizacion', $idOrganizacion)->first();

        return view('dte.configuracion', compact('config'));
    }

    /**
     * Guardar configuración DTE
     */
    public function guardarConfiguracion(Request $request)
    {
        $validated = $request->validate([
            'rut_emisor' => 'required|string|max:12',
            'razon_social' => 'required|string|max:200',
            'giro' => 'required|string|max:200',
            'direccion_casa_matriz' => 'required|string|max:255',
            'comuna' => 'required|string|max:100',
            'ciudad' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email_contacto' => 'required|email|max:150',
            'libredte_hash' => 'required|string|max:100',
            'libredte_url' => 'nullable|url|max:255',
            'ambiente' => 'required|in:certificacion,produccion',
        ]);

        $idOrganizacion = auth()->user()->id_organizacion;

        ConfiguracionDTE::updateOrCreate(
            ['id_organizacion' => $idOrganizacion],
            array_merge($validated, [
                'libredte_url' => $validated['libredte_url'] ?? 'https://libredte.cl',
                'activo' => true,
            ])
        );

        return redirect()->route('dte.configuracion')
            ->with('success', 'Configuración DTE guardada exitosamente');
    }

    /**
     * Emitir DTE para una boleta
     */
    public function emitir($idBoleta)
    {
        try {
            $boleta = Boleta::with('socio')->findOrFail($idBoleta);

            // Verificar que pertenece a la organización del usuario
            if ($boleta->id_organizacion != auth()->user()->id_organizacion) {
                return redirect()->back()->with('error', 'Acceso denegado');
            }

            // Verificar que no tenga DTE ya emitido
            if ($boleta->dteEmitido()) {
                return redirect()->back()->with('error', 'La boleta ya tiene DTE emitido');
            }

            // Configurar servicio
            $this->libredteService->setOrganizacion($boleta->id_organizacion);

            // Emitir boleta
            $response = $this->libredteService->emitirBoleta($boleta);

            // Enviar email automático con PDF timbrado si el socio tiene email
            if ($boleta->socio && $boleta->socio->email && $boleta->pdf_url) {
                \App\Jobs\EnviarBoletaDTEEmail::dispatch($boleta->fresh());
                return redirect()->back()->with('success', "Boleta electrónica emitida exitosamente. Folio: {$response['folio']}. Email enviado a {$boleta->socio->email}");
            }

            return redirect()->back()->with('success', "Boleta electrónica emitida exitosamente. Folio: {$response['folio']}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al emitir DTE: ' . $e->getMessage());
        }
    }

    /**
     * Consultar estado de DTE en el SII
     */
    public function consultarEstado($idBoleta)
    {
        try {
            $boleta = Boleta::findOrFail($idBoleta);

            if ($boleta->id_organizacion != auth()->user()->id_organizacion) {
                return redirect()->back()->with('error', 'Acceso denegado');
            }

            $this->libredteService->setOrganizacion($boleta->id_organizacion);
            $estado = $this->libredteService->consultarEstado($boleta);

            return redirect()->back()->with('success', "Estado SII: {$estado['estado']}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al consultar estado: ' . $e->getMessage());
        }
    }

    /**
     * Anular DTE (emitir Nota de Crédito)
     */
    public function anular(Request $request, $idBoleta)
    {
        $request->validate([
            'motivo' => 'required|string|min:10|max:500',
        ], [
            'motivo.required' => 'Debe ingresar un motivo de anulación',
            'motivo.min' => 'El motivo debe tener al menos 10 caracteres',
            'motivo.max' => 'El motivo no puede exceder 500 caracteres',
        ]);

        try {
            $boleta = Boleta::findOrFail($idBoleta);

            // Verificar multi-tenancy
            if ($boleta->id_organizacion != auth()->user()->id_organizacion) {
                return redirect()->back()->with('error', 'Acceso denegado');
            }

            // Verificar que tenga DTE emitido
            if (!$boleta->dteEmitido()) {
                return redirect()->back()->with('error', 'La boleta no tiene DTE emitido para anular');
            }

            // Verificar que no esté ya anulada
            if ($boleta->estado_dte === 'anulada') {
                return redirect()->back()->with('error', 'El DTE ya está anulado');
            }

            // Verificar que el estado sea válido para anular (emitida o aceptada)
            if (!in_array($boleta->estado_dte, ['emitida', 'aceptada'])) {
                return redirect()->back()->with('error', 'Solo se pueden anular DTEs emitidos o aceptados por el SII');
            }

            // Emitir Nota de Crédito
            $this->libredteService->setOrganizacion($boleta->id_organizacion);
            $response = $this->libredteService->anularDocumento($boleta, $request->motivo);

            return redirect()->back()->with('success', "DTE anulado exitosamente. Nota de Crédito Folio: {$response['folio']}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al anular DTE: ' . $e->getMessage());
        }
    }

    /**
     * Descargar PDF del DTE
     */
    public function descargarPDF($idBoleta)
    {
        $boleta = Boleta::findOrFail($idBoleta);

        if ($boleta->id_organizacion != auth()->user()->id_organizacion) {
            abort(403);
        }

        // Prioridad 1: Descargar desde almacenamiento local si existe
        if ($boleta->pdf_local_path && Storage::exists($boleta->pdf_local_path)) {
            $nombreArchivo = "DTE_{$boleta->tipo_dte}_F{$boleta->folio_sii}_B{$boleta->numero_boleta}.pdf";
            return Storage::download($boleta->pdf_local_path, $nombreArchivo);
        }

        // Prioridad 2: Redirigir a LibreDTE si no hay copia local
        if ($boleta->pdf_url) {
            return redirect($boleta->pdf_url);
        }

        return redirect()->back()->with('error', 'No hay PDF disponible para esta boleta');
    }

    /**
     * Verificar conexión con LibreDTE
     */
    public function verificarConexion()
    {
        try {
            $idOrganizacion = auth()->user()->id_organizacion;
            $this->libredteService->setOrganizacion($idOrganizacion);

            $conectado = $this->libredteService->verificarConexion();

            if ($conectado) {
                return response()->json(['status' => 'success', 'message' => 'Conexión exitosa con LibreDTE']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'No se pudo conectar con LibreDTE'], 500);
            }

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Gestión de Folios DTE
     */
    public function folios()
    {
        $idOrganizacion = auth()->user()->id_organizacion;

        // Obtener folios disponibles desde LibreDTE
        $foliosData = null;
        $errorConexion = false;

        try {
            $this->libredteService->setOrganizacion($idOrganizacion);
            $foliosData = $this->libredteService->obtenerFoliosDisponibles();

            if (isset($foliosData['error'])) {
                $errorConexion = true;
            }
        } catch (\Exception $e) {
            $errorConexion = true;
            $foliosData = [
                'tipo_dte' => 39,
                'disponibles' => null,
                'siguiente' => null,
                'alerta' => false,
                'error' => $e->getMessage(),
            ];
        }

        // Obtener historial de uso de folios (últimos 30 DTEs emitidos)
        $historialFolios = Boleta::where('id_organizacion', $idOrganizacion)
            ->whereNotNull('folio_sii')
            ->with('socio')
            ->orderBy('fecha_emision_dte', 'desc')
            ->limit(30)
            ->get();

        // Estadísticas de uso
        $totalFoliosUsados = Boleta::where('id_organizacion', $idOrganizacion)
            ->whereNotNull('folio_sii')
            ->count();

        $foliosUsadosEsteMes = Boleta::where('id_organizacion', $idOrganizacion)
            ->whereNotNull('folio_sii')
            ->whereYear('fecha_emision_dte', now()->year)
            ->whereMonth('fecha_emision_dte', now()->month)
            ->count();

        $ultimoFolioEmitido = Boleta::where('id_organizacion', $idOrganizacion)
            ->whereNotNull('folio_sii')
            ->orderBy('folio_sii', 'desc')
            ->first();

        return view('dte.folios', compact(
            'foliosData',
            'errorConexion',
            'historialFolios',
            'totalFoliosUsados',
            'foliosUsadosEsteMes',
            'ultimoFolioEmitido'
        ));
    }

    /**
     * Emisión masiva de DTEs
     */
    public function emitirMasivo(Request $request)
    {
        $request->validate([
            'boleta_ids' => 'required|array|min:1',
            'boleta_ids.*' => 'required|integer|exists:boletas,id',
        ]);

        $idOrganizacion = auth()->user()->id_organizacion;
        $boletaIds = $request->input('boleta_ids');

        // Verificar que todas las boletas pertenecen a la organización
        $boletas = Boleta::whereIn('id', $boletaIds)
            ->where('id_organizacion', $idOrganizacion)
            ->get();

        if ($boletas->count() !== count($boletaIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Algunas boletas no pertenecen a su organización',
            ], 403);
        }

        // Verificar configuración DTE
        try {
            $this->libredteService->setOrganizacion($idOrganizacion);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de configuración: ' . $e->getMessage(),
            ], 500);
        }

        $exitosos = 0;
        $errores = 0;
        $resultados = [];

        foreach ($boletas as $boleta) {
            // Verificar si ya tiene DTE
            if ($boleta->tieneDTE()) {
                $errores++;
                $resultados[] = [
                    'boleta_id' => $boleta->id,
                    'numero_boleta' => $boleta->numero_boleta,
                    'success' => false,
                    'message' => 'Ya tiene DTE emitido',
                ];
                continue;
            }

            try {
                $resultado = $this->libredteService->emitirBoleta($boleta);

                if ($resultado['success']) {
                    $exitosos++;
                    $resultados[] = [
                        'boleta_id' => $boleta->id,
                        'numero_boleta' => $boleta->numero_boleta,
                        'success' => true,
                        'folio' => $resultado['folio'] ?? null,
                    ];
                } else {
                    $errores++;
                    $resultados[] = [
                        'boleta_id' => $boleta->id,
                        'numero_boleta' => $boleta->numero_boleta,
                        'success' => false,
                        'message' => $resultado['error'] ?? 'Error desconocido',
                    ];
                }

                // Delay de 1 segundo entre emisiones para no sobrecargar la API
                sleep(1);

            } catch (\Exception $e) {
                $errores++;
                $resultados[] = [
                    'boleta_id' => $boleta->id,
                    'numero_boleta' => $boleta->numero_boleta,
                    'success' => false,
                    'message' => $e->getMessage(),
                ];

                Log::error('Error al emitir DTE masivamente', [
                    'boleta_id' => $boleta->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Emisión masiva de DTEs completada', [
            'total' => count($boletaIds),
            'exitosos' => $exitosos,
            'errores' => $errores,
        ]);

        return response()->json([
            'success' => true,
            'total' => count($boletaIds),
            'exitosos' => $exitosos,
            'errores' => $errores,
            'message' => "Proceso completado: {$exitosos} éxitos, {$errores} errores",
            'resultados' => $resultados,
        ]);
    }

    /**
     * Libro de Ventas Electrónico (IECV)
     */
    public function libroVentas(Request $request)
    {
        $idOrganizacion = auth()->user()->id_organizacion;

        // Obtener filtros de fecha (por defecto: mes actual)
        $mesInicio = $request->input('mes_inicio', now()->startOfMonth()->format('Y-m-d'));
        $mesFin = $request->input('mes_fin', now()->endOfMonth()->format('Y-m-d'));

        // DTEs del período seleccionado
        $dtes = Boleta::where('id_organizacion', $idOrganizacion)
            ->whereNotNull('folio_sii')
            ->whereBetween('fecha_emision_dte', [$mesInicio, $mesFin])
            ->with('socio')
            ->orderBy('fecha_emision_dte', 'asc')
            ->get();

        // Estadísticas del período
        $totalDTEs = $dtes->count();
        $montoNeto = $dtes->sum(function($dte) {
            // Calcular neto (total / 1.19 para documentos con IVA)
            return round($dte->total / 1.19);
        });
        $montoIVA = $dtes->sum(function($dte) {
            return round($dte->total - ($dte->total / 1.19));
        });
        $montoTotal = $dtes->sum('total');

        // Agrupar por tipo de DTE
        $dtesPorTipo = $dtes->groupBy('tipo_dte')->map(function($grupo) {
            return [
                'cantidad' => $grupo->count(),
                'monto' => $grupo->sum('total'),
            ];
        });

        $config = ConfiguracionDTE::where('id_organizacion', $idOrganizacion)->first();

        return view('dte.libro-ventas', compact(
            'dtes',
            'totalDTEs',
            'montoNeto',
            'montoIVA',
            'montoTotal',
            'dtesPorTipo',
            'mesInicio',
            'mesFin',
            'config'
        ));
    }

    /**
     * Descargar Libro de Ventas en formato IECV (CSV para SII)
     */
    public function descargarLibroVentas(Request $request)
    {
        $idOrganizacion = auth()->user()->id_organizacion;
        $config = ConfiguracionDTE::where('id_organizacion', $idOrganizacion)->first();

        if (!$config) {
            return redirect()->back()->with('error', 'No hay configuración DTE');
        }

        $mesInicio = $request->input('mes_inicio');
        $mesFin = $request->input('mes_fin');

        $dtes = Boleta::where('id_organizacion', $idOrganizacion)
            ->whereNotNull('folio_sii')
            ->whereBetween('fecha_emision_dte', [$mesInicio, $mesFin])
            ->with('socio')
            ->orderBy('fecha_emision_dte', 'asc')
            ->get();

        // Generar archivo CSV en formato IECV del SII
        $csv = $this->generarIECV($dtes, $config);

        $filename = 'libro_ventas_' . str_replace('-', '', $mesInicio) . '_' . str_replace('-', '', $mesFin) . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=ISO-8859-1')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Generar archivo IECV en formato CSV
     */
    protected function generarIECV($dtes, $config)
    {
        $lineas = [];

        // Encabezado CSV (formato SII)
        $lineas[] = implode(';', [
            'Tipo Doc',
            'Folio',
            'Fecha Emision',
            'RUT Receptor',
            'Razon Social',
            'Monto Exento',
            'Monto Neto',
            'Monto IVA',
            'Monto Total',
            'IVA No Recuperable',
            'IVA Uso Comun',
        ]);

        foreach ($dtes as $dte) {
            $montoNeto = round($dte->total / 1.19);
            $montoIVA = round($dte->total - $montoNeto);

            $lineas[] = implode(';', [
                $dte->tipo_dte ?? 39,                           // Tipo Doc
                $dte->folio_sii,                                 // Folio
                $dte->fecha_emision_dte->format('d/m/Y'),       // Fecha
                $dte->socio->rut ?? '66666666-6',               // RUT (o genérico)
                $this->limpiarTexto($dte->socio->nombre_completo ?? 'Cliente Final'), // Razón Social
                0,                                               // Monto Exento
                $montoNeto,                                      // Monto Neto
                $montoIVA,                                       // Monto IVA
                $dte->total,                                     // Monto Total
                0,                                               // IVA No Recuperable
                0,                                               // IVA Uso Común
            ]);
        }

        // Convertir a ISO-8859-1 para compatibilidad con SII
        return mb_convert_encoding(implode("\r\n", $lineas), 'ISO-8859-1', 'UTF-8');
    }

    /**
     * Limpiar texto para CSV (eliminar caracteres especiales)
     */
    protected function limpiarTexto($texto)
    {
        $texto = str_replace([';', "\n", "\r", '"'], ' ', $texto);
        return trim($texto);
    }
}
