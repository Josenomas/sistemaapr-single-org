@extends('layouts.app')

@section('title', 'Detalle Incidente - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-exclamation-triangle"></i>
        Detalle del Incidente
    </h2>
    <div class="header-actions">
        <a href="{{ route('incidentes.edit', $incidente->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('incidentes.index') }}" class="btn btn-secondary">
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

<!-- Estado y Prioridad Cards -->
<div class="status-cards">
    <div class="status-card status-{{ $incidente->estado }}">
        <div class="status-icon">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="status-info">
            <div class="status-label">Estado</div>
            <div class="status-value">{{ ucfirst(str_replace('_', ' ', $incidente->estado)) }}</div>
        </div>
    </div>

    <div class="status-card priority-{{ $incidente->prioridad }}">
        <div class="status-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="status-info">
            <div class="status-label">Prioridad</div>
            <div class="status-value">{{ ucfirst($incidente->prioridad) }}</div>
        </div>
    </div>

    <div class="status-card type-info">
        <div class="status-icon">
            <i class="fas fa-tag"></i>
        </div>
        <div class="status-info">
            <div class="status-label">Tipo</div>
            <div class="status-value">{{ ucfirst(str_replace('_', ' ', $incidente->tipo)) }}</div>
        </div>
    </div>
</div>

<!-- Información Principal -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-info-circle"></i>
            Información del Incidente
        </h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">
                    <i class="fas fa-calendar-alt"></i>
                    Fecha de Reporte
                </span>
                <span class="detail-value">{{ $incidente->fecha_reporte->format('d/m/Y H:i') }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">
                    <i class="fas fa-map-marker-alt"></i>
                    Ubicación
                </span>
                <span class="detail-value">{{ $incidente->ubicacion }}</span>
            </div>

            @if($incidente->sector)
            <div class="detail-item">
                <span class="detail-label">
                    <i class="fas fa-map"></i>
                    Sector
                </span>
                <span class="detail-value">{{ $incidente->sector }}</span>
            </div>
            @endif

            @if($incidente->socioReporta)
            <div class="detail-item">
                <span class="detail-label">
                    <i class="fas fa-user"></i>
                    Reportado por
                </span>
                <span class="detail-value">{{ $incidente->socioReporta->nombre_completo }}</span>
            </div>
            @endif

            @if($incidente->usuarioAsignado)
            <div class="detail-item">
                <span class="detail-label">
                    <i class="fas fa-user-tie"></i>
                    Asignado a
                </span>
                <span class="detail-value">{{ $incidente->usuarioAsignado->nombre_completo }}</span>
            </div>
            @endif

            @if($incidente->fecha_atencion)
            <div class="detail-item">
                <span class="detail-label">
                    <i class="fas fa-clock"></i>
                    Fecha de Atención
                </span>
                <span class="detail-value">{{ $incidente->fecha_atencion->format('d/m/Y H:i') }}</span>
            </div>
            @endif

            @if($incidente->fecha_resolucion)
            <div class="detail-item">
                <span class="detail-label">
                    <i class="fas fa-check-circle"></i>
                    Fecha de Resolución
                </span>
                <span class="detail-value">{{ $incidente->fecha_resolucion->format('d/m/Y H:i') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Descripción -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-file-alt"></i>
            Descripción del Incidente
        </h3>
    </div>
    <div class="card-body">
        <p class="section-content">{{ $incidente->descripcion }}</p>
    </div>
</div>

<!-- Solución -->
@if($incidente->solucion)
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-tools"></i>
            Solución Aplicada
        </h3>
    </div>
    <div class="card-body">
        <p class="section-content">{{ $incidente->solucion }}</p>
    </div>
</div>
@endif

<!-- Observaciones -->
@if($incidente->observaciones)
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-sticky-note"></i>
            Observaciones
        </h3>
    </div>
    <div class="card-body">
        <p class="section-content">{{ $incidente->observaciones }}</p>
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
        color: #f59e0b;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .status-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .status-card {
        background: white;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 16px;
        border-left: 4px solid;
    }

    .status-card.status-reportado {
        border-left-color: #3b82f6;
    }

    .status-card.status-en_atencion {
        border-left-color: #f59e0b;
    }

    .status-card.status-resuelto {
        border-left-color: #10b981;
    }

    .status-card.status-cerrado {
        border-left-color: #6b7280;
    }

    .status-card.priority-baja {
        border-left-color: #10b981;
    }

    .status-card.priority-media {
        border-left-color: #f59e0b;
    }

    .status-card.priority-alta {
        border-left-color: #f97316;
    }

    .status-card.priority-critica {
        border-left-color: #ef4444;
    }

    .status-card.type-info {
        border-left-color: #8b5cf6;
    }

    .status-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        background: var(--gray-100);
        color: var(--primary);
    }

    .status-info {
        flex: 1;
    }

    .status-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 500;
        margin-bottom: 4px;
    }

    .status-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
    }

    .card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 24px;
        border: 1px solid var(--gray-200);
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        border-radius: 12px 12px 0 0;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title i {
        color: var(--primary);
    }

    .card-body {
        padding: 24px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .detail-label {
        font-size: 0.813rem;
        font-weight: 600;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .detail-label i {
        color: var(--primary);
        font-size: 0.875rem;
    }

    .detail-value {
        font-size: 1rem;
        color: var(--dark);
        font-weight: 500;
    }

    .section-content {
        color: var(--gray-700);
        line-height: 1.7;
        font-size: 0.938rem;
        margin: 0;
        white-space: pre-wrap;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
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
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
    }

    .btn-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-secondary:hover {
        background: var(--gray-300);
    }

    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        border: 1px solid;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-color: #a7f3d0;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .header-actions {
            width: 100%;
        }

        .status-cards {
            grid-template-columns: 1fr;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
