<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SociosController;
use App\Http\Controllers\LecturasController;
use App\Http\Controllers\BoletasController;
use App\Http\Controllers\PagosController;
use App\Http\Controllers\IncidentesController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\FuncionariosController;
use App\Http\Controllers\SueldosController;
use App\Http\Controllers\CortesSuministroController;
use App\Http\Controllers\TrabajosRealizadosController;
use App\Http\Controllers\RenovacionesMedidoresController;
use App\Http\Controllers\VacacionesController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\RecordatoriosController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\MovimientosInventarioController;
use App\Http\Controllers\GirosBancariosController;
use App\Http\Controllers\DirectivaController;
use App\Http\Controllers\HistorialConsumoController;
use App\Http\Controllers\HistorialPagosController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\ConfiguracionTarifasController;
use App\Http\Controllers\FlowController;
use App\Http\Controllers\ImportarLecturasController;
use App\Http\Controllers\ImportarInventarioController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\ConsultaPublicaController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\ActivosFijosController;
use App\Http\Controllers\RendicionMensualController;
use App\Http\Controllers\FolioSIIController;
use App\Http\Controllers\NotificacionesSistemaController;
use App\Http\Controllers\NotificacionesStreamController;
use App\Http\Controllers\PagosSuscripcionController;
use App\Http\Controllers\ActividadController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema APR (Agua Potable Rural)
|--------------------------------------------------------------------------
*/

// Landing Page en la raíz
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Conoce tu Boleta - Página informativa
Route::get('/conoce-tu-boleta', function () {
    return view('conoce-boleta');
})->name('conoce.boleta');

// Términos y Condiciones
Route::get('/terminos-y-condiciones', function () {
    return view('terminos-condiciones');
})->name('terminos.condiciones');

// Política de Privacidad
Route::get('/politicas-de-privacidad', function () {
    return view('politicas-privacidad');
})->name('politicas.privacidad');

// Libro de Reclamos SERNAC (Público - Ley 19.496)
Route::get('/libro-reclamos', [App\Http\Controllers\ReclamosController::class, 'create'])->name('reclamos.create');
Route::post('/libro-reclamos', [App\Http\Controllers\ReclamosController::class, 'store'])->name('reclamos.store');
Route::get('/reclamo/{numeroReclamo}/confirmacion', [App\Http\Controllers\ReclamosController::class, 'confirmacion'])->name('reclamos.confirmacion');

// Formulario de contacto
Route::post('/contacto', [ContactoController::class, 'enviar'])->name('contacto.enviar');

// Rutas de registro público
Route::middleware('guest')->group(function () {
    Route::get('/registro', [App\Http\Controllers\RegistroController::class, 'mostrarFormulario'])->name('registro.formulario');
    Route::post('/registro', [App\Http\Controllers\RegistroController::class, 'registrar'])->name('registro.procesar');
    Route::get('/registro/confirmacion', [App\Http\Controllers\RegistroController::class, 'confirmacion'])->name('registro.confirmacion');
});

// Ruta de verificación de email (sin middleware guest porque el usuario ya está registrando)
Route::get('/registro/verificar/{token}', [App\Http\Controllers\RegistroController::class, 'verificarEmail'])->name('registro.verificar');

// Rutas públicas (sin autenticación)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Recuperación de contraseña
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Rutas públicas de Flow (callbacks sin autenticación)
Route::post('/flow/confirmar', [FlowController::class, 'confirmar'])->name('flow.confirmar');
Route::match(['get', 'post'], '/flow/retorno', [FlowController::class, 'retorno'])->name('flow.retorno');
Route::get('/comprobante-pago/{id}', [PagosController::class, 'comprobante'])->name('comprobante.publico');
Route::get('/comprobante-pago/{id}/descargar', [PagosController::class, 'descargarComprobante'])->name('comprobante.descargar');

