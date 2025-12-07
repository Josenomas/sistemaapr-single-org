@extends('layouts.app')

@section('title', 'Detalle Producto - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-box"></i>
        Detalle del Producto
    </h2>
    <div class="header-actions">
        <a href="{{ route('inventario.edit', $producto->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('inventario.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<!-- Alertas -->
@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<!-- Información Principal -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Información del Producto</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Código de Producto:</span>
                <span class="detail-value"><strong>{{ $producto->codigo_producto }}</strong></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Nombre:</span>
                <span class="detail-value"><strong>{{ $producto->nombre }}</strong></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Categoría:</span>
                <span class="detail-value">{!! $producto->categoria_badge !!}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Estado:</span>
                <span class="detail-value">{!! $producto->estado_badge !!}</span>
            </div>
        </div>

        @if($producto->descripcion)
        <div class="detail-section">
            <h4 class="section-title">Descripción</h4>
            <p class="section-content">{{ $producto->descripcion }}</p>
        </div>
        @endif
    </div>
</div>

<!-- Stock e Inventario -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Stock e Inventario</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Cantidad Actual:</span>
                <span class="detail-value">
                    <strong class="{{ $producto->alerta_stock['nivel'] != 'normal' ? 'text-' . ($producto->alerta_stock['nivel'] == 'critico' ? 'danger' : 'warning') : '' }}">
                        {{ number_format($producto->cantidad_actual, 2, ',', '.') }} {{ $producto->unidad_medida }}
                    </strong>
                    @if($producto->alerta_stock['nivel'] != 'normal')
                        <br><small class="text-{{ $producto->alerta_stock['nivel'] == 'critico' ? 'danger' : 'warning' }}">
                            <i class="fas fa-exclamation-triangle"></i> {{ $producto->alerta_stock['mensaje'] }}
                        </small>
                    @endif
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Cantidad Mínima:</span>
                <span class="detail-value">{{ number_format($producto->cantidad_minima, 2, ',', '.') }} {{ $producto->unidad_medida }}</span>
            </div>

            @if($producto->cantidad_maxima)
            <div class="detail-item">
                <span class="detail-label">Cantidad Máxima:</span>
                <span class="detail-value">{{ number_format($producto->cantidad_maxima, 2, ',', '.') }} {{ $producto->unidad_medida }}</span>
            </div>
            @endif

            <div class="detail-item">
                <span class="detail-label">Unidad de Medida:</span>
                <span class="detail-value">{{ $producto->unidad_medida }}</span>
            </div>

            @if($producto->ubicacion)
            <div class="detail-item">
                <span class="detail-label">Ubicación:</span>
                <span class="detail-value">{{ $producto->ubicacion }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Información Financiera -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Información Financiera</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            @if($producto->precio_unitario)
            <div class="detail-item">
                <span class="detail-label">Precio Unitario:</span>
                <span class="detail-value"><strong>{{ $producto->precio_unitario_formateado }}</strong></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Valor Total en Stock:</span>
                <span class="detail-value"><strong class="text-success">{{ $producto->valor_total_formateado }}</strong></span>
            </div>
            @else
            <div class="detail-item">
                <span class="detail-label">Precio:</span>
                <span class="detail-value text-muted">No especificado</span>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Información del Proveedor -->
@if($producto->proveedor)
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Información del Proveedor</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Proveedor:</span>
                <span class="detail-value">{{ $producto->proveedor }}</span>
            </div>

            @if($producto->fecha_ultima_compra)
            <div class="detail-item">
                <span class="detail-label">Última Compra:</span>
                <span class="detail-value">{{ $producto->fecha_ultima_compra_formateada }}</span>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Movimientos -->
@if($producto->fecha_ultimo_movimiento)
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Movimientos</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Último Movimiento:</span>
                <span class="detail-value">{{ $producto->fecha_ultimo_movimiento_formateada }}</span>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Observaciones -->
@if($producto->observaciones)
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Observaciones</h3>
    </div>
    <div class="card-body">
        <p class="section-content">{{ $producto->observaciones }}</p>
    </div>
</div>
@endif

<!-- Información de Registro -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de Registro</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Fecha de Creación:</span>
                <span class="detail-value">{{ $producto->fecha_creacion_formateada }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Última Actualización:</span>
                <span class="detail-value">{{ $producto->fecha_actualizacion_formateada }}</span>
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

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .card-body {
        padding: 24px;
    }

    .mb-4 {
        margin-bottom: 24px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .detail-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .detail-value {
        font-size: 1rem;
        color: var(--dark);
    }

    .detail-section {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-200);
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 12px;
    }

    .section-content {
        font-size: 0.95rem;
        color: var(--gray-700);
        line-height: 1.6;
        margin: 0;
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

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-warning:hover {
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

    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-block;
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

    .text-success {
        color: #059669;
    }

    .text-danger {
        color: #991b1b;
    }

    .text-warning {
        color: #92400e;
    }

    .text-muted {
        color: var(--gray-500);
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

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .header-actions {
            width: 100%;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
