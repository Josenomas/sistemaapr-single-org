@extends('layouts.app')

@section('title', 'Comparar Socios - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-exchange-alt"></i>
        Comparación de Pagos entre Socios
    </h2>
    <div class="btn-group">
        <a href="{{ route('historial-pagos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<!-- Formulario de Comparación -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter"></i>
            Seleccionar Socios y Período
        </h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('historial-pagos.comparar') }}" class="comparar-form">
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="socios_comparar">Socios a Comparar:</label>
                    <select id="socios_comparar"
                            name="socios_comparar[]"
                            class="form-control"
                            multiple
                            size="8"
                            required>
                        @foreach($socios as $s)
                            <option value="{{ $s->id }}"
                                {{ in_array($s->id, request('socios_comparar', [])) ? 'selected' : '' }}>
                                {{ $s->nombre_completo }} - {{ $s->rut }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text">Mantenga presionado Ctrl (Windows) o Cmd (Mac) para seleccionar múltiples socios</small>
                </div>

                <div class="form-group">
                    <label for="fecha_inicio">Fecha Inicio:</label>
                    <input type="date"
                           id="fecha_inicio"
                           name="fecha_inicio"
                           class="form-control"
                           value="{{ request('fecha_inicio') }}"
                           required>
                </div>

                <div class="form-group">
                    <label for="fecha_fin">Fecha Fin:</label>
                    <input type="date"
                           id="fecha_fin"
                           name="fecha_fin"
                           class="form-control"
                           value="{{ request('fecha_fin') }}"
                           required>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-chart-bar"></i>
                    Comparar
                </button>
                <a href="{{ route('historial-pagos.comparar') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

@if(isset($comparacion) && $comparacion !== null)
    <!-- Estadísticas Globales -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Socios</div>
                <div class="stat-value">{{ number_format($estadisticasComparacion['total_socios']) }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Monto Total Global</div>
                <div class="stat-value">${{ number_format($estadisticasComparacion['monto_total_global'], 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-info">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Promedio Global</div>
                <div class="stat-value">${{ number_format($estadisticasComparacion['promedio_global'], 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="stat-card highlight">
            <div class="stat-icon bg-warning">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Ranking</div>
                <div class="stat-detail">
                    <span class="ranking-badge mayor">
                        <i class="fas fa-arrow-up"></i>
                        Mayor: {{ $estadisticasComparacion['socio_mayor_pago'] }}
                    </span>
                    <span class="ranking-badge menor">
                        <i class="fas fa-arrow-down"></i>
                        Menor: {{ $estadisticasComparacion['socio_menor_pago'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Comparación -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-bar"></i>
                Comparación Visual de Montos Totales
            </h3>
        </div>
        <div class="card-body">
            <canvas id="comparacionChart"></canvas>
        </div>
    </div>

    <!-- Tabla de Comparación -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-table"></i>
                Resultados de la Comparación
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Socio</th>
                            <th>Total Pagos</th>
                            <th>Monto Total</th>
                            <th>Monto Promedio</th>
                            <th>Participación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comparacion as $resultado)
                            <tr>
                                <td>
                                    <strong>{{ $resultado->nombre_completo }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $resultado->rut }}</small>
                                </td>
                                <td>{{ number_format($resultado->total_pagos) }}</td>
                                <td>
                                    <strong class="monto-valor">${{ number_format($resultado->monto_total, 0, ',', '.') }}</strong>
                                </td>
                                <td>${{ number_format($resultado->monto_promedio, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $participacion = $estadisticasComparacion['monto_total_global'] > 0
                                            ? ($resultado->monto_total / $estadisticasComparacion['monto_total_global']) * 100
                                            : 0;
                                    @endphp
                                    <div class="participacion-container">
                                        <div class="participacion-bar">
                                            <div class="participacion-fill" style="width: {{ $participacion }}%"></div>
                                        </div>
                                        <span class="participacion-text">{{ number_format($participacion, 1) }}%</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('historial-pagos.analisis-socio', $resultado->id) }}"
                                       class="btn btn-sm btn-primary"
                                       title="Ver análisis">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p>No se encontraron pagos para los criterios seleccionados</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(isset($comparacion) && count($comparacion) > 0)
        const ctx = document.getElementById('comparacionChart');
        const comparacionData = @json($comparacion);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: comparacionData.map(item => item.nombre_completo),
                datasets: [{
                    label: 'Monto Total ($)',
                    data: comparacionData.map(item => item.monto_total),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(34, 197, 94, 0.7)',
                        'rgba(251, 146, 60, 0.7)',
                        'rgba(168, 85, 247, 0.7)',
                        'rgba(236, 72, 153, 0.7)',
                        'rgba(14, 165, 233, 0.7)',
                        'rgba(132, 204, 22, 0.7)',
                        'rgba(249, 115, 22, 0.7)',
                    ],
                    borderColor: [
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(251, 146, 60)',
                        'rgb(168, 85, 247)',
                        'rgb(236, 72, 153)',
                        'rgb(14, 165, 233)',
                        'rgb(132, 204, 22)',
                        'rgb(249, 115, 22)',
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Monto: $' + context.parsed.y.toLocaleString('es-CL');
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

    .comparar-form .form-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 6px;
        font-size: 0.875rem;
    }

    .form-control {
        padding: 8px 12px;
        border: 1px solid var(--gray-300);
        border-radius: 6px;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control[multiple] {
        padding: 4px;
    }

    .form-control[multiple] option {
        padding: 8px;
        border-radius: 4px;
        margin-bottom: 2px;
    }

    .form-control[multiple] option:hover {
        background: var(--primary-light);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-text {
        font-size: 0.75rem;
        color: var(--gray-500);
        margin-top: 4px;
    }

    .filter-actions {
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

    .stat-detail {
        margin-top: 8px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .ranking-badge {
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .ranking-badge.mayor {
        background: var(--success-light);
        color: var(--success-dark);
    }

    .ranking-badge.menor {
        background: var(--info-light);
        color: var(--info-dark);
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

    .participacion-container {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .participacion-bar {
        flex: 1;
        height: 20px;
        background: var(--gray-200);
        border-radius: 10px;
        overflow: hidden;
    }

    .participacion-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary), var(--primary-dark));
        transition: width 0.3s ease;
    }

    .participacion-text {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--gray-700);
        min-width: 45px;
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

        .comparar-form .form-row {
            grid-template-columns: 1fr;
        }

        .stats-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
