@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1><i class="fas fa-umbrella-beach"></i> Detalle de Vacación</h1>
            <p class="page-description">Información completa de la vacación</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('vacaciones.edit', $vacacion->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i>
                Editar
            </a>
            <a href="{{ route('vacaciones.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Volver
            </a>
        </div>
    </div>
</div>

<div class="details-grid">
    <!-- Información del Funcionario -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-user"></i> Información del Funcionario</h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <label>Funcionario</label>
                    <div class="user-info-large">
                        <div class="user-avatar-large">
                            {{ strtoupper(substr($vacacion->funcionario->nombre, 0, 1)) }}{{ strtoupper(substr($vacacion->funcionario->apellido_paterno, 0, 1)) }}
                        </div>
                        <div>
                            <div class="user-name-large">{{ $vacacion->funcionario->nombre }} {{ $vacacion->funcionario->apellido_paterno }}</div>
                            <div class="user-detail">RUT: {{ $vacacion->funcionario->rut }}</div>
                        </div>
                    </div>
                </div>

                <div class="info-item">
                    <label>Cargo</label>
                    <span class="info-value">{{ $vacacion->funcionario->cargo }}</span>
                </div>

                @if($vacacion->funcionario->email)
                <div class="info-item">
                    <label>Email</label>
                    <span class="info-value">{{ $vacacion->funcionario->email }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Detalles de la Vacación -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-calendar-alt"></i> Detalles de la Vacación</h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <label>Periodo</label>
                    <span class="info-value badge-lg badge-primary">{{ $vacacion->periodo }}</span>
                </div>

                <div class="info-item">
                    <label>Tipo</label>
                    <span class="info-value">{!! $vacacion->tipo_badge !!}</span>
                </div>

                <div class="info-item">
                    <label>Estado</label>
                    <span class="info-value">{!! $vacacion->estado_badge !!}</span>
                </div>

                <div class="info-item">
                    <label>Días Hábiles</label>
                    <span class="info-value"><strong>{{ $vacacion->dias_habiles }}</strong> días</span>
                </div>
            </div>

            <div class="periodo-visual">
                <div class="periodo-header">
                    <h4><i class="fas fa-calendar-check"></i> Periodo de Vacaciones</h4>
                </div>
                <div class="periodo-content">
                    <div class="fecha-box inicio">
                        <div class="fecha-label">Inicio</div>
                        <div class="fecha-valor">{{ $vacacion->fecha_inicio_formateada }}</div>
                    </div>
                    <div class="fecha-arrow">
                        <i class="fas fa-arrow-right"></i>
                        <span>{{ $vacacion->dias_habiles }} días</span>
                    </div>
                    <div class="fecha-box termino">
                        <div class="fecha-label">Término</div>
                        <div class="fecha-valor">{{ $vacacion->fecha_termino_formateada }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Información de Aprobación -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-check-circle"></i> Información de Aprobación</h3>
    </div>
    <div class="card-body">
        <div class="info-grid info-grid-3">
            <div class="info-item">
                <label>Fecha de Solicitud</label>
                <span class="info-value">{{ $vacacion->fecha_solicitud_formateada }}</span>
            </div>

            <div class="info-item">
                <label>Aprobado Por</label>
                @if($vacacion->aprobador)
                    <div class="user-info-small">
                        <div class="user-avatar-small">
                            {{ strtoupper(substr($vacacion->aprobador->nombre, 0, 1)) }}{{ strtoupper(substr($vacacion->aprobador->apellido_paterno, 0, 1)) }}
                        </div>
                        <div>
                            <div class="user-name-small">{{ $vacacion->aprobador->nombre }} {{ $vacacion->aprobador->apellido_paterno }}</div>
                            <div class="user-detail-small">{{ $vacacion->aprobador->cargo }}</div>
                        </div>
                    </div>
                @else
                    <span class="info-value text-muted">Sin asignar</span>
                @endif
            </div>

            <div class="info-item">
                <label>Fecha de Aprobación</label>
                <span class="info-value">{{ $vacacion->fecha_aprobacion ? $vacacion->fecha_aprobacion_formateada : 'Pendiente' }}</span>
            </div>
        </div>

        @if($vacacion->motivo_rechazo)
            <div class="alert alert-danger" style="margin-top: 20px;">
                <strong><i class="fas fa-exclamation-triangle"></i> Motivo de Rechazo:</strong>
                <p style="margin: 8px 0 0 0;">{{ $vacacion->motivo_rechazo }}</p>
            </div>
        @endif
    </div>
</div>

<!-- Suplente -->
@if($vacacion->funcionarioSuplente)
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-user-friends"></i> Funcionario Suplente</h3>
    </div>
    <div class="card-body">
        <div class="user-info-large">
            <div class="user-avatar-large">
                {{ strtoupper(substr($vacacion->funcionarioSuplente->nombre, 0, 1)) }}{{ strtoupper(substr($vacacion->funcionarioSuplente->apellido_paterno, 0, 1)) }}
            </div>
            <div>
                <div class="user-name-large">{{ $vacacion->funcionarioSuplente->nombre }} {{ $vacacion->funcionarioSuplente->apellido_paterno }}</div>
                <div class="user-detail">{{ $vacacion->funcionarioSuplente->cargo }}</div>
                @if($vacacion->funcionarioSuplente->email)
                    <div class="user-detail"><i class="fas fa-envelope"></i> {{ $vacacion->funcionarioSuplente->email }}</div>
                @endif
                @if($vacacion->funcionarioSuplente->telefono)
                    <div class="user-detail"><i class="fas fa-phone"></i> {{ $vacacion->funcionarioSuplente->telefono }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Observaciones -->
@if($vacacion->observaciones)
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-sticky-note"></i> Observaciones</h3>
    </div>
    <div class="card-body">
        <p class="observaciones-text">{{ $vacacion->observaciones }}</p>
    </div>
</div>
@endif

<!-- Información del Sistema -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-info-circle"></i> Información del Sistema</h3>
    </div>
    <div class="card-body">
        <div class="info-grid info-grid-3">
            <div class="info-item">
                <label>Fecha de Registro</label>
                <span class="info-value">{{ $vacacion->created_at->format('d/m/Y H:i') }}</span>
            </div>

            <div class="info-item">
                <label>Última Actualización</label>
                <span class="info-value">{{ $vacacion->updated_at->format('d/m/Y H:i') }}</span>
            </div>

            <div class="info-item">
                <label>Estado del Registro</label>
                <span class="badge {{ $vacacion->activo ? 'badge-success' : 'badge-danger' }}">
                    {{ $vacacion->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
    .page-header {
        background: var(--white);
        padding: 24px 32px;
        border-radius: 12px;
        box-shadow: var(--shadow);
        margin-bottom: 24px;
    }

    .page-header-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .page-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-description {
        color: var(--gray-600);
        margin: 0;
    }

    .page-actions {
        display: flex;
        gap: 12px;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
    }

    .card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: var(--shadow);
        margin-bottom: 24px;
    }

    .card-header {
        background: var(--gray-50);
        padding: 16px 24px;
        border-bottom: 2px solid var(--primary);
        border-radius: 12px 12px 0 0;
    }

    .card-header h3 {
        margin: 0;
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-900);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-body {
        padding: 24px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .info-grid-3 {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .info-item label {
        font-weight: 600;
        color: var(--gray-600);
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .info-value {
        font-size: 1rem;
        color: var(--gray-900);
    }

    .text-muted {
        color: var(--gray-500);
        font-style: italic;
    }

    .badge-lg {
        font-size: 1.125rem;
        padding: 8px 16px;
        display: inline-block;
    }

    .user-info-large {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .user-avatar-large {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--primary);
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
    }

    .user-name-large {
        font-weight: 700;
        font-size: 1.125rem;
        color: var(--gray-900);
    }

    .user-detail {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-top: 4px;
    }

    .user-info-small {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar-small {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary);
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
    }

    .user-name-small {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 0.9375rem;
    }

    .user-detail-small {
        font-size: 0.8125rem;
        color: var(--gray-600);
    }

    .periodo-visual {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-200);
    }

    .periodo-header {
        margin-bottom: 16px;
    }

    .periodo-header h4 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-700);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .periodo-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 20px;
        background: var(--gray-50);
        border-radius: 8px;
    }

    .fecha-box {
        flex: 1;
        text-align: center;
        padding: 16px;
        background: var(--white);
        border-radius: 8px;
        border: 2px solid var(--gray-300);
    }

    .fecha-box.inicio {
        border-color: var(--success);
    }

    .fecha-box.termino {
        border-color: var(--danger);
    }

    .fecha-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }

    .fecha-valor {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gray-900);
    }

    .fecha-arrow {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        color: var(--primary);
        font-weight: 600;
    }

    .fecha-arrow i {
        font-size: 1.5rem;
    }

    .fecha-arrow span {
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .observaciones-text {
        margin: 0;
        line-height: 1.6;
        color: var(--gray-700);
    }

    @media (max-width: 768px) {
        .details-grid {
            grid-template-columns: 1fr;
        }

        .periodo-content {
            flex-direction: column;
        }

        .fecha-arrow {
            transform: rotate(90deg);
        }
    }
</style>
