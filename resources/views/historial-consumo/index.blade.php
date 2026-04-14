@extends('layouts.app')

@section('title', 'Historial de Consumo - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-chart-line"></i>
        Historial de Consumo de Agua
    </h2>
    <div class="btn-group">
        <button type="button" id="startTourBtn" class="btn btn-warning" title="Ayuda sobre sincronización">
            <i class="fas fa-question-circle"></i>
            Ayuda
        </button>
        <a href="{{ route('historial-consumo.sincronizar') }}"
           class="btn btn-primary"
           onclick="return confirm('¿Desea sincronizar el historial desde las lecturas registradas?')"
           data-intro="¿No ves tus datos ingresados? Presiona 'Sincronizar' para actualizar el historial desde las lecturas registradas. Esto crea automáticamente los registros de consumo basados en tus lecturas mensuales."
           data-step="2">
            <i class="fas fa-sync"></i>
            Sincronizar
        </a>
        <a href="{{ route('historial-consumo.comparar') }}"
           class="btn btn-info"
           data-intro="Usa 'Comparar' para analizar el consumo entre diferentes periodos o socios."
           data-step="3">
            <i class="fas fa-exchange-alt"></i>
            Comparar
        </a>
    </div>
</div>

<!-- Estadísticas -->
<div class="stats-row"
     data-intro="Estas estadísticas muestran el resumen de consumos sincronizados desde las lecturas registradas."
     data-step="1">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-database"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Registros</div>
            <div class="stat-value">{{ number_format($estadisticas['total_registros']) }}</div>
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
            <div class="stat-label">Promedio de Consumo</div>
            <div class="stat-value">{{ number_format($estadisticas['promedio_consumo'], 2) }} m³</div>
        </div>
    </div>

    <div class="stat-card highlight">
        <div class="stat-icon bg-warning">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Con Anomalías</div>
            <div class="stat-value">{{ number_format($estadisticas['con_anomalias']) }}</div>
            <div class="stat-detail">
                <span class="anomalia-badge alto">{{ $estadisticas['consumo_alto'] }} Alto</span>
                <span class="anomalia-badge bajo">{{ $estadisticas['consumo_bajo'] }} Bajo</span>
                <span class="anomalia-badge cero">{{ $estadisticas['sin_consumo'] }} Cero</span>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <h3 class="filter-title">
            <i class="fas fa-filter"></i>
            Filtros de Búsqueda
        </h3>
        <form method="GET" action="{{ route('historial-consumo.index') }}" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="search">Buscar:</label>
                    <input type="text"
                           id="search"
                           name="search"
                           class="form-control"
                           value="{{ request('search') }}"
                           placeholder="Nombre, RUT, período...">
                </div>

                <div class="form-group">
                    <label for="socio">Socio:</label>
                    <select id="socio" name="socio" class="form-control">
                        <option value="">Todos</option>
                        @foreach($socios as $s)
                            <option value="{{ $s->id }}" {{ request('socio') == $s->id ? 'selected' : '' }}>
                                {{ $s->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="periodo">Período:</label>
                    <select id="periodo" name="periodo" class="form-control">
                        <option value="">Todos</option>
                        @foreach($periodos as $p)
                            <option value="{{ $p }}" {{ request('periodo') == $p ? 'selected' : '' }}>
                                {{ $p }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="anomalia">Anomalía:</label>
                    <select id="anomalia" name="anomalia" class="form-control">
                        <option value="">Todas</option>
                        <option value="normal" {{ request('anomalia') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="alto" {{ request('anomalia') == 'alto' ? 'selected' : '' }}>Consumo Alto</option>
                        <option value="bajo" {{ request('anomalia') == 'bajo' ? 'selected' : '' }}>Consumo Bajo</option>
                        <option value="cero" {{ request('anomalia') == 'cero' ? 'selected' : '' }}>Sin Consumo</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="consumo_min">Consumo Mín (m³):</label>
                    <input type="number"
                           id="consumo_min"
                           name="consumo_min"
                           class="form-control"
                           value="{{ request('consumo_min') }}"
                           step="0.01"
                           placeholder="0.00">
                </div>

                <div class="form-group">
                    <label for="consumo_max">Consumo Máx (m³):</label>
                    <input type="number"
                           id="consumo_max"
                           name="consumo_max"
                           class="form-control"
                           value="{{ request('consumo_max') }}"
                           step="0.01"
                           placeholder="0.00">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Filtrar
                </button>
                <a href="{{ route('historial-consumo.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Historial -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Socio</th>
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
                                <strong>{{ $historial->socio->nombre_completo }}</strong>
                                <br>
                                <small class="text-muted">{{ $historial->socio->rut }}</small>
                            </td>
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
                                    <a href="{{ route('historial-consumo.analisis-socio', $historial->id_socio) }}"
                                       class="btn btn-sm btn-primary"
                                       title="Análisis del socio">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>No hay registros de historial de consumo</p>
                                    <a href="{{ route('historial-consumo.sincronizar') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-sync"></i>
                                        Sincronizar desde Lecturas
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($historiales->hasPages())
            <div class="pagination-wrapper">
                {{ $historiales->appends(request()->only(['search', 'socio', 'periodo', 'anomalia', 'consumo_min', 'consumo_max']))->links() }}
            </div>
        @endif
    </div>
</div>

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

    .stat-detail {
        margin-top: 8px;
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .anomalia-badge {
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .anomalia-badge.alto {
        background: var(--warning-light);
        color: var(--warning-dark);
    }

    .anomalia-badge.bajo {
        background: var(--info-light);
        color: var(--info-dark);
    }

    .anomalia-badge.cero {
        background: var(--danger-light);
        color: var(--danger-dark);
    }

    .card {
        background: var(--white);
        border-radius: 8px;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        margin-bottom: 24px;
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

    .filter-form .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
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

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 12px;
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

    .btn-info {
        background: var(--info);
        color: white;
    }

    .btn-info:hover {
        background: var(--info-dark);
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

    .pagination-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: center;
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

        .filter-form .form-row {
            grid-template-columns: 1fr;
        }
    }

    /* Estilos para Intro.js */
    .introjs-tooltip {
        min-width: 300px;
        max-width: 500px;
    }

    .custom-tooltip {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .custom-tooltip .introjs-tooltiptext {
        padding: 20px;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .custom-tooltip .introjs-tooltipbuttons {
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        padding-top: 15px;
    }

    .custom-tooltip .introjs-button {
        border: 2px solid white;
        background: transparent;
        color: white;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 6px;
        transition: all 0.2s;
    }

    .custom-tooltip .introjs-button:hover {
        background: white;
        color: #667eea;
    }

    .custom-tooltip .introjs-skipbutton {
        color: rgba(255, 255, 255, 0.8);
    }

    .custom-tooltip .introjs-skipbutton:hover {
        color: white;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tutorial interactivo con Intro.js
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

    // Evento del botón Ayuda
    const startTourBtn = document.getElementById('startTourBtn');
    if (startTourBtn) {
        startTourBtn.addEventListener('click', function() {
            intro.start();
        });
    }

    // Auto-mostrar tour en primera visita
    const tourShown = localStorage.getItem('historialConsumoTourShown');
    if (!tourShown) {
        setTimeout(function() {
            intro.start();
            localStorage.setItem('historialConsumoTourShown', 'true');
        }, 500);
    }
});
</script>
@endsection
