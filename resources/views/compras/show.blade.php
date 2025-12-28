@extends('layouts.app')

@section('title', 'Detalle Compra - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-shopping-cart"></i>
        Compra: {{ $compra->numero_compra }}
    </h2>
    <div class="btn-group">
        <a href="{{ route('compras.edit', $compra->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('compras.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<div class="row">
    <!-- Información Principal -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Información de la Compra
                </h3>
                {!! $compra->estado_badge !!}
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Número de Compra</label>
                        <value><strong>{{ $compra->numero_compra }}</strong></value>
                    </div>

                    <div class="info-item">
                        <label>Fecha de Compra</label>
                        <value>{{ $compra->fecha_compra_formateada }}</value>
                    </div>

                    <div class="info-item">
                        <label>Tipo de Compra</label>
                        <value>{!! $compra->tipo_compra_badge !!}</value>
                    </div>

                    <div class="info-item">
                        <label>Estado</label>
                        <value>{!! $compra->estado_badge !!}</value>
                    </div>

                    <div class="info-item full-width">
                        <label>Proveedor</label>
                        <value>{{ $compra->proveedor }}</value>
                    </div>

                    @if($compra->rut_proveedor)
                    <div class="info-item">
                        <label>RUT Proveedor</label>
                        <value>{{ $compra->rut_proveedor }}</value>
                    </div>
                    @endif

                    <div class="info-item full-width">
                        <label>Descripción</label>
                        <value>{{ $compra->descripcion }}</value>
                    </div>

                    <div class="info-item">
                        <label>Cantidad</label>
                        <value>{{ $compra->cantidad_formateada }}</value>
                    </div>

                    <div class="info-item">
                        <label>Precio Unitario</label>
                        <value>{{ $compra->precio_unitario_formateado }}</value>
                    </div>

                    <div class="info-item">
                        <label>Subtotal</label>
                        <value><strong>{{ $compra->subtotal_formateado }}</strong></value>
                    </div>

                    <div class="info-item">
                        <label>IVA</label>
                        <value>{{ $compra->iva_formateado }}</value>
                    </div>

                    <div class="info-item">
                        <label>Total</label>
                        <value class="total-amount">{{ $compra->total_formateado }}</value>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de Pago -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-money-bill-wave"></i>
                    Información de Pago
                </h3>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Método de Pago</label>
                        <value>{{ $compra->metodo_pago_texto }}</value>
                    </div>

                    @if($compra->numero_factura)
                    <div class="info-item">
                        <label>Número de Factura</label>
                        <value>{{ $compra->numero_factura }}</value>
                    </div>
                    @endif

                    @if($compra->fecha_pago)
                    <div class="info-item">
                        <label>Fecha de Pago</label>
                        <value>{{ $compra->fecha_pago_formateada }}</value>
                    </div>
                    @endif

                    @if($compra->responsable)
                    <div class="info-item">
                        <label>Responsable</label>
                        <value>{{ $compra->responsable->nombre }} {{ $compra->responsable->apellido_paterno }}</value>
                    </div>
                    @endif

                    @if($compra->observaciones)
                    <div class="info-item full-width">
                        <label>Observaciones</label>
                        <value>{{ $compra->observaciones }}</value>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen y Acciones -->
    <div class="col-md-4">
        <!-- Resumen Financiero -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calculator"></i>
                    Resumen Financiero
                </h3>
            </div>
            <div class="card-body">
                <div class="stat-box">
                    <div class="stat-icon" style="background: #3b82f6;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Subtotal</div>
                        <div class="stat-value">{{ $compra->subtotal_formateado }}</div>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon" style="background: #f59e0b;">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">IVA</div>
                        <div class="stat-value">{{ $compra->iva_formateado }}</div>
                    </div>
                </div>

                <div class="stat-box total-box">
                    <div class="stat-icon" style="background: #10b981;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Total</div>
                        <div class="stat-value total">{{ $compra->total_formateado }}</div>
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
                <a href="{{ route('compras.edit', $compra->id) }}" class="action-btn">
                    <i class="fas fa-edit"></i>
                    Editar Compra
                </a>
                <form action="{{ route('compras.destroy', $compra->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('¿Está seguro de eliminar esta compra?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn danger">
                        <i class="fas fa-trash"></i>
                        Eliminar Compra
                    </button>
                </form>
            </div>
        </div>

        <!-- Información del Sistema -->
        <!-- Card de Información del Sistema removido - timestamps deshabilitados -->
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

    .total-amount {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: var(--primary) !important;
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

    .stat-box.total-box {
        background: #d1fae5;
        border: 2px solid #10b981;
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

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
    }

    .stat-value.total {
        font-size: 1.5rem;
        color: #065f46;
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

    .btn-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-secondary:hover {
        background: var(--gray-300);
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
