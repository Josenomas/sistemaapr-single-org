@extends('layouts.app')

@section('title', 'Detalle Notificación - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-bell"></i>
        Notificación: {{ $notificacion->titulo }}
    </h2>
    <div class="btn-group">
        <a href="{{ route('notificaciones.edit', $notificacion->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        <a href="{{ route('notificaciones.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<div class="row">
    <!-- Información de la Notificación -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Información
                </h3>
                <span class="badge badge-{{ $notificacion->estado === 'enviada' ? 'success' : ($notificacion->estado === 'programada' ? 'warning' : 'secondary') }}">
                    {{ ucfirst($notificacion->estado) }}
                </span>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Título</label>
                        <value><strong>{{ $notificacion->titulo }}</strong></value>
                    </div>

                    <div class="info-item">
                        <label>Tipo</label>
                        <value><span class="badge badge-info">{{ ucfirst($notificacion->tipo) }}</span></value>
                    </div>

                    <div class="info-item full-width">
                        <label>Mensaje</label>
                        <value>{{ $notificacion->mensaje }}</value>
                    </div>

                    <div class="info-item">
                        <label>Estado</label>
                        <value><span class="badge badge-{{ $notificacion->estado === 'enviada' ? 'success' : ($notificacion->estado === 'programada' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($notificacion->estado) }}
                        </span></value>
                    </div>

                    <div class="info-item">
                        <label>Canal(es)</label>
                        <value>{{ is_array($notificacion->canal) ? implode(', ', $notificacion->canal) : $notificacion->canal }}</value>
                    </div>

                    <div class="info-item">
                        <label>Fecha Programada</label>
                        <value>{{ $notificacion->fecha_programada ? $notificacion->fecha_programada->format('d/m/Y H:i') : 'No programada' }}</value>
                    </div>

                    <div class="info-item">
                        <label>Fecha de Envío</label>
                        <value>{{ $notificacion->fecha_enviada ? $notificacion->fecha_enviada->format('d/m/Y H:i') : 'No enviada' }}</value>
                    </div>

                    @if($notificacion->observaciones)
                    <div class="info-item full-width">
                        <label>Observaciones</label>
                        <value>{{ $notificacion->observaciones }}</value>
                    </div>
                    @endif

                    <div class="info-item">
                        <label>Creada</label>
                        <value>{{ $notificacion->fecha_creacion ? $notificacion->fecha_creacion->format('d/m/Y H:i') : '-' }}</value>
                    </div>

                    <div class="info-item">
                        <label>Última Actualización</label>
                        <value>{{ $notificacion->fecha_actualizacion ? $notificacion->fecha_actualizacion->diffForHumans() : '-' }}</value>
                    </div>
                </div>
            </div>
        </div>

        <!-- Destinatarios -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users"></i>
                    Destinatarios
                </h3>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Tipo de Destinatario</label>
                        <value><strong>{{ ucfirst($notificacion->destinatario) }}</strong></value>
                    </div>

                    @if($notificacion->destinatario === 'socio' && $notificacion->socio)
                    <div class="info-item">
                        <label>Socio</label>
                        <value>{{ $notificacion->socio->numero_socio }} - {{ $notificacion->socio->nombre_completo }}</value>
                    </div>
                    @endif

                    @if($notificacion->destinatario === 'sector' && $notificacion->sector)
                    <div class="info-item">
                        <label>Sector</label>
                        <value>{{ $notificacion->sector }}</value>
                    </div>
                    @endif

                    @if($notificacion->destinatario === 'todos')
                    <div class="info-item">
                        <label>Alcance</label>
                        <value>Todos los socios del sistema</value>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Estadísticas de Envío -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line"></i>
                    Estadísticas de Envío
                </h3>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Total Enviados</label>
                        <value><strong>{{ $notificacion->total_enviados ?? 0 }}</strong></value>
                    </div>

                    <div class="info-item">
                        <label>Total Leídos</label>
                        <value><strong>{{ $notificacion->total_leidos ?? 0 }}</strong></value>
                    </div>

                    <div class="info-item">
                        <label>Tasa de Apertura</label>
                        <value>
                            @if($notificacion->total_enviados > 0)
                                <strong>{{ round(($notificacion->total_leidos / $notificacion->total_enviados) * 100, 2) }}%</strong>
                            @else
                                <strong>0%</strong>
                            @endif
                        </value>
                    </div>

                    <div class="info-item">
                        <label>Errores</label>
                        <value><strong class="text-danger">{{ $notificacion->total_errores ?? 0 }}</strong></value>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen y Acciones -->
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
                    <div class="stat-icon" style="background: #3b82f6;">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ $notificacion->total_enviados ?? 0 }}</div>
                        <div class="stat-label">Enviados</div>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon" style="background: #10b981;">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ $notificacion->total_leidos ?? 0 }}</div>
                        <div class="stat-label">Leídos</div>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon" style="background: #f59e0b;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ ($notificacion->total_enviados ?? 0) - ($notificacion->total_leidos ?? 0) }}</div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon" style="background: #ef4444;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ $notificacion->total_errores ?? 0 }}</div>
                        <div class="stat-label">Errores</div>
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
                @if($notificacion->estado !== 'enviada')
                <form action="{{ route('notificaciones.enviar', $notificacion->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro de enviar esta notificación?')">
                    @csrf
                    <button type="submit" class="action-btn">
                        <i class="fas fa-paper-plane"></i>
                        Enviar Ahora
                    </button>
                </form>
                @endif

                <a href="{{ route('notificaciones.edit', $notificacion->id) }}" class="action-btn">
                    <i class="fas fa-edit"></i>
                    Editar Notificación
                </a>

                <form action="{{ route('notificaciones.destroy', $notificacion->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('¿Está seguro de eliminar esta notificación?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn danger">
                        <i class="fas fa-trash"></i>
                        Eliminar Notificación
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

    .badge-secondary {
        background: #e5e7eb;
        color: #4b5563;
    }

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
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

    .text-danger {
        color: #ef4444;
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