// Rutas de suscripción (requieren autenticación pero NO suscripción activa, para renovación)
Route::middleware(['auth'])->group(function () {
    Route::get('/suscripcion/renovar', [App\Http\Controllers\SuscripcionController::class, 'renovar'])->name('suscripcion.renovar');
    Route::get('/suscripcion/estado', [App\Http\Controllers\SuscripcionController::class, 'estado'])->name('suscripcion.estado');
    Route::post('/suscripcion/enviar-soporte', [App\Http\Controllers\SuscripcionController::class, 'enviarSolicitudSoporte'])->name('suscripcion.enviar-soporte');

    // Logout (debe estar fuera del middleware suscripcion.activa para permitir logout a usuarios suspendidos)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get'); // Fallback para tokens expirados

    // Pagos de suscripción (deben estar FUERA del middleware suscripcion.activa para permitir renovación)
    Route::prefix('organizacion')->name('organizacion.')->group(function () {
        Route::get('/pagos-suscripcion', [PagosSuscripcionController::class, 'index'])->name('pagos-suscripcion');
        Route::post('/pagos-suscripcion/{id}/pagar', [PagosSuscripcionController::class, 'pagar'])->name('pagos-suscripcion.pagar');
        Route::get('/cambiar-plan/{idSuscripcionNueva}/confirmar', [App\Http\Controllers\OrganizacionController::class, 'mostrarConfirmacionCambioPlan'])->name('confirmar-cambio-plan');
        Route::post('/cambiar-plan/{idSuscripcionNueva}', [App\Http\Controllers\OrganizacionController::class, 'iniciarCambioPlan'])->name('cambiar-plan');

        // Solicitudes de pago manual
        Route::get('/solicitud-pago-manual/{idPago}', [App\Http\Controllers\SolicitudPagoManualController::class, 'create'])->name('solicitud-pago-manual.create');
        Route::post('/solicitud-pago-manual/{idPago}', [App\Http\Controllers\SolicitudPagoManualController::class, 'store'])->name('solicitud-pago-manual.store');
        Route::get('/solicitud-pago-manual/{id}/ver', [App\Http\Controllers\SolicitudPagoManualController::class, 'show'])->name('solicitud-pago-manual.show');

        // Solicitud de compra de dominio
        Route::post('/dominio/solicitar', [App\Http\Controllers\OrganizacionController::class, 'solicitarCompraDominio'])->name('dominio.solicitar');
    });
});

