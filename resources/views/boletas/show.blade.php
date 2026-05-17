@extends('layouts.app')

@section('title', 'Detalle Boleta - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-file-invoice-dollar"></i>
        Detalle de la Boleta
    </h2>
    <div class="header-actions">
        @if($boleta->estado !== 'pagada' && !$boleta->folio_sii)
        <a href="{{ route('boletas.edit', $boleta->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        @endif
        <a href="{{ route('boletas.imprimir', $boleta->id) }}" class="btn btn-secondary" target="_blank">
            <i class="fas fa-print"></i>
            Imprimir
        </a>
        <form action="{{ route('boletas.enviar-email', $boleta->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Enviar esta boleta por correo electrónico a {{ $boleta->socio->email }}?');">
            @csrf
            <button type="submit" class="btn btn-info" {{ !$boleta->socio->email ? 'disabled' : '' }}>
                <i class="fas fa-envelope"></i>
                Enviar por Email
            </button>
        </form>
        @if($boleta->estado !== 'pagada' && $boleta->estado !== 'anulada' && $boleta->pagos->count() == 0)
        <form action="{{ route('boletas.anular', $boleta->id) }}"
              method="POST"
              style="display: inline;"
              onsubmit="return confirm('¿Está seguro de anular esta boleta?');">
            @csrf
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-ban"></i>
                Anular
            </button>
        </form>
        @endif
        <a href="{{ route('boletas.index') }}" class="btn btn-secondary">
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

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<!-- Información Principal -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Información de la Boleta</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Número de Boleta:</span>
                <span class="detail-value"><strong>{{ $boleta->numero_boleta }}</strong></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Mes:</span>
                <span class="detail-value"><strong>{{ $boleta->mes_texto }}</strong></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Estado:</span>
                <span class="detail-value">{!! $boleta->estado_badge !!}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Fecha de Emisión:</span>
                <span class="detail-value">{{ $boleta->fecha_emision_formateada }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Fecha de Vencimiento:</span>
                <span class="detail-value">
                    {{ $boleta->fecha_vencimiento_formateada }}
                    @if($boleta->dias_atraso > 0)
                        <br><small class="text-danger">
                            <i class="fas fa-exclamation-triangle"></i> Vencida hace {{ $boleta->dias_atraso }} días
                        </small>
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Información del Socio -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Información del Socio</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Número de Socio:</span>
                <span class="detail-value">
                    <a href="{{ route('socios.show', $boleta->socio->id) }}">
                        {{ $boleta->socio->numero_socio }}
                    </a>
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Nombre Completo:</span>
                <span class="detail-value">
                    <a href="{{ route('socios.show', $boleta->socio->id) }}">
                        {{ $boleta->socio->nombre_completo }}
                    </a>
                </span>
            </div>

            @if($boleta->socio->rut)
            <div class="detail-item">
                <span class="detail-label">RUT:</span>
                <span class="detail-value">{{ $boleta->socio->rut }}</span>
            </div>
            @endif

            @if($boleta->socio->direccion)
            <div class="detail-item">
                <span class="detail-label">Dirección:</span>
                <span class="detail-value">{{ $boleta->socio->direccion }}</span>
            </div>
            @endif

            @if($boleta->socio->telefono)
            <div class="detail-item">
                <span class="detail-label">Teléfono:</span>
                <span class="detail-value">{{ $boleta->socio->telefono }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Detalles de Consumo y Cargos -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Detalles de Consumo y Cargos</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Consumo:</span>
                <span class="detail-value"><strong>{{ $boleta->consumo_m3 }} m³</strong></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Cargo Fijo:</span>
                <span class="detail-value">{{ $boleta->cargo_fijo_formateado }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Cargo por Consumo:</span>
                <span class="detail-value">{{ $boleta->cargo_consumo_formateado }}</span>
            </div>

            @if($boleta->otros_cargos > 0)
            <div class="detail-item">
                <span class="detail-label">Otros Cargos:</span>
                <span class="detail-value">{{ $boleta->otros_cargos_formateado }}</span>
            </div>
            @endif

            @if($boleta->descuentos > 0)
            <div class="detail-item">
                <span class="detail-label">Descuentos:</span>
                <span class="detail-value text-success">-{{ $boleta->descuentos_formateado }}</span>
            </div>
            @endif

            <div class="detail-item total-item">
                <span class="detail-label">TOTAL A PAGAR:</span>
                <span class="detail-value total-value">{{ $boleta->total_formateado }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Pagos Realizados -->
@if($boleta->pagos->count() > 0)
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Pagos Realizados</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha de Pago</th>
                        <th>Número de Comprobante</th>
                        <th>Método de Pago</th>
                        <th>Monto Pagado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($boleta->pagos as $pago)
                    <tr>
                        <td>{{ $pago->fecha_pago ? date('d/m/Y', strtotime($pago->fecha_pago)) : '-' }}</td>
                        <td>{{ $pago->numero_comprobante ?? '-' }}</td>
                        <td>{{ ucfirst($pago->metodo_pago ?? '-') }}</td>
                        <td><strong>${{ number_format($pago->monto_pagado, 0, ',', '.') }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Total Pagado:</strong></td>
                        <td><strong>${{ number_format($boleta->pagos->sum('monto_pagado'), 0, ',', '.') }}</strong></td>
                    </tr>
                    @if($boleta->monto_pendiente > 0)
                    <tr>
                        <td colspan="3" class="text-right"><strong>Saldo Pendiente:</strong></td>
                        <td><strong class="text-danger">{{ $boleta->monto_pendiente_formateado }}</strong></td>
                    </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>
</div>
@else
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Pagos Realizados</h3>
    </div>
    <div class="card-body">
        <p class="text-muted text-center">
            <i class="fas fa-info-circle"></i>
            No se han registrado pagos para esta boleta
        </p>
    </div>
</div>
@endif

<!-- Observaciones -->
@if($boleta->observaciones)
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Observaciones</h3>
    </div>
    <div class="card-body">
        <p class="section-content">{{ $boleta->observaciones }}</p>
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
                <span class="detail-value">{{ $boleta->fecha_creacion_formateada }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Última Actualización:</span>
                <span class="detail-value">{{ $boleta->fecha_actualizacion_formateada }}</span>
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

    .detail-item.total-item {
        grid-column: 1 / -1;
        padding: 16px;
        background: var(--gray-50);
        border-radius: var(--radius);
        border: 2px solid var(--primary);
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

    .total-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--primary);
    }

    .section-content {
        font-size: 0.95rem;
        color: var(--gray-700);
        line-height: 1.6;
        margin: 0;
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

    .table tfoot td {
        font-weight: 600;
        border-top: 2px solid var(--gray-200);
    }

    .text-right {
        text-align: right;
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

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
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

    .text-success {
        color: #059669;
    }

    .text-danger {
        color: #dc2626;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .header-actions {
            width: 100%;
            flex-wrap: wrap;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
