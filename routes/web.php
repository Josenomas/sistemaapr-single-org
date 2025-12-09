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
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\ConsultaPublicaController;
use App\Http\Controllers\EventosController;

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

// Consulta Pública de Pagos
Route::get('/consultar-cuenta', [ConsultaPublicaController::class, 'mostrarFormulario'])->name('consulta.pago');
Route::post('/consultar-cuenta/buscar', [ConsultaPublicaController::class, 'buscarPorRut'])->name('consulta.buscar');
Route::post('/consultar-cuenta/generar-pago', [ConsultaPublicaController::class, 'generarPago'])->name('consulta.generar.pago');

// Formulario de contacto
Route::post('/contacto', [ContactoController::class, 'enviar'])->name('contacto.enviar');

// Rutas públicas (sin autenticación)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Rutas públicas de Flow (callbacks sin autenticación)
Route::post('/flow/confirmar', [FlowController::class, 'confirmar'])->name('flow.confirmar');
Route::get('/flow/retorno', [FlowController::class, 'retorno'])->name('flow.retorno');

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ========================================
    // GESTIÓN DE SOCIOS
    // ========================================
    Route::middleware('permission:socios')->group(function () {
        Route::resource('socios', SociosController::class);
        Route::post('/socios/{id}/toggle-exento-iva', [SociosController::class, 'toggleExentoIva'])->name('socios.toggleExentoIva');
    });

    // ========================================
    // GESTIÓN DE LECTURAS
    // ========================================
    Route::middleware('permission:lecturas')->group(function () {
        Route::resource('lecturas', LecturasController::class);
        Route::get('/lecturas-masivo', [LecturasController::class, 'masivo'])->name('lecturas.masivo');
        Route::post('/lecturas-masivo', [LecturasController::class, 'storeMasivo'])->name('lecturas.storeMasivo');
    });

    // ========================================
    // GESTIÓN DE BOLETAS
    // ========================================
    Route::middleware('permission:boletas')->group(function () {
        Route::resource('boletas', BoletasController::class);
        Route::get('/boletas-generar', [BoletasController::class, 'generar'])->name('boletas.generar');
        Route::post('/boletas-generar', [BoletasController::class, 'storeGenerar'])->name('boletas.storeGenerar');
        Route::post('/boletas/{id}/anular', [BoletasController::class, 'anular'])->name('boletas.anular');
        Route::get('/boletas/{id}/imprimir', [BoletasController::class, 'imprimir'])->name('boletas.imprimir');
        Route::get('/boletas-vencidas', [BoletasController::class, 'vencidas'])->name('boletas.vencidas');
        Route::post('/boletas/{id}/recordatorio', [BoletasController::class, 'enviarRecordatorio'])->name('boletas.recordatorio');
        Route::post('/boletas/{id}/enviar-email', [BoletasController::class, 'enviarEmail'])->name('boletas.enviar-email');
        Route::post('/boletas/calcular-montos', [BoletasController::class, 'calcularMontos'])->name('boletas.calcularMontos');
    });

    // ========================================
    // GESTIÓN DE PAGOS
    // ========================================
    Route::middleware('permission:pagos')->group(function () {
        Route::resource('pagos', PagosController::class);
        Route::get('/pagos/{id}/imprimir', [PagosController::class, 'imprimir'])->name('pagos.imprimir');
        Route::get('/api/socios/{id}/boletas-pendientes', [PagosController::class, 'boletasPendientes'])->name('pagos.boletasPendientes');
        Route::get('/reporte-caja', [PagosController::class, 'reporteCaja'])->name('pagos.reporteCaja');
        Route::post('/pagos/generar-link-flow', [PagosController::class, 'generarLinkFlow'])->name('pagos.generarLinkFlow');
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
        Route::resource('usuarios', UsuariosController::class);
    });

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
    // GESTIÓN DE COMPRAS
    // ========================================
    Route::middleware('permission:compras')->group(function () {
        Route::resource('compras', ComprasController::class);
    });

    // ========================================
    // GESTIÓN DE INVENTARIO
    // ========================================
    Route::middleware('permission:inventario')->group(function () {
        Route::resource('inventario', InventarioController::class);
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

});