// Rutas protegidas (requieren autenticación y suscripción activa)
Route::middleware(['auth', 'suscripcion.activa'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Actividad Reciente
    Route::get('/actividades', [ActividadController::class, 'index'])->name('actividades.index');

    // Onboarding
    Route::get('/bienvenida', [App\Http\Controllers\OnboardingController::class, 'bienvenida'])->name('onboarding.bienvenida');

    // RUTA ALTERNATIVA PARA GENERAR PDFs (sin OPcache)
    Route::get('/pdf-boleta/{id}', function($id) {
        \Log::info('===== INICIANDO GENERACION PDF RUTA DIRECTA =====', ['boleta_id' => $id]);

        $boleta = App\Models\Boleta::with(['socio.organizacion', 'lectura'])->findOrFail($id);

        if (!$boleta) {
            return 'Boleta no encontrada';
        }

        \Log::info('Boleta encontrada', ['numero' => $boleta->numero_boleta]);

        $historialConsumo = App\Models\Boleta::where('id_socio', $boleta->id_socio)
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

        $ultimoPago = DB::table('pagos')
            ->where('id_socio', $boleta->id_socio)
            ->orderBy('fecha_pago', 'desc')
            ->first();

        $boletasPendientes = App\Models\Boleta::where('id_socio', $boleta->id_socio)
            ->whereIn('estado', ['pendiente', 'vencida'])
            ->with('pagos')
            ->orderBy('mes', 'asc')
            ->get();

        $totalAdeudado = 0;
        foreach ($boletasPendientes as $boletaPendiente) {
            $totalPagado = $boletaPendiente->pagos->sum('monto_pagado');
            $saldoPendiente = $boletaPendiente->total - $totalPagado;
            $totalAdeudado += $saldoPendiente;
        }
        $mesesAdeudados = $boletasPendientes->count();

        // Generar HTML y guardarlo
        \Log::info('Generando HTML con vista boletas.pdf_new');
        $html = view('boletas.pdf_new', compact('boleta', 'historialConsumo', 'ultimoPago', 'boletasPendientes', 'totalAdeudado', 'mesesAdeudados'))->render();

        $tempHtmlPath = public_path('boleta_temp_' . $boleta->id . '_' . time() . '.html');
        file_put_contents($tempHtmlPath, $html);
        \Log::info('HTML guardado', ['path' => $tempHtmlPath, 'size' => strlen($html)]);

        // Generar PDF con wkhtmltopdf directamente
        $pdfPath = storage_path('app/boleta_temp_' . $boleta->id . '_' . time() . '.pdf');

        // Detectar sistema operativo y usar ruta correcta
        $wkhtmltopdfPath = PHP_OS_FAMILY === 'Windows'
            ? '"C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe"'
            : '/usr/bin/wkhtmltopdf';

        $fileUrl = 'file:///' . str_replace('\\', '/', $tempHtmlPath);

        $command = $wkhtmltopdfPath . ' --enable-local-file-access --page-size Letter "' . $fileUrl . '" "' . $pdfPath . '" 2>&1';
        \Log::info('Ejecutando wkhtmltopdf', ['command' => $command]);
        exec($command, $output, $returnCode);
        \Log::info('wkhtmltopdf ejecutado', ['returnCode' => $returnCode, 'output' => implode("\n", $output)]);

        if (!file_exists($pdfPath)) {
            \Log::error('PDF no generado', ['output' => $output]);
            return '<h1>Error al generar PDF</h1><pre>' . implode("\n", $output) . '</pre>';
        }

        \Log::info('PDF generado exitosamente', ['path' => $pdfPath]);

        $pdfContent = file_get_contents($pdfPath);

        // Eliminar archivos temporales
        @unlink($tempHtmlPath);
        @unlink($pdfPath);

        $nombreArchivo = 'Boleta-' . $boleta->numero_boleta . '.pdf';
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache'
        ]);
    });

    // ========================================
    // GESTIÓN DE SOCIOS
    // ========================================
    Route::middleware('permission:socios')->group(function () {
        Route::get('/socios', [SociosController::class, 'index'])->name('socios.index');
        Route::get('/socios/create', [SociosController::class, 'create'])->name('socios.create');
        Route::post('/socios', [SociosController::class, 'store'])->name('socios.store')->middleware('limite.socios');
        Route::get('/socios/{socio}', [SociosController::class, 'show'])->name('socios.show');
        Route::get('/socios/{socio}/edit', [SociosController::class, 'edit'])->name('socios.edit');
        Route::put('/socios/{socio}', [SociosController::class, 'update'])->name('socios.update');
        Route::delete('/socios/{socio}', [SociosController::class, 'destroy'])->name('socios.destroy');
        Route::post('/socios/{id}/toggle-exento-iva', [SociosController::class, 'toggleExentoIva'])->name('socios.toggleExentoIva');

        // Exportaciones
        Route::get('/socios-exportar-pdf', [SociosController::class, 'exportarPDF'])->name('socios.exportar-pdf');
        Route::get('/socios-exportar-excel', [SociosController::class, 'exportarExcel'])->name('socios.exportar-excel');
    });

    // ========================================
    // GESTIÓN DE LECTURAS
    // ========================================
    Route::middleware('permission:lecturas')->group(function () {
        Route::resource('lecturas', LecturasController::class);
        Route::get('/lecturas-masivo', [LecturasController::class, 'masivo'])->name('lecturas.masivo');
        Route::post('/lecturas-masivo', [LecturasController::class, 'storeMasivo'])->name('lecturas.storeMasivo');

        // Importación masiva CSV
        Route::get('/lecturas-importar', [ImportarLecturasController::class, 'index'])->name('lecturas.importar.index');
        Route::post('/lecturas-importar', [ImportarLecturasController::class, 'importar'])->name('lecturas.importar.procesar');
        Route::post('/lecturas-importar-confirmar', [ImportarLecturasController::class, 'confirmar'])->name('lecturas.importar.confirmar');
        Route::get('/lecturas-importar-plantilla', [ImportarLecturasController::class, 'descargarPlantilla'])->name('lecturas.importar.plantilla');

        // Exportaciones
        Route::get('/lecturas-exportar-excel', [LecturasController::class, 'exportarExcel'])->name('lecturas.exportar-excel');
    });

    // ========================================
    // GESTIÓN DE BOLETAS
    // ========================================
    Route::middleware('permission:boletas')->group(function () {
        Route::resource('boletas', BoletasController::class);
        Route::get('/boletas-generar', [BoletasController::class, 'generar'])->name('boletas.generar');
        Route::post('/boletas-generar', [BoletasController::class, 'storeGenerar'])->name('boletas.storeGenerar');
        Route::post('/boletas/{id}/anular', [BoletasController::class, 'anular'])->name('boletas.anular');
        Route::get('/boletas/{id}/imprimir', [BoletasController::class, 'imprimirV2'])->name('boletas.imprimir');
        Route::get('/boletas/{id}/imprimir-v2', [BoletasController::class, 'imprimirV2'])->name('boletas.imprimir.v2');
        Route::get('/boletas-vencidas', [BoletasController::class, 'vencidas'])->name('boletas.vencidas');
        Route::post('/boletas/{id}/recordatorio', [BoletasController::class, 'enviarRecordatorio'])->name('boletas.recordatorio');
        Route::post('/boletas/{id}/enviar-email', [BoletasController::class, 'enviarEmail'])->name('boletas.enviar-email');
        Route::post('/boletas/calcular-montos', [BoletasController::class, 'calcularMontos'])->name('boletas.calcularMontos');

        // Exportaciones
        Route::get('/boletas-exportar-pdf', [BoletasController::class, 'exportarPDF'])->name('boletas.exportar-pdf');
        Route::get('/boletas-exportar-excel', [BoletasController::class, 'exportarExcel'])->name('boletas.exportar-excel');

        // Folios SII
        Route::resource('folios-sii', FolioSIIController::class)->names('folios-sii');
        Route::post('/folios-sii-obtener-siguiente', [FolioSIIController::class, 'obtenerSiguiente'])->name('folios-sii.obtener-siguiente');
    });

    // ========================================
    // GESTIÓN DE PAGOS
    // ========================================
    Route::middleware('permission:pagos')->group(function () {
        Route::get('/pagos-dashboard', [PagosController::class, 'dashboard'])->name('pagos.dashboard');
        Route::resource('pagos', PagosController::class);
        Route::get('/pagos/{id}/imprimir', [PagosController::class, 'imprimir'])->name('pagos.imprimir');
        Route::get('/pagos/{id}/descargar-recibo', [PagosController::class, 'descargarRecibo'])->name('pagos.descargar-recibo');
        Route::post('/pagos/buscar-por-rut', [PagosController::class, 'buscarPorRut'])->name('pagos.buscarPorRut');
        Route::get('/api/socios/{id}/boletas-pendientes', [PagosController::class, 'boletasPendientes'])->name('pagos.boletasPendientes');
        Route::get('/reporte-caja', [PagosController::class, 'reporteCaja'])->name('pagos.reporteCaja');
        Route::get('/reporte-caja-imprimir', [PagosController::class, 'reporteCajaImprimir'])->name('pagos.reporteCaja.imprimir');
        Route::get('/reporte-mensual', [PagosController::class, 'reporteMensual'])->name('pagos.reporteMensual');
        Route::get('/reporte-mensual-imprimir', [PagosController::class, 'reporteMensualImprimir'])->name('pagos.reporteMensual.imprimir');
        Route::post('/pagos/generar-link-flow', [PagosController::class, 'generarLinkFlow'])->name('pagos.generarLinkFlow');
        Route::post('/pagos/{id}/enviar-email', [PagosController::class, 'enviarEmail'])->name('pagos.enviar-email');

        // Exportaciones
        Route::get('/pagos-exportar-excel', [PagosController::class, 'exportarExcel'])->name('pagos.exportar-excel');
    });

    // ========================================
    // FLOW - PASARELA DE PAGOS
    // ========================================
    Route::get('/flow/transaccion/{id}', [FlowController::class, 'verTransaccion'])->name('flow.transaccion');

    // ========================================
    // GESTIÓN DE INCIDENTES
    // ========================================
    Route::middleware('permission:incidentes')->group(function () {
        Route::resource('incidentes', IncidentesController::class);
        Route::post('/incidentes/{id}/asignar', [IncidentesController::class, 'asignar'])->name('incidentes.asignar');
        Route::post('/incidentes/{id}/resolver', [IncidentesController::class, 'resolver'])->name('incidentes.resolver');
        Route::post('/incidentes/{id}/cerrar', [IncidentesController::class, 'cerrar'])->name('incidentes.cerrar');
        Route::get('/incidentes-mapa', [IncidentesController::class, 'mapa'])->name('incidentes.mapa');
    });

    // ========================================
    // GESTIÓN DE USUARIOS
    // ========================================
    Route::middleware('permission:usuarios')->group(function () {
        Route::resource('usuarios', UsuariosController::class)->except(['store']);
        Route::post('/usuarios', [UsuariosController::class, 'store'])->name('usuarios.store')->middleware('limite.usuarios');
    });

    // Eliminar cuenta propia (Derecho ARCO de Cancelación) - Sin middleware de permiso
    Route::delete('/usuarios/{usuario}/eliminar-cuenta', [UsuariosController::class, 'eliminarCuenta'])->name('usuarios.eliminar-cuenta');

    // ========================================
    // GESTIÓN DE FUNCIONARIOS
    // ========================================
    Route::middleware('permission:funcionarios')->group(function () {
        Route::resource('funcionarios', FuncionariosController::class);
    });

    // ========================================
    // GESTIÓN DE SUELDOS
    // ========================================
    Route::middleware('permission:sueldos')->group(function () {
        Route::resource('sueldos', SueldosController::class);
    });

    // ========================================
    // GESTIÓN DE CORTES DE SUMINISTRO
    // ========================================
    Route::middleware('permission:cortes')->group(function () {
        Route::resource('cortes', CortesSuministroController::class);
    });

    // ========================================
    // GESTIÓN DE TRABAJOS REALIZADOS
    // ========================================
    Route::middleware('permission:trabajos')->group(function () {
        Route::resource('trabajos', TrabajosRealizadosController::class);
    });

    // ========================================
    // GESTIÓN DE RENOVACIONES DE MEDIDORES
    // ========================================
    Route::middleware('permission:renovaciones')->group(function () {
        Route::resource('renovaciones', RenovacionesMedidoresController::class);
    });

    // ========================================
    // GESTIÓN DE VACACIONES
    // ========================================
    Route::middleware('permission:vacaciones')->group(function () {
        Route::resource('vacaciones', VacacionesController::class);
    });

    // ========================================
    // GESTIÓN DE COMPRAS Y ACTIVOS FIJOS
    // ========================================
    Route::middleware('permission:compras')->group(function () {
        Route::resource('compras', ComprasController::class);
        Route::resource('activos-fijos', ActivosFijosController::class);
        Route::get('/activos-fijos-imprimir', [ActivosFijosController::class, 'imprimir'])->name('activos-fijos.imprimir');
    });

    // ========================================
    // RENDICIÓN MENSUAL
    // ========================================
    Route::middleware('permission:pagos')->group(function () {
        Route::resource('rendiciones-mensuales', RendicionMensualController::class);
        Route::post('/rendiciones-mensuales/{id}/cerrar-mes', [RendicionMensualController::class, 'cerrarMes'])->name('rendiciones-mensuales.cerrar-mes');
        Route::post('/rendiciones-mensuales/{id}/reabrir-mes', [RendicionMensualController::class, 'reabrirMes'])->name('rendiciones-mensuales.reabrir-mes');
        Route::get('/rendiciones-mensuales/{id}/exportar-pdf', [RendicionMensualController::class, 'exportarPDF'])->name('rendiciones-mensuales.exportar-pdf');
    });

    // ========================================
    // GESTIÓN DE INVENTARIO
    // ========================================
    Route::middleware('permission:inventario')->group(function () {
        Route::resource('inventario', InventarioController::class);

        // Importación masiva de inventario
        Route::get('/inventario-importar', [ImportarInventarioController::class, 'index'])->name('inventario.importar.index');
        Route::post('/inventario-importar', [ImportarInventarioController::class, 'importar'])->name('inventario.importar.procesar');
        Route::get('/inventario-importar-plantilla', [ImportarInventarioController::class, 'descargarPlantilla'])->name('inventario.importar.plantilla');
    });

    // ========================================
    // GESTIÓN DE MOVIMIENTOS DE INVENTARIO
    // ========================================
    Route::middleware('permission:movimientos_inventario')->group(function () {
        Route::resource('movimientos-inventario', MovimientosInventarioController::class);
        Route::get('/movimientos-inventario/{movimientosInventario}/imprimir', [MovimientosInventarioController::class, 'imprimir'])->name('movimientos-inventario.imprimir');
    });

    // ========================================
    // GESTIÓN DE TICKETS
    // ========================================
    Route::middleware('permission:tickets')->group(function () {
        Route::resource('tickets', TicketsController::class);
        Route::post('/tickets/{ticket}/respuestas', [TicketsController::class, 'agregarRespuesta'])->name('tickets.agregar-respuesta');
    });

    // ========================================
    // GESTIÓN DE RECORDATORIOS
    // ========================================
    Route::middleware('permission:recordatorios')->group(function () {
        Route::resource('recordatorios', RecordatoriosController::class);
    });

    // ========================================
    // GESTIÓN DE NOTIFICACIONES
    // ========================================
    Route::middleware('permission:notificaciones')->group(function () {
        Route::resource('notificaciones', NotificacionesController::class);
        Route::post('/notificaciones/{id}/enviar', [NotificacionesController::class, 'enviar'])->name('notificaciones.enviar');
    });

    // ========================================
    // GESTIÓN DE GIROS BANCARIOS
    // ========================================
    Route::middleware('permission:giros_bancarios')->group(function () {
        Route::resource('giros-bancarios', GirosBancariosController::class);
    });

    // ========================================
    // GESTIÓN DE DIRECTIVA
    // ========================================
    Route::middleware('permission:directiva')->group(function () {
        Route::resource('directiva', DirectivaController::class);
    });

    // ========================================
    // HISTORIAL DE CONSUMO
    // ========================================
    Route::middleware('permission:historial_consumo')->group(function () {
        Route::get('/historial-consumo', [HistorialConsumoController::class, 'index'])->name('historial-consumo.index');
        Route::get('/historial-consumo/{id}', [HistorialConsumoController::class, 'show'])->name('historial-consumo.show');
        Route::get('/historial-consumo/socio/{id}/analisis', [HistorialConsumoController::class, 'analisisSocio'])->name('historial-consumo.analisis-socio');
        Route::get('/historial-consumo-comparar', [HistorialConsumoController::class, 'comparar'])->name('historial-consumo.comparar');
        Route::get('/historial-consumo-comparar-pdf', [HistorialConsumoController::class, 'descargarComparacion'])->name('historial-consumo.comparar.pdf');
        Route::get('/historial-consumo-sincronizar', [HistorialConsumoController::class, 'sincronizar'])->name('historial-consumo.sincronizar');
    });

    // ========================================
    // HISTORIAL DE PAGOS
    // ========================================
    Route::middleware('permission:historial_pagos')->group(function () {
        Route::get('/historial-pagos', [HistorialPagosController::class, 'index'])->name('historial-pagos.index');
        Route::get('/historial-pagos/{id}', [HistorialPagosController::class, 'show'])->name('historial-pagos.show');
        Route::get('/historial-pagos/socio/{id}/analisis', [HistorialPagosController::class, 'analisisSocio'])->name('historial-pagos.analisis-socio');
        Route::get('/historial-pagos-comparar', [HistorialPagosController::class, 'comparar'])->name('historial-pagos.comparar');
        Route::get('/historial-pagos-reporte-recaudacion', [HistorialPagosController::class, 'reporteRecaudacion'])->name('historial-pagos.reporte-recaudacion');
    });

    // ========================================
    // REPORTES
    // ========================================
    Route::middleware('permission:reportes')->group(function () {
        Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/socios', [ReportesController::class, 'reporteSocios'])->name('reportes.socios');
        Route::get('/reportes/financiero', [ReportesController::class, 'reporteFinanciero'])->name('reportes.financiero');
        Route::get('/reportes/consumo', [ReportesController::class, 'reporteConsumo'])->name('reportes.consumo');
        Route::get('/reportes/operacional', [ReportesController::class, 'reporteOperacional'])->name('reportes.operacional');

        // Descargas PDF
        Route::get('/reportes/socios/descargar', [ReportesController::class, 'descargarReporteSocios'])->name('reportes.socios.descargar');
        Route::get('/reportes/financiero/descargar', [ReportesController::class, 'descargarReporteFinanciero'])->name('reportes.financiero.descargar');
        Route::get('/reportes/consumo/descargar', [ReportesController::class, 'descargarReporteConsumo'])->name('reportes.consumo.descargar');
        Route::get('/reportes/operacional/descargar', [ReportesController::class, 'descargarReporteOperacional'])->name('reportes.operacional.descargar');
    });

    // ========================================
    // CONFIGURACIONES TARIFARIAS
    // ========================================
    Route::resource('configuraciones-tarifas', ConfiguracionTarifasController::class);
    Route::get('/configuraciones-tarifas-simulador', [ConfiguracionTarifasController::class, 'simulador'])->name('configuraciones-tarifas.simulador');
    Route::post('/configuraciones-tarifas-calcular', [ConfiguracionTarifasController::class, 'calcular'])->name('configuraciones-tarifas.calcular');

    // ========================================
    // GESTIÓN DE EVENTOS
    // ========================================
    Route::middleware('permission:eventos')->group(function () {
        Route::resource('eventos', EventosController::class);
    });

    // ========================================
    // GESTIÓN DE ORGANIZACIÓN Y SUSCRIPCIÓN
    // ========================================
    Route::prefix('organizacion')->name('organizacion.')->group(function () {
        Route::get('/', [App\Http\Controllers\OrganizacionController::class, 'index'])->name('index');
        Route::get('/editar', [App\Http\Controllers\OrganizacionController::class, 'edit'])->name('edit');
        Route::put('/actualizar', [App\Http\Controllers\OrganizacionController::class, 'update'])->name('update');
        Route::delete('/logo', [App\Http\Controllers\OrganizacionController::class, 'deleteLogo'])->name('deleteLogo');
        Route::post('/reverificar-dns', [App\Http\Controllers\OrganizacionController::class, 'reverificarDNS'])->name('reverificar-dns');
        Route::get('/upgrade', [App\Http\Controllers\OrganizacionController::class, 'upgrade'])->name('upgrade');
    });

    // ========================================
    // NOTIFICACIONES DEL SISTEMA
    // ========================================
    Route::prefix('notificaciones-sistema')->name('notificaciones-sistema.')->group(function () {
        Route::get('/', [NotificacionesSistemaController::class, 'index'])->name('index');
        Route::post('/{id}/marcar-leida', [NotificacionesSistemaController::class, 'marcarLeida'])->name('marcar-leida');
        Route::post('/marcar-todas-leidas', [NotificacionesSistemaController::class, 'marcarTodasLeidas'])->name('marcar-todas-leidas');
        Route::get('/no-leidas', [NotificacionesSistemaController::class, 'noLeidas'])->name('no-leidas');
        Route::delete('/{id}', [NotificacionesSistemaController::class, 'eliminar'])->name('eliminar');
        Route::get('/stream', [NotificacionesStreamController::class, 'stream'])->name('stream');
    });

    // ========================================
    // MÓDULO DE NOTICIAS (Solo plan Enterprise)
    // ========================================
    Route::middleware('modulo.permitido:noticias')->group(function () {
        Route::resource('noticias', App\Http\Controllers\NoticiasController::class);
    });

});

