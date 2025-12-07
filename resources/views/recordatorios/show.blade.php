@extends('layouts.app')

@section('title', 'Detalle Recordatorio - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-bell"></i>
        Detalle del Recordatorio
    </h2>
    <div class="header-actions">
        <a href="{{ route('recordatorios.edit', $recordatorio->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('recordatorios.index') }}" class="btn btn-secondary">
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

<!-- Información Principal -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Información del Recordatorio</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Tipo:</span>
                <span class="detail-value">{!! $recordatorio->tipo_recordatorio_badge !!}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Prioridad:</span>
                <span class="detail-value">{!! $recordatorio->prioridad_badge !!}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Estado:</span>
                <span class="detail-value">{!! $recordatorio->estado_badge !!}</span>
            </div>

            @if($recordatorio->estado == 'pendiente')
            <div class="detail-item">
                <span class="detail-label">Días Restantes:</span>
                <span class="detail-value">
                    <strong class="{{ $recordatorio->dias_restantes_color }}">
                        {{ $recordatorio->dias_restantes }}
                    </strong>
                </span>
            </div>
            @endif
        </div>

        <div class="detail-section">
            <h4 class="section-title">Título</h4>
            <p class="section-content"><strong>{{ $recordatorio->titulo }}</strong></p>
        </div>

        <div class="detail-section">
            <h4 class="section-title">Descripción</h4>
            <p class="section-content">{{ $recordatorio->descripcion }}</p>
        </div>
    </div>
</div>

<!-- Fechas y Horario -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Fechas y Horario</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Fecha de Recordatorio:</span>
                <span class="detail-value">
                    <strong>{{ $recordatorio->fecha_recordatorio_formateada }}</strong>
                    @if($recordatorio->esHoy())
                        <br><span class="badge badge-warning">HOY</span>
                    @endif
                </span>
            </div>

            @if($recordatorio->hora_recordatorio)
            <div class="detail-item">
                <span class="detail-label">Hora:</span>
                <span class="detail-value">{{ $recordatorio->hora_recordatorio_formateada }}</span>
            </div>
            @endif

            @if($recordatorio->fecha_vencimiento)
            <div class="detail-item">
                <span class="detail-label">Fecha de Vencimiento:</span>
                <span class="detail-value">{{ $recordatorio->fecha_vencimiento_formateada }}</span>
            </div>
            @endif

            @if($recordatorio->fecha_completado)
            <div class="detail-item">
                <span class="detail-label">Fecha de Completado:</span>
                <span class="detail-value">{{ $recordatorio->fecha_completado_formateada }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Asignación -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Asignación</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Asignado a:</span>
                <span class="detail-value">
                    @if($recordatorio->asignado)
                        {{ $recordatorio->asignado->nombre_completo }}
                        <br><small class="text-muted">{{ $recordatorio->asignado->cargo }}</small>
                    @else
                        <span class="text-muted">Sin asignar</span>
                    @endif
                </span>
            </div>

            @if($recordatorio->ubicacion)
            <div class="detail-item">
                <span class="detail-label">Ubicación:</span>
                <span class="detail-value">
                    <i class="fas fa-map-marker-alt"></i> {{ $recordatorio->ubicacion }}
                </span>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Notas -->
@if($recordatorio->notas)
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Notas</h3>
    </div>
    <div class="card-body">
        <p class="section-content">{{ $recordatorio->notas }}</p>
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
                <span class="detail-value">{{ $recordatorio->fecha_creacion_formateada }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Última Actualización:</span>
                <span class="detail-value">{{ $recordatorio->fecha_actualizacion_formateada }}</span>
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

    .detail-section {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-200);
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 12px;
    }

    .section-content {
        font-size: 0.95rem;
        color: var(--gray-700);
        line-height: 1.6;
        margin: 0;
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

    .badge-primary {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-info {
        background: #cffafe;
        color: #155e75;
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

    .badge-dark {
        background: var(--gray-700);
        color: var(--white);
    }

    .text-muted {
        color: var(--gray-500);
    }

    .text-success {
        color: #059669;
    }

    .text-warning {
        color: #d97706;
    }

    .text-danger {
        color: #dc2626;
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

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .header-actions {
            width: 100%;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
