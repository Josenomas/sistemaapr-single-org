@extends('layouts.app')

@section('title', 'Comparar Consumo entre Socios - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-exchange-alt"></i>
        Comparar Consumo entre Socios
    </h2>
    <div class="btn-group">
        <a href="{{ route('historial-consumo.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<!-- Formulario de Comparación -->
<div class="card mb-3">
    <div class="card-body">
        <h3 class="filter-title">
            <i class="fas fa-sliders-h"></i>
            Seleccionar Socios y Período
        </h3>
        <form method="GET" action="{{ route('historial-consumo.comparar') }}" class="comparison-form">
            <div class="form-row">
                <div class="form-group form-group-large">
                    <label for="socios">Socios a Comparar:</label>
                    <select id="socios" name="socios_comparar[]" class="form-control" multiple required size="8">
                        @foreach($socios as $s)
                            <option value="{{ $s->id }}"
                                {{ in_array($s->id, request('socios_comparar', [])) ? 'selected' : '' }}>
                                {{ $s->nombre_completo }} - {{ $s->rut }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-help">Mantén presionada la tecla Ctrl (Cmd en Mac) para seleccionar múltiples socios</small>
                </div>

                <div class="form-group">
                    <label for="periodo">Período:</label>
                    <select id="periodo" name="periodo" class="form-control" required>
                        <option value="">Seleccione un período</option>
                        @foreach($periodos as $p)
                            <option value="{{ $p }}" {{ request('periodo') == $p ? 'selected' : '' }}>
                                {{ $p }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-chart-bar"></i>
                    Comparar
                </button>
                <a href="{{ route('historial-consumo.comparar') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

@if(isset($comparacion) && count($comparacion) > 0)
    <!-- Estadísticas de Comparación -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Socios Comparados</div>
                <div class="stat-value">{{ count($comparacion) }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Promedio del Grupo</div>
                <div class="stat-value">{{ number_format($estadisticasComparacion['promedio_grupo'], 2) }} m³</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-info">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Consumo Máximo</div>
                <div class="stat-value">{{ number_format($estadisticasComparacion['maximo'], 2) }} m³</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Consumo Mínimo</div>
                <div class="stat-value">{{ number_format($estadisticasComparacion['minimo'], 2) }} m³</div>
            </div>
        </div>
    </div>

    <!-- Estadística Adicional -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="estadistica-destacada">
                <div class="estadistica-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="estadistica-info">
                    <label>Desviación Estándar</label>
                    <value>{{ number_format($estadisticasComparacion['desviacion'], 2) }} m³</value>
                    <small>Indica la variabilidad del consumo entre los socios seleccionados</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Comparación -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-bar"></i>
                Comparación Visual de Consumo
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="comparacionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabla de Comparación -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-table"></i>
                Detalle de Comparación - Período: {{ request('periodo') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Socio</th>
                            <th>RUT</th>
                            <th>Lectura Anterior</th>
                            <th>Lectura Actual</th>
                            <th>Consumo (m³)</th>
                            <th>Desviación vs Promedio</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($comparacion as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->socio->nombre_completo }}</strong>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $item->socio->rut }}</small>
                                </td>
                                <td>{{ number_format($item->lectura_anterior, 2) }} m³</td>
                                <td>{{ number_format($item->lectura_actual, 2) }} m³</td>
                                <td>
                                    <strong class="consumo-valor">{{ $item->consumo_formateado }}</strong>
                                </td>
                                <td>
                                    @php
                                        $desviacion = $item->consumo - $estadisticasComparacion['promedio_grupo'];
                                        $porcentaje = $estadisticasComparacion['promedio_grupo'] > 0
                                            ? ($desviacion / $estadisticasComparacion['promedio_grupo']) * 100
                                            : 0;
                                    @endphp
                                    @if($desviacion > 0)
                                        <span class="badge badge-warning">
                                            <i class="fas fa-arrow-up"></i>
                                            +{{ number_format(abs($desviacion), 2) }} m³ (+{{ number_format(abs($porcentaje), 1) }}%)
                                        </span>
                                    @elseif($desviacion < 0)
                                        <span class="badge badge-info">
                                            <i class="fas fa-arrow-down"></i>
                                            -{{ number_format(abs($desviacion), 2) }} m³ (-{{ number_format(abs($porcentaje), 1) }}%)
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-equals"></i>
                                            En promedio
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $item->monto_formateado }}</td>
                                <td>{!! $item->anomalia_badge !!}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('historial-consumo.show', $item->id) }}"
                                           class="btn btn-sm btn-info"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('historial-consumo.analisis-socio', $item->id_socio) }}"
                                           class="btn btn-sm btn-primary"
                                           title="Análisis del socio">
                                            <i class="fas fa-chart-line"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-summary">
                            <td colspan="4"><strong>Promedio del Grupo</strong></td>
                            <td><strong class="consumo-valor">{{ number_format($estadisticasComparacion['promedio_grupo'], 2) }} m³</strong></td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@elseif(request('periodo') && request('socios'))
    <!-- Sin resultados -->
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <p>No se encontraron registros para los socios y período seleccionados</p>
                <small class="text-muted">Intente con otros socios o un período diferente</small>
            </div>
        </div>
    </div>
