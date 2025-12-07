@extends('layouts.app')

@section('title', 'Detalle Movimiento - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-exchange-alt"></i>
        Movimiento: {{ $movimiento->numero_movimiento }}
    </h2>
    <div class="btn-group">
        <a href="{{ route('movimientos-inventario.imprimir', $movimiento->id) }}" class="btn btn-success" target="_blank">
            <i class="fas fa-print"></i>
            Imprimir Manifiesto
        </a>
        <a href="{{ route('movimientos-inventario.edit', $movimiento->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('movimientos-inventario.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<div class="row">
    <!-- Información del Movimiento -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Información del Movimiento
                </h3>
                <span class="badge badge-{{ $movimiento->tipo_movimiento === 'entrada' ? 'success' : 'danger' }}">
                    {{ ucfirst($movimiento->tipo_movimiento) }}
                </span>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Número de Movimiento</label>
                        <value><strong>{{ $movimiento->numero_movimiento }}</strong></value>
                    </div>

                    <div class="info-item">
                        <label>Tipo de Movimiento</label>
                        <value><span class="badge badge-{{ $movimiento->tipo_movimiento === 'entrada' ? 'success' : 'danger' }}">{{ ucfirst($movimiento->tipo_movimiento) }}</span></value>
                    </div>

                    <div class="info-item">
                        <label>Producto</label>
                        <value><a href="{{ route('productos.show', $movimiento->id_producto) }}" style="color: var(--primary); text-decoration: none;">{{ $movimiento->producto->nombre }}</a></value>
                    </div>

                    <div class="info-item">
                        <label>Cantidad</label>
                        <value><strong>{{ number_format($movimiento->cantidad, 2) }}</strong></value>
                    </div>

                    <div class="info-item">
                        <label>Motivo</label>
                        <value>{{ $movimiento->motivo ?? 'No especificado' }}</value>
                    </div>

                    <div class="info-item">
                        <label>Destino</label>
                        <value>{{ $movimiento->destino ?? 'No especificado' }}</value>
                    </div>

                    <div class="info-item full-width">
                        <label>Descripción</label>
                        <value>{{ $movimiento->descripcion ?? 'No especificada' }}</value>
                    </div>

                    <div class="info-item">
                        <label>Documento de Referencia</label>
                        <value>{{ $movimiento->documento_referencia ?? 'No especificado' }}</value>
                    </div>

                    <div class="info-item">
                        <label>Fecha del Movimiento</label>
                        <value>{{ date('d/m/Y', strtotime($movimiento->fecha_movimiento)) }}</value>
                    </div>

                    <div class="info-item">
                        <label>Cantidad Anterior</label>
                        <value>{{ number_format($movimiento->cantidad_anterior, 2) }}</value>
                    </div>

                    <div class="info-item">
                        <label>Cantidad Nueva</label>
                        <value>{{ number_format($movimiento->cantidad_nueva, 2) }}</value>
                    </div>

                    <div class="info-item">
                        <label>Responsable</label>
                        <value>{{ $movimiento->responsable->name ?? 'No asignado' }}</value>
                    </div>

                    <div class="info-item">
                        <label>Registrado Hace</label>
                        <value>{{ $movimiento->created_at->diffForHumans() }}</value>
                    </div>

                    @if($movimiento->observaciones)
                    <div class="info-item full-width">
                        <label>Observaciones</label>
                        <value>{{ $movimiento->observaciones }}</value>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información del Producto -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-box"></i>
                    Información del Producto
                </h3>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Nombre del Producto</label>
                        <value>{{ $movimiento->producto->nombre }}</value>
                    </div>

                    <div class="info-item">
                        <label>Código</label>
                        <value>{{ $movimiento->producto->codigo ?? 'No especificado' }}</value>
                    </div>

                    <div class="info-item">
                        <label>Categoría</label>
                        <value>{{ $movimiento->producto->categoria ?? 'No especificada' }}</value>
                    </div>

                    <div class="info-item">
                        <label>Stock Actual</label>
                        <value><strong>{{ number_format($movimiento->producto->cantidad_stock, 2) }}</strong></value>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen y Acciones -->
    <div class="col-md-4">
        <!-- Resumen -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar"></i>
                    Resumen
                </h3>
            </div>
            <div class="card-body">
                <div class="stat-box">
                    <div class="stat-icon" style="background: {{ $movimiento->tipo_movimiento === 'entrada' ? '#10b981' : '#ef4444' }};">
                        <i class="fas fa-{{ $movimiento->tipo_movimiento === 'entrada' ? 'arrow-down' : 'arrow-up' }}"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ ucfirst($movimiento->tipo_movimiento) }}</div>
                        <div class="stat-label">Tipo</div>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon" style="background: #3b82f6;">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ number_format($movimiento->cantidad, 2) }}</div>
                        <div class="stat-label">Cantidad</div>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon" style="background: #f59e0b;">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ number_format($movimiento->cantidad_nueva, 2) }}</div>
                        <div class="stat-label">Stock Resultante</div>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon" style="background: #06b6d4;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ date('d/m/Y', strtotime($movimiento->fecha_movimiento)) }}</div>
                        <div class="stat-label">Fecha</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt"></i>
                    Acciones Rápidas
                </h3>
            </div>
            <div class="card-body">
                <a href="{{ route('movimientos-inventario.edit', $movimiento->id) }}" class="action-btn">
                    <i class="fas fa-edit"></i>
                    Editar Movimiento
                </a>
                <a href="{{ route('productos.show', $movimiento->id_producto) }}" class="action-btn">
                    <i class="fas fa-box"></i>
                    Ver Producto
                </a>
                <a href="{{ route('productos.index') }}" class="action-btn">
                    <i class="fas fa-warehouse"></i>
                    Ver Inventario
                </a>
            </div>
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

    .row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    .col-md-8 {
        grid-column: 1;
    }

    .col-md-4 {
        grid-column: 2;
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
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
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-title i {
        color: var(--primary);
    }

    .card-body {
        padding: 24px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-item.full-width {
        grid-column: span 2;
    }

    .info-item label {
        font-weight: 600;
        color: var(--gray-500);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }

    .info-item value {
        color: var(--dark);
        font-size: 0.95rem;
    }

    .stat-box {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        background: var(--gray-50);
        border-radius: var(--radius);
        margin-bottom: 12px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .stat-info {
        flex: 1;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        color: var(--dark);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s;
        margin-bottom: 8px;
        cursor: pointer;
        width: 100%;
    }

    .action-btn:hover {
        background: var(--primary-light);
        border-color: var(--primary);
        color: var(--primary-dark);
    }

    .action-btn.danger:hover {
        background: #fee2e2;
        border-color: var(--danger);
        color: var(--danger);
    }

    .badge {
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
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

    .btn-group {
        display: flex;
        gap: 8px;
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

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-success {
        background: #10b981;
        color: white;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--gray-500);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-300);
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
    }

    .table th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid var(--gray-200);
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: var(--gray-500);
    }

    .mt-4 {
        margin-top: 24px;
    }

    @media (max-width: 768px) {
        .row {
            grid-template-columns: 1fr;
        }

        .col-md-8,
        .col-md-4 {
            grid-column: 1;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .info-item.full-width {
            grid-column: span 1;
        }
    }
</style>
@endsection