// Rutas públicas de noticias (sin autenticación)
Route::get('/noticias-publicas/{slug}', [App\Http\Controllers\NoticiasController::class, 'publicas'])->name('noticias.publicas');
Route::get('/noticia/{slugOrganizacion}/{slugNoticia}', [App\Http\Controllers\NoticiasController::class, 'verPublica'])->name('noticia.publica');

// ========================================
// PANEL SUPER-ADMIN
// ========================================
Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    // Dashboard principal
    Route::get('/', [App\Http\Controllers\SuperAdminController::class, 'dashboard'])->name('dashboard');

    // Gestión de organizaciones
    Route::get('/organizaciones', [App\Http\Controllers\SuperAdminController::class, 'organizaciones'])->name('organizaciones');
    Route::get('/organizaciones/{id}', [App\Http\Controllers\SuperAdminController::class, 'verOrganizacion'])->name('organizacion.ver');
    Route::post('/organizaciones/{id}/suspender', [App\Http\Controllers\SuperAdminController::class, 'suspenderOrganizacion'])->name('organizacion.suspender');
    Route::post('/organizaciones/{id}/activar', [App\Http\Controllers\SuperAdminController::class, 'activarOrganizacion'])->name('organizacion.activar');
    Route::post('/organizaciones/{id}/cambiar-plan', [App\Http\Controllers\SuperAdminController::class, 'cambiarPlan'])->name('organizacion.cambiar-plan');

    // Importación masiva de socios
    Route::get('/organizaciones/{id}/importar-socios', [App\Http\Controllers\ImportarSociosController::class, 'mostrarFormulario'])->name('importar-socios.formulario');
    Route::post('/organizaciones/{id}/importar-socios', [App\Http\Controllers\ImportarSociosController::class, 'importar'])->name('importar-socios.procesar');
    Route::get('/plantilla-socios', [App\Http\Controllers\ImportarSociosController::class, 'descargarPlantilla'])->name('importar-socios.plantilla');
    Route::delete('/organizaciones/{id}', [App\Http\Controllers\SuperAdminController::class, 'eliminarOrganizacion'])->name('organizacion.eliminar');

    // Gestión de registros pendientes
    Route::get('/registros-pendientes', [App\Http\Controllers\SuperAdminController::class, 'registrosPendientes'])->name('registros.pendientes');
    Route::get('/registros/{id}', [App\Http\Controllers\SuperAdminController::class, 'verRegistro'])->name('registros.ver');
    Route::post('/registros/{id}/aprobar', [App\Http\Controllers\SuperAdminController::class, 'aprobarRegistro'])->name('registros.aprobar');
    Route::post('/registros/{id}/rechazar', [App\Http\Controllers\SuperAdminController::class, 'rechazarRegistro'])->name('registros.rechazar');

    // Reportes avanzados
    Route::get('/reportes/financiero', [App\Http\Controllers\SuperAdminController::class, 'reporteFinanciero'])->name('reportes.financiero');
    Route::get('/reportes/uso', [App\Http\Controllers\SuperAdminController::class, 'reporteUso'])->name('reportes.uso');
    Route::get('/reportes/comparativo', [App\Http\Controllers\SuperAdminController::class, 'reporteComparativo'])->name('reportes.comparativo');

    // Gestión de Libro de Reclamos (Ley 19.496)
    Route::get('/reclamos', [App\Http\Controllers\ReclamosController::class, 'index'])->name('reclamos.index');
    Route::get('/reclamos/{id}', [App\Http\Controllers\ReclamosController::class, 'show'])->name('reclamos.show');
    Route::put('/reclamos/{id}/responder', [App\Http\Controllers\ReclamosController::class, 'responder'])->name('reclamos.responder');

    // Renovaciones y vencimientos
    Route::get('/renovaciones', [App\Http\Controllers\SuperAdminController::class, 'renovaciones'])->name('renovaciones');
    Route::post('/renovaciones/{id}/pagar', [App\Http\Controllers\SuperAdminController::class, 'pagarRenovacion'])->name('renovaciones.pagar');

    // Solicitudes de pago manual
    Route::get('/solicitudes-pago', [App\Http\Controllers\SolicitudPagoManualController::class, 'index'])->name('solicitudes-pago.index');
    Route::get('/solicitudes-pago/{id}', [App\Http\Controllers\SolicitudPagoManualController::class, 'showSuperAdmin'])->name('solicitudes-pago.show');
    Route::post('/solicitudes-pago/{id}/aprobar', [App\Http\Controllers\SolicitudPagoManualController::class, 'aprobar'])->name('solicitudes-pago.aprobar');
    Route::post('/solicitudes-pago/{id}/rechazar', [App\Http\Controllers\SolicitudPagoManualController::class, 'rechazar'])->name('solicitudes-pago.rechazar');

    // Auditoría y logs
    Route::get('/auditoria', [App\Http\Controllers\SuperAdminController::class, 'auditoria'])->name('auditoria');

    // Gestión de dominios personalizados
    Route::get('/dominios-personalizados', [App\Http\Controllers\SuperAdminController::class, 'dominiosPersonalizados'])->name('dominios.index');
    Route::post('/dominios/{id}/aprobar', [App\Http\Controllers\SuperAdminController::class, 'aprobarDominio'])->name('dominios.aprobar');
    Route::post('/dominios/{id}/rechazar', [App\Http\Controllers\SuperAdminController::class, 'rechazarDominio'])->name('dominios.rechazar');
    Route::post('/dominios/{id}/suspender', [App\Http\Controllers\SuperAdminController::class, 'suspenderDominio'])->name('dominios.suspender');

    // Solicitudes de compra de dominio
    Route::get('/solicitudes-dominio', [App\Http\Controllers\SuperAdminController::class, 'solicitudesDominio'])->name('solicitudes-dominio.index');
    Route::post('/solicitudes-dominio/{id}/aprobar', [App\Http\Controllers\SuperAdminController::class, 'aprobarSolicitudDominio'])->name('solicitudes-dominio.aprobar');
    Route::post('/solicitudes-dominio/{id}/rechazar', [App\Http\Controllers\SuperAdminController::class, 'rechazarSolicitudDominio'])->name('solicitudes-dominio.rechazar');
    Route::post('/solicitudes-dominio/{id}/marcar-pagado', [App\Http\Controllers\SuperAdminController::class, 'marcarPagadoDominio'])->name('solicitudes-dominio.marcar-pagado');
    Route::post('/solicitudes-dominio/{id}/marcar-comprado', [App\Http\Controllers\SuperAdminController::class, 'marcarCompradoDominio'])->name('solicitudes-dominio.marcar-comprado');
    Route::post('/solicitudes-dominio/{id}/activar', [App\Http\Controllers\SuperAdminController::class, 'activarDominio'])->name('solicitudes-dominio.activar');

    // Perfil del super admin
    Route::get('/perfil', [App\Http\Controllers\SuperAdminController::class, 'perfil'])->name('perfil');
    Route::put('/perfil', [App\Http\Controllers\SuperAdminController::class, 'actualizarPerfil'])->name('perfil.actualizar');
});
