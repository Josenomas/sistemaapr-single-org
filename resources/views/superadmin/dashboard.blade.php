@extends('layouts.superadmin')

@section('title', 'Panel Super-Admin')
@section('page-title', 'Dashboard Global')

@section('page-title')

@section('content')
<!-- Quick Actions -->
<div class="d-flex gap-2 mb-4">
    <a href="{{ route('superadmin.organizaciones') }}" class="btn btn-primary">
        <i class="fas fa-building"></i> Ver Organizaciones
    </a>
    <a href="{{ route('superadmin.registros.pendientes') }}" class="btn btn-warning">
        <i class="fas fa-user-clock"></i> Registros Pendientes
        @if($registrosPendientes > 0)
            <span class="badge bg-danger">{{ $registrosPendientes }}</span>
        @endif
    </a>
</div>

<!-- Métricas principales -->
    <div class="row mb-4">
        <!-- Total Organizaciones -->
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Organizaciones</p>
                            <h2 class="mb-0">{{ $totalOrganizaciones }}</h2>
                            <small class="text-success">
                                <i class="fas fa-plus-circle"></i> {{ $nuevasOrganizaciones }} nuevas (30d)
                            </small>
                        </div>
                        <div class="text-primary" style="font-size: 3rem; opacity: 0.2;">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Organizaciones Activas -->
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Activas</p>
                            <h2 class="mb-0 text-success">{{ $organizacionesActivas }}</h2>
                            <small class="text-muted">
                                {{ $totalOrganizaciones > 0 ? round(($organizacionesActivas / $totalOrganizaciones) * 100, 1) : 0 }}%
                            </small>
                        </div>
                        <div class="text-success" style="font-size: 3rem; opacity: 0.2;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- En Prueba -->
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">En Período de Prueba</p>
                            <h2 class="mb-0 text-warning">{{ $enPrueba }}</h2>
                            <small class="text-muted">30 días gratis</small>
                        </div>
                        <div class="text-warning" style="font-size: 3rem; opacity: 0.2;">
                            <i class="fas fa-gift"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ingresos Estimados -->
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Ingresos Mensuales</p>
                            <h2 class="mb-0 text-success">${{ number_format($ingresosMesActual, 0, ',', '.') }}</h2>
                            <small class="text-muted">Suscripciones activas</small>
                        </div>
                        <div class="text-success" style="font-size: 3rem; opacity: 0.2;">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas secundarias -->
    <div class="row mb-4">
        <!-- Total Usuarios -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-info mb-3" style="opacity: 0.3;"></i>
                    <h3>{{ number_format($totalUsuarios) }}</h3>
                    <p class="text-muted mb-0">Total Usuarios</p>
                </div>
            </div>
        </div>

        <!-- Total Socios -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-user-friends fa-3x text-primary mb-3" style="opacity: 0.3;"></i>
                    <h3>{{ number_format($totalSocios) }}</h3>
                    <p class="text-muted mb-0">Total Socios</p>
                </div>
            </div>
        </div>

        <!-- Pagos Flow (30 días) -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-credit-card fa-3x text-success mb-3" style="opacity: 0.3;"></i>
                    <h3>${{ number_format($pagosFlow30Dias, 0, ',', '.') }}</h3>
                    <p class="text-muted mb-0">Pagos Flow (30 días)</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Distribución por Suscripción -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Distribución por Plan</h5>
                </div>
                <div class="card-body">
                    @if($orgPorSuscripcion->isEmpty())
                        <p class="text-muted text-center py-4">No hay organizaciones registradas</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Plan</th>
                                        <th class="text-end">Organizaciones</th>
                                        <th class="text-end">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orgPorSuscripcion as $nombre => $total)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ $nombre }}</span>
                                            </td>
                                            <td class="text-end">{{ $total }}</td>
                                            <td class="text-end">
                                                {{ round(($total / $totalOrganizaciones) * 100, 1) }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Últimas Organizaciones -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Últimas Organizaciones Registradas</h5>
                </div>
                <div class="card-body">
                    @if($ultimasOrganizaciones->isEmpty())
                        <p class="text-muted text-center py-4">No hay organizaciones registradas</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($ultimasOrganizaciones as $org)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('superadmin.organizacion.ver', $org->id) }}" class="text-decoration-none">
                                                    {{ $org->nombre_apr }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-tag"></i> {{ $org->suscripcion->nombre }} |
                                                <i class="fas fa-clock"></i> {{ $org->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div>
                                            @if($org->activo)
                                                <span class="badge bg-success">Activa</span>
                                            @else
                                                <span class="badge bg-danger">Inactiva</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Suspendidas y Pendientes -->
    <div class="row">
        @if($organizacionesSuspendidas > 0)
            <div class="col-md-6 mb-4">
                <div class="card border-danger border-0 shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Organizaciones Suspendidas</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">Hay <strong>{{ $organizacionesSuspendidas }}</strong> organizaciones suspendidas</p>
                        <a href="{{ route('superadmin.organizaciones', ['activo' => '0']) }}" class="btn btn-sm btn-danger">
                            Ver Suspendidas
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @if($registrosPendientes > 0)
            <div class="col-md-6 mb-4">
                <div class="card border-warning border-0 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-user-clock"></i> Registros Pendientes</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">Hay <strong>{{ $registrosPendientes }}</strong> registros esperando verificación de email</p>
                        <a href="{{ route('superadmin.registros.pendientes') }}" class="btn btn-sm btn-warning">
                            Ver Registros
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
