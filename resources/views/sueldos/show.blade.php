@extends('layouts.app')

@section('title', 'Ver Sueldo - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-money-check-alt"></i>
        Detalle del Sueldo
    </h2>
    <div class="header-actions">
        <a href="{{ route('sueldos.edit', $sueldo->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('sueldos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <div class="header-content">
            <div class="funcionario-avatar">
                {{ $sueldo->funcionario->iniciales }}
            </div>
            <div class="header-info">
                <h3 class="funcionario-nombre">{{ $sueldo->funcionario->nombre_completo }}</h3>
                <p class="funcionario-cargo">
                    <i class="fas fa-briefcase"></i>
                    {{ $sueldo->funcionario->cargo }}
                </p>
            </div>
        </div>
        <div class="header-badge">
            @if($sueldo->estado === 'pendiente')
                <span class="badge badge-warning">Pendiente</span>
            @elseif($sueldo->estado === 'pagado')
                <span class="badge badge-success">Pagado</span>
            @else
                <span class="badge badge-danger">Anulado</span>
            @endif
        </div>
    </div>

    <div class="card-body">
        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-calendar-alt"></i>
                Período y Fecha
            </h4>
            <div class="info-grid">
                <div class="info-item">
                    <label>Período</label>
                    <span class="highlight">{{ $sueldo->periodo_formateado }}</span>
                </div>
                <div class="info-item">
                    <label>Fecha de Pago</label>
                    <span>{{ $sueldo->fecha_pago->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-calculator"></i>
                Detalle de Montos
            </h4>
            <div class="montos-card">
                <div class="monto-item">
                    <div class="monto-label">
                        <i class="fas fa-dollar-sign"></i>
                        Sueldo Base
                    </div>
                    <div class="monto-valor base">{{ $sueldo->sueldo_base_formateado }}</div>
                </div>
                <div class="monto-item positivo">
                    <div class="monto-label">
                        <i class="fas fa-plus-circle"></i>
                        Bonos
                    </div>
                    <div class="monto-valor">{{ $sueldo->bonos_formateado }}</div>
                </div>
                <div class="monto-item negativo">
                    <div class="monto-label">
                        <i class="fas fa-minus-circle"></i>
                        Descuentos
                    </div>
                    <div class="monto-valor">{{ $sueldo->descuentos_formateado }}</div>
                </div>
                <div class="monto-item total">
                    <div class="monto-label">
                        <i class="fas fa-money-check-alt"></i>
                        Total Líquido
                    </div>
                    <div class="monto-valor">{{ $sueldo->total_liquido_formateado }}</div>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-credit-card"></i>
                Información de Pago
            </h4>
            <div class="info-grid">
                <div class="info-item">
                    <label>Método de Pago</label>
                    <span>
                        @if($sueldo->metodo_pago === 'efectivo')
                            <i class="fas fa-money-bill-wave"></i> Efectivo
                        @elseif($sueldo->metodo_pago === 'transferencia')
                            <i class="fas fa-exchange-alt"></i> Transferencia
                        @else
                            <i class="fas fa-money-check"></i> Cheque
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <label>Estado</label>
                    <span>
                        @if($sueldo->estado === 'pendiente')
                            <span class="badge badge-warning">Pendiente</span>
                        @elseif($sueldo->estado === 'pagado')
                            <span class="badge badge-success">Pagado</span>
                        @else
                            <span class="badge badge-danger">Anulado</span>
                        @endif
                    </span>
                </div>
                @if($sueldo->comprobante)
                <div class="info-item full-width">
                    <label>Número de Comprobante</label>
                    <span class="comprobante">{{ $sueldo->comprobante }}</span>
                </div>
                @endif
            </div>
        </div>

        @if($sueldo->observaciones)
        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-sticky-note"></i>
                Observaciones
            </h4>
            <div class="observations-box">
                {{ $sueldo->observaciones }}
            </div>
        </div>
        @endif

        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-info-circle"></i>
                Información del Registro
            </h4>
            <div class="info-grid">
                <div class="info-item">
                    <label>Fecha de Creación</label>
                    <span>{{ $sueldo->fecha_creacion->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-item">
                    <label>Última Actualización</label>
                    <span>{{ $sueldo->fecha_actualizacion->format('d/m/Y H:i') }}</span>
                </div>
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
        gap: 8px;
    }

    .alert {
        padding: 16px 20px;
        border-radius: var(--radius);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #059669;
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
    }

    .card-header {
        padding: 24px;
        border-bottom: 2px solid var(--gray-200);
        background: linear-gradient(135deg, var(--primary-light), var(--white));
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .funcionario-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        box-shadow: var(--shadow-md);
    }

    .header-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .funcionario-nombre {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
    }

    .funcionario-cargo {
        font-size: 1rem;
        color: var(--gray-600);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .header-badge {
        align-self: flex-start;
    }

    .card-body {
        padding: 24px;
    }

    .info-section {
        margin-bottom: 32px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--gray-200);
    }

    .info-section:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: var(--primary);
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .info-item.full-width {
        grid-column: 1 / -1;
    }

    .info-item label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .info-item span {
        font-size: 0.875rem;
        color: var(--dark);
        font-weight: 500;
    }

    .info-item span.highlight {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--primary);
    }

    .info-item span.comprobante {
        font-family: monospace;
        background: var(--gray-100);
        padding: 8px 12px;
        border-radius: var(--radius);
        display: inline-block;
    }

    .montos-card {
        background: var(--gray-50);
        border-radius: var(--radius);
        padding: 20px;
        display: grid;
        gap: 16px;
    }

    .monto-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        background: white;
        border-radius: var(--radius);
        border-left: 4px solid var(--gray-300);
    }

    .monto-item.positivo {
        border-left-color: #10b981;
    }

    .monto-item.negativo {
        border-left-color: #ef4444;
    }

    .monto-item.total {
        border-left-color: var(--primary);
        background: var(--primary-light);
        font-weight: 700;
    }

    .monto-label {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.875rem;
        color: var(--gray-700);
        font-weight: 600;
    }

    .monto-label i {
        color: var(--primary);
    }

    .monto-item.positivo .monto-label i {
        color: #10b981;
    }

    .monto-item.negativo .monto-label i {
        color: #ef4444;
    }

    .monto-valor {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--dark);
    }

    .monto-item.total .monto-valor {
        font-size: 1.5rem;
        color: var(--primary);
    }

    .observations-box {
        background: var(--gray-50);
        padding: 16px;
        border-radius: var(--radius);
        border-left: 4px solid var(--primary);
        font-size: 0.875rem;
        color: var(--gray-700);
        line-height: 1.6;
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
        background: #d97706;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--gray-600);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-700);
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

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
