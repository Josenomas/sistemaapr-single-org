@extends('layouts.app')

@section('title', 'Análisis de Consumo del Socio - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-chart-line"></i>
        Análisis de Consumo: {{ $socio->nombre_completo }}
    </h2>
    <div class="btn-group">
        <a href="{{ route('socios.show', $socio->id) }}" class="btn btn-info">
            <i class="fas fa-user"></i>
            Ver Socio
        </a>
        <a href="{{ route('historial-consumo.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<!-- Estadísticas del Socio -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Períodos</div>
            <div class="stat-value">{{ number_format($estadisticas['total_periodos']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-tint"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Consumo Total</div>
            <div class="stat-value">{{ number_format($estadisticas['consumo_total'], 2) }} m³</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-chart-bar"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Promedio General</div>
            <div class="stat-value">{{ number_format($estadisticas['consumo_promedio'], 2) }} m³</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-warning">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Monto Total</div>
            <div class="stat-value">${{ number_format($estadisticas['monto_total'], 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<!-- Estadísticas Adicionales -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-arrow-up"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Consumo Máximo</div>
            <div class="stat-value">{{ number_format($estadisticas['consumo_maximo'], 2) }} m³</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-arrow-down"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Consumo Mínimo</div>
            <div class="stat-value">{{ number_format($estadisticas['consumo_minimo'], 2) }} m³</div>
        </div>
    </div>

    <div class="stat-card highlight">
        <div class="stat-icon bg-warning">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Anomalías Detectadas</div>
            <div class="stat-value">{{ number_format($estadisticas['anomalias']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Último Período</div>
            <div class="stat-value">{{ $estadisticas['ultimo_periodo'] }}</div>
        </div>
    </div>
</div>

<!-- Promedios Recientes -->
<div class="card mb-3">
    <div class="card-body">
        <div class="promedios-grid">
            <div class="promedio-item">
                <div class="promedio-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="promedio-info">
                    <label>Promedio 6 Meses</label>
                    <value>{{ number_format($estadisticas['promedio_6_meses'], 2) }} m³</value>
                </div>
            </div>

            <div class="promedio-item">
                <div class="promedio-icon">
                    <i class="fas fa-chart-area"></i>
                </div>
                <div class="promedio-info">
                    <label>Promedio 12 Meses</label>
                    <value>{{ number_format($estadisticas['promedio_12_meses'], 2) }} m³</value>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico de Tendencia -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-area"></i>
            Tendencia de Consumo (Últimos 12 Meses)
        </h3>
    </div>
    <div class="card-body">
        @if(count($tendencia) > 0)
            <div class="chart-container">
                <canvas id="tendenciaChart"></canvas>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-chart-line"></i>
                <p>No hay datos suficientes para mostrar la tendencia</p>
            </div>
        @endif
    </div>
</div>

<!-- Historial Completo -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-history"></i>
            Historial Completo de Consumo
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Período</th>
                        <th>Lectura Anterior</th>
                        <th>Lectura Actual</th>
                        <th>Consumo (m³)</th>
                        <th>Promedio Diario</th>
                        <th>Monto</th>
                        <th>Variación</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historiales as $historial)
                        <tr>
                            <td>
                                <span class="badge badge-secondary">
                                    <i class="fas fa-calendar"></i>
                                    {{ $historial->periodo_formateado }}
                                </span>
                            </td>
                            <td>{{ number_format($historial->lectura_anterior, 2) }} m³</td>
                            <td>{{ number_format($historial->lectura_actual, 2) }} m³</td>
                            <td>
                                <strong class="consumo-valor">{{ $historial->consumo_formateado }}</strong>
                            </td>
                            <td>
                                <small class="text-muted">{{ $historial->promedio_diario_formateado }}</small>
                            </td>
                            <td>{{ $historial->monto_formateado }}</td>
                            <td>{!! $historial->variacion_badge !!}</td>
                            <td>{!! $historial->anomalia_badge !!}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('historial-consumo.show', $historial->id) }}"
                                       class="btn btn-sm btn-info"
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>No hay registros de historial para este socio</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(count($tendencia) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('tendenciaChart').getContext('2d');
    const tendenciaData = @json($tendencia);

    const promedio = {{ $estadisticas['consumo_promedio'] }};

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: tendenciaData.map(item => item.periodo),
            datasets: [
                {
                    label: 'Consumo (m³)',
                    data: tendenciaData.map(item => item.consumo_m3),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Promedio General',
                    data: tendenciaData.map(() => promedio),
                    borderColor: 'rgb(16, 185, 129)',
                    borderDash: [5, 5],
                    borderWidth: 2,
                    fill: false,
                    pointRadius: 0
                }
            ]
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
                            return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' m³';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Consumo (m³)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Período'
                    }
                }
            }
        }
    });
</script>
@endif

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        padding-bottom: 16px;
        border-bottom: 2px solid var(--gray-200);
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--gray-800);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title i {
        color: var(--primary);
    }

    .btn-group {
        display: flex;
        gap: 12px;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: var(--white);
        border-radius: 8px;
        padding: 20px;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stat-card.highlight {
        border: 2px solid var(--warning);
        background: linear-gradient(135deg, #fff 0%, #fffbeb 100%);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.bg-primary { background: var(--primary); }
    .stat-icon.bg-success { background: var(--success); }
    .stat-icon.bg-info { background: var(--info); }
    .stat-icon.bg-warning { background: var(--warning); }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-800);
    }

    .card {
        background: var(--white);
        border-radius: 8px;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        margin-bottom: 24px;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-body {
        padding: 24px;
    }

    .promedios-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .promedio-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        background: var(--gray-50);
        border-radius: 8px;
        border: 1px solid var(--gray-200);
    }

    .promedio-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .promedio-info {
        flex: 1;
    }

    .promedio-info label {
        font-size: 0.875rem;
        color: var(--gray-600);
        display: block;
        margin-bottom: 4px;
    }

    .promedio-info value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gray-800);
    }

    .chart-container {
        position: relative;
        height: 350px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background: var(--gray-50);
    }

    .table th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
        border-bottom: 2px solid var(--gray-200);
        font-size: 0.875rem;
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid var(--gray-200);
        font-size: 0.875rem;
        color: var(--gray-700);
    }

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .consumo-valor {
        color: var(--primary);
        font-weight: 700;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-success { background: var(--success-light); color: var(--success-dark); }
    .badge-warning { background: var(--warning-light); color: var(--warning-dark); }
    .badge-danger { background: var(--danger-light); color: var(--danger-dark); }
    .badge-info { background: var(--info-light); color: var(--info-dark); }
    .badge-secondary { background: var(--gray-200); color: var(--gray-700); }

    .action-buttons {
        display: flex;
        gap: 6px;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }

    .btn-sm {
        padding: 6px 10px;
        font-size: 0.75rem;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
    }

    .btn-secondary {
        background: var(--gray-500);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-600);
    }

    .btn-info {
        background: var(--info);
        color: white;
    }

    .btn-info:hover {
        background: var(--info-dark);
    }

    .text-muted {
        color: var(--gray-500);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--gray-400);
        margin-bottom: 16px;
    }

    .empty-state p {
        color: var(--gray-600);
        margin-bottom: 0;
    }

    .pagination-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }

    .mb-3 {
        margin-bottom: 24px;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .stats-row {
            grid-template-columns: 1fr;
        }

        .promedios-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
