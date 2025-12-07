@extends('layouts.app')

@section('title', 'Detalle Giro Bancario - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-money-check"></i>
        Giro: {{ $giro->numero_giro }}
    </h2>
    <div class="btn-group">
        <a href="{{ route('giros-bancarios.edit', $giro->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('giros-bancarios.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<div class="row">
    <!-- Información del Giro -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-invoice-dollar"></i>
                    Información del Giro Bancario
                </h3>
                {!! $giro->estado_badge !!}
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Número de Giro</label>
                        <value><strong>{{ $giro->numero_giro }}</strong></value>
                    </div>

                    <div class="info-item">
                        <label>Fecha de Emisión</label>
                        <value>{{ $giro->fecha_emision_formateada }}</value>
                    </div>

                    <div class="info-item">
                        <label>Banco</label>
                        <value>{{ $giro->banco }}</value>
                    </div>

                    <div class="info-item">
                        <label>Número de Cuenta</label>
                        <value>{{ $giro->numero_cuenta }}</value>
                    </div>

                    <div class="info-item">
                        <label>Tipo de Cuenta</label>
                        <value>{!! $giro->tipo_cuenta_badge !!}</value>
                    </div>

                    <div class="info-item">
                        <label>Estado</label>
                        <value>{!! $giro->estado_badge !!}</value>
                    </div>

                    <div class="info-item full-width">
                        <label>Beneficiario</label>
                        <value>{{ $giro->beneficiario }}</value>
                    </div>

                    @if($giro->rut_beneficiario)
                    <div class="info-item">
                        <label>RUT Beneficiario</label>
                        <value>{{ $giro->rut_beneficiario }}</value>
                    </div>
                    @endif

                    <div class="info-item">
                        <label>Monto</label>
                        <value><strong class="monto-destacado">{{ $giro->monto_formateado }}</strong></value>
                    </div>

                    <div class="info-item full-width">
                        <label>Concepto</label>
                        <value>{{ $giro->concepto }}</value>
                    </div>

                    @if($giro->descripcion)
                    <div class="info-item full-width">
                        <label>Descripción</label>
                        <value>{{ $giro->descripcion }}</value>
                    </div>
                    @endif

                    <div class="info-item">
                        <label>Método de Entrega</label>
                        <value>{{ $giro->metodo_entrega_texto }}</value>
                    </div>

                    @if($giro->numero_comprobante)
                    <div class="info-item">
                        <label>Número de Comprobante</label>
                        <value>{{ $giro->numero_comprobante }}</value>
                    </div>
                    @endif

                    @if($giro->responsable)
                    <div class="info-item">
                        <label>Responsable</label>
                        <value>{{ $giro->responsable->nombre_completo }}</value>
                    </div>
                    @endif

                    @if($giro->fecha_pago)
                    <div class="info-item">
                        <label>Fecha de Pago</label>
                        <value>{{ $giro->fecha_pago_formateada }}</value>
                    </div>
                    @endif

                    @if($giro->observaciones)
                    <div class="info-item full-width">
                        <label>Observaciones</label>
                        <value>{{ $giro->observaciones }}</value>
                    </div>
                    @endif

                    <div class="info-item">
                        <label>Registrado Hace</label>
                        <value>{{ $giro->fecha_creacion->diffForHumans() }}</value>
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
                    <div class="stat-icon" style="background: #10b981;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ $giro->monto_formateado }}</div>
                        <div class="stat-label">Monto</div>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon" style="background: {{ $giro->estado == 'emitido' ? '#3b82f6' : ($giro->estado == 'pagado' ? '#10b981' : '#f59e0b') }};">
                        <i class="fas fa-{{ $giro->estado == 'emitido' ? 'hourglass-half' : ($giro->estado == 'pagado' ? 'check-circle' : 'exclamation-triangle') }}"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ $giro->estado_texto }}</div>
                        <div class="stat-label">Estado</div>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon" style="background: #06b6d4;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ $giro->fecha_emision->format('d/m/Y') }}</div>
                        <div class="stat-label">Fecha Emisión</div>
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
                <a href="{{ route('giros-bancarios.edit', $giro->id) }}" class="action-btn">
                    <i class="fas fa-edit"></i>
                    Editar Giro
                </a>
                <form action="{{ route('giros-bancarios.destroy', $giro->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('¿Está seguro de eliminar este giro bancario?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn danger">
                        <i class="fas fa-trash"></i>
                        Eliminar Giro
                    </button>
                </form>
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

    .monto-destacado {
        color: var(--success);
        font-size: 1.25rem;
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

    .badge-primary {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
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
