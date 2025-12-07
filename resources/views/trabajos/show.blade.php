@extends('layouts.app')

@section('title', 'Ver Trabajo - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-tools"></i>
        Detalle del Trabajo
    </h2>
    <div class="header-actions">
        <a href="{{ route('trabajos.edit', $trabajo->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('trabajos.index') }}" class="btn btn-secondary">
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
            <div class="trabajo-icon">
                <i class="fas fa-tools"></i>
            </div>
            <div class="header-info">
                <h3 class="trabajo-titulo">{{ $trabajo->titulo }}</h3>
                <p class="trabajo-tipo">
                    <i class="fas fa-wrench"></i>
                    {{ $trabajo->tipo_trabajo_formateado }}
                    @if($trabajo->ubicacion)
                        <span class="separator">•</span>
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $trabajo->ubicacion }}
                    @endif
                </p>
            </div>
        </div>
        <div class="header-badges">
            @if($trabajo->estado === 'planificado')
                <span class="badge badge-info">Planificado</span>
            @elseif($trabajo->estado === 'en_proceso')
                <span class="badge badge-warning">En Proceso</span>
            @elseif($trabajo->estado === 'completado')
                <span class="badge badge-success">Completado</span>
            @else
                <span class="badge badge-secondary">Cancelado</span>
            @endif

            @if($trabajo->prioridad === 'baja')
                <span class="badge badge-info">Baja</span>
            @elseif($trabajo->prioridad === 'media')
                <span class="badge badge-warning">Media</span>
            @elseif($trabajo->prioridad === 'alta')
                <span class="badge badge-danger">Alta</span>
            @else
                <span class="badge badge-danger pulse">Urgente</span>
            @endif
        </div>
    </div>

    <div class="card-body">
        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-info-circle"></i>
                Descripción
            </h4>
            <div class="description-box">
                {{ $trabajo->descripcion }}
            </div>
        </div>

        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-calendar-alt"></i>
                Fechas
            </h4>
            <div class="info-grid">
                <div class="info-item">
                    <label>Fecha de Inicio</label>
                    <span class="date-display">
                        <i class="fas fa-calendar"></i>
                        {{ $trabajo->fecha_inicio->format('d/m/Y') }}
                    </span>
                </div>
                <div class="info-item">
                    <label>Fecha de Término</label>
                    <span class="date-display">
                        @if($trabajo->fecha_termino)
                            <i class="fas fa-calendar-check"></i>
                            {{ $trabajo->fecha_termino->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                @if($trabajo->duracion_dias !== null)
                <div class="info-item">
                    <label>Duración</label>
                    <span class="highlight">{{ $trabajo->duracion_dias }} {{ $trabajo->duracion_dias == 1 ? 'día' : 'días' }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-dollar-sign"></i>
                Información de Costos
            </h4>
            <div class="costos-card">
                <div class="costo-item">
                    <div class="costo-label">
                        <i class="fas fa-calculator"></i>
                        Costo Estimado
                    </div>
                    <div class="costo-valor">{{ $trabajo->costo_estimado_formateado }}</div>
                </div>
                <div class="costo-item">
                    <div class="costo-label">
                        <i class="fas fa-receipt"></i>
                        Costo Real
                    </div>
                    <div class="costo-valor">{{ $trabajo->costo_real_formateado }}</div>
                </div>
                @if($trabajo->diferencia_presupuesto !== null)
                <div class="costo-item {{ $trabajo->diferencia_presupuesto > 0 ? 'exceso' : 'ahorro' }}">
                    <div class="costo-label">
                        <i class="fas fa-balance-scale"></i>
                        Diferencia
                    </div>
                    <div class="costo-valor">{{ $trabajo->diferencia_presupuesto_formateada }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-user-tie"></i>
                Responsable
            </h4>
            <div class="info-grid">
                <div class="info-item">
                    <label>Funcionario Responsable</label>
                    <span>
                        @if($trabajo->responsable)
                            <div class="responsable-info">
                                <i class="fas fa-user-circle"></i>
                                <div>
                                    <strong>{{ $trabajo->responsable->nombre_completo }}</strong>
                                    <small>{{ $trabajo->responsable->cargo }}</small>
                                </div>
                            </div>
                        @else
                            Sin asignar
                        @endif
                    </span>
                </div>
            </div>
        </div>

        @if($trabajo->materiales_utilizados)
        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-box"></i>
                Materiales Utilizados
            </h4>
            <div class="materials-box">
                {{ $trabajo->materiales_utilizados }}
            </div>
        </div>
        @endif

        @if($trabajo->observaciones)
        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-sticky-note"></i>
                Observaciones
            </h4>
            <div class="observations-box">
                {{ $trabajo->observaciones }}
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
                    <span>{{ $trabajo->fecha_creacion->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-item">
                    <label>Última Actualización</label>
                    <span>{{ $trabajo->fecha_actualizacion->format('d/m/Y H:i') }}</span>
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

    .trabajo-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        box-shadow: var(--shadow-md);
    }

    .header-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .trabajo-titulo {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
    }

    .trabajo-tipo {
        font-size: 1rem;
        color: var(--gray-600);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .separator {
        color: var(--gray-400);
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

    .description-box, .materials-box, .observations-box {
        background: var(--gray-50);
        padding: 16px;
        border-radius: var(--radius);
        border-left: 4px solid var(--primary);
        font-size: 0.875rem;
        color: var(--gray-700);
        line-height: 1.6;
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

    .responsable-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .responsable-info i {
        font-size: 2rem;
        color: var(--primary);
    }

    .responsable-info strong {
        display: block;
        color: var(--dark);
    }

    .responsable-info small {
        display: block;
        color: var(--gray-500);
        font-size: 0.75rem;
    }

    .costos-card {
        background: var(--gray-50);
        border-radius: var(--radius);
        padding: 20px;
        display: grid;
        gap: 16px;
    }

    .costo-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        background: white;
        border-radius: var(--radius);
        border-left: 4px solid var(--gray-300);
    }

    .costo-item.exceso {
        border-left-color: #ef4444;
    }

    .costo-item.ahorro {
        border-left-color: #10b981;
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

    .costo-item.exceso .costo-label i {
        color: #ef4444;
    }

    .costo-item.ahorro .costo-label i {
        color: #10b981;
    }

    .costo-valor {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
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

    .badge.pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .7;
        }
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
