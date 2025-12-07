@extends('layouts.app')

@section('title', 'Historial de Pagos - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-money-bill-wave"></i>
        Historial de Pagos
    </h2>
    <div class="btn-group">
        <a href="{{ route('historial-pagos.comparar') }}" class="btn btn-info">
            <i class="fas fa-exchange-alt"></i>
            Comparar Socios
        </a>
        <a href="#" class="btn btn-success">
            <i class="fas fa-file-invoice-dollar"></i>
            Reporte Recaudación
        </a>
    </div>
</div>

<!-- Estadísticas -->
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
            <div class="stat-label">Monto Total</div>
            <div class="stat-value">${{ number_format($estadisticas['monto_total'], 0, ',', '.') }}</div>
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

    <div class="stat-card highlight">
        <div class="stat-icon bg-warning">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Mes Actual</div>
            <div class="stat-value">{{ number_format($estadisticas['pagos_mes_actual']) }}</div>
            <div class="stat-detail">
                <span class="metodo-badge efectivo">Efectivo: {{ $estadisticas['por_metodo']['efectivo'] ?? 0 }}</span>
                <span class="metodo-badge transferencia">Transfer: {{ $estadisticas['por_metodo']['transferencia'] ?? 0 }}</span>
                <span class="metodo-badge cheque">Cheque: {{ $estadisticas['por_metodo']['cheque'] ?? 0 }}</span>
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
        <form method="GET" action="{{ route('historial-pagos.index') }}" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="search">Buscar:</label>
                    <input type="text"
                           id="search"
                           name="search"
                           class="form-control"
                           value="{{ request('search') }}"
                           placeholder="Nombre, RUT, comprobante...">
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
                    <label for="metodo_pago">Método de Pago:</label>
                    <select id="metodo_pago" name="metodo_pago" class="form-control">
                        <option value="">Todos</option>
                        <option value="efectivo" {{ request('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                        <option value="transferencia" {{ request('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                        <option value="cheque" {{ request('metodo_pago') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fecha_desde">Fecha Desde:</label>
                    <input type="date"
                           id="fecha_desde"
                           name="fecha_desde"
                           class="form-control"
                           value="{{ request('fecha_desde') }}">
                </div>

                <div class="form-group">
                    <label for="fecha_hasta">Fecha Hasta:</label>
                    <input type="date"
                           id="fecha_hasta"
                           name="fecha_hasta"
                           class="form-control"
                           value="{{ request('fecha_hasta') }}">
                </div>

                <div class="form-group">
                    <label for="monto_min">Monto Mínimo:</label>
                    <input type="number"
                           id="monto_min"
                           name="monto_min"
                           class="form-control"
                           value="{{ request('monto_min') }}"
                           step="1000"
                           placeholder="0">
                </div>

                <div class="form-group">
                    <label for="monto_max">Monto Máximo:</label>
                    <input type="number"
                           id="monto_max"
                           name="monto_max"
                           class="form-control"
                           value="{{ request('monto_max') }}"
                           step="1000"
                           placeholder="0">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Filtrar
                </button>
                <a href="{{ route('historial-pagos.index') }}" class="btn btn-secondary">
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
                        <th>Fecha Pago</th>
                        <th>N° Recibo</th>
                        <th>Boleta</th>
                        <th>Monto Pagado</th>
                        <th>Método Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagos as $pago)
                        <tr>
                            <td>
                                <strong>{{ $pago->socio->nombre_completo }}</strong>
                                <br>
                                <small class="text-muted">{{ $pago->socio->rut }}</small>
                            </td>
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
                                <div class="action-buttons">
                                    <a href="{{ route('historial-pagos.show', $pago->id) }}"
                                       class="btn btn-sm btn-info"
                                       title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('historial-pagos.analisis-socio', $pago->id_socio) }}"
                                       class="btn btn-sm btn-primary"
                                       title="Análisis del socio">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>No hay registros de pagos</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($pagos->hasPages())
            <div class="pagination-wrapper">
                {{ $pagos->links() }}
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

    .metodo-badge {
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .metodo-badge.efectivo {
        background: var(--primary-light);
        color: var(--primary-dark);
    }

    .metodo-badge.transferencia {
        background: var(--info-light);
        color: var(--info-dark);
    }

    .metodo-badge.cheque {
        background: var(--gray-200);
        color: var(--gray-700);
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

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-success:hover {
        background: var(--success-dark);
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
</style>
@endsection
