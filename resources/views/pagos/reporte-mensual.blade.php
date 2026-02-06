@extends('layouts.app')

@section('title', 'Reporte Mensual - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-calendar-alt"></i>
        Reporte Mensual de Ingresos
    </h2>
    <div class="btn-group">
        <a href="{{ route('pagos.reporteMensual.imprimir', ['mes' => $mes]) }}" class="btn btn-primary" target="_blank">
            <i class="fas fa-file-pdf"></i>
            Descargar PDF
        </a>
        <a href="{{ route('pagos.reporteCaja') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<!-- Filtro de Mes -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('pagos.reporteMensual') }}" class="filter-form-inline">
            <div class="form-group-inline">
                <label for="mes">Mes:</label>
                <input type="month"
                       id="mes"
                       name="mes"
                       value="{{ $mes }}"
                       class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                Consultar
            </button>
        </form>
    </div>
</div>

<!-- Resumen del Mes -->
<div class="stats-row mb-4">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Período</div>
            <div class="stat-value">{{ \Carbon\Carbon::parse($mes . '-01')->locale('es')->isoFormat('MMMM YYYY') }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #10b981;">
            <i class="fas fa-receipt"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Transacciones</div>
            <div class="stat-value">{{ $totalPagos }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #f59e0b;">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Recaudado</div>
            <div class="stat-value">${{ number_format($totalMes, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #6366f1;">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Promedio Diario</div>
            <div class="stat-value">${{ number_format($totalMes / count($diasDelMes), 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<!-- Totales por Método de Pago -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-pie"></i>
            Totales por Método de Pago
        </h3>
    </div>
    <div class="card-body">
        <div class="metodos-pago-grid">
            @foreach($totalesPorMetodo as $metodo)
                <div class="metodo-card">
                    <div class="metodo-icon">
                        @switch($metodo->metodo_pago)
                            @case('efectivo')
                                <i class="fas fa-money-bill-wave"></i>
                                @break
                            @case('transferencia')
                                <i class="fas fa-exchange-alt"></i>
                                @break
                            @case('cheque')
                                <i class="fas fa-money-check"></i>
                                @break
                            @case('debito')
                                <i class="fas fa-credit-card"></i>
                                @break
                            @case('credito')
                                <i class="fas fa-credit-card"></i>
                                @break
                            @default
                                <i class="fas fa-wallet"></i>
                        @endswitch
                    </div>
                    <div class="metodo-info">
                        <div class="metodo-label">{{ ucfirst($metodo->metodo_pago) }}</div>
                        <div class="metodo-count">{{ $metodo->cantidad }} pagos</div>
                        <div class="metodo-value">${{ number_format($metodo->total, 0, ',', '.') }}</div>
                    </div>
                </div>
            @endforeach

            @if($totalesPorMetodo->isEmpty())
                <div class="text-center text-muted" style="grid-column: span 5;">
                    No hay pagos registrados para este mes
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Calendario Mensual de Transacciones -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-calendar"></i>
            Detalle Diario del Mes
        </h3>
    </div>
    <div class="card-body">
        <div class="calendar-grid">
            @foreach($diasDelMes as $dia)
                <div class="dia-card {{ $dia['cantidad'] == 0 ? 'sin-transacciones' : 'con-transacciones' }}">
                    <div class="dia-header">
                        <div class="dia-numero">{{ date('d', strtotime($dia['fecha'])) }}</div>
                        <div class="dia-nombre">{{ substr($dia['dia_semana'], 0, 3) }}</div>
                    </div>
                    <div class="dia-contenido">
                        @if($dia['cantidad'] > 0)
                            <div class="dia-stats">
                                <div class="stat-item">
                                    <i class="fas fa-receipt"></i>
                                    <span>{{ $dia['cantidad'] }} pago{{ $dia['cantidad'] > 1 ? 's' : '' }}</span>
                                </div>
                                <div class="stat-item total">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span>${{ number_format($dia['total'], 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Detalle de transacciones -->
                            <div class="transacciones-lista">
                                @foreach($dia['pagos'] as $pago)
                                    <div class="transaccion-item">
                                        <div class="transaccion-info">
                                            <strong>{{ $pago->numero_recibo }}</strong>
                                            <span class="socio-nombre">{{ $pago->socio->nombre_completo }}</span>
                                        </div>
                                        <div class="transaccion-detalles">
                                            <span class="badge badge-{{
                                                $pago->metodo_pago == 'efectivo' ? 'success' :
                                                ($pago->metodo_pago == 'transferencia' ? 'info' :
                                                ($pago->metodo_pago == 'credito' ? 'warning' : 'secondary'))
                                            }}">
                                                {{ ucfirst($pago->metodo_pago) }}
                                            </span>
                                            <strong class="monto">${{ number_format($pago->monto_pagado, 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="sin-pagos">
                                <i class="fas fa-ban"></i>
                                <span>Sin transacciones</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
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

    .filter-form-inline {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .form-group-inline {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-group-inline label {
        font-weight: 600;
        color: var(--gray-700);
        font-size: 0.875rem;
        margin: 0;
    }

    .form-control {
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
        background: var(--white);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
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
        transition: all 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .bg-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: 4px;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.05em;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        text-transform: capitalize;
    }

    .metodos-pago-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .metodo-card {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .metodo-icon {
        width: 48px;
        height: 48px;
        background: var(--primary);
        color: white;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .metodo-info {
        flex: 1;
    }

    .metodo-label {
        font-size: 0.75rem;
        color: var(--gray-600);
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.05em;
    }

    .metodo-count {
        font-size: 0.75rem;
        color: var(--gray-500);
        margin: 2px 0;
    }

    .metodo-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
    }

    /* Calendario de días */
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 16px;
    }

    .dia-card {
        background: var(--white);
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        overflow: hidden;
        transition: all 0.3s;
    }

    .dia-card.con-transacciones {
        border-color: #10b981;
    }

    .dia-card.sin-transacciones {
        opacity: 0.6;
    }

    .dia-card.con-transacciones:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .dia-header {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dia-card.sin-transacciones .dia-header {
        background: var(--gray-400);
    }

    .dia-numero {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .dia-nombre {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 600;
    }

    .dia-contenido {
        padding: 12px;
    }

    .dia-stats {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 12px;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .stat-item.total {
        color: #10b981;
        font-weight: 700;
        font-size: 1rem;
    }

    .stat-item i {
        font-size: 0.75rem;
    }

    .transacciones-lista {
        border-top: 1px solid var(--gray-200);
        padding-top: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-height: 200px;
        overflow-y: auto;
    }

    .transaccion-item {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: 6px;
        padding: 8px;
        font-size: 0.75rem;
    }

    .transaccion-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
        margin-bottom: 6px;
    }

    .transaccion-info strong {
        color: var(--primary);
        font-size: 0.875rem;
    }

    .socio-nombre {
        color: var(--gray-600);
        font-size: 0.75rem;
    }

    .transaccion-detalles {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .monto {
        color: var(--dark);
        font-size: 0.875rem;
    }

    .sin-pagos {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 20px;
        color: var(--gray-400);
    }

    .sin-pagos i {
        font-size: 2rem;
    }

    .sin-pagos span {
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: var(--gray-500);
    }

    .mb-3 {
        margin-bottom: 16px;
    }

    .mb-4 {
        margin-bottom: 24px;
    }

    @media (max-width: 768px) {
        .calendar-grid {
            grid-template-columns: 1fr;
        }

        .stats-row {
            grid-template-columns: 1fr;
        }

        .metodos-pago-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
