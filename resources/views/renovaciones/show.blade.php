@extends('layouts.app')

@section('title', 'Ver Renovación de Medidor - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-sync-alt"></i>
        Detalle de Renovación de Medidor
    </h2>
    <div class="header-actions">
        <a href="{{ route('renovaciones.edit', $renovacion->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('renovaciones.index') }}" class="btn btn-secondary">
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
            <div class="socio-avatar">
                {{ strtoupper(substr($renovacion->socio->nombre, 0, 1)) }}{{ strtoupper(substr($renovacion->socio->apellido_paterno, 0, 1)) }}
            </div>
            <div class="header-info">
                <h3 class="socio-nombre">{{ $renovacion->socio->nombre_completo }}</h3>
                <p class="socio-numero">
                    <i class="fas fa-id-card"></i>
                    Socio N° {{ $renovacion->socio->numero_socio }}
                </p>
            </div>
        </div>
        <div class="header-badges">
            @if($renovacion->estado === 'planificado')
                <span class="badge badge-warning">Planificado</span>
            @elseif($renovacion->estado === 'ejecutado')
                <span class="badge badge-success">Ejecutado</span>
            @else
                <span class="badge badge-secondary">Cancelado</span>
            @endif

            @if($renovacion->motivo === 'deterioro')
                <span class="badge badge-warning">Deterioro</span>
            @elseif($renovacion->motivo === 'falla')
                <span class="badge badge-danger">Falla</span>
            @elseif($renovacion->motivo === 'actualizacion')
                <span class="badge badge-info">Actualización</span>
            @elseif($renovacion->motivo === 'robo')
                <span class="badge badge-danger">Robo</span>
            @else
                <span class="badge badge-secondary">Otro</span>
            @endif
        </div>
    </div>

    <div class="card-body">
        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-calendar-alt"></i>
                Fecha de Renovación
            </h4>
            <div class="info-grid">
                <div class="info-item">
                    <label>Fecha</label>
                    <span class="date-display">
                        <i class="fas fa-calendar"></i>
                        {{ $renovacion->fecha_renovacion->format('d/m/Y') }}
                    </span>
                </div>
                <div class="info-item">
                    <label>Motivo</label>
                    <span class="highlight">{{ $renovacion->motivo_formateado }}</span>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-exchange-alt"></i>
                Cambio de Medidor
            </h4>
            <div class="medidores-comparison">
                <div class="medidor-card anterior">
                    <div class="medidor-header">
                        <i class="fas fa-arrow-left"></i>
                        <h5>Medidor Anterior</h5>
                    </div>
                    <div class="medidor-content">
                        <div class="medidor-numero">
                            <label>Número</label>
                            <span>{{ $renovacion->medidor_anterior ?? 'No registrado' }}</span>
                        </div>
                        <div class="medidor-lectura">
                            <label>Lectura Final</label>
                            <span>{{ $renovacion->lectura_anterior_formateada }}</span>
                        </div>
                    </div>
                </div>

                <div class="medidor-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>

                <div class="medidor-card nuevo">
                    <div class="medidor-header">
                        <i class="fas fa-arrow-right"></i>
                        <h5>Medidor Nuevo</h5>
                    </div>
                    <div class="medidor-content">
                        <div class="medidor-numero">
                            <label>Número</label>
                            <span class="highlight">{{ $renovacion->medidor_nuevo }}</span>
                        </div>
                        <div class="medidor-lectura">
                            <label>Lectura Inicial</label>
                            <span>{{ $renovacion->lectura_inicial_formateada }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($renovacion->costo_renovacion)
        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-dollar-sign"></i>
                Costo de Renovación
            </h4>
            <div class="costo-card">
                <div class="costo-item">
                    <div class="costo-label">
                        <i class="fas fa-receipt"></i>
                        Costo Total
                    </div>
                    <div class="costo-valor">{{ $renovacion->costo_renovacion_formateado }}</div>
                </div>
            </div>
        </div>
        @endif

        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-user-cog"></i>
                Técnico Responsable
            </h4>
            <div class="info-grid">
                <div class="info-item">
                    <label>Técnico</label>
                    <span>
                        @if($renovacion->tecnico)
                            <div class="tecnico-info">
                                <i class="fas fa-user-circle"></i>
                                <div>
                                    <strong>{{ $renovacion->tecnico->nombre_completo }}</strong>
                                    <small>{{ $renovacion->tecnico->cargo }}</small>
                                </div>
                            </div>
                        @else
                            Sin asignar
                        @endif
                    </span>
                </div>
            </div>
        </div>

        @if($renovacion->observaciones)
        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-sticky-note"></i>
                Observaciones
            </h4>
            <div class="observations-box">
                {{ $renovacion->observaciones }}
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
                    <span>{{ $renovacion->fecha_creacion->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-item">
                    <label>Última Actualización</label>
                    <span>{{ $renovacion->fecha_actualizacion->format('d/m/Y H:i') }}</span>
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

    .socio-avatar {
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

    .socio-nombre {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
    }

    .socio-numero {
        font-size: 1rem;
        color: var(--gray-600);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .header-badges {
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-end;
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

    .date-display {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .date-display i {
        color: var(--primary);
    }

    .medidores-comparison {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 24px;
        align-items: center;
    }

    .medidor-card {
        background: var(--gray-50);
        border-radius: var(--radius);
        padding: 20px;
        border: 2px solid var(--gray-200);
    }

    .medidor-card.anterior {
        border-left: 4px solid #f59e0b;
    }

    .medidor-card.nuevo {
        border-left: 4px solid #10b981;
    }

    .medidor-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--gray-200);
    }

    .medidor-header i {
        color: var(--primary);
        font-size: 1.25rem;
    }

    .medidor-header h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-700);
    }

    .medidor-content {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .medidor-numero label, .medidor-lectura label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: block;
        margin-bottom: 4px;
    }

    .medidor-numero span, .medidor-lectura span {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--dark);
        display: block;
    }

    .medidor-arrow {
        font-size: 2rem;
        color: var(--primary);
    }

    .tecnico-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .tecnico-info i {
        font-size: 2rem;
        color: var(--primary);
    }

    .tecnico-info strong {
        display: block;
        color: var(--dark);
    }

    .tecnico-info small {
        display: block;
        color: var(--gray-500);
        font-size: 0.75rem;
    }

    .costo-card {
        background: var(--gray-50);
        border-radius: var(--radius);
        padding: 20px;
    }

    .costo-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        background: white;
        border-radius: var(--radius);
        border-left: 4px solid var(--primary);
    }

    .costo-label {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.875rem;
        color: var(--gray-700);
        font-weight: 600;
    }

    .costo-label i {
        color: var(--primary);
    }

    .costo-valor {
        font-size: 1.5rem;
        font-weight: 700;
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

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-secondary {
        background: #e5e7eb;
        color: #4b5563;
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

        .header-badges {
            align-items: flex-start;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .medidores-comparison {
            grid-template-columns: 1fr;
        }

        .medidor-arrow {
            transform: rotate(90deg);
        }
    }
</style>
@endsection
