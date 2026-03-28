@extends('layouts.app')

@section('title', 'Dashboard de Pagos')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-chart-line text-primary"></i>
            Dashboard de Pagos
        </h1>
        <div>
            <a href="{{ route('pagos.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i> Ver Todos los Pagos
            </a>
            <a href="{{ route('pagos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Registrar Pago
            </a>
        </div>
    </div>

    <!-- Estadísticas Principales -->
    <div class="row mb-4">
        <!-- Total Hoy -->
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Recaudado Hoy</p>
                            <h3 class="mb-0 text-success">
                                ${{ number_format($estadisticas['total_hoy'], 0, ',', '.') }}
                            </h3>
                            <small class="text-muted">{{ $estadisticas['pagos_hoy'] }} pagos</small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-calendar-day fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Mes -->
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Total del Mes</p>
                            <h3 class="mb-0 text-primary">
                                ${{ number_format($estadisticas['total_mes'], 0, ',', '.') }}
                            </h3>
                            <small class="text-muted">{{ $estadisticas['pagos_mes'] }} pagos</small>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-calendar-alt fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Año -->
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Total del Año</p>
                            <h3 class="mb-0 text-info">
                                ${{ number_format($estadisticas['total_año'], 0, ',', '.') }}
                            </h3>
                            <small class="text-muted">{{ $estadisticas['pagos_año'] }} pagos</small>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-calendar fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Promedio Mensual -->
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Promedio Mensual</p>
                            @php
                                $promedioMensual = count($ingresosMensuales) > 0
                                    ? collect($ingresosMensuales)->avg('total')
                                    : 0;
                            @endphp
                            <h3 class="mb-0 text-warning">
                                ${{ number_format($promedioMensual, 0, ',', '.') }}
                            </h3>
                            <small class="text-muted">Últimos 12 meses</small>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-chart-bar fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <!-- Gráfico de Ingresos Mensuales -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line text-primary"></i>
                        Ingresos Mensuales (Últimos 12 Meses)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="graficoIngresosMensuales" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Métodos de Pago -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card text-success"></i>
                        Métodos de Pago (Mes Actual)
                    </h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    @if($metodosPago->count() > 0)
                        <canvas id="graficoMetodosPago" height="200"></canvas>
                    @else
                        <p class="text-muted text-center">No hay datos disponibles</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Métodos de Pago Detalle -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-wallet text-info"></i>
                        Detalle por Método de Pago
                    </h5>
                </div>
                <div class="card-body">
                    @if($metodosPago->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Método</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($metodosPago as $metodo)
                                    <tr>
                                        <td>
                                            @if($metodo->metodo_pago == 'efectivo')
                                                <i class="fas fa-money-bill text-success"></i>
                                            @elseif($metodo->metodo_pago == 'transferencia')
                                                <i class="fas fa-exchange-alt text-primary"></i>
                                            @elseif($metodo->metodo_pago == 'cheque')
                                                <i class="fas fa-money-check text-info"></i>
                                            @elseif($metodo->metodo_pago == 'debito')
                                                <i class="fas fa-credit-card text-warning"></i>
                                            @else
                                                <i class="fas fa-credit-card text-danger"></i>
                                            @endif
                                            {{ ucfirst($metodo->metodo_pago) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $metodo->cantidad }}</span>
                                        </td>
                                        <td class="text-end">
                                            <strong>${{ number_format($metodo->total, 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <th>TOTAL</th>
                                        <th class="text-center">{{ $metodosPago->sum('cantidad') }}</th>
                                        <th class="text-end">${{ number_format($metodosPago->sum('total'), 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No hay datos disponibles para el mes actual.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pagos Recientes -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-clock text-warning"></i>
                        Pagos Recientes
                    </h5>
                </div>
                <div class="card-body">
                    @if($pagosRecientes->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($pagosRecientes as $pago)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            {{ $pago->socio->nombre_completo }}
                                            <small class="text-muted">({{ $pago->socio->numero_socio }})</small>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-receipt"></i> {{ $pago->numero_recibo }}
                                            · <i class="far fa-calendar"></i> {{ $pago->fecha_pago_formateada }}
                                            ·
                                            @if($pago->metodo_pago == 'efectivo')
                                                <i class="fas fa-money-bill text-success"></i>
                                            @elseif($pago->metodo_pago == 'transferencia')
                                                <i class="fas fa-exchange-alt text-primary"></i>
                                            @else
                                                <i class="fas fa-credit-card"></i>
                                            @endif
                                            {{ ucfirst($pago->metodo_pago) }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <strong class="text-success">{{ $pago->monto_pagado_formateado }}</strong>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('pagos.index') }}" class="btn btn-sm btn-outline-primary">
                                Ver todos los pagos <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No hay pagos recientes.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para gráfico de ingresos mensuales
    const ingresosMensuales = @json($ingresosMensuales);
    const labels = ingresosMensuales.map(item => item.mes_nombre);
    const data = ingresosMensuales.map(item => item.total);

    // Gráfico de Ingresos Mensuales
    const ctxIngresos = document.getElementById('graficoIngresosMensuales');
    if (ctxIngresos) {
        new Chart(ctxIngresos, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ingresos ($)',
                    data: data,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '$' + context.parsed.y.toLocaleString('es-CL');
                                return label;
                            }
                        }
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
    }

    // Gráfico de Métodos de Pago
    @if($metodosPago->count() > 0)
    const ctxMetodos = document.getElementById('graficoMetodosPago');
    if (ctxMetodos) {
        const metodosPago = @json($metodosPago);
        const metodosLabels = metodosPago.map(item => {
            return item.metodo_pago.charAt(0).toUpperCase() + item.metodo_pago.slice(1);
        });
        const metodosData = metodosPago.map(item => item.total);

        const colores = [
            'rgba(34, 197, 94, 0.8)',   // green
            'rgba(59, 130, 246, 0.8)',   // blue
            'rgba(99, 102, 241, 0.8)',   // indigo
            'rgba(234, 179, 8, 0.8)',    // yellow
            'rgba(239, 68, 68, 0.8)'     // red
        ];

        new Chart(ctxMetodos, {
            type: 'doughnut',
            data: {
                labels: metodosLabels,
                datasets: [{
                    data: metodosData,
                    backgroundColor: colores,
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '$' + context.parsed.toLocaleString('es-CL');

                                // Calcular porcentaje
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                label += ' (' + percentage + '%)';

                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endpush
