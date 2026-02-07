@extends('layouts.app')

@section('title', 'Movimientos de Inventario - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-exchange-alt"></i>
        Movimientos de Inventario
    </h2>
    <a href="{{ route('movimientos-inventario.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nuevo Movimiento
    </a>
</div>

<!-- Estadísticas -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-exchange-alt"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Movimientos</div>
            <div class="stat-value">{{ number_format($estadisticas['total_movimientos']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-arrow-down"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Entradas</div>
            <div class="stat-value">{{ number_format($estadisticas['entradas']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-warning">
            <i class="fas fa-arrow-up"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Salidas</div>
            <div class="stat-value">{{ number_format($estadisticas['salidas']) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-sliders-h"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Ajustes</div>
            <div class="stat-value">{{ number_format($estadisticas['ajustes']) }}</div>
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
        <form method="GET" action="{{ route('movimientos-inventario.index') }}" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="search">Buscar:</label>
                    <input type="text"
                           id="search"
                           name="search"
                           class="form-control"
                           value="{{ request('search') }}"
                           placeholder="N° movimiento, motivo, destino...">
                </div>

                <div class="form-group">
                    <label for="tipo_movimiento">Tipo:</label>
                    <select id="tipo_movimiento" name="tipo_movimiento" class="form-control">
                        <option value="">Todos</option>
                        <option value="entrada" {{ request('tipo_movimiento') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                        <option value="salida" {{ request('tipo_movimiento') == 'salida' ? 'selected' : '' }}>Salida</option>
                        <option value="ajuste" {{ request('tipo_movimiento') == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_producto">Producto:</label>
                    <select id="id_producto" name="id_producto" class="form-control">
                        <option value="">Todos</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}" {{ request('id_producto') == $producto->id ? 'selected' : '' }}>
                                {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_responsable">Responsable:</label>
                    <select id="id_responsable" name="id_responsable" class="form-control">
                        <option value="">Todos</option>
                        @foreach($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}" {{ request('id_responsable') == $funcionario->id ? 'selected' : '' }}>
                                {{ $funcionario->nombre_completo }}
                            </option>
                        @endforeach
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
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Filtrar
                </button>
                <a href="{{ route('movimientos-inventario.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Movimientos -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>N° Movimiento</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Stock</th>
                        <th>Motivo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $movimiento)
                        <tr>
                            <td><strong>{{ $movimiento->numero_movimiento }}</strong></td>
                            <td>{{ $movimiento->fecha_movimiento_formateada }}</td>
                            <td>{!! $movimiento->tipo_movimiento_badge !!}</td>
                            <td>
                                @if($movimiento->detalles && $movimiento->detalles->count() > 0)
                                    @if($movimiento->detalles->count() == 1)
                                        {{ $movimiento->detalles->first()->producto->nombre }}
                                    @else
                                        <span style="color: #2563eb; font-weight: 600;">{{ $movimiento->detalles->count() }} productos</span>
                                    @endif
                                @elseif($movimiento->producto)
                                    {{ $movimiento->producto->nombre }}
                                @else
                                    <span style="color: #94a3b8;">Sin producto</span>
                                @endif
                            </td>
                            <td>
                                @if($movimiento->detalles && $movimiento->detalles->count() > 0)
                                    @if($movimiento->detalles->count() == 1)
                                        <strong>{{ $movimiento->detalles->first()->cantidad_formateada }}</strong> {{ $movimiento->detalles->first()->producto->unidad_medida }}
                                    @else
                                        <span style="color: #2563eb;">Ver detalle</span>
                                    @endif
                                @elseif($movimiento->cantidad)
                                    <strong>{{ $movimiento->cantidad_formateada }}</strong> {{ optional($movimiento->producto)->unidad_medida }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($movimiento->detalles && $movimiento->detalles->count() > 0)
                                    <span style="color: #2563eb;">Ver detalle</span>
                                @else
                                    <span style="color: #64748b; font-size: 0.85rem;">
                                        {{ $movimiento->cantidad_anterior_formateada }} →
                                        <strong style="color: #1e293b;">{{ $movimiento->cantidad_nueva_formateada }}</strong>
                                    </span>
                                @endif
                            </td>
                            <td>{{ $movimiento->motivo }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('movimientos-inventario.show', $movimiento->id) }}" class="btn btn-sm btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('movimientos-inventario.edit', $movimiento->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('movimientos-inventario.destroy', $movimiento->id) }}"
                                          method="POST"
                                          style="display: inline;"
                                          onsubmit="return confirm('¿Está seguro de eliminar este movimiento?')">
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
                            <td colspan="8" class="text-center">No se encontraron movimientos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $movimientos->appends(request()->only(['search', 'tipo_movimiento', 'id_producto', 'id_responsable', 'fecha_desde', 'fecha_hasta']))->links() }}
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
        margin-bottom: 6px;
        font-size: 0.875rem;
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
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .filter-actions {
        display: flex;
        gap: 10px;
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
        background: var(--gray-500);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-600);
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
        background: #ef4444;
        color: white;
    }

    .btn-group {
        display: flex;
        gap: 4px;
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
