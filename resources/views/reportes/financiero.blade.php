@extends('layouts.app')

@section('title', 'Reporte Financiero - Sistema APR')

@section('styles')
<style>
    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .report-title {
        font-size: 2rem;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        margin: 0;
    }

    .report-title i {
        color: var(--success);
        font-size: 1.75rem;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--white);
        color: var(--primary);
        border: 2px solid var(--primary);
        border-radius: var(--radius);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s;
    }

    .back-btn:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 20px;
        transition: all 0.3s;
        border: 1px solid var(--gray-200);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }

    .stat-card.success::before {
        background: linear-gradient(180deg, var(--success), #059669);
    }

    .stat-card.danger::before {
        background: linear-gradient(180deg, var(--danger), #dc2626);
    }

    .stat-card.info::before {
        background: linear-gradient(180deg, #0ea5e9, #0284c7);
    }

    .stat-card.warning::before {
        background: linear-gradient(180deg, var(--warning), #d97706);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .stat-title {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 600;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }

    .success-bg {
        background: linear-gradient(135deg, var(--success), #059669);
    }

    .danger-bg {
        background: linear-gradient(135deg, var(--danger), #dc2626);
    }

    .info-bg {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
    }

    .warning-bg {
        background: linear-gradient(135deg, var(--warning), #d97706);
    }

    .stat-value {
        font-size: 1.875rem;
        font-weight: 700;
        margin-bottom: 6px;
        color: var(--gray-900);
    }

    .stat-description {
        font-size: 0.8125rem;
        color: var(--gray-500);
    }

    .filters-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid var(--gray-200);
    }

    .filters-title {
        font-size: 1.125rem;
        color: var(--dark);
        font-weight: 700;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filters-title i {
        color: var(--primary);
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        align-items: end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
    }

    .form-control {
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .btn {
        padding: 10px 20px;
        border-radius: var(--radius);
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        justify-content: center;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-secondary:hover {
        background: var(--gray-300);
    }

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .chart-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 24px;
        border: 1px solid var(--gray-200);
    }

    .chart-title {
        font-size: 1.125rem;
        color: var(--dark);
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chart-title i {
        color: var(--primary);
    }

    .table-container {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 24px;
        border: 1px solid var(--gray-200);
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background: linear-gradient(135deg, var(--gray-50), var(--gray-100));
    }

    .table th {
        padding: 12px 16px;
        text-align: left;
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--gray-700);
        border-bottom: 2px solid var(--gray-300);
        white-space: nowrap;
    }

    .table td {
        padding: 12px 16px;
        font-size: 0.875rem;
        color: var(--gray-800);
        border-bottom: 1px solid var(--gray-200);
    }

    .table tbody tr {
        transition: background 0.2s;
    }

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .text-success {
        color: var(--success);
        font-weight: 600;
    }

    .text-danger {
        color: var(--danger);
        font-weight: 600;
    }

    .text-info {
        color: #0ea5e9;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .charts-grid {
            grid-template-columns: 1fr;
        }

        .filters-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="report-container">
    <div class="report-header">
        <h2 class="report-title">
            <i class="fas fa-dollar-sign"></i>
            Reporte Financiero
        </h2>
        <a href="{{ route('reportes.index') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Volver al Centro de Reportes
        </a>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card success">
            <div class="stat-header">
                <div class="stat-title">Total Ingresos</div>
                <div class="stat-icon success-bg">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
            <div class="stat-value">${{ number_format($totalIngresos, 0, ',', '.') }}</div>
            <div class="stat-description">Período seleccionado</div>
        </div>

        <div class="stat-card danger">
            <div class="stat-header">
                <div class="stat-title">Total Egresos</div>
                <div class="stat-icon danger-bg">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
            <div class="stat-value">${{ number_format($totalEgresos, 0, ',', '.') }}</div>
            <div class="stat-description">Período seleccionado</div>
        </div>

        <div class="stat-card info">
            <div class="stat-header">
                <div class="stat-title">Balance</div>
                <div class="stat-icon info-bg">
                    <i class="fas fa-balance-scale"></i>
                </div>
            </div>
            <div class="stat-value">${{ number_format($balance, 0, ',', '.') }}</div>
            <div class="stat-description">Ingresos - Egresos</div>
        </div>

        <div class="stat-card warning">
            <div class="stat-header">
                <div class="stat-title">Pagos Pendientes</div>
                <div class="stat-icon warning-bg">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-value">${{ number_format($boletasPendientes, 0, ',', '.') }}</div>
            <div class="stat-description">Por cobrar</div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-card">
        <h3 class="filters-title">
            <i class="fas fa-filter"></i>
            Filtros de Período
        </h3>
        <form method="GET" action="{{ route('reportes.financiero') }}">
            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control"
                           value="{{ request('fecha_inicio', $fechaInicio) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control"
                           value="{{ request('fecha_fin', $fechaFin) }}">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filtrar
                    </button>
                </div>

                <div class="form-group">
                    <a href="{{ route('reportes.financiero') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Gráficos -->
    <div class="charts-grid">
        <div class="chart-card">
            <h4 class="chart-title">
                <i class="fas fa-chart-line"></i>
                Ingresos vs Egresos
            </h4>
            <canvas id="ingresosEgresosChart"></canvas>
        </div>

        <div class="chart-card">
            <h4 class="chart-title">
                <i class="fas fa-chart-bar"></i>
                Ingresos por Método de Pago
            </h4>
            <canvas id="ingresosPorMetodoChart"></canvas>
        </div>
    </div>

    <div class="chart-card" style="margin-bottom: 32px;">
        <h4 class="chart-title">
            <i class="fas fa-chart-bar"></i>
            Egresos por Tipo de Compra
        </h4>
        <canvas id="egresosPorTipoChart"></canvas>
    </div>

    <!-- Tabla de Detalle Mensual -->
    <div class="table-container">
        <h4 class="chart-title">
            <i class="fas fa-table"></i>
            Detalle Mensual
        </h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Mes</th>
                    <th>Ingresos</th>
                    <th>Egresos</th>
                    <th>Balance</th>
                    <th>Var. Ingresos</th>
                    <th>Var. Egresos</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comparativoMensual as $index => $mes)
                <tr>
                    <td><strong>{{ $mes['mes'] }}</strong></td>
                    <td class="text-success">${{ number_format($mes['ingresos'], 0, ',', '.') }}</td>
                    <td class="text-danger">${{ number_format($mes['egresos'], 0, ',', '.') }}</td>
                    <td class="text-info">${{ number_format($mes['balance'], 0, ',', '.') }}</td>
                    <td>
                        @if($index > 0)
                            @php
                                $anterior = $comparativoMensual[$index - 1]['ingresos'];
                                $variacion = $anterior > 0 ? (($mes['ingresos'] - $anterior) / $anterior) * 100 : 0;
                            @endphp
                            @if($variacion > 0)
                                <span class="text-success">
                                    <i class="fas fa-arrow-up"></i> {{ number_format($variacion, 1) }}%
                                </span>
                            @elseif($variacion < 0)
                                <span class="text-danger">
                                    <i class="fas fa-arrow-down"></i> {{ number_format(abs($variacion), 1) }}%
                                </span>
                            @else
                                <span style="color: var(--gray-500);">0%</span>
                            @endif
                        @else
                            <span style="color: var(--gray-500);">-</span>
                        @endif
                    </td>
                    <td>
                        @if($index > 0)
                            @php
                                $anterior = $comparativoMensual[$index - 1]['egresos'];
                                $variacion = $anterior > 0 ? (($mes['egresos'] - $anterior) / $anterior) * 100 : 0;
                            @endphp
                            @if($variacion > 0)
                                <span class="text-danger">
                                    <i class="fas fa-arrow-up"></i> {{ number_format($variacion, 1) }}%
                                </span>
                            @elseif($variacion < 0)
                                <span class="text-success">
                                    <i class="fas fa-arrow-down"></i> {{ number_format(abs($variacion), 1) }}%
                                </span>
                            @else
                                <span style="color: var(--gray-500);">0%</span>
                            @endif
                        @else
                            <span style="color: var(--gray-500);">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--gray-500);">No hay datos disponibles</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de Ingresos vs Egresos
    const ingresosEgresosCtx = document.getElementById('ingresosEgresosChart').getContext('2d');
    new Chart(ingresosEgresosCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($comparativoMensual, 'mes')) !!},
            datasets: [
                {
                    label: 'Ingresos',
                    data: {!! json_encode(array_column($comparativoMensual, 'ingresos')) !!},
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Egresos',
                    data: {!! json_encode(array_column($comparativoMensual, 'egresos')) !!},
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
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

    // Gráfico de Ingresos por Método de Pago
    const ingresosPorMetodoCtx = document.getElementById('ingresosPorMetodoChart').getContext('2d');
    new Chart(ingresosPorMetodoCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($ingresos->pluck('metodo_pago')->toArray()) !!},
            datasets: [{
                label: 'Monto',
                data: {!! json_encode($ingresos->pluck('total')->toArray()) !!},
                backgroundColor: [
                    'rgba(37, 99, 235, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(14, 165, 233, 0.8)'
                ],
                borderWidth: 1
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

    // Gráfico de Egresos por Tipo de Compra
    const egresosPorTipoCtx = document.getElementById('egresosPorTipoChart').getContext('2d');
    new Chart(egresosPorTipoCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($egresos->pluck('tipo_compra')->toArray()) !!},
            datasets: [{
                label: 'Monto',
                data: {!! json_encode($egresos->pluck('total')->toArray()) !!},
                backgroundColor: [
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(37, 99, 235, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)'
                ],
                borderWidth: 1
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
</script>
@endsection
