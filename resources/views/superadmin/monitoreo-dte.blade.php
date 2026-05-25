@extends('layouts.superadmin')

@section('title', 'Monitoreo DTE')
@section('page-title', 'Monitoreo de Facturación Electrónica')

@section('content')
<div class="d-flex gap-2 mb-4">
    <a href="{{ route('superadmin.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver al Dashboard
    </a>
</div>

<!-- Tarjetas de Estadísticas Generales -->
<div class="row mb-4">
    <!-- Total Organizaciones con DTE -->
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #7c3aed !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-secondary mb-1 fw-semibold">Con DTE Configurado</p>
                        <h2 class="mb-0 text-dark fw-bold">{{ $organizacionesConDTE }}</h2>
                        <small class="text-muted">de {{ $totalOrganizaciones }} total</small>
                    </div>
                    <div class="text-primary" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sin DTE -->
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ef4444 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-secondary mb-1 fw-semibold">Sin DTE</p>
                        <h2 class="mb-0 text-dark fw-bold">{{ $organizacionesSinDTE }}</h2>
                        <small class="text-danger">Pendientes configurar</small>
                    </div>
                    <div class="text-danger" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ambiente Certificación -->
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f59e0b !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-secondary mb-1 fw-semibold">En Certificación</p>
                        <h2 class="mb-0 text-dark fw-bold">{{ $enCertificacion }}</h2>
                        <small class="text-warning">Ambiente de pruebas</small>
                    </div>
                    <div class="text-warning" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-flask"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ambiente Producción -->
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #10b981 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-secondary mb-1 fw-semibold">En Producción</p>
                        <h2 class="mb-0 text-dark fw-bold">{{ $enProduccion }}</h2>
                        <small class="text-success">DTEs válidos ante SII</small>
                    </div>
                    <div class="text-success" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tarjetas de DTEs Emitidos -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-secondary mb-1 fw-semibold">Total DTEs Emitidos</p>
                        <h2 class="mb-0 text-dark fw-bold">{{ number_format($totalDTEsEmitidos, 0, ',', '.') }}</h2>
                        <small class="text-muted">Desde el inicio</small>
                    </div>
                    <div class="text-primary" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-secondary mb-1 fw-semibold">DTEs Últimos 30 Días</p>
                        <h2 class="mb-0 text-dark fw-bold">{{ number_format($dtesUltimos30Dias, 0, ',', '.') }}</h2>
                        <small class="text-info">Actividad reciente</small>
                    </div>
                    <div class="text-info" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Organizaciones -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-list text-primary"></i> Estado por Organización
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0">Organización</th>
                        <th class="border-0">RUT</th>
                        <th class="border-0 text-center">Ambiente</th>
                        <th class="border-0 text-center">Estado</th>
                        <th class="border-0 text-center">DTEs Emitidos</th>
                        <th class="border-0 text-center">Últimos 30d</th>
                        <th class="border-0">Último DTE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($organizacionesMetricas as $item)
                    <tr>
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-building text-primary"></i>
                                </div>
                                <div>
                                    <strong>{{ $item['organizacion']->nombre }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $item['config']->razon_social ?? 'Sin configurar' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle">
                            <code>{{ $item['config']->rut_emisor ?? 'N/A' }}</code>
                        </td>
                        <td class="align-middle text-center">
                            @if($item['config']->ambiente === 'certificacion')
                                <span class="badge bg-warning">
                                    <i class="fas fa-flask"></i> Certificación
                                </span>
                            @else
                                <span class="badge bg-success">
                                    <i class="fas fa-shield-alt"></i> Producción
                                </span>
                            @endif
                        </td>
                        <td class="align-middle text-center">
                            @if($item['config']->activo)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> Activo
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-times-circle"></i> Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="align-middle text-center">
                            <strong class="text-primary">{{ number_format($item['dtes_emitidos'], 0, ',', '.') }}</strong>
                        </td>
                        <td class="align-middle text-center">
                            @if($item['dtes_recientes'] > 0)
                                <span class="badge bg-info">{{ number_format($item['dtes_recientes'], 0, ',', '.') }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td class="align-middle">
                            @if($item['ultimo_dte'])
                                <small class="text-muted">
                                    {{ $item['ultimo_dte']->fecha_emision_dte->format('d/m/Y H:i') }}
                                </small>
                                <br>
                                <small class="badge bg-light text-dark">Folio: {{ $item['ultimo_dte']->folio_sii }}</small>
                            @else
                                <span class="text-muted">Sin DTEs</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p class="mb-0">No hay configuraciones DTE registradas</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Información -->
<div class="alert alert-info mt-4">
    <i class="fas fa-info-circle"></i>
    <strong>Nota:</strong> Este panel es de solo lectura. Cada organización gestiona su propia configuración DTE desde su panel de usuario.
    El Super Admin puede ver el estado de todas las organizaciones para fines de soporte y monitoreo.
</div>
@endsection
