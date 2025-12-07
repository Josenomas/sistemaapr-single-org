@extends('layouts.app')

@section('title', 'Detalle Pago - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-receipt"></i>
        Recibo: {{ $pago->numero_recibo }}
    </h2>
    <div class="btn-group">
        <a href="{{ route('pagos.edit', $pago->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('pagos.imprimir', $pago->id) }}" class="btn btn-success" target="_blank">
            <i class="fas fa-print"></i>
            Imprimir
        </a>
        <form action="{{ route('pagos.destroy', $pago->id) }}"
              method="POST"
              style="display: inline;"
              onsubmit="return confirm('¿Está seguro de eliminar este pago?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i>
                Eliminar
            </button>
        </form>
        <a href="{{ route('pagos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<div class="row">
    <!-- Información Principal -->
    <div class="col-md-8">
        <!-- Información del Recibo -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-receipt"></i>
                    Información del Recibo
                </h3>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>N° Recibo</label>
                        <value><strong>{{ $pago->numero_recibo }}</strong></value>
                    </div>

                    <div class="info-item">
                        <label>Fecha de Pago</label>
                        <value>{{ $pago->fecha_pago_formateada }}</value>
                    </div>

                    <div class="info-item">
                        <label>Monto Pagado</label>
                        <value><strong style="color: #10b981;">{{ $pago->monto_pagado_formateado }}</strong></value>
                    </div>

                    <div class="info-item">
                        <label>Método de Pago</label>
                        <value>{!! $pago->metodo_pago_badge !!}</value>
                    </div>

                    @if($pago->numero_comprobante)
                    <div class="info-item">
                        <label>N° Comprobante</label>
                        <value>{{ $pago->numero_comprobante }}</value>
                    </div>
                    @endif

                    <div class="info-item">
                        <label>Fecha de Registro</label>
                        <value>{{ $pago->fecha_creacion->format('d/m/Y H:i') }}</value>
                    </div>

                    @if($pago->usuarioRegistro)
                    <div class="info-item">
                        <label>Registrado por</label>
                        <value>{{ $pago->usuarioRegistro->nombre_usuario ?? $pago->usuarioRegistro->name }}</value>
                    </div>
                    @endif

                    @if($pago->observaciones)
                    <div class="info-item full-width">
                        <label>Observaciones</label>
                        <value>{{ $pago->observaciones }}</value>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información del Socio -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user"></i>
                    Información del Socio
                </h3>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>N° Socio</label>
                        <value>{{ $pago->socio->numero_socio }}</value>
                    </div>

                    <div class="info-item">
                        <label>RUT</label>
                        <value>{{ $pago->socio->rut }}</value>
                    </div>

                    <div class="info-item full-width">
                        <label>Nombre</label>
                        <value>
                            <a href="{{ route('socios.show', $pago->socio->id) }}" class="link">
                                {{ $pago->socio->nombre_completo }}
                            </a>
                        </value>
                    </div>

                    @if($pago->socio->telefono)
                    <div class="info-item">
                        <label>Teléfono</label>
                        <value>{{ $pago->socio->telefono }}</value>
                    </div>
                    @endif

                    @if($pago->socio->email)
                    <div class="info-item">
                        <label>Email</label>
                        <value>{{ $pago->socio->email }}</value>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información de la Boleta -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-invoice"></i>
                    Información de la Boleta
                </h3>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>N° Boleta</label>
                        <value>
                            <a href="{{ route('boletas.show', $pago->boleta->id) }}" class="link">
                                {{ $pago->boleta->numero_boleta }}
                            </a>
                        </value>
                    </div>

                    <div class="info-item">
                        <label>Período</label>
                        <value>{{ $pago->boleta->mes_texto }}</value>
                    </div>

                    <div class="info-item">
                        <label>Total Boleta</label>
                        <value>{{ $pago->boleta->total_formateado }}</value>
                    </div>

                    <div class="info-item">
                        <label>Estado Boleta</label>
                        <value>{!! $pago->boleta->estado_badge !!}</value>
                    </div>

                    <div class="info-item">
                        <label>Fecha Emisión</label>
                        <value>{{ $pago->boleta->fecha_emision_formateada }}</value>
                    </div>

                    <div class="info-item">
                        <label>Fecha Vencimiento</label>
                        <value>{{ $pago->boleta->fecha_vencimiento_formateada }}</value>
                    </div>

                    <div class="info-item">
                        <label>Total Pagado</label>
                        <value><strong>${{ number_format($pago->boleta->total - $saldo, 0, ',', '.') }}</strong></value>
                    </div>

                    <div class="info-item">
                        <label>Saldo Pendiente</label>
                        <value>
                            <strong style="color: {{ $saldo > 0 ? '#ef4444' : '#10b981' }};">
                                ${{ number_format($saldo, 0, ',', '.') }}
                            </strong>
                        </value>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Lateral -->
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
                    <div class="stat-icon" style="background: #3b82f6;">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ $pago->monto_pagado_formateado }}</div>
                        <div class="stat-label">Monto Pagado</div>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon" style="background: {{ $saldo > 0 ? '#f59e0b' : '#10b981' }};">
                        <i class="fas {{ $saldo > 0 ? 'fa-exclamation-circle' : 'fa-check-circle' }}"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">${{ number_format($saldo, 0, ',', '.') }}</div>
                        <div class="stat-label">Saldo {{ $saldo > 0 ? 'Pendiente' : 'Liquidado' }}</div>
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
                <a href="{{ route('pagos.imprimir', $pago->id) }}" class="action-btn" target="_blank">
                    <i class="fas fa-print"></i>
                    Imprimir Recibo
                </a>
                <a href="{{ route('pagos.edit', $pago->id) }}" class="action-btn">
                    <i class="fas fa-edit"></i>
                    Editar Pago
                </a>
                <a href="{{ route('socios.show', $pago->socio->id) }}" class="action-btn">
                    <i class="fas fa-user"></i>
                    Ver Socio
                </a>
                <a href="{{ route('boletas.show', $pago->boleta->id) }}" class="action-btn">
                    <i class="fas fa-file-invoice"></i>
                    Ver Boleta
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Otros pagos de la misma boleta -->
@if($pago->boleta->pagos->where('id', '!=', $pago->id)->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list"></i>
            Otros Pagos de esta Boleta
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>N° Recibo</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pago->boleta->pagos->where('id', '!=', $pago->id)->sortByDesc('fecha_pago') as $otroPago)
                        <tr>
                            <td><strong>{{ $otroPago->numero_recibo }}</strong></td>
                            <td>{{ $otroPago->fecha_pago_formateada }}</td>
                            <td>{{ $otroPago->monto_pagado_formateado }}</td>
                            <td>{!! $otroPago->metodo_pago_badge !!}</td>
                            <td>
                                <a href="{{ route('pagos.show', $otroPago->id) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
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

    .link {
        color: var(--primary);
        text-decoration: none;
    }

    .link:hover {
        text-decoration: underline;
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

    .btn-success {
        background: #10b981;
        color: white;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .btn-secondary {
        background: var(--gray-500);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-600);
    }

    .btn:hover {
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

    .badge {
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
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

        .btn-group {
            flex-wrap: wrap;
        }
    }
</style>
@endsection
