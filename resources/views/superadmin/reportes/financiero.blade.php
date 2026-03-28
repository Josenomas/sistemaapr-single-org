@extends('layouts.superadmin')

@section('title', 'Reporte Financiero - Super Admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-dollar-sign"></i>
        Reporte Financiero Global
    </h1>
</div>

<!-- Estadísticas Principales -->
<div class="stats-grid">
    <div class="stat-card stat-success">
        <div class="stat-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Ingresos Mensuales (Estimados)</div>
            <div class="stat-value">${{ number_format($ingresosSuscripciones, 0, ',', '.') }}</div>
            <div class="stat-sublabel">Basado en suscripciones activas</div>
        </div>
    </div>

    <div class="stat-card stat-primary">
        <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pagos Recibidos (Mes Actual)</div>
            <div class="stat-value">${{ number_format($pagosRecibidos, 0, ',', '.') }}</div>
            <div class="stat-sublabel">Pagos confirmados en {{ now()->format('F Y') }}</div>
        </div>
    </div>

    <div class="stat-card stat-warning">
        <div class="stat-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pagos Pendientes</div>
            <div class="stat-value">${{ number_format($pagosPendientes, 0, ',', '.') }}</div>
            <div class="stat-sublabel">Por cobrar</div>
        </div>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-icon">
            <i class="fas fa-percentage"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Tasa de Conversión</div>
            <div class="stat-value">{{ $tasaConversion }}%</div>
            <div class="stat-sublabel">De prueba a pago</div>
        </div>
    </div>
</div>

<!-- Ingresos por Plan -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-layer-group"></i> Ingresos por Plan de Suscripción</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Plan</th>
                        <th>Organizaciones</th>
                        <th>Ingreso Mensual</th>
                        <th>% del Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalIngresos = $ingresosPorPlan->sum('ingresos');
                    @endphp
                    @foreach($ingresosPorPlan as $plan)
                    <tr>
                        <td><strong>{{ $plan->nombre }}</strong></td>
                        <td>{{ $plan->organizaciones }} org.</td>
                        <td><strong>${{ number_format($plan->ingresos, 0, ',', '.') }}</strong></td>
                        <td>
                            @php
                                $porcentaje = $totalIngresos > 0 ? round(($plan->ingresos / $totalIngresos) * 100, 2) : 0;
                            @endphp
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: {{ $porcentaje }}%"></div>
                                <span class="progress-text">{{ $porcentaje }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL</th>
                        <th>{{ $ingresosPorPlan->sum('organizaciones') }}</th>
                        <th><strong>${{ number_format($totalIngresos, 0, ',', '.') }}</strong></th>
                        <th>100%</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Evolución de Ingresos -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-chart-area"></i> Evolución de Ingresos (Últimos 6 Meses)</h3>
    </div>
    <div class="card-body">
        <div class="chart-container">
            <canvas id="ingresosChart"></canvas>
        </div>
    </div>
</div>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--dark-card);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        flex-shrink: 0;
    }

    .stat-success .stat-icon {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .stat-primary .stat-icon {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        color: white;
    }

    .stat-warning .stat-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .stat-info .stat-icon {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
        color: white;
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-light);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-sublabel {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .progress-bar-container {
        position: relative;
        height: 24px;
        background: rgba(124, 58, 237, 0.1);
        border-radius: 0.25rem;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--primary), var(--secondary));
        transition: width 0.3s;
    }

    .progress-text {
        position: absolute;
        top: 50%;
        right: 8px;
        transform: translateY(-50%);
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-light);
    }

    .chart-container {
        position: relative;
        height: 400px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('ingresosChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json(array_column($evolucionIngresos, 'mes')),
            datasets: [{
                label: 'Ingresos',
                data: @json(array_column($evolucionIngresos, 'ingresos')),
                borderColor: '#7c3aed',
                backgroundColor: 'rgba(124, 58, 237, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.y.toLocaleString('es-CL');
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
                        },
                        color: '#94a3b8'
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: '#94a3b8'
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    }
                }
            }
        }
    });
</script>
@endsection
