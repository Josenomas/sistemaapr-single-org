@extends('layouts.app')

@section('title', 'Ver Funcionario - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-user-tie"></i>
        Detalle del Funcionario
    </h2>
    <div class="header-actions">
        <a href="{{ route('funcionarios.edit', $funcionario->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('funcionarios.index') }}" class="btn btn-secondary">
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
                {{ $funcionario->iniciales }}
            </div>
            <div class="header-info">
                <h3 class="funcionario-nombre">{{ $funcionario->nombre_completo }}</h3>
                <p class="funcionario-cargo">
                    <i class="fas fa-briefcase"></i>
                    {{ $funcionario->cargo }}
                </p>
            </div>
        </div>
        <div class="header-badge">
            @if($funcionario->estado === 'activo')
                <span class="badge badge-success">Activo</span>
            @elseif($funcionario->estado === 'licencia')
                <span class="badge badge-warning">Licencia</span>
            @else
                <span class="badge badge-danger">Inactivo</span>
            @endif
        </div>
    </div>

    <div class="card-body">
        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-id-card"></i>
                Datos Personales
            </h4>
            <div class="info-grid">
                <div class="info-item">
                    <label>RUT</label>
                    <span>{{ $funcionario->rut }}</span>
                </div>
                <div class="info-item">
                    <label>Nombre Completo</label>
                    <span>{{ $funcionario->nombre_completo }}</span>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-briefcase"></i>
                Información Laboral
            </h4>
            <div class="info-grid">
                <div class="info-item">
                    <label>Cargo</label>
                    <span><span class="badge badge-info">{{ $funcionario->cargo }}</span></span>
                </div>
                <div class="info-item">
                    <label>Estado</label>
                    <span>
                        @if($funcionario->estado === 'activo')
                            <span class="badge badge-success">Activo</span>
                        @elseif($funcionario->estado === 'licencia')
                            <span class="badge badge-warning">Licencia</span>
                        @else
                            <span class="badge badge-danger">Inactivo</span>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <label>Fecha de Ingreso</label>
                    <span>{{ $funcionario->fecha_ingreso->format('d/m/Y') }}</span>
                </div>
                <div class="info-item">
                    <label>Fecha de Término</label>
                    <span>{{ $funcionario->fecha_termino ? $funcionario->fecha_termino->format('d/m/Y') : '-' }}</span>
                </div>
                <div class="info-item">
                    <label>Años de Servicio</label>
                    <span>{{ $funcionario->anios_servicio }} {{ $funcionario->anios_servicio == 1 ? 'año' : 'años' }}</span>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-address-book"></i>
                Datos de Contacto
            </h4>
            <div class="info-grid">
                <div class="info-item">
                    <label>Teléfono</label>
                    <span>{{ $funcionario->telefono ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <span>{{ $funcionario->email ?? '-' }}</span>
                </div>
                <div class="info-item full-width">
                    <label>Dirección</label>
                    <span>{{ $funcionario->direccion ?? '-' }}</span>
                </div>
            </div>
        </div>

        @if($funcionario->observaciones)
        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-sticky-note"></i>
                Observaciones
            </h4>
            <div class="observations-box">
                {{ $funcionario->observaciones }}
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
                    <span>{{ $funcionario->fecha_creacion->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-item">
                    <label>Última Actualización</label>
                    <span>{{ $funcionario->fecha_actualizacion->format('d/m/Y H:i') }}</span>
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
