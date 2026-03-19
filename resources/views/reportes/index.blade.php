@extends('layouts.app')

@section('title', 'Centro de Reportes - Sistema APR')

@section('styles')
<style>
    .reports-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .reports-title {
        font-size: 2rem;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        margin: 0;
    }

    .reports-title i {
        color: var(--primary);
        font-size: 1.75rem;
    }

    .stats-section {
        margin-bottom: 32px;
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
        margin-bottom: 32px;
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

    .badge-info {
        background: #e0f2fe;
        color: #075985;
    }

    .quick-reports-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .quick-report-btn {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        background: var(--white);
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        text-decoration: none;
        color: var(--dark);
        transition: all 0.3s;
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
    }

    .quick-report-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        transition: width 0.3s;
    }

    .quick-report-btn:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary);
    }

    .quick-report-btn:hover::before {
        width: 100%;
        opacity: 0.05;
    }

    .quick-report-btn.primary::before {
        background: var(--primary);
    }

    .quick-report-btn.success::before {
        background: var(--success);
    }

    .quick-report-btn.warning::before {
        background: var(--warning);
    }

    .quick-report-btn.danger::before {
        background: var(--danger);
    }

    .quick-report-icon {
        width: 56px;
        height: 56px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
        flex-shrink: 0;
    }

    .quick-report-content {
        flex: 1;
    }

    .quick-report-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 4px;
    }

    .quick-report-description {
        font-size: 0.8125rem;
        color: var(--gray-500);
    }

    @media (max-width: 768px) {
        .charts-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }

        .reports-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    .btn-info {
        background: #06b6d4;
        color: white;
        padding: 10px 20px;
        border-radius: var(--radius);
        border: none;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-info:hover {
        background: #0891b2;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* Estilos personalizados para Intro.js */
    .custom-tooltip {
        max-width: 400px;
    }

    .introjs-tooltip {
        border-radius: 12px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2) !important;
    }

    .introjs-button {
        border-radius: 6px !important;
        padding: 8px 16px !important;
        font-weight: 600 !important;
        text-shadow: none !important;
    }

    .introjs-nextbutton {
        background: var(--primary) !important;
        border: none !important;
    }

    .introjs-prevbutton {
        background: var(--gray-500) !important;
        border: none !important;
    }

    .introjs-skipbutton {
        color: var(--gray-600) !important;
    }

    .introjs-donebutton {
        background: var(--success) !important;
        border: none !important;
    }
</style>
@endsection

@section('content')
<div class="reports-container">
    <div class="reports-header">
        <h2 class="reports-title">
            <i class="fas fa-chart-bar"></i>
            Centro de Reportes
        </h2>
        <button id="startTourBtn" class="btn btn-info" title="Iniciar tutorial">
            <i class="fas fa-question-circle"></i>
            Ayuda
        </button>
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-section" data-intro="Panel de estadísticas generales del sistema: total de socios, boletas emitidas, tickets abiertos y cortes activos." data-step="1">
        <div class="section-header">
            <i class="fas fa-chart-line"></i>
            <h3>Estadísticas Generales</h3>
        </div>
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-header">
                    <div class="stat-title">Total Socios</div>
                    <div class="stat-icon primary-bg">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($estadisticasGenerales['total_socios'], 0, ',', '.') }}</div>
                <div class="stat-description">{{ $estadisticasGenerales['total_socios'] }} activos</div>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <div class="stat-title">Boletas Este Mes</div>
                    <div class="stat-icon success-bg">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($estadisticasFinancieras['boletas_emitidas_mes'], 0, ',', '.') }}</div>
                <div class="stat-description">Mes actual</div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <div class="stat-title">Tickets Abiertos</div>
                    <div class="stat-icon warning-bg">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($estadisticasGenerales['tickets_abiertos'], 0, ',', '.') }}</div>
                <div class="stat-description">Requieren atención</div>
            </div>

            <div class="stat-card danger">
                <div class="stat-header">
                    <div class="stat-title">Cortes Activos</div>
                    <div class="stat-icon danger-bg">
                        <i class="fas fa-hand-paper"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($estadisticasOperacionales['cortes_activos'], 0, ',', '.') }}</div>
                <div class="stat-description">Por falta de pago</div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Financieras -->
    <div class="stats-section" data-intro="Estadísticas financieras: ingresos del mes, pagos recibidos, deuda total pendiente y porcentaje de morosidad." data-step="2">
        <div class="section-header">
            <i class="fas fa-dollar-sign"></i>
            <h3>Estadísticas Financieras</h3>
        </div>
        <div class="stats-grid">
            <div class="stat-card success">
                <div class="stat-header">
                    <div class="stat-title">Ingresos del Mes</div>
                    <div class="stat-icon success-bg">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
                <div class="stat-value">${{ number_format($estadisticasFinancieras['ingresos_mes'], 0, ',', '.') }}</div>
                <div class="stat-description">Mes actual</div>
            </div>

            <div class="stat-card danger">
                <div class="stat-header">
                    <div class="stat-title">Egresos del Mes</div>
                    <div class="stat-icon danger-bg">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                </div>
                <div class="stat-value">${{ number_format($estadisticasFinancieras['compras_mes'], 0, ',', '.') }}</div>
                <div class="stat-description">Mes actual</div>
            </div>

            <div class="stat-card info">
                <div class="stat-header">
                    <div class="stat-title">Balance del Mes</div>
                    <div class="stat-icon info-bg">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                </div>
                <div class="stat-value">${{ number_format($estadisticasFinancieras['ingresos_mes'] - $estadisticasFinancieras['compras_mes'], 0, ',', '.') }}</div>
                <div class="stat-description">Ingresos - Egresos</div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <div class="stat-title">Pagos Pendientes</div>
                    <div class="stat-icon warning-bg">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-value">${{ number_format($estadisticasFinancieras['pagos_pendientes'], 0, ',', '.') }}</div>
                <div class="stat-description">Por cobrar</div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="charts-grid" data-intro="Gráficos visuales con tendencias: ingresos mensuales, consumo de agua, tickets por estado y métodos de pago utilizados." data-step="3">
        <div class="chart-card">
            <h4 class="chart-title">
                <i class="fas fa-chart-line"></i>
                Ingresos por Mes
            </h4>
            <canvas id="ingresosChart"></canvas>
        </div>

        <div class="chart-card">
            <h4 class="chart-title">
                <i class="fas fa-chart-bar"></i>
                Consumo por Mes
            </h4>
            <canvas id="consumoChart"></canvas>
        </div>

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
                Pagos por Método
            </h4>
            <canvas id="pagosChart"></canvas>
        </div>
    </div>

    <!-- Top 10 Consumidores -->
    <div class="table-container" data-intro="Ranking de los 10 socios con mayor consumo de agua en el mes actual." data-step="4">
        <h4 class="chart-title">
            <i class="fas fa-fire"></i>
            Top 10 Consumidores del Mes
        </h4>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Socio</th>
                    <th>Sector</th>
                    <th>Consumo (m³)</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topConsumidores as $index => $consumidor)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $consumidor->socio->nombre_completo }}</td>
                    <td>{{ $consumidor->socio->sector ?? 'N/A' }}</td>
                    <td>{{ number_format($consumidor->consumo_m3, 2, ',', '.') }}</td>
                    <td>${{ number_format($consumidor->monto_consumo, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--gray-500);">No hay datos disponibles</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Top 10 Deudores -->
    <div class="table-container">
        <h4 class="chart-title">
            <i class="fas fa-exclamation-triangle"></i>
            Top 10 Deudores
        </h4>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Socio</th>
                    <th>Sector</th>
                    <th>Boletas Pendientes</th>
                    <th>Deuda Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topDeudores as $index => $deudor)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $deudor->socio->nombre_completo }}</td>
                    <td>{{ $deudor->socio->sector ?? 'N/A' }}</td>
                    <td>{{ $deudor->socio->boletas()->where('estado', 'pendiente')->count() }}</td>
                    <td>${{ number_format($deudor->deuda_total, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge badge-warning">Pendiente</span>
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

    <!-- Acceso Rápido a Reportes -->
    <div class="section-header">
        <i class="fas fa-bolt"></i>
        <h3>Acceso Rápido a Reportes</h3>
    </div>
    <div class="quick-reports-grid">
        <a href="{{ route('reportes.socios') }}" class="quick-report-btn primary">
            <div class="quick-report-icon primary-bg">
                <i class="fas fa-users"></i>
            </div>
            <div class="quick-report-content">
                <div class="quick-report-title">Reporte de Socios</div>
                <div class="quick-report-description">Estadísticas y listado de socios</div>
            </div>
        </a>

        <a href="{{ route('reportes.financiero') }}" class="quick-report-btn success">
            <div class="quick-report-icon success-bg">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="quick-report-content">
                <div class="quick-report-title">Reporte Financiero</div>
                <div class="quick-report-description">Ingresos, egresos y balance</div>
            </div>
        </a>

        <a href="{{ route('reportes.consumo') }}" class="quick-report-btn warning">
            <div class="quick-report-icon warning-bg">
                <i class="fas fa-tint"></i>
            </div>
            <div class="quick-report-content">
                <div class="quick-report-title">Reporte de Consumo</div>
                <div class="quick-report-description">Análisis de consumos de agua</div>
            </div>
        </a>

        <a href="{{ route('reportes.operacional') }}" class="quick-report-btn danger">
            <div class="quick-report-icon danger-bg">
                <i class="fas fa-cogs"></i>
            </div>
            <div class="quick-report-content">
                <div class="quick-report-title">Reporte Operacional</div>
                <div class="quick-report-description">Tickets, trabajos y cortes</div>
            </div>
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuración común de gráficos
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            }
        }
    };

    // Gráfico de Ingresos por Mes
    const ingresosCtx = document.getElementById('ingresosChart').getContext('2d');
    new Chart(ingresosCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($ingresosPorMes, 'mes')) !!},
            datasets: [{
                label: 'Ingresos',
                data: {!! json_encode(array_column($ingresosPorMes, 'total')) !!},
                borderColor: 'rgb(37, 99, 235)',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            ...commonOptions,
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

    // Gráfico de Consumo por Mes
    const consumoCtx = document.getElementById('consumoChart').getContext('2d');
    new Chart(consumoCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($consumoPorMes, 'mes')) !!},
            datasets: [{
                label: 'Consumo (m³)',
                data: {!! json_encode(array_column($consumoPorMes, 'total')) !!},
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: 'rgb(16, 185, 129)',
                borderWidth: 1
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de Tickets por Estado
    const ticketsCtx = document.getElementById('ticketsChart').getContext('2d');
    new Chart(ticketsCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($ticketsPorEstado->pluck('estado')->toArray()) !!},
            datasets: [{
                data: {!! json_encode($ticketsPorEstado->pluck('total')->toArray()) !!},
                backgroundColor: [
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(37, 99, 235, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: commonOptions
    });

    // Gráfico de Pagos por Método
    const pagosCtx = document.getElementById('pagosChart').getContext('2d');
    new Chart(pagosCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($pagosPorMetodo->pluck('metodo_pago')->toArray()) !!},
            datasets: [{
                label: 'Monto',
                data: {!! json_encode($pagosPorMetodo->pluck('monto')->toArray()) !!},
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
            ...commonOptions,
            indexAxis: 'y',
            scales: {
                x: {
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

    // Tour con Intro.js
    const intro = introJs();
    intro.setOptions({
        nextLabel: 'Siguiente',
        prevLabel: 'Anterior',
        doneLabel: 'Finalizar',
        skipLabel: 'Salir',
        showProgress: true,
        showBullets: false,
        exitOnOverlayClick: false,
        disableInteraction: true,
        tooltipClass: 'custom-tooltip'
    });

    document.getElementById('startTourBtn').addEventListener('click', function() {
        intro.start();
    });

    const tourShown = localStorage.getItem('reportesTourShown');
    if (!tourShown) {
        setTimeout(function() {
            intro.start();
            localStorage.setItem('reportesTourShown', 'true');
        }, 500);
    }
</script>
@endsection
