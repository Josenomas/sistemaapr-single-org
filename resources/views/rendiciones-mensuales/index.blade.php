@extends('layouts.app')

@section('title', 'Rendición Mensual - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-file-invoice-dollar"></i>
        Rendición Mensual
    </h2>
    <a href="{{ route('rendiciones-mensuales.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nueva Rendición
    </a>
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

<!-- Estadísticas -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['total_rendiciones'] }}</div>
            <div class="stat-label">Total Rendiciones</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-folder-open"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['abiertas'] }}</div>
            <div class="stat-label">Rendiciones Abiertas</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-lock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['cerradas'] }}</div>
            <div class="stat-label">Rendiciones Cerradas</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-warning">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['anio_actual'] }}</div>
            <div class="stat-label">Año Actual</div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('rendiciones-mensuales.index') }}" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por código o periodo..." value="{{ request('search') }}">
                </div>

                <div class="form-group">
                    <select name="anio" class="form-control">
                        <option value="">Todos los años</option>
                        @foreach($aniosDisponibles as $anio)
                            <option value="{{ $anio }}" {{ request('anio') == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <select name="mes" class="form-control">
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
                    <select name="estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="abierto" {{ request('estado') == 'abierto' ? 'selected' : '' }}>Abierto</option>
                        <option value="cerrado" {{ request('estado') == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrar
                </button>

                @if(request()->hasAny(['search', 'anio', 'mes', 'estado']))
                    <a href="{{ route('rendiciones-mensuales.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
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
                        <th>Código</th>
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
                                    <i class="fas fa-exclamation-triangle text-danger" title="Déficit"></i>
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
                                        <form action="{{ route('rendiciones-mensuales.destroy', $rendicion->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro de eliminar esta rendición?');">
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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        border: 1px solid var(--gray-200);
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
    }

    .stat-icon.bg-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); }
    .stat-icon.bg-success { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-icon.bg-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-icon.bg-danger { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .stat-icon.bg-info { background: linear-gradient(135deg, #3b82f6, #2563eb); }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        line-height: 1;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 500;
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

    .mb-3 {
        margin-bottom: 20px;
    }

    .filter-form .form-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto auto;
        gap: 12px;
        align-items: center;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
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
        font-size: 0.875rem;
        border-bottom: 2px solid var(--gray-200);
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid var(--gray-200);
        font-size: 0.95rem;
    }

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .row-danger {
        background-color: #fee2e2 !important;
    }

    .text-success {
        color: #059669;
    }

    .text-danger {
        color: #dc2626;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: var(--radius);
        border: none;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.875rem;
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

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: var(--gray-500);
    }

    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .filter-form .form-row {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
