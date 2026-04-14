@extends('layouts.superadmin')

@section('title', 'Detalle Solicitud de Pago - Super Admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-file-invoice-dollar"></i>
        Detalle de Solicitud #{{ $solicitud->id }}
    </h1>
    <div class="breadcrumb">
        <a href="{{ route('superadmin.solicitudes-pago.index') }}">Solicitudes de Pago</a>
        <span> / </span>
        <span>Detalle #{{ $solicitud->id }}</span>
    </div>
</div>

<!-- Alertas -->
@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    {{ session('error') }}
</div>
@endif

<!-- Estado de la Solicitud -->
<div class="card mb-4">
    <div class="card-body">
        <div class="status-header">
            @php
                $estadoBadge = match($solicitud->estado) {
                    'pendiente' => ['class' => 'bg-warning text-dark', 'icon' => 'clock'],
                    'aprobada' => ['class' => 'bg-success', 'icon' => 'check-circle'],
                    'rechazada' => ['class' => 'bg-danger', 'icon' => 'times-circle'],
                    default => ['class' => 'bg-secondary', 'icon' => 'question']
                };
            @endphp
            <div class="status-badge">
                <span class="badge {{ $estadoBadge['class'] }} badge-lg">
                    <i class="fas fa-{{ $estadoBadge['icon'] }}"></i>
                    {{ strtoupper($solicitud->estado) }}
                </span>
            </div>
            <div class="status-info">
                <div class="info-item">
                    <span class="label">Fecha Solicitud:</span>
                    <span class="value">{{ $solicitud->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($solicitud->estado !== 'pendiente')
                    <div class="info-item">
                        <span class="label">Revisado por:</span>
                        <span class="value">{{ $solicitud->revisor->nombre }} {{ $solicitud->revisor->apellido }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Fecha Revisión:</span>
                        <span class="value">{{ $solicitud->fecha_revision->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <!-- Información de la Organización -->
        <div class="card mb-4">
            <div class="card-header">
                <h3><i class="fas fa-building"></i> Organización</h3>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="label">Nombre APR:</span>
                        <span class="value">{{ $solicitud->organizacion->nombre_apr }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Slug:</span>
                        <span class="value">{{ $solicitud->organizacion->slug }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">RUT:</span>
                        <span class="value">{{ $solicitud->organizacion->rut }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Email:</span>
                        <span class="value">{{ $solicitud->organizacion->email_contacto }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Pago -->
        <div class="card mb-4">
            <div class="card-header">
                <h3><i class="fas fa-receipt"></i> Información del Pago</h3>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="label">Plan:</span>
                        <span class="value">
                            <span class="badge bg-primary">{{ $solicitud->pagoSuscripcion->suscripcion->nombre }}</span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Período:</span>
                        <span class="value">
                            {{ $solicitud->pagoSuscripcion->periodo_inicio->format('d/m/Y') }} -
                            {{ $solicitud->pagoSuscripcion->periodo_fin->format('d/m/Y') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Monto del Pago:</span>
                        <span class="value highlight">${{ number_format($solicitud->pagoSuscripcion->monto, 0, ',', '.') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Estado del Pago:</span>
                        <span class="value">
                            <span class="badge {{ $solicitud->pagoSuscripcion->estado === 'pagado' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ ucfirst($solicitud->pagoSuscripcion->estado) }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <!-- Datos de la Transferencia -->
        <div class="card mb-4">
            <div class="card-header">
                <h3><i class="fas fa-money-check-alt"></i> Datos de la Transferencia</h3>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="label">Banco Origen:</span>
                        <span class="value">{{ $solicitud->banco_origen }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">N° Operación:</span>
                        <span class="value"><code>{{ $solicitud->numero_operacion }}</code></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Fecha Transferencia:</span>
                        <span class="value">{{ $solicitud->fecha_transferencia->format('d/m/Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Monto Transferido:</span>
                        <span class="value highlight">${{ number_format($solicitud->monto, 0, ',', '.') }}</span>
                    </div>
                    @if($solicitud->notas)
                        <div class="detail-item full-width">
                            <span class="label">Notas:</span>
                            <span class="value">{{ $solicitud->notas }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Comprobante -->
        @if($solicitud->comprobante_path)
        <div class="card mb-4">
            <div class="card-header">
                <h3><i class="fas fa-file-pdf"></i> Comprobante</h3>
            </div>
            <div class="card-body text-center">
                <a href="{{ asset('storage/' . $solicitud->comprobante_path) }}"
                   target="_blank"
                   class="btn btn-primary btn-lg">
                    <i class="fas fa-download"></i> Ver/Descargar Comprobante
                </a>
                <p class="mt-3 text-muted">
                    <small>{{ basename($solicitud->comprobante_path) }}</small>
                </p>
            </div>
        </div>
        @endif

        <!-- Motivo de Rechazo -->
        @if($solicitud->estado === 'rechazada' && $solicitud->motivo_rechazo)
        <div class="card mb-4 border-danger">
            <div class="card-header bg-danger text-white">
                <h3 style="color: white; margin: 0;"><i class="fas fa-exclamation-triangle"></i> Motivo de Rechazo</h3>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $solicitud->motivo_rechazo }}</p>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Acciones -->
@if($solicitud->estado === 'pendiente')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-cogs"></i> Acciones</h3>
    </div>
    <div class="card-body">
        <div class="action-buttons">
            <form method="POST" action="{{ route('superadmin.solicitudes-pago.aprobar', $solicitud->id) }}"
                  onsubmit="return confirm('¿Estás seguro de aprobar esta solicitud? El pago se marcará como pagado automáticamente.')">
                @csrf
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-check-circle"></i> Aprobar Solicitud
                </button>
            </form>

            <button type="button" class="btn btn-danger btn-lg" onclick="mostrarModalRechazo()">
                <i class="fas fa-times-circle"></i> Rechazar Solicitud
            </button>

            <a href="{{ route('superadmin.solicitudes-pago.index') }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>
</div>
@else
<div class="text-center mt-4">
    <a href="{{ route('superadmin.solicitudes-pago.index') }}" class="btn btn-secondary btn-lg">
        <i class="fas fa-arrow-left"></i> Volver al Listado
    </a>
</div>
@endif

<!-- Modal de Rechazo -->
<div class="modal" id="modalRechazo" style="display: none;">
    <div class="modal-backdrop" onclick="cerrarModalRechazo()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-times-circle"></i> Rechazar Solicitud</h3>
            <button onclick="cerrarModalRechazo()" class="close-btn">&times;</button>
        </div>
        <form method="POST" action="{{ route('superadmin.solicitudes-pago.rechazar', $solicitud->id) }}">
            @csrf
            <div class="modal-body">
                <p>Por favor, indica el motivo del rechazo:</p>
                <textarea name="motivo_rechazo" class="form-control" rows="4" required
                          placeholder="Ej: El comprobante no es válido, el monto no coincide, etc."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="cerrarModalRechazo()" class="btn btn-secondary">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-times"></i> Rechazar Solicitud
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .breadcrumb {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-top: 8px;
    }

    .breadcrumb a {
        color: var(--primary);
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .status-header {
        display: flex;
        gap: 2rem;
        align-items: flex-start;
    }

    .status-badge {
        flex-shrink: 0;
    }

    .badge-lg {
        padding: 12px 24px;
        font-size: 1.125rem;
        border-radius: 8px;
    }

    .status-info {
        flex: 1;
        display: grid;
        gap: 12px;
    }

    .info-item {
        display: flex;
        gap: 8px;
    }

    .info-item .label {
        font-weight: 600;
        color: var(--text-muted);
        min-width: 140px;
    }

    .info-item .value {
        color: var(--text-light);
    }

    .detail-grid {
        display: grid;
        gap: 16px;
    }

    .detail-item {
        display: grid;
        grid-template-columns: 140px 1fr;
        gap: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border);
    }

    .detail-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .detail-item.full-width {
        grid-template-columns: 1fr;
    }

    .detail-item .label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-muted);
    }

    .detail-item .value {
        color: var(--text-light);
        word-break: break-word;
    }

    .detail-item .value.highlight {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--success);
    }

    .action-buttons {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .btn-lg {
        padding: 14px 28px;
        font-size: 1rem;
    }

    .card-header h3 {
        margin: 0;
        font-size: 1.125rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 1.5rem;
    }

    .modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
    }

    .modal-content {
        position: relative;
        background: var(--dark-card);
        border-radius: 0.75rem;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow: auto;
    }

    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-header h3 {
        margin: 0;
        color: var(--text-light);
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-muted);
        cursor: pointer;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        background: var(--dark-input);
        color: var(--text-light);
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    @media (max-width: 768px) {
        .row {
            grid-template-columns: 1fr;
        }

        .status-header {
            flex-direction: column;
            gap: 1rem;
        }

        .detail-item {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons .btn {
            width: 100%;
        }
    }
</style>

<script>
function mostrarModalRechazo() {
    document.getElementById('modalRechazo').style.display = 'flex';
}

function cerrarModalRechazo() {
    document.getElementById('modalRechazo').style.display = 'none';
}
</script>
@endsection
