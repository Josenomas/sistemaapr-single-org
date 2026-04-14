@extends('layouts.superadmin')

@section('title', 'Solicitudes de Pago Manual - Super Admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-money-check-alt"></i>
        Solicitudes de Pago Manual
    </h1>
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

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-warning">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pendientes</div>
            <div class="stat-value">{{ $stats['pendientes'] }}</div>
            <div class="stat-sublabel">Requieren revisión</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Aprobadas</div>
            <div class="stat-value">{{ $stats['aprobadas'] }}</div>
            <div class="stat-sublabel">Pagos confirmados</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-danger">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Rechazadas</div>
            <div class="stat-value">{{ $stats['rechazadas'] }}</div>
            <div class="stat-sublabel">Solicitudes denegadas</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-list"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-sublabel">Todas las solicitudes</div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="filter-form">
            <div class="filter-row">
                <div class="filter-item">
                    <label>Estado:</label>
                    <select name="estado" class="form-select" onchange="this.form.submit()">
                        <option value="">Todas</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                        <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobadas</option>
                        <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazadas</option>
                    </select>
                </div>

                <div class="filter-item">
                    <label>Organización:</label>
                    <select name="organizacion" class="form-select" onchange="this.form.submit()">
                        <option value="">Todas</option>
                        @foreach($organizaciones as $org)
                            <option value="{{ $org->id }}" {{ request('organizacion') == $org->id ? 'selected' : '' }}>
                                {{ $org->nombre_apr }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(request('estado') || request('organizacion'))
                    <div class="filter-item">
                        <a href="{{ route('superadmin.solicitudes-pago.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar Filtros
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Solicitudes -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Organización</th>
                        <th>Monto</th>
                        <th>Banco</th>
                        <th>N° Operación</th>
                        <th>Fecha Trans.</th>
                        <th>Fecha Solicitud</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitudes as $solicitud)
                        @php
                            $estadoBadge = match($solicitud->estado) {
                                'pendiente' => 'bg-warning text-dark',
                                'aprobada' => 'bg-success',
                                'rechazada' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <tr>
                            <td><code>#{{ $solicitud->id }}</code></td>
                            <td>
                                <strong>{{ $solicitud->organizacion->nombre_apr }}</strong>
                                <br>
                                <small class="text-muted">{{ $solicitud->organizacion->slug }}</small>
                            </td>
                            <td>
                                <strong>${{ number_format($solicitud->monto, 0, ',', '.') }}</strong>
                                <br>
                                <small class="text-muted">
                                    Plan: {{ $solicitud->pagoSuscripcion->suscripcion->nombre }}
                                </small>
                            </td>
                            <td>{{ $solicitud->banco_origen }}</td>
                            <td><code>{{ $solicitud->numero_operacion }}</code></td>
                            <td>{{ $solicitud->fecha_transferencia->format('d/m/Y') }}</td>
                            <td>
                                {{ $solicitud->created_at->format('d/m/Y H:i') }}
                                <br>
                                <small class="text-muted">{{ $solicitud->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $estadoBadge }}">
                                    {{ ucfirst($solicitud->estado) }}
                                </span>
                                @if($solicitud->estado !== 'pendiente' && $solicitud->revisor)
                                    <br>
                                    <small class="text-muted">
                                        Por: {{ $solicitud->revisor->nombre }} {{ $solicitud->revisor->apellido }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('superadmin.solicitudes-pago.show', $solicitud->id) }}"
                                       class="btn btn-sm btn-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($solicitud->estado === 'pendiente')
                                        <button type="button"
                                                class="btn btn-sm btn-success"
                                                title="Aprobar"
                                                onclick="aprobarSolicitud({{ $solicitud->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>

                                        <button type="button"
                                                class="btn btn-sm btn-danger"
                                                title="Rechazar"
                                                onclick="mostrarModalRechazo({{ $solicitud->id }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif

                                    @if($solicitud->comprobante_path)
                                        <a href="{{ asset('storage/' . $solicitud->comprobante_path) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-secondary"
                                           title="Ver comprobante">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>No hay solicitudes de pago manual</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($solicitudes->hasPages())
            <div class="pagination-container mt-4">
                {{ $solicitudes->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal de Rechazo -->
<div class="modal" id="modalRechazo" style="display: none;">
    <div class="modal-backdrop" onclick="cerrarModalRechazo()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-times-circle"></i> Rechazar Solicitud</h3>
            <button onclick="cerrarModalRechazo()" class="close-btn">&times;</button>
        </div>
        <form id="formRechazo" method="POST">
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
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--dark-card);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.bg-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .stat-icon.bg-success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .stat-icon.bg-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .stat-icon.bg-primary {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-light);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-sublabel {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .filter-form {
        padding: 0;
    }

    .filter-row {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .filter-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        min-width: 200px;
    }

    .filter-item label {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-muted);
        margin: 0;
    }

    .form-select {
        background: var(--dark-input);
        color: var(--text-light);
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .btn-group {
        display: flex;
        gap: 0.25rem;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8rem;
        border-radius: 0.375rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
    }

    .btn-info {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
        color: white;
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .btn-secondary {
        background: var(--gray-700);
        color: white;
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
        padding: 0.5rem 0.75rem;
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
</style>

<script>
function mostrarModalRechazo(idSolicitud) {
    const modal = document.getElementById('modalRechazo');
    const form = document.getElementById('formRechazo');
    form.action = `/superadmin/solicitudes-pago/${idSolicitud}/rechazar`;
    modal.style.display = 'flex';
}

function cerrarModalRechazo() {
    document.getElementById('modalRechazo').style.display = 'none';
}

function aprobarSolicitud(idSolicitud) {
    if (confirm('¿Estás seguro de aprobar esta solicitud de pago manual?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/superadmin/solicitudes-pago/${idSolicitud}/aprobar`;

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';

        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
