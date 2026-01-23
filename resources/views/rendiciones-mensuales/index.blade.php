@extends('layouts.app')

@section('title', 'Rendicion Mensual - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-file-invoice-dollar"></i>
        Rendicion Mensual
    </h2>
    <div class="btn-group">
        <a href="{{ route('rendiciones-mensuales.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nueva Rendicion
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<!-- Estadisticas -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Rendiciones</div>
            <div class="stat-value">{{ $estadisticas['total_rendiciones'] }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-folder-open"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Rendiciones Abiertas</div>
            <div class="stat-value">{{ $estadisticas['abiertas'] }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-lock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Rendiciones Cerradas</div>
            <div class="stat-value">{{ $estadisticas['cerradas'] }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-warning">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Anio Actual</div>
            <div class="stat-value">{{ $estadisticas['anio_actual'] }}</div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <h3 class="filter-title">
            <i class="fas fa-filter"></i>
            Filtros de Busqueda
        </h3>
        <form method="GET" action="{{ route('rendiciones-mensuales.index') }}" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="search">Buscar:</label>
                    <input type="text" id="search" name="search" class="form-control" placeholder="Buscar por codigo o periodo..." value="{{ request('search') }}">
                </div>

                <div class="form-group">
                    <label for="anio">Anio:</label>
                    <select id="anio" name="anio" class="form-control">
                        <option value="">Todos los anios</option>
                        @foreach($aniosDisponibles as $anio)
                            <option value="{{ $anio }}" {{ request('anio') == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="mes">Mes:</label>
                    <select id="mes" name="mes" class="form-control">
                        <option value="">Todos los meses</option>
                        <option value="1" {{ request('mes') == '1' ? 'selected' : '' }}>Enero</option>
                        <option value="2" {{ request('mes') == '2' ? 'selected' : '' }}>Febrero</option>
                        <option value="3" {{ request('mes') == '3' ? 'selected' : '' }}>Marzo</option>
                        <option value="4" {{ request('mes') == '4' ? 'selected' : '' }}>Abril</option>
                        <option value="5" {{ request('mes') == '5' ? 'selected' : '' }}>Mayo</option>
                        <option value="6" {{ request('mes') == '6' ? 'selected' : '' }}>Junio</option>
                        <option value="7" {{ request('mes') == '7' ? 'selected' : '' }}>Julio</option>
                        <option value="8" {{ request('mes') == '8' ? 'selected' : '' }}>Agosto</option>
                        <option value="9" {{ request('mes') == '9' ? 'selected' : '' }}>Septiembre</option>
                        <option value="10" {{ request('mes') == '10' ? 'selected' : '' }}>Octubre</option>
                        <option value="11" {{ request('mes') == '11' ? 'selected' : '' }}>Noviembre</option>
                        <option value="12" {{ request('mes') == '12' ? 'selected' : '' }}>Diciembre</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="abierto" {{ request('estado') == 'abierto' ? 'selected' : '' }}>Abierto</option>
                        <option value="cerrado" {{ request('estado') == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>

                @if(request()->hasAny(['search', 'anio', 'mes', 'estado']))
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <a href="{{ route('rendiciones-mensuales.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Rendiciones -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Periodo</th>
                        <th>Saldo Anterior</th>
                        <th>Ingresos</th>
                        <th>Egresos</th>
                        <th>Saldo Final</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rendiciones as $rendicion)
                        <tr class="{{ $rendicion->es_deficit ? 'row-danger' : '' }}">
                            <td><strong>{{ $rendicion->codigo_rendicion }}</strong></td>
                            <td>{{ $rendicion->periodo_texto }}</td>
                            <td>{{ $rendicion->saldo_anterior_formateado }}</td>
                            <td class="text-success"><strong>{{ $rendicion->total_ingresos_formateado }}</strong></td>
                            <td class="text-danger"><strong>{{ $rendicion->total_egresos_formateado }}</strong></td>
                            <td class="{{ $rendicion->es_deficit ? 'text-danger' : 'text-success' }}">
                                <strong>{{ $rendicion->saldo_final_formateado }}</strong>
                                @if($rendicion->es_deficit)
                                    <i class="fas fa-exclamation-triangle text-danger" title="Deficit"></i>
                                @endif
                            </td>
                            <td>{!! $rendicion->estado_badge !!}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('rendiciones-mensuales.show', $rendicion->id) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($rendicion->estado == 'abierto')
                                        <a href="{{ route('rendiciones-mensuales.edit', $rendicion->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('rendiciones-mensuales.destroy', $rendicion->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Esta seguro de eliminar esta rendicion?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                <i class="fas fa-inbox"></i>
                                <p>No se encontraron rendiciones mensuales</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rendiciones->hasPages())
            <div class="pagination-wrapper">
                {{ $rendiciones->appends(request()->only(['search', 'anio', 'mes', 'estado']))->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    /* Stats Row */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--white);
        border-radius: var(--radius);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stat-card.highlight {
        border: 2px solid var(--warning);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.bg-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .stat-icon.bg-success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .stat-icon.bg-info {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .stat-icon.bg-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .stat-icon.bg-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .stat-icon.bg-purple {
        background: linear-gradient(135deg, #a855f7, #9333ea);
    }

    .stat-icon.bg-green {
        background: linear-gradient(135deg, #22c55e, #16a34a);
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--gray-600);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        line-height: 1;
    }

    /* Filter Title */
    .filter-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-title i {
        color: var(--primary);
    }

    /* Filter Form */
    .filter-form .form-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto auto;
        gap: 16px;
        align-items: end;
    }

    .filter-form .form-group {
        display: flex;
        flex-direction: column;
    }

    .filter-form .form-group label {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 6px;
        font-size: 0.875rem;
    }

    /* Cards */
    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
    }

    .card-body {
        padding: 24px;
    }

    .mb-3 {
        margin-bottom: 20px;
    }

    /* Buttons */
    .btn-group {
        display: flex;
        gap: 12px;
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
        background: #3b82f6;
        color: white;
    }

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    /* Form Controls */
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    /* Table */
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
        font-size: 0.875rem;
        border-bottom: 2px solid var(--gray-200);
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid var(--gray-200);
        font-size: 0.875rem;
    }

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .row-danger {
        background-color: #fee2e2 !important;
    }

    .text-success {
        color: #059669;
        font-weight: 600;
    }

    .text-danger {
        color: #dc2626;
        font-weight: 600;
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: var(--gray-500);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 6px;
    }

    /* Badges */
    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-block;
    }

    .badge-info {
        background: #cffafe;
        color: #155e75;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    /* Alerts */
    .alert {
        padding: 16px 20px;
        border-radius: var(--radius);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* Pagination */
    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .filter-form .form-row {
            grid-template-columns: 1fr;
        }

        .stats-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
