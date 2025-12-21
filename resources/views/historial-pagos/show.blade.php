@extends('layouts.app')

@section('title', 'Detalle de Pago - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-file-invoice-dollar"></i>
        Detalle de Pago
    </h2>
    <div class="btn-group">
        <a href="{{ route('historial-pagos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<div class="row">
    <!-- Columna Principal - Información del Pago -->
    <div class="col-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Información del Pago
                </h3>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Número de Comprobante:</label>
                        <div class="detail-value">
                            <strong>{{ $pago->numero_comprobante }}</strong>
                        </div>
                    </div>

                    <div class="detail-item">
                        <label>Fecha de Pago:</label>
                        <div class="detail-value">
                            <span class="badge badge-secondary">
                                <i class="fas fa-calendar"></i>
                                {{ $pago->fecha_pago->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <label>Monto Pagado:</label>
                        <div class="detail-value">
                            <strong class="monto-valor">${{ number_format($pago->monto_pagado, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <div class="detail-item">
                        <label>Método de Pago:</label>
                        <div class="detail-value">
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
                            @elseif($pago->metodo_pago == 'credito')
                                <span class="badge badge-success">
                                    <i class="fas fa-credit-card"></i> Crédito/Débito
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="detail-item">
                        <label>Estado del Pago:</label>
                        <div class="detail-value">
                            @if($pago->estado_pago == 'pagado')
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Pagado
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    <i class="fas fa-clock"></i> Pendiente
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($pago->observaciones)
                    <div class="detail-item full-width">
                        <label>Observaciones:</label>
                        <div class="detail-value">
                            {{ $pago->observaciones }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($pago->boleta)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-invoice"></i>
                    Información de la Boleta
                </h3>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Número de Boleta:</label>
                        <div class="detail-value">
                            <strong>{{ $pago->boleta->numero_boleta }}</strong>
                        </div>
                    </div>

                    <div class="detail-item">
                        <label>Fecha Emisión:</label>
                        <div class="detail-value">
                            {{ $pago->boleta->fecha_emision->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="detail-item">
                        <label>Monto Total:</label>
                        <div class="detail-value">
                            ${{ number_format($pago->boleta->monto_total, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="detail-item">
                        <label>Estado:</label>
                        <div class="detail-value">
                            @if($pago->boleta->estado == 'pagada')
                                <span class="badge badge-success">Pagada</span>
                            @elseif($pago->boleta->estado == 'pendiente')
                                <span class="badge badge-warning">Pendiente</span>
                            @else
                                <span class="badge badge-danger">Anulada</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Columna Lateral - Información del Socio -->
    <div class="col-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user"></i>
                    Información del Socio
                </h3>
            </div>
            <div class="card-body">
                <div class="socio-info">
                    <div class="socio-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h4>{{ $pago->socio->nombre_completo }}</h4>
                    <p class="text-muted">{{ $pago->socio->rut }}</p>

                    <div class="socio-details">
                        <div class="socio-detail-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $pago->socio->direccion ?? 'No especificada' }}</span>
                        </div>
                        @if($pago->socio->telefono)
                        <div class="socio-detail-item">
                            <i class="fas fa-phone"></i>
                            <span>{{ $pago->socio->telefono }}</span>
                        </div>
                        @endif
                        @if($pago->socio->email)
                        <div class="socio-detail-item">
                            <i class="fas fa-envelope"></i>
                            <span>{{ $pago->socio->email }}</span>
                        </div>
                        @endif
                    </div>

                    <a href="{{ route('historial-pagos.analisis-socio', $pago->socio->id) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-chart-line"></i>
                        Ver Análisis Completo
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history"></i>
                    Pagos Recientes
                </h3>
            </div>
            <div class="card-body">
                @forelse($historialSocio as $historial)
                    <div class="historial-item {{ $historial->id == $pago->id ? 'active' : '' }}">
                        <div class="historial-date">
                            <i class="fas fa-calendar"></i>
                            {{ $historial->fecha_pago->format('d/m/Y') }}
                        </div>
                        <div class="historial-amount">
                            ${{ number_format($historial->monto_pagado, 0, ',', '.') }}
                        </div>
                        <div class="historial-method">
                            @if($historial->metodo_pago == 'efectivo')
                                <span class="badge badge-primary badge-sm">Efectivo</span>
                            @elseif($historial->metodo_pago == 'transferencia')
                                <span class="badge badge-info badge-sm">Transfer.</span>
                            @else
                                <span class="badge badge-secondary badge-sm">Cheque</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state-small">
                        <i class="fas fa-inbox"></i>
                        <p>No hay otros pagos registrados</p>
                    </div>
                @endforelse
            </div>
        </div>
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

    .row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    .col-8, .col-4 {
        min-width: 0;
    }

    .card {
        background: var(--white);
        border-radius: 8px;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        margin-bottom: 24px;
    }

    .card-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    .card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-700);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-body {
        padding: 24px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .detail-item.full-width {
        grid-column: 1 / -1;
    }

    .detail-item label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-600);
    }

    .detail-value {
        font-size: 0.9375rem;
        color: var(--gray-800);
    }

    .monto-valor {
        color: var(--success);
        font-weight: 700;
        font-size: 1.25rem;
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

    .badge-sm {
        padding: 2px 6px;
        font-size: 0.7rem;
    }

    .badge-success { background: var(--success-light); color: var(--success-dark); }
    .badge-warning { background: var(--warning-light); color: var(--warning-dark); }
    .badge-danger { background: var(--danger-light); color: var(--danger-dark); }
    .badge-info { background: var(--info-light); color: var(--info-dark); }
    .badge-secondary { background: var(--gray-200); color: var(--gray-700); }
    .badge-primary { background: var(--primary-light); color: var(--primary-dark); }

    .socio-info {
        text-align: center;
    }

    .socio-avatar {
        font-size: 4rem;
        color: var(--primary);
        margin-bottom: 12px;
    }

    .socio-info h4 {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 4px;
    }

    .socio-details {
        margin: 20px 0;
        text-align: left;
    }

    .socio-detail-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid var(--gray-100);
        font-size: 0.875rem;
        color: var(--gray-700);
    }

    .socio-detail-item i {
        color: var(--primary);
        width: 16px;
    }

    .historial-item {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 8px;
        padding: 12px;
        border-bottom: 1px solid var(--gray-100);
        font-size: 0.875rem;
        align-items: center;
    }

    .historial-item.active {
        background: var(--primary-light);
        border-radius: 6px;
        border-bottom-color: transparent;
    }

    .historial-date {
        color: var(--gray-600);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .historial-date i {
        font-size: 0.75rem;
    }

    .historial-amount {
        font-weight: 700;
        color: var(--gray-800);
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
        justify-content: center;
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

    .btn-block {
        width: 100%;
        display: flex;
    }

    .text-muted {
        color: var(--gray-500);
    }

    .empty-state-small {
        text-align: center;
        padding: 20px;
    }

    .empty-state-small i {
        font-size: 2rem;
        color: var(--gray-400);
        margin-bottom: 8px;
    }

    .empty-state-small p {
        color: var(--gray-600);
        font-size: 0.875rem;
        margin: 0;
    }

    @media (max-width: 768px) {
        .row {
            grid-template-columns: 1fr;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }
    }
</style>
@endsection
