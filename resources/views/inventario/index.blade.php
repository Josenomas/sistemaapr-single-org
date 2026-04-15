@extends('layouts.app')

@section('title', 'Inventario - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-boxes"></i>
        Gestión de Inventario
    </h2>
    <div class="header-actions">
        <button id="startTourBtn" class="btn btn-info" title="Iniciar tutorial">
            <i class="fas fa-question-circle"></i>
            Ayuda
        </button>
        <a href="{{ route('inventario.importar.index') }}" class="btn btn-success">
            <i class="fas fa-file-excel"></i>
            Importar Excel
        </a>
        <a href="{{ route('inventario.create') }}" class="btn btn-primary" data-intro="Registra un nuevo producto en el inventario: materiales, equipos, herramientas, insumos, químicos o repuestos." data-step="1">
            <i class="fas fa-plus"></i>
            Nuevo Producto
        </a>
    </div>
</div>

<!-- Estadísticas -->
<div class="stats-grid" data-intro="Resumen del inventario: Total de productos, productos con bajo stock (cerca del mínimo), productos agotados (stock 0) y valor total del inventario." data-step="2">
    <div class="stat-card">
        <div class="stat-icon" style="background: #3b82f6;">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['total_productos'] }}</div>
            <div class="stat-label">Total Productos</div>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-icon" style="background: #f59e0b;">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['bajo_stock'] }}</div>
            <div class="stat-label">Bajo Stock</div>
        </div>
    </div>

    <div class="stat-card danger">
        <div class="stat-icon" style="background: #ef4444;">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $estadisticas['agotados'] }}</div>
            <div class="stat-label">Agotados</div>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-icon" style="background: #10b981;">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ '$' . number_format($estadisticas['valor_total'], 0, ',', '.') }}</div>
            <div class="stat-label">Valor Total</div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4" data-intro="Filtra productos por nombre/código, categoría o estado de stock (todos, bajo stock, agotados)." data-step="3">
    <div class="card-body">
        <form method="GET" action="{{ route('inventario.index') }}" class="filters-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="buscar">Buscar</label>
                    <input type="text" name="buscar" id="buscar" class="form-control"
                           placeholder="Código, nombre o descripción" value="{{ request('buscar') }}">
                </div>

                <div class="form-group">
                    <label for="categoria">Categoría</label>
                    <select name="categoria" id="categoria" class="form-control">
                        <option value="">Todas</option>
                        <option value="materiales" {{ request('categoria') == 'materiales' ? 'selected' : '' }}>Materiales</option>
                        <option value="equipos" {{ request('categoria') == 'equipos' ? 'selected' : '' }}>Equipos</option>
                        <option value="herramientas" {{ request('categoria') == 'herramientas' ? 'selected' : '' }}>Herramientas</option>
                        <option value="insumos" {{ request('categoria') == 'insumos' ? 'selected' : '' }}>Insumos</option>
                        <option value="quimicos" {{ request('categoria') == 'quimicos' ? 'selected' : '' }}>Químicos</option>
                        <option value="repuestos" {{ request('categoria') == 'repuestos' ? 'selected' : '' }}>Repuestos</option>
                        <option value="otro" {{ request('categoria') == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="disponible" {{ request('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="bajo_stock" {{ request('estado') == 'bajo_stock' ? 'selected' : '' }}>Bajo Stock</option>
                        <option value="agotado" {{ request('estado') == 'agotado' ? 'selected' : '' }}>Agotado</option>
                        <option value="descontinuado" {{ request('estado') == 'descontinuado' ? 'selected' : '' }}>Descontinuado</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="alerta">Alertas</label>
                    <select name="alerta" id="alerta" class="form-control">
                        <option value="">Todas</option>
                        <option value="bajo_stock" {{ request('alerta') == 'bajo_stock' ? 'selected' : '' }}>Bajo Stock</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="margin-top: 22px;">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Alertas -->
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

<!-- Tabla -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive" data-intro="Lista de todos los productos en inventario. Muestra código, nombre, categoría, stock actual, stock mínimo y valor." data-step="4">
            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th data-intro="Cantidad actual en inventario. Se actualizará automáticamente con cada entrada o salida registrada." data-step="5">Cantidad</th>
                        <th>Stock Mínimo</th>
                        <th>Precio Unit.</th>
                        <th>Valor Total</th>
                        <th data-intro="Estado: Normal (stock suficiente), Bajo Stock (cerca del mínimo), Agotado (stock 0)." data-step="6">Estado</th>
                        <th data-intro="Ver detalles, Editar producto o Registrar movimiento de entrada/salida." data-step="7">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventario as $producto)
                    <tr class="{{ $producto->alerta_stock['nivel'] == 'critico' ? 'row-danger' : ($producto->alerta_stock['nivel'] == 'bajo' ? 'row-warning' : '') }}">
                        <td><strong>{{ $producto->codigo_producto }}</strong></td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{!! $producto->categoria_badge !!}</td>
                        <td>
                            <strong>{{ number_format($producto->cantidad_actual, 2, ',', '.') }}</strong> {{ $producto->unidad_medida }}
                            @if($producto->alerta_stock['nivel'] != 'normal')
                                <br><small class="text-{{ $producto->alerta_stock['nivel'] == 'critico' ? 'danger' : 'warning' }}">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $producto->alerta_stock['mensaje'] }}
                                </small>
                            @endif
                        </td>
                        <td>{{ number_format($producto->cantidad_minima, 2, ',', '.') }} {{ $producto->unidad_medida }}</td>
                        <td>{{ $producto->precio_unitario_formateado }}</td>
                        <td><strong>{{ $producto->valor_total_formateado }}</strong></td>
                        <td>{!! $producto->estado_badge !!}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('inventario.show', $producto->id) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('inventario.edit', $producto->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">No hay productos registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $inventario->appends(request()->only(['buscar', 'categoria', 'estado', 'alerta']))->links() }}
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
        border: 1px solid var(--gray-200);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark);
        line-height: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-top: 4px;
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

    .mb-4 {
        margin-bottom: 24px;
    }

    .filters-form .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
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

    .row-warning {
        background: #fef3c7 !important;
    }

    .row-danger {
        background: #fee2e2 !important;
    }

    .text-danger {
        color: #991b1b;
    }

    .text-warning {
        color: #92400e;
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

    .badge-primary {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-info {
        background: #cffafe;
        color: #155e75;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .badge-dark {
        background: var(--gray-700);
        color: var(--white);
    }

    .text-center {
        text-align: center;
    }

    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
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

    .header-actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .btn-info {
        background: #06b6d4;
        color: white;
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar el tour
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

        // Botón para iniciar el tour
        document.getElementById('startTourBtn').addEventListener('click', function() {
            intro.start();
        });

        // Mostrar tour automáticamente solo la primera vez
        const tourShown = localStorage.getItem('inventarioTourShown');
        if (!tourShown) {
            setTimeout(function() {
                intro.start();
                localStorage.setItem('inventarioTourShown', 'true');
            }, 500);
        }
    });
</script>
@endsection
