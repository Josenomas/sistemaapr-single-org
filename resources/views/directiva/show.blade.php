@extends('layouts.app')

@section('title', 'Detalle Miembro Directiva - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-user-tie"></i>
        Miembro: {{ $directiva->socio->nombre_completo }}
    </h2>
    <div class="btn-group">
        <a href="{{ route('directiva.edit', $directiva->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('directiva.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<div class="row">
    <!-- Información del Miembro -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-id-card"></i>
                    Información del Miembro de Directiva
                </h3>
                {!! $directiva->estado_badge !!}
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Nombre Completo</label>
                        <value><strong>{{ $directiva->socio->nombre_completo }}</strong></value>
                    </div>

                    <div class="info-item">
                        <label>RUT</label>
                        <value>{{ $directiva->socio->rut }}</value>
                    </div>

                    <div class="info-item">
                        <label>Cargo</label>
                        <value>{!! $directiva->cargo_badge !!}</value>
                    </div>

                    <div class="info-item">
                        <label>Estado</label>
                        <value>{!! $directiva->estado_badge !!}</value>
                    </div>

                    <div class="info-item">
                        <label>Período</label>
                        <value>
                            <span class="badge badge-secondary">
                                <i class="fas fa-calendar-alt"></i>
                                {{ $directiva->periodo }}
                            </span>
                        </value>
                    </div>

                    <div class="info-item">
                        <label>Duración en el Cargo</label>
                        <value>
                            <i class="fas fa-clock text-muted"></i>
                            {{ $directiva->duracion }}
                        </value>
                    </div>

                    <div class="info-item">
                        <label>Fecha de Inicio</label>
                        <value>{{ $directiva->fecha_inicio->format('d/m/Y') }}</value>
                    </div>

                    <div class="info-item">
                        <label>Fecha de Término</label>
                        <value>
                            @if($directiva->fecha_termino)
                                {{ $directiva->fecha_termino->format('d/m/Y') }}
                            @else
                                <span class="text-muted">En curso</span>
                            @endif
                        </value>
                    </div>

                    @if($directiva->acta_nombramiento)
                    <div class="info-item full-width">
                        <label>Acta de Nombramiento</label>
                        <value>{{ $directiva->acta_nombramiento }}</value>
                    </div>
                    @endif

                    @if($directiva->observaciones)
                    <div class="info-item full-width">
                        <label>Observaciones</label>
                        <value>{{ $directiva->observaciones }}</value>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Socio -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user"></i>
                    Datos del Socio
                </h3>
            </div>
            <div class="card-body">
                <div class="socio-info">
                    <div class="info-item-compact">
                        <label>Nombre</label>
                        <value>{{ $directiva->socio->nombre }}</value>
                    </div>

                    <div class="info-item-compact">
                        <label>Apellido Paterno</label>
                        <value>{{ $directiva->socio->apellido_paterno }}</value>
                    </div>

                    @if($directiva->socio->apellido_materno)
                    <div class="info-item-compact">
                        <label>Apellido Materno</label>
                        <value>{{ $directiva->socio->apellido_materno }}</value>
                    </div>
                    @endif

                    <div class="info-item-compact">
                        <label>RUT</label>
                        <value>{{ $directiva->socio->rut }}</value>
                    </div>

                    @if($directiva->socio->email)
                    <div class="info-item-compact">
                        <label>Email</label>
                        <value>
                            <a href="mailto:{{ $directiva->socio->email }}" class="link-primary">
                                {{ $directiva->socio->email }}
                            </a>
                        </value>
                    </div>
                    @endif

                    @if($directiva->socio->telefono)
                    <div class="info-item-compact">
                        <label>Teléfono</label>
                        <value>
                            <a href="tel:{{ $directiva->socio->telefono }}" class="link-primary">
                                {{ $directiva->socio->telefono }}
                            </a>
                        </value>
                    </div>
                    @endif

                    <div class="info-item-compact">
                        <label>Estado Socio</label>
                        <value>{!! $directiva->socio->estado_badge !!}</value>
                    </div>

                    <div class="action-link">
                        <a href="{{ route('socios.show', $directiva->socio->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt"></i>
                            Ver Perfil Completo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metadatos -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Información del Registro
                </h3>
            </div>
            <div class="card-body">
                <div class="metadata">
                    <div class="metadata-item">
                        <i class="fas fa-calendar-plus"></i>
                        <div>
                            <label>Creado</label>
                            <value>{{ $directiva->fecha_creacion->format('d/m/Y H:i') }}</value>
                        </div>
                    </div>

                    <div class="metadata-item">
                        <i class="fas fa-sync-alt"></i>
                        <div>
                            <label>Última Actualización</label>
                            <value>{{ $directiva->fecha_actualizacion->format('d/m/Y H:i') }}</value>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        padding-bottom: 16px;
        border-bottom: 2px solid var(--gray-200);
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--gray-800);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title i {
        color: var(--primary);
    }

    .btn-group {
        display: flex;
        gap: 12px;
    }

    .row {
        display: flex;
        gap: 24px;
        margin: 0 -12px;
    }

    .col-md-8 {
        flex: 0 0 66.666667%;
        max-width: 66.666667%;
        padding: 0 12px;
    }

    .col-md-4 {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
        padding: 0 12px;
    }

    .card {
        background: var(--white);
        border-radius: 8px;
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
        color: var(--gray-800);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
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

    .info-item value {
        font-size: 0.9375rem;
        color: var(--gray-800);
        font-weight: 500;
    }

    .socio-info {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .info-item-compact {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--gray-100);
    }

    .info-item-compact:last-of-type {
        border-bottom: none;
        padding-bottom: 0;
    }

    .info-item-compact label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
    }

    .info-item-compact value {
        font-size: 0.875rem;
        color: var(--gray-800);
    }

    .action-link {
        margin-top: 8px;
    }

    .metadata {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .metadata-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px;
        background: var(--gray-50);
        border-radius: 6px;
    }

    .metadata-item i {
        color: var(--primary);
        margin-top: 2px;
    }

    .metadata-item label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--gray-500);
        display: block;
        margin-bottom: 2px;
    }

    .metadata-item value {
        font-size: 0.875rem;
        color: var(--gray-700);
    }

    .badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-primary { background: var(--primary-light); color: var(--primary-dark); }
    .badge-success { background: var(--success-light); color: var(--success-dark); }
    .badge-warning { background: var(--warning-light); color: var(--warning-dark); }
    .badge-danger { background: var(--danger-light); color: var(--danger-dark); }
    .badge-info { background: var(--info-light); color: var(--info-dark); }
    .badge-secondary { background: var(--gray-200); color: var(--gray-700); }
    .badge-light { background: var(--gray-100); color: var(--gray-600); border: 1px solid var(--gray-300); }

    .text-muted {
        color: var(--gray-500);
    }

    .link-primary {
        color: var(--primary);
        text-decoration: none;
    }

    .link-primary:hover {
        text-decoration: underline;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.8125rem;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
    }

    .btn-warning {
        background: var(--warning);
        color: white;
    }

    .btn-warning:hover {
        background: var(--warning-dark);
    }

    .btn-secondary {
        background: var(--gray-500);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-600);
    }

    .btn-outline-primary {
        background: transparent;
        color: var(--primary);
        border: 1px solid var(--primary);
    }

    .btn-outline-primary:hover {
        background: var(--primary);
        color: white;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .row {
            flex-direction: column;
        }

        .col-md-8,
        .col-md-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
@endsection
