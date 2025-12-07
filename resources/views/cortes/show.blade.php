@extends('layouts.app')

@section('title', 'Ver Corte de Suministro - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-plug"></i>
        Detalle del Corte de Suministro
    </h2>
    <div class="header-actions">
        <a href="{{ route('cortes.edit', $corte->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('cortes.index') }}" class="btn btn-secondary">
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
                {{ strtoupper(substr($corte->socio->nombre, 0, 1)) }}{{ strtoupper(substr($corte->socio->apellido_paterno, 0, 1)) }}
            </div>
            <div class="header-info">
                <h3 class="socio-nombre">{{ $corte->socio->nombre_completo }}</h3>
                <p class="socio-numero">
                    <i class="fas fa-id-card"></i>
                    Socio N° {{ $corte->socio->numero_socio }}
                </p>
            </div>
        </div>
        <div class="header-badges">
            @if($corte->estado === 'pendiente')
                <span class="badge badge-warning">Pendiente</span>
            @elseif($corte->estado === 'ejecutado')
                <span class="badge badge-danger">Ejecutado</span>
            @elseif($corte->estado === 'reconectado')
                <span class="badge badge-success">Reconectado</span>
            @else
                <span class="badge badge-secondary">Cancelado</span>
            @endif

            @if($corte->motivo === 'morosidad')
                <span class="badge badge-danger">Morosidad</span>
            @elseif($corte->motivo === 'solicitud_socio')
                <span class="badge badge-info">Solicitud del Socio</span>
            @elseif($corte->motivo === 'mantenimiento')
                <span class="badge badge-warning">Mantenimiento</span>
            @else
                <span class="badge badge-secondary">Otro</span>
            @endif
        </div>
    </div>

    <div class="card-body">
        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-info-circle"></i>
                Información del Corte
            </h4>
            <div class="info-grid">
                <div class="info-item">
                    <label>Motivo</label>
                    <span class="highlight">{{ $corte->motivo_formateado }}</span>
                </div>
                <div class="info-item">
                    <label>Estado</label>
                    <span>
                        @if($corte->estado === 'pendiente')
                            <span class="badge badge-warning">Pendiente</span>
                        @elseif($corte->estado === 'ejecutado')
                            <span class="badge badge-danger">Ejecutado</span>
                        @elseif($corte->estado === 'reconectado')
                            <span class="badge badge-success">Reconectado</span>
                        @else
                            <span class="badge badge-secondary">Cancelado</span>
                        @endif
                    </span>
                </div>
                @if($corte->descripcion)
                <div class="info-item full-width">
                    <label>Descripción</label>
                    <span>{{ $corte->descripcion }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-calendar-alt"></i>
                Fechas
            </h4>
            <div class="info-grid">
                <div class="info-item">
                    <label>Fecha de Corte</label>
                    <span class="date-display">
                        <i class="fas fa-calendar"></i>
                        {{ $corte->fecha_corte->format('d/m/Y') }}
                    </span>
                </div>
                <div class="info-item">
                    <label>Fecha de Reconexión</label>
                    <span class="date-display">
                        @if($corte->fecha_reconexion)
                            <i class="fas fa-calendar-check"></i>
                            {{ $corte->fecha_reconexion->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                @if($corte->dias_corte !== null)
                <div class="info-item">
                    <label>Días de Corte</label>
                    <span class="highlight">{{ $corte->dias_corte }} {{ $corte->dias_corte == 1 ? 'día' : 'días' }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-dollar-sign"></i>
                Información Económica
            </h4>
            <div class="montos-card">
                <div class="monto-item">
                    <div class="monto-label">
                        <i class="fas fa-exclamation-triangle"></i>
                        Monto Adeudado
                    </div>
                    <div class="monto-valor">{{ $corte->monto_adeudado_formateado }}</div>
                </div>
                @if($corte->monto_reconexion)
                <div class="monto-item reconexion">
                    <div class="monto-label">
                        <i class="fas fa-hand-holding-usd"></i>
                        Monto de Reconexión
                    </div>
                    <div class="monto-valor">{{ $corte->monto_reconexion_formateado }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-user-tie"></i>
                Información de Ejecución
            </h4>
            <div class="info-grid">
                <div class="info-item">
                    <label>Ejecutor</label>
                    <span>
                        @if($corte->ejecutor)
                            <div class="ejecutor-info">
                                <i class="fas fa-user-circle"></i>
                                <div>
                                    <strong>{{ $corte->ejecutor->nombre_completo }}</strong>
                                    <small>{{ $corte->ejecutor->cargo }}</small>
                                </div>
                            </div>
                        @else
                            Sin asignar
                        @endif
                    </span>
                </div>
            </div>
        </div>

        @if($corte->observaciones)
        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-sticky-note"></i>
                Observaciones
            </h4>
            <div class="observations-box">
                {{ $corte->observaciones }}
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
                    <span>{{ $corte->fecha_creacion->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-item">
                    <label>Última Actualización</label>
                    <span>{{ $corte->fecha_actualizacion->format('d/m/Y H:i') }}</span>
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

    .date-display {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .date-display i {
        color: var(--primary);
    }

    .ejecutor-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ejecutor-info i {
        font-size: 2rem;
        color: var(--primary);
    }

    .ejecutor-info strong {
        display: block;
        color: var(--dark);
    }

    .ejecutor-info small {
        display: block;
        color: var(--gray-500);
        font-size: 0.75rem;
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
        border-left: 4px solid #ef4444;
    }

    .monto-item.reconexion {
        border-left-color: #10b981;
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
        color: #ef4444;
    }

    .monto-item.reconexion .monto-label i {
        color: #10b981;
    }

    .monto-valor {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
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
    }
</style>
@endsection
