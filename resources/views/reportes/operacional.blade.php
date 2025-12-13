@extends('layouts.app')

@section('title', 'Reporte Operacional - Sistema APR')

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
        color: var(--danger);
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

    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--gray-200);
    }

    .section-header h3 {
        font-size: 1.25rem;
        color: var(--dark);
        font-weight: 700;
        margin: 0;
    }

    .section-header i {
        color: var(--primary);
        font-size: 1.125rem;
    }

    .stats-section {
        margin-bottom: 32px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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

    .stat-card.success::before {
        background: linear-gradient(180deg, var(--success), #059669);
    }

    .stat-card.warning::before {
        background: linear-gradient(180deg, var(--warning), #d97706);
    }

    .stat-card.danger::before {
        background: linear-gradient(180deg, var(--danger), #dc2626);
    }

    .stat-card.info::before {
        background: linear-gradient(180deg, #0ea5e9, #0284c7);
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

    .success-bg {
        background: linear-gradient(135deg, var(--success), #059669);
    }

    .warning-bg {
        background: linear-gradient(135deg, var(--warning), #d97706);
    }

    .danger-bg {
        background: linear-gradient(135deg, var(--danger), #dc2626);
    }

    .info-bg {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
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
        grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
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
        margin-bottom: 24px;
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

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-info {
        background: #e0f2fe;
        color: #075985;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-primary {
        background: #dbeafe;
        color: #1e40af;
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
            <i class="fas fa-cogs"></i>
            Reporte Operacional
        </h2>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('reportes.operacional.descargar', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-file-pdf"></i>
                Descargar PDF
            </a>
            <a href="{{ route('reportes.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Volver
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-card">
        <h3 class="filters-title">
            <i class="fas fa-filter"></i>
            Filtros de Período
        </h3>
        <form method="GET" action="{{ route('reportes.operacional') }}">
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
                    <a href="{{ route('reportes.operacional') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Sección Tickets -->
    <div class="stats-section">
        <div class="section-header">
            <i class="fas fa-ticket-alt"></i>
            <h3>Tickets de Soporte</h3>
        </div>
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-header">
                    <div class="stat-title">Total Tickets</div>
                    <div class="stat-icon primary-bg">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($ticketsEstadisticas['total'], 0, ',', '.') }}</div>
                <div class="stat-description">Período seleccionado</div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <div class="stat-title">Pendientes</div>
                    <div class="stat-icon warning-bg">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($ticketsEstadisticas['por_estado']['abierto'] ?? 0, 0, ',', '.') }}</div>
                <div class="stat-description">Sin atender</div>
            </div>

            <div class="stat-card info">
                <div class="stat-header">
                    <div class="stat-title">En Proceso</div>
                    <div class="stat-icon info-bg">
                        <i class="fas fa-sync"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($ticketsEstadisticas['por_estado']['en_proceso'] ?? 0, 0, ',', '.') }}</div>
                <div class="stat-description">En atención</div>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <div class="stat-title">Resueltos</div>
                    <div class="stat-icon success-bg">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($ticketsEstadisticas['por_estado']['resuelto'] ?? 0, 0, ',', '.') }}</div>
                <div class="stat-description">Completados</div>
            </div>
        </div>
    </div>

    <!-- Sección Trabajos -->
    <div class="stats-section">
        <div class="section-header">
            <i class="fas fa-wrench"></i>
            <h3>Trabajos Realizados</h3>
        </div>
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-header">
                    <div class="stat-title">Total Trabajos</div>
                    <div class="stat-icon primary-bg">
                        <i class="fas fa-wrench"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($trabajosEstadisticas['total'], 0, ',', '.') }}</div>
                <div class="stat-description">Período seleccionado</div>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <div class="stat-title">Completados</div>
                    <div class="stat-icon success-bg">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($trabajosEstadisticas['por_estado']['completado'] ?? 0, 0, ',', '.') }}</div>
                <div class="stat-description">Finalizados</div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <div class="stat-title">En Curso</div>
                    <div class="stat-icon warning-bg">
                        <i class="fas fa-spinner"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($trabajosEstadisticas['por_estado']['en_proceso'] ?? 0, 0, ',', '.') }}</div>
                <div class="stat-description">En ejecución</div>
            </div>
        </div>
    </div>

    <!-- Sección Cortes -->
    <div class="stats-section">
        <div class="section-header">
            <i class="fas fa-hand-paper"></i>
            <h3>Cortes de Servicio</h3>
        </div>
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-header">
                    <div class="stat-title">Total Cortes</div>
                    <div class="stat-icon primary-bg">
                        <i class="fas fa-hand-paper"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($cortesEstadisticas['total'], 0, ',', '.') }}</div>
                <div class="stat-description">Período seleccionado</div>
            </div>

            <div class="stat-card danger">
                <div class="stat-header">
                    <div class="stat-title">Activos</div>
                    <div class="stat-icon danger-bg">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($cortesEstadisticas['por_estado']['ejecutado'] ?? 0, 0, ',', '.') }}</div>
                <div class="stat-description">Sin reconectar</div>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <div class="stat-title">Reconectados</div>
                    <div class="stat-icon success-bg">
                        <i class="fas fa-plug"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($cortesEstadisticas['reconexiones'], 0, ',', '.') }}</div>
                <div class="stat-description">Servicio restaurado</div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="charts-grid">
        <div class="chart-card">
            <h4 class="chart-title">
                <i class="fas fa-chart-pie"></i>
                Tickets por Estado
            </h4>
            <canvas id="ticketsChart"></canvas>
        </div>

        <div class="chart-card">
            <h4 class="chart-title">
                <i class="fas fa-chart-bar"></i>
                Trabajos por Tipo
            </h4>
            <canvas id="trabajosChart"></canvas>
        </div>

        <div class="chart-card">
            <h4 class="chart-title">
                <i class="fas fa-chart-bar"></i>
                Cortes por Motivo
            </h4>
            <canvas id="cortesChart"></canvas>
        </div>
    </div>

    <!-- Tablas Resumen -->
    <div class="table-container">
        <h4 class="chart-title">
            <i class="fas fa-ticket-alt"></i>
            Resumen de Tickets
        </h4>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Socio</th>
                    <th>Asunto</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha Creación</th>
                    <th>Asignado a</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->id_ticket }}</td>
                    <td>{{ $ticket->nombre_completo }}</td>
                    <td>{{ $ticket->asunto }}</td>
                    <td>
                        @if($ticket->prioridad == 'alta')
                            <span class="badge badge-danger">Alta</span>
                        @elseif($ticket->prioridad == 'media')
                            <span class="badge badge-warning">Media</span>
                        @else
                            <span class="badge badge-info">Baja</span>
                        @endif
                    </td>
                    <td>
                        @if($ticket->estado == 'pendiente')
                            <span class="badge badge-warning">Pendiente</span>
                        @elseif($ticket->estado == 'en_proceso')
                            <span class="badge badge-info">En Proceso</span>
                        @elseif($ticket->estado == 'resuelto')
                            <span class="badge badge-success">Resuelto</span>
                        @else
                            <span class="badge badge-danger">Cerrado</span>
                        @endif
                    </td>
                    <td>{{ date('d/m/Y', strtotime($ticket->fecha_creacion)) }}</td>
                    <td>{{ $ticket->asignado_nombre ?? 'Sin asignar' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--gray-500);">No hay tickets disponibles</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-container">
        <h4 class="chart-title">
            <i class="fas fa-wrench"></i>
            Resumen de Trabajos
        </h4>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th>Funcionario</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trabajos as $trabajo)
                <tr>
                    <td>{{ $trabajo->id_trabajo }}</td>
                    <td>{{ $trabajo->descripcion }}</td>
                    <td>{{ ucfirst($trabajo->tipo) }}</td>
                    <td>{{ $trabajo->funcionario_nombre }}</td>
                    <td>{{ date('d/m/Y', strtotime($trabajo->fecha)) }}</td>
                    <td>
                        @if($trabajo->estado == 'completado')
                            <span class="badge badge-success">Completado</span>
                        @else
                            <span class="badge badge-warning">En Curso</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--gray-500);">No hay trabajos disponibles</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-container">
        <h4 class="chart-title">
            <i class="fas fa-hand-paper"></i>
            Resumen de Cortes
        </h4>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Socio</th>
                    <th>Motivo</th>
                    <th>Fecha Corte</th>
                    <th>Fecha Reconexión</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cortes as $corte)
                <tr>
                    <td>{{ $corte->id_corte }}</td>
                    <td>{{ $corte->nombre_completo }}</td>
                    <td>{{ ucfirst($corte->motivo) }}</td>
                    <td>{{ date('d/m/Y', strtotime($corte->fecha_corte)) }}</td>
                    <td>{{ $corte->fecha_reconexion ? date('d/m/Y', strtotime($corte->fecha_reconexion)) : '-' }}</td>
                    <td>
                        @if($corte->estado == 'activo')
                            <span class="badge badge-danger">Activo</span>
                        @else
                            <span class="badge badge-success">Reconectado</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--gray-500);">No hay cortes disponibles</td>
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
    // Gráfico de Tickets por Estado
    const ticketsCtx = document.getElementById('ticketsChart').getContext('2d');
    new Chart(ticketsCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($ticketsEstadisticas['por_estado']->toArray())) !!},
            datasets: [{
                data: {!! json_encode(array_values($ticketsEstadisticas['por_estado']->toArray())) !!},
                backgroundColor: [
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(14, 165, 233, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
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

    // Gráfico de Trabajos por Tipo
    const trabajosCtx = document.getElementById('trabajosChart').getContext('2d');
    new Chart(trabajosCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($trabajosEstadisticas['por_tipo']->toArray())) !!},
            datasets: [{
                label: 'Cantidad',
                data: {!! json_encode(array_values($trabajosEstadisticas['por_tipo']->toArray())) !!},
                backgroundColor: [
                    'rgba(37, 99, 235, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
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

    // Gráfico de Cortes por Motivo
    const cortesCtx = document.getElementById('cortesChart').getContext('2d');
    new Chart(cortesCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($cortesEstadisticas['por_motivo']->toArray())) !!},
            datasets: [{
                label: 'Cantidad',
                data: {!! json_encode(array_values($cortesEstadisticas['por_motivo']->toArray())) !!},
                backgroundColor: [
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(37, 99, 235, 0.8)'
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
</script>
@endsection
