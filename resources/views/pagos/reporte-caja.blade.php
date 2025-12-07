@extends('layouts.app')

@section('title', 'Reporte de Caja - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-cash-register"></i>
        Reporte de Caja
    </h2>
    <div class="btn-group">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i>
            Imprimir
        </button>
        <a href="{{ route('pagos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<!-- Filtro de Fecha -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('pagos.reporteCaja') }}" class="filter-form-inline">
            <div class="form-group-inline">
                <label for="fecha">Fecha:</label>
                <input type="date"
                       id="fecha"
                       name="fecha"
                       value="{{ $fecha }}"
                       class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                Consultar
            </button>
        </form>
    </div>
</div>

<!-- Resumen del Día -->
<div class="stats-row mb-4">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Fecha</div>
            <div class="stat-value">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #10b981;">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Pagos</div>
            <div class="stat-value">{{ $pagos->count() }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #f59e0b;">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Recaudado</div>
            <div class="stat-value">${{ number_format($totalDia, 0, ',', '.') }}</div>
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
                        <div class="metodo-value">${{ number_format($metodo->total, 0, ',', '.') }}</div>
                    </div>
                </div>
            @endforeach

            @if($totalesPorMetodo->isEmpty())
                <div class="text-center text-muted" style="grid-column: span 5;">
                    No hay pagos registrados para esta fecha
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Detalle de Pagos -->
@if($pagos->count() > 0)
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list"></i>
            Detalle de Pagos del Día
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>N° Recibo</th>
                        <th>Socio</th>
                        <th>N° Boleta</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <th>Comprobante</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pagos as $index => $pago)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $pago->numero_recibo }}</strong></td>
                            <td>{{ $pago->socio->nombre_completo }}</td>
                            <td>{{ $pago->boleta->numero_boleta }}</td>
                            <td>
                                <span class="badge badge-{{
                                    $pago->metodo_pago == 'efectivo' ? 'success' :
                                    ($pago->metodo_pago == 'transferencia' ? 'info' : 'secondary')
                                }}">
                                    {{ ucfirst($pago->metodo_pago) }}
                                </span>
                            </td>
                            <td><strong>{{ $pago->monto_pagado_formateado }}</strong></td>
                            <td>{{ $pago->numero_comprobante ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="5" class="text-right"><strong>TOTAL DEL DÍA:</strong></td>
                        <td colspan="2"><strong class="total-amount">${{ number_format($totalDia, 0, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif
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

    .metodo-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
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

    .table tbody tr:hover {
        background: var(--gray-50);
    }

    .total-row {
        background: var(--gray-100);
        border-top: 2px solid var(--gray-300);
    }

    .total-row td {
        padding: 16px 12px;
        font-size: 1rem;
        border: none;
    }

    .text-right {
        text-align: right;
    }

    .total-amount {
        color: var(--primary);
        font-size: 1.25rem;
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

    .text-muted {
        color: var(--gray-500);
    }

    .mb-3 {
        margin-bottom: 16px;
    }

    .mb-4 {
        margin-bottom: 24px;
    }

    /* Estilos para impresión */
    @media print {
        .page-header .btn-group,
        .card:first-of-type {
            display: none !important;
        }

        .card {
            box-shadow: none;
            border: 1px solid #000;
            page-break-inside: avoid;
        }

        .stat-card {
            box-shadow: none;
            border: 1px solid #000;
        }

        body {
            background: white;
        }
    }

    @media (max-width: 768px) {
        .filter-form-inline {
            flex-direction: column;
            align-items: stretch;
        }

        .form-group-inline {
            flex-direction: column;
            align-items: flex-start;
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