@else
    <!-- Estado inicial -->
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <i class="fas fa-chart-bar"></i>
                <p>Seleccione los socios y el período para realizar la comparación</p>
                <small class="text-muted">Puede comparar el consumo de múltiples socios en un mismo período</small>
            </div>
        </div>
    </div>
@endif

@if(isset($comparacion) && count($comparacion) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('comparacionChart').getContext('2d');
    const comparacionData = @json($comparacion);
    const promedio = {{ $estadisticasComparacion['promedio_grupo'] }};

    console.log('Datos de comparación:', comparacionData);
    console.log('Promedio grupo:', promedio);

    // Preparar datos para el gráfico
    const labels = [];
    const consumos = [];

    comparacionData.forEach(item => {
        console.log('Item:', item);
        const nombreCompleto = item.socio?.nombre_completo || item.socio?.nombre || 'Sin nombre';
        const consumo = parseFloat(item.consumo_m3) || 0;
        labels.push(nombreCompleto);
        consumos.push(consumo);
        console.log(`${nombreCompleto}: ${consumo} m³`);
    });

    console.log('Labels:', labels);
    console.log('Consumos:', consumos);

    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Consumo (m³)',
                    data: consumos,
                    backgroundColor: consumos.map(consumo => {
                        if (consumo > promedio * 1.2) return 'rgba(245, 158, 11, 0.7)';
                        if (consumo < promedio * 0.8) return 'rgba(59, 130, 246, 0.7)';
                        return 'rgba(16, 185, 129, 0.7)';
                    }),
                    borderColor: consumos.map(consumo => {
                        if (consumo > promedio * 1.2) return 'rgb(245, 158, 11)';
                        if (consumo < promedio * 0.8) return 'rgb(59, 130, 246)';
                        return 'rgb(16, 185, 129)';
                    }),
                    borderWidth: 2
                },
                {
                    label: 'Promedio del Grupo',
                    data: comparacionData.map(() => promedio),
                    type: 'line',
                    borderColor: 'rgb(239, 68, 68)',
                    borderDash: [5, 5],
                    borderWidth: 2,
                    fill: false,
                    pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Consumo (m³)') {
                                return 'Consumo: ' + context.parsed.y.toFixed(2) + ' m³';
                            }
                            return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' m³';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: Math.max(...consumos) * 1.2,
                    title: {
                        display: true,
                        text: 'Consumo (m³)'
                    },
                    ticks: {
                        precision: 1
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Socios'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
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

    .filter-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .comparison-form .form-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group-large {
        grid-column: 1;
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

    .form-control option {
        padding: 6px 8px;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-help {
        font-size: 0.75rem;
        color: var(--gray-500);
        margin-top: 4px;
        font-style: italic;
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

    .estadistica-destacada {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px;
        background: var(--gray-50);
        border-radius: 8px;
        border: 1px solid var(--gray-200);
    }

    .estadistica-icon {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        background: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.75rem;
        flex-shrink: 0;
    }

    .estadistica-info {
        flex: 1;
    }

    .estadistica-info label {
        font-size: 0.875rem;
        color: var(--gray-600);
        display: block;
        margin-bottom: 4px;
        font-weight: 600;
    }

    .estadistica-info value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--gray-800);
        display: block;
        margin-bottom: 4px;
    }

    .estadistica-info small {
        font-size: 0.75rem;
        color: var(--gray-500);
    }

    .chart-container {
        position: relative;
        height: 400px;
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

    .table-summary {
        background: var(--gray-50);
        font-weight: 600;
    }

    .table-summary td {
        border-top: 2px solid var(--gray-300);
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
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 3.5rem;
        color: var(--gray-400);
        margin-bottom: 20px;
    }

    .empty-state p {
        color: var(--gray-700);
        font-size: 1.125rem;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .empty-state small {
        color: var(--gray-500);
        font-size: 0.875rem;
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

        .comparison-form .form-row {
            grid-template-columns: 1fr;
        }

        .stats-row {
            grid-template-columns: 1fr;
        }

        .chart-container {
            height: 300px;
        }
    }
</style>
@endsection
