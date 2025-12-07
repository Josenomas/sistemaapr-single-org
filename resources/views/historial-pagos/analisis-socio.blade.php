@extends('layouts.app')

@section('title', 'Análisis de Pagos del Socio - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-chart-line"></i>
        Análisis de Pagos - {{ $socio->nombre_completo }}
    </h2>
    <div class="btn-group">
        <a href="{{ route('historial-pagos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<!-- Estadísticas del Socio -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-receipt"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Pagos</div>
            <div class="stat-value">{{ number_format($estadisticas['total_pagos']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Monto Total Pagado</div>
            <div class="stat-value">${{ number_format($estadisticas['monto_total_pagado'], 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-chart-bar"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Monto Promedio</div>
            <div class="stat-value">${{ number_format($estadisticas['monto_promedio'], 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-warning">
            <i class="fas fa-arrow-up"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Monto Máximo</div>
            <div class="stat-value">${{ number_format($estadisticas['monto_maximo'], 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-secondary">
            <i class="fas fa-arrow-down"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Monto Mínimo</div>
            <div class="stat-value">${{ number_format($estadisticas['monto_minimo'], 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="stat-card highlight">
        <div class="stat-icon bg-primary">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Último Pago</div>
            <div class="stat-value stat-value-small">
                @if($estadisticas['ultimo_pago'])
                    {{ $estadisticas['ultimo_pago']->fecha_pago->format('d/m/Y') }}
                @else
                    Sin pagos
                @endif
            </div>
            <div class="stat-detail">
                <span class="info-badge">
                    <i class="fas fa-check-circle"></i>
                    Puntualidad: {{ number_format($estadisticas['puntualidad'], 1) }}%
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico de Tendencia -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-area"></i>
            Tendencia de Pagos - Últimos 12 Meses
        </h3>
    </div>
    <div class="card-body">
        <canvas id="tendenciaChart"></canvas>
    </div>
</div>

<!-- Resumen Anual -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-calendar-alt"></i>
            Resumen del Último Año
        </h3>
    </div>
    <div class="card-body">
        <div class="resumen-grid">
            <div class="resumen-item">
                <i class="fas fa-receipt"></i>
                <div>
                    <div class="resumen-label">Pagos Realizados</div>
                    <div class="resumen-value">{{ number_format($estadisticas['pagos_ultimo_año']) }}</div>
                </div>
            </div>
            <div class="resumen-item">
                <i class="fas fa-dollar-sign"></i>
                <div>
                    <div class="resumen-label">Monto Total</div>
                    <div class="resumen-value">${{ number_format($estadisticas['monto_ultimo_año'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="resumen-item">
                <i class="fas fa-chart-line"></i>
                <div>
                    <div class="resumen-label">Promedio Mensual</div>
                    <div class="resumen-value">${{ number_format($estadisticas['promedio_mensual'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="resumen-item">
                <i class="fas fa-credit-card"></i>
                <div>
                    <div class="resumen-label">Método Preferido</div>
                    <div class="resumen-value">{{ ucfirst($estadisticas['metodo_preferido'] ?? 'N/A') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Historial Completo -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-history"></i>
            Historial Completo de Pagos
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>N° Comprobante</th>
                        <th>Boleta</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagos as $pago)
                        <tr>
                            <td>
                                <span class="badge badge-secondary">
                                    <i class="fas fa-calendar"></i>
                                    {{ $pago->fecha_pago->format('d/m/Y') }}
                                </span>
                            </td>
                            <td>{{ $pago->numero_recibo }}</td>
                            <td>
                                @if($pago->boleta)
                                    <span class="badge badge-info">
                                        <i class="fas fa-file-invoice"></i>
                                        {{ $pago->boleta->numero_boleta }}
                                    </span>
                                @else
                                    <small class="text-muted">Sin boleta</small>
                                @endif
                            </td>
                            <td>
                                <strong class="monto-valor">${{ number_format($pago->monto_pagado, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                @if($pago->metodo_pago == 'efectivo')
                                    <span class="badge badge-primary">
                                        <i class="fas fa-money-bill"></i> Efectivo
                                    </span>
                                @elseif($pago->metodo_pago == 'transferencia')
                                    <span class="badge badge-info">
                                        <i class="fas fa-exchange-alt"></i> Transferencia
                                    </span>
                                @elseif($pago->metodo_pago == 'cheque')
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-money-check"></i> Cheque
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($pago->estado_pago == 'pagado')
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Pagado
                                    </span>
                                @else
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clock"></i> Pendiente
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('historial-pagos.show', $pago->id) }}"
                                   class="btn btn-sm btn-info"
                                   title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>No hay pagos registrados para este socio</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('tendenciaChart');

        @if(count($tendencia) > 0)
        const tendenciaData = @json($tendencia);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: tendenciaData.map(item => item.periodo),
                datasets: [
                    {
                        label: 'Monto Total ($)',
                        data: tendenciaData.map(item => item.monto_total),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Cantidad de Pagos',
                        data: tendenciaData.map(item => item.total_pagos),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
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
                                if (context.parsed.y !== null) {
                                    if (context.datasetIndex === 0) {
                                        label += '$' + context.parsed.y.toLocaleString('es-CL');
                                    } else {
                                        label += context.parsed.y;
                                    }
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Monto ($)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-CL');
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Cantidad'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
        @else
        ctx.parentElement.innerHTML = '<div class="empty-state"><i class="fas fa-chart-line"></i><p>No hay datos suficientes para mostrar la tendencia</p></div>';
        @endif
    });
</script>

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
        border: 2px solid var(--primary);
        background: linear-gradient(135deg, #fff 0%, #eff6ff 100%);
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
    .stat-icon.bg-secondary { background: var(--gray-500); }

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

    .stat-value-small {
        font-size: 1.125rem;
    }

    .stat-detail {
        margin-top: 8px;
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .info-badge {
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 600;
        background: var(--success-light);
        color: var(--success-dark);
    }

    .card {
        background: var(--white);
        border-radius: 8px;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        margin-bottom: 24px;
    }

    .card-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    .card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-700);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-body {
        padding: 24px;
    }

    .resumen-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .resumen-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        background: var(--gray-50);
        border-radius: 8px;
    }

    .resumen-item i {
        font-size: 2rem;
        color: var(--primary);
    }

    .resumen-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: 4px;
    }

    .resumen-value {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--gray-800);
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

    .monto-valor {
        color: var(--success);
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
    .badge-primary { background: var(--primary-light); color: var(--primary-dark); }

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

    .btn-sm {
        padding: 6px 10px;
        font-size: 0.75rem;
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
        margin-bottom: 16px;
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

        .resumen-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
