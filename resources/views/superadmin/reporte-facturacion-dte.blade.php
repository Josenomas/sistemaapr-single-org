@extends('layouts.superadmin')

@section('title', 'Reporte de Facturación DTE')
@section('page-title', 'Reporte Avanzado de Facturación Electrónica')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('superadmin.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver al Dashboard
    </a>

    <div class="btn-group">
        <button type="button" class="btn btn-success" onclick="exportarExcel()">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </button>
        <button type="button" class="btn btn-danger" onclick="window.print()">
            <i class="fas fa-file-pdf"></i> Imprimir PDF
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('superadmin.reporte-facturacion-dte') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Fecha Desde</label>
                <input type="date" name="fecha_desde" class="form-control" value="{{ $filtros['fecha_desde'] ?? '' }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Fecha Hasta</label>
                <input type="date" name="fecha_hasta" class="form-control" value="{{ $filtros['fecha_hasta'] ?? '' }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Aplicar Filtros
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tarjetas de Resumen General -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #10b981 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-secondary mb-1 fw-semibold">Total DTEs Emitidos</p>
                        <h2 class="mb-0 text-dark fw-bold">{{ number_format($resumenGeneral['total_dtes_emitidos']) }}</h2>
                        <small class="text-success">
                            <i class="fas fa-arrow-up"></i> {{ number_format($resumenGeneral['dtes_este_mes']) }} este mes
                        </small>
                    </div>
                    <div class="text-success" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #3b82f6 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-secondary mb-1 fw-semibold">Ingresos Totales</p>
                        <h2 class="mb-0 text-dark fw-bold">${{ number_format($resumenGeneral['ingresos_totales'], 0, ',', '.') }}</h2>
                        <small class="text-muted">Histórico</small>
                    </div>
                    <div class="text-primary" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f59e0b !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-secondary mb-1 fw-semibold">Ingresos Este Mes</p>
                        <h2 class="mb-0 text-dark fw-bold">${{ number_format($resumenGeneral['ingresos_este_mes'], 0, ',', '.') }}</h2>
                        <small class="text-muted">{{ now()->locale('es')->isoFormat('MMMM YYYY') }}</small>
                    </div>
                    <div class="text-warning" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #8b5cf6 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-secondary mb-1 fw-semibold">Adopción DTE</p>
                        <h2 class="mb-0 text-dark fw-bold">{{ $resumenGeneral['porcentaje_adopcion'] }}%</h2>
                        <small class="text-muted">{{ $resumenGeneral['organizaciones_con_dte'] }} de {{ $resumenGeneral['total_organizaciones'] }} APRs</small>
                    </div>
                    <div class="text-purple" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-percentage"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row mb-4">
    <!-- Evolución Mensual -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-chart-area text-primary"></i>
                    Evolución Mensual de DTEs
                </h5>
            </div>
            <div class="card-body">
                <canvas id="chartEvolucionMensual" height="80"></canvas>
            </div>
        </div>
    </div>

    <!-- Distribución por Tipo -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-chart-pie text-success"></i>
                    Distribución por Tipo
                </h5>
            </div>
            <div class="card-body">
                <canvas id="chartDistribucionTipo"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top 10 Organizaciones -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-trophy text-warning"></i>
                    Top 10 Organizaciones por Facturación
                </h5>
            </div>
            <div class="card-body">
                <canvas id="chartTop10" height="60"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Facturación Detallada -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-table text-info"></i>
            Facturación Detallada por Organización
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 fw-semibold">#</th>
                        <th class="px-4 py-3 fw-semibold">Organización</th>
                        <th class="px-4 py-3 fw-semibold text-center">Total DTEs</th>
                        <th class="px-4 py-3 fw-semibold text-center">Boletas</th>
                        <th class="px-4 py-3 fw-semibold text-center">Facturas</th>
                        <th class="px-4 py-3 fw-semibold text-center">NC</th>
                        <th class="px-4 py-3 fw-semibold text-center">ND</th>
                        <th class="px-4 py-3 fw-semibold text-end">Ingresos Totales</th>
                        <th class="px-4 py-3 fw-semibold">Último DTE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($facturacionPorOrg as $index => $org)
                    <tr>
                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">
                            <div>
                                <div class="fw-semibold">{{ $org->nombre_organizacion }}</div>
                                <small class="text-muted">{{ $org->razon_social }}</small>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge bg-primary rounded-pill">{{ $org->total_dtes }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">{{ $org->total_boletas }}</td>
                        <td class="px-4 py-3 text-center">{{ $org->total_facturas }}</td>
                        <td class="px-4 py-3 text-center">{{ $org->total_nc }}</td>
                        <td class="px-4 py-3 text-center">{{ $org->total_nd }}</td>
                        <td class="px-4 py-3 text-end fw-bold text-success">
                            ${{ number_format($org->ingresos_totales, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            @if($org->ultimo_dte)
                                <small class="text-muted">{{ \Carbon\Carbon::parse($org->ultimo_dte)->format('d/m/Y') }}</small>
                            @else
                                <small class="text-muted">N/A</small>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No hay datos de facturación disponibles</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Análisis de Adopción -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-users text-purple"></i>
            Análisis de Adopción DTE
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <canvas id="chartAdopcion"></canvas>
            </div>
            <div class="col-md-6">
                <div class="d-flex flex-column justify-content-center h-100">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">Organizaciones Activas</span>
                            <span class="badge bg-success rounded-pill">{{ $analisisAdopcion['organizaciones_activas'] }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: {{ ($analisisAdopcion['organizaciones_activas'] / $analisisAdopcion['total_organizaciones'] * 100) }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">Configuradas sin Emisión</span>
                            <span class="badge bg-warning rounded-pill">{{ $analisisAdopcion['organizaciones_configuradas_sin_emision'] }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-warning" style="width: {{ ($analisisAdopcion['organizaciones_configuradas_sin_emision'] / $analisisAdopcion['total_organizaciones'] * 100) }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">Sin Configuración</span>
                            <span class="badge bg-secondary rounded-pill">{{ $analisisAdopcion['organizaciones_sin_configuracion'] }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-secondary" style="width: {{ ($analisisAdopcion['organizaciones_sin_configuracion'] / $analisisAdopcion['total_organizaciones'] * 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Configuración global de Chart.js
Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
Chart.defaults.color = '#6b7280';

// Evolución Mensual
const ctxEvolucion = document.getElementById('chartEvolucionMensual').getContext('2d');
new Chart(ctxEvolucion, {
    type: 'line',
    data: {
        labels: {!! json_encode($evolucionMensual['meses']) !!},
        datasets: [{
            label: 'DTEs Emitidos',
            data: {!! json_encode(array_column($evolucionMensual['data'], 'total_dtes')) !!},
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});

// Distribución por Tipo
const ctxDistribucion = document.getElementById('chartDistribucionTipo').getContext('2d');
new Chart(ctxDistribucion, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($distribucionTipo->pluck('nombre_tipo')) !!},
        datasets: [{
            data: {!! json_encode($distribucionTipo->pluck('cantidad')) !!},
            backgroundColor: [
                '#3b82f6',
                '#10b981',
                '#f59e0b',
                '#ef4444'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Top 10 Organizaciones
const ctxTop10 = document.getElementById('chartTop10').getContext('2d');
new Chart(ctxTop10, {
    type: 'bar',
    data: {
        labels: {!! json_encode($top10Organizaciones->pluck('nombre_organizacion')) !!},
        datasets: [{
            label: 'Ingresos ($)',
            data: {!! json_encode($top10Organizaciones->pluck('ingresos_totales')) !!},
            backgroundColor: '#10b981',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString('es-CL');
                    }
                }
            }
        }
    }
});

// Gráfico de Adopción
const ctxAdopcion = document.getElementById('chartAdopcion').getContext('2d');
new Chart(ctxAdopcion, {
    type: 'pie',
    data: {
        labels: ['Activas', 'Configuradas sin Emisión', 'Sin Configuración'],
        datasets: [{
            data: [
                {{ $analisisAdopcion['organizaciones_activas'] }},
                {{ $analisisAdopcion['organizaciones_configuradas_sin_emision'] }},
                {{ $analisisAdopcion['organizaciones_sin_configuracion'] }}
            ],
            backgroundColor: [
                '#10b981',
                '#f59e0b',
                '#6b7280'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Función de exportación Excel
function exportarExcel() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '{{ route("superadmin.exportar-reporte-dte-excel") }}?' + params.toString();
}
</script>

<style>
@media print {
    .btn, .navbar, .sidebar {
        display: none !important;
    }
    .card {
        page-break-inside: avoid;
    }
}
</style>
@endpush
