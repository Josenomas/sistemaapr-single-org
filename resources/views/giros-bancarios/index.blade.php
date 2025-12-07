@extends('layouts.app')

@section('title', 'Giros Bancarios - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-money-check"></i>
        Gestión de Giros Bancarios
    </h2>
    <a href="{{ route('giros-bancarios.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nuevo Giro
    </a>
</div>

<!-- Estadísticas -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Giros</div>
            <div class="stat-value">{{ number_format($estadisticas['total_giros']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Emitidos</div>
            <div class="stat-value">{{ number_format($estadisticas['emitidos']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pagados</div>
            <div class="stat-value">{{ number_format($estadisticas['pagados']) }}</div>
        </div>
    </div>

    <div class="stat-card highlight">
        <div class="stat-icon bg-warning">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Monto Emitido</div>
            <div class="stat-value">${{ number_format($estadisticas['total_monto_emitido'], 0, ',', '.') }}</div>
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
        <form method="GET" action="{{ route('giros-bancarios.index') }}" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="search">Buscar:</label>
                    <input type="text"
                           id="search"
                           name="search"
                           class="form-control"
                           value="{{ request('search') }}"
                           placeholder="N° Giro, beneficiario, comprobante...">
                </div>

                <div class="form-group">
                    <label for="banco">Banco:</label>
                    <input type="text"
                           id="banco"
                           name="banco"
                           class="form-control"
                           value="{{ request('banco') }}"
                           placeholder="Nombre del banco...">
                </div>

                <div class="form-group">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="emitido" {{ request('estado') == 'emitido' ? 'selected' : '' }}>Emitido</option>
                        <option value="pagado" {{ request('estado') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                        <option value="anulado" {{ request('estado') == 'anulado' ? 'selected' : '' }}>Anulado</option>
                        <option value="vencido" {{ request('estado') == 'vencido' ? 'selected' : '' }}>Vencido</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fecha_desde">Desde:</label>
                    <input type="date"
                           id="fecha_desde"
                           name="fecha_desde"
                           class="form-control"
                           value="{{ request('fecha_desde') }}">
                </div>

                <div class="form-group">
                    <label for="fecha_hasta">Hasta:</label>
                    <input type="date"
                           id="fecha_hasta"
                           name="fecha_hasta"
                           class="form-control"
                           value="{{ request('fecha_hasta') }}">
                </div>

                <div class="form-group">
                    <label for="beneficiario">Beneficiario:</label>
                    <input type="text"
                           id="beneficiario"
                           name="beneficiario"
                           class="form-control"
                           value="{{ request('beneficiario') }}"
                           placeholder="Nombre del beneficiario...">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Filtrar
                </button>
                <a href="{{ route('giros-bancarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Giros -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>N° Giro</th>
                        <th>Banco</th>
                        <th>Beneficiario</th>
                        <th>Monto</th>
                        <th>Fecha Emisión</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($giros as $giro)
                        <tr>
                            <td><strong>{{ $giro->numero_giro }}</strong></td>
                            <td>{{ $giro->banco }}</td>
                            <td>{{ $giro->beneficiario }}</td>
                            <td><strong>{{ $giro->monto_formateado }}</strong></td>
                            <td>{{ $giro->fecha_emision_formateada }}</td>
                            <td>{!! $giro->estado_badge !!}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('giros-bancarios.show', $giro->id) }}" class="btn btn-sm btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('giros-bancarios.edit', $giro->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('giros-bancarios.destroy', $giro->id) }}"
                                          method="POST"
                                          style="display: inline;"
                                          onsubmit="return confirm('¿Está seguro de eliminar este giro bancario?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron giros bancarios.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $giros->links() }}
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .page-title i {
        color: var(--primary);
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .stat-card.highlight {
        border-left: 4px solid var(--warning);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: var(--white);
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
        color: var(--dark);
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
    }

    .card-body {
        padding: 24px;
    }

    .filter-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-title i {
        color: var(--primary);
    }

    .filter-form .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
        font-size: 0.875rem;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.95rem;
        transition: all 0.2s;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .filter-actions {
        display: flex;
        gap: 10px;
    }

    .mb-3 {
        margin-bottom: 24px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .table thead tr {
        background: var(--gray-100);
        border-bottom: 2px solid var(--gray-300);
    }

    .table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
        white-space: nowrap;
    }

    .table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--gray-200);
    }

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .btn {
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
        text-decoration: none;
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

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.75rem;
    }

    .btn-info {
        background: #06b6d4;
        color: white;
    }

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-danger {
        background: #dc2626;
        color: white;
    }

    .btn-group {
        display: flex;
        gap: 4px;
    }

    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
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
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .text-center {
        text-align: center;
    }

    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }
</style>
@endsection
