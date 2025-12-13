@extends('layouts.app')

@section('title', 'Reporte de Consumo - Sistema APR')

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
        color: #0ea5e9;
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
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

    .stat-card.primary::before {
        background: linear-gradient(180deg, var(--primary), var(--primary-dark));
    }

    .stat-card.info::before {
        background: linear-gradient(180deg, #0ea5e9, #0284c7);
    }

    .stat-card.success::before {
        background: linear-gradient(180deg, var(--success), #059669);
    }

    .stat-card.warning::before {
        background: linear-gradient(180deg, var(--warning), #d97706);
    }

    .stat-card.danger::before {
        background: linear-gradient(180deg, var(--danger), #dc2626);
    }

    .stat-card.purple::before {
        background: linear-gradient(180deg, #8b5cf6, #7c3aed);
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

    .primary-bg {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .info-bg {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
    }

    .success-bg {
        background: linear-gradient(135deg, var(--success), #059669);
    }

    .warning-bg {
        background: linear-gradient(135deg, var(--warning), #d97706);
    }

    .danger-bg {
        background: linear-gradient(135deg, var(--danger), #dc2626);
    }

    .purple-bg {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
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

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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
            <i class="fas fa-tint"></i>
            Reporte de Consumo
        </h2>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('reportes.consumo.descargar', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-file-pdf"></i>
                Descargar PDF
            </a>
            <a href="{{ route('reportes.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Volver
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-header">
                <div class="stat-title">Total Registros</div>
                <div class="stat-icon primary-bg">
                    <i class="fas fa-list"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($estadisticas['total_registros'], 0, ',', '.') }}</div>
            <div class="stat-description">Lecturas del período</div>
        </div>

        <div class="stat-card info">
            <div class="stat-header">
                <div class="stat-title">Consumo Total</div>
                <div class="stat-icon info-bg">
                    <i class="fas fa-tint"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($estadisticas['consumo_total'], 0, ',', '.') }}</div>
            <div class="stat-description">m³ consumidos</div>
        </div>

        <div class="stat-card success">
            <div class="stat-header">
                <div class="stat-title">Promedio</div>
                <div class="stat-icon success-bg">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($estadisticas['consumo_promedio'], 2, ',', '.') }}</div>
            <div class="stat-description">m³ por socio</div>
        </div>

        <div class="stat-card warning">
            <div class="stat-header">
                <div class="stat-title">Consumo Máximo</div>
                <div class="stat-icon warning-bg">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($estadisticas['consumo_maximo'], 2, ',', '.') }}</div>
            <div class="stat-description">m³ en un solo socio</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <div class="stat-title">Consumo Mínimo</div>
                <div class="stat-icon purple-bg">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($estadisticas['consumo_minimo'], 2, ',', '.') }}</div>
            <div class="stat-description">m³ en un solo socio</div>
        </div>

        <div class="stat-card danger">
            <div class="stat-header">
                <div class="stat-title">Anomalías</div>
                <div class="stat-icon danger-bg">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($estadisticas['anomalias']['alto'] + $estadisticas['anomalias']['bajo'] + $estadisticas['anomalias']['cero'], 0, ',', '.') }}</div>
            <div class="stat-description">Consumos anormales</div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-card">
        <h3 class="filters-title">
            <i class="fas fa-filter"></i>
            Filtros de Período
        </h3>
        <form method="GET" action="{{ route('reportes.consumo') }}">
            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">Período (Mes)</label>
                    <input type="month" name="periodo" class="form-control"
                           value="{{ request('periodo', $periodo) }}">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filtrar
                    </button>
                </div>

                <div class="form-group">
                    <a href="{{ route('reportes.consumo') }}" class="btn btn-secondary">
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
                <i class="fas fa-chart-bar"></i>
                Distribución de Consumo por Rangos
            </h4>
            <canvas id="distribucionChart"></canvas>
        </div>

        <div class="chart-card">
            <h4 class="chart-title">
                <i class="fas fa-chart-pie"></i>
                Anomalías Detectadas
            </h4>
            <canvas id="anomaliasChart"></canvas>
        </div>
    </div>

    <!-- Tabla de Consumos -->
    <div class="table-container">
        <h4 class="chart-title">
            <i class="fas fa-fire"></i>
            Consumos del Período (Ordenado por Mayor Consumo)
        </h4>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Socio</th>
                    <th>Sector</th>
                    <th>Medidor</th>
                    <th>Lectura Anterior</th>
                    <th>Lectura Actual</th>
                    <th>Consumo (m³)</th>
                    <th>Monto</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($consumos as $index => $consumo)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $consumo->socio->nombre_completo }}</td>
                    <td>{{ $consumo->socio->sector ?? 'N/A' }}</td>
                    <td>{{ $consumo->socio->numero_medidor ?? 'N/A' }}</td>
                    <td>{{ number_format($consumo->lectura_anterior, 2, ',', '.') }}</td>
                    <td>{{ number_format($consumo->lectura_actual, 2, ',', '.') }}</td>
                    <td><strong>{{ number_format($consumo->consumo_m3, 2, ',', '.') }}</strong></td>
                    <td>${{ number_format($consumo->monto_consumo, 0, ',', '.') }}</td>
                    <td>
                        @if($consumo->anomalia == 'alto')
                            <span class="badge badge-danger">Alto</span>
                        @elseif($consumo->anomalia == 'bajo')
                            <span class="badge badge-warning">Bajo</span>
                        @elseif($consumo->anomalia == 'cero')
                            <span class="badge badge-secondary">Cero</span>
                        @else
                            <span class="badge badge-success">Normal</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: var(--gray-500);">No hay consumos disponibles</td>
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
    // Gráfico de Distribución de Consumo por Rangos
    const distribucionCtx = document.getElementById('distribucionChart').getContext('2d');
    new Chart(distribucionCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($distribucion)) !!},
            datasets: [{
                label: 'Cantidad de Socios',
                data: {!! json_encode(array_values($distribucion)) !!},
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(37, 99, 235, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)'
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
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Gráfico de Anomalías
    const anomaliasCtx = document.getElementById('anomaliasChart').getContext('2d');
    new Chart(anomaliasCtx, {
        type: 'doughnut',
        data: {
            labels: ['Normal', 'Alto', 'Bajo', 'Cero'],
            datasets: [{
                data: [
                    {{ $consumos->where('anomalia', 'normal')->count() }},
                    {{ $estadisticas['anomalias']['alto'] }},
                    {{ $estadisticas['anomalias']['bajo'] }},
                    {{ $estadisticas['anomalias']['cero'] }}
                ],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(107, 114, 128, 0.8)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endsection
