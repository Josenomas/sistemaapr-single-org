@extends('layouts.superadmin')

@section('title', 'Solicitudes de Compra de Dominio')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-globe"></i>
            Solicitudes de Compra de Dominio
        </h1>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Solicitudes</div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Pendientes Verificación</div>
                    <div class="stat-value">{{ $stats['solicitados'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Pagados (Por Comprar)</div>
                    <div class="stat-value">{{ $stats['pagados'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Activos</div>
                    <div class="stat-value">{{ $stats['activos'] }}</div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tabla de Solicitudes -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i>
                Listado de Solicitudes
            </h3>
        </div>
        <div class="card-body">
            @if($solicitudes->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No hay solicitudes</h3>
                    <p>Aún no se han recibido solicitudes de compra de dominio.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Organización</th>
                                <th>Dominio Solicitado</th>
                                <th>Estado</th>
                                <th>Monto</th>
                                <th>Fecha Solicitud</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudes as $solicitud)
                                <tr>
                                    <td><code>#{{ $solicitud->id }}</code></td>
                                    <td>
                                        <strong>{{ $solicitud->organizacion->nombre_apr }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $solicitud->organizacion->rut }}</small>
                                    </td>
                                    <td>
                                        <code style="font-size: 1.1em; color: #1e40af;">{{ $solicitud->dominio_solicitado }}</code>
                                        @if($solicitud->fecha_vencimiento)
                                            <br><small class="text-muted">Vence: {{ $solicitud->fecha_vencimiento->format('d/m/Y') }}</small>
                                        @endif
                                    </td>
                                    <td>{!! $solicitud->badge_estado !!}</td>
                                    <td><strong>${{ number_format($solicitud->monto, 0, ',', '.') }}</strong></td>
                                    <td>
                                        {{ $solicitud->created_at->format('d/m/Y H:i') }}
                                        <br>
                                        <small class="text-muted">{{ $solicitud->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical btn-group-sm" role="group">
                                            @if($solicitud->estado === 'solicitado')
                                                <!-- Verificar disponibilidad -->
                                                <a href="https://nic.cl/whois?query={{ urlencode($solicitud->dominio_solicitado) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-search"></i> Verificar en NIC Chile
                                                </a>
                                                <form action="{{ route('superadmin.solicitudes-dominio.aprobar', $solicitud->id) }}"
                                                      method="POST"
                                                      style="display: inline;">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm btn-success w-100"
                                                            onclick="return confirm('¿Confirmas que el dominio ESTÁ DISPONIBLE?')">
                                                        <i class="fas fa-check"></i> Aprobar (Disponible)
                                                    </button>
                                                </form>
                                                <button type="button"
                                                        class="btn btn-sm btn-danger w-100"
                                                        onclick="rechazarSolicitud({{ $solicitud->id }})">
                                                    <i class="fas fa-times"></i> Rechazar (Ocupado)
                                                </button>

                                            @elseif($solicitud->estado === 'verificado_disponible')
                                                <!-- Esperando pago del cliente -->
                                                <div class="alert alert-info mb-2 p-2">
                                                    <small><i class="fas fa-info-circle"></i> Esperando pago del cliente</small>
                                                </div>
                                                <form action="{{ route('superadmin.solicitudes-dominio.marcar-pagado', $solicitud->id) }}"
                                                      method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm btn-primary w-100"
                                                            onclick="return confirm('¿Confirmas que recibiste el pago de $20.000?')">
                                                        <i class="fas fa-dollar-sign"></i> Marcar como Pagado
                                                    </button>
                                                </form>

                                            @elseif($solicitud->estado === 'pagado')
                                                <!-- Comprar en NIC Chile -->
                                                <div class="alert alert-warning mb-2 p-2">
                                                    <small><i class="fas fa-exclamation-triangle"></i> Pagado - Comprar en NIC Chile</small>
                                                </div>
                                                <a href="https://clientes.nic.cl"
                                                   target="_blank"
                                                   class="btn btn-sm btn-info w-100">
                                                    <i class="fas fa-external-link-alt"></i> Ir a NIC Chile
                                                </a>
                                                <form action="{{ route('superadmin.solicitudes-dominio.marcar-comprado', $solicitud->id) }}"
                                                      method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm btn-success w-100"
                                                            onclick="return confirm('¿Confirmas que ya compraste el dominio en NIC Chile?')">
                                                        <i class="fas fa-shopping-cart"></i> Marcar como Comprado
                                                    </button>
                                                </form>

                                            @elseif($solicitud->estado === 'comprado')
                                                <!-- Activar dominio -->
                                                <div class="alert alert-success mb-2 p-2">
                                                    <small><i class="fas fa-check"></i> Comprado - Activar en sistema</small>
                                                </div>
                                                <form action="{{ route('superadmin.solicitudes-dominio.activar', $solicitud->id) }}"
                                                      method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm btn-primary w-100"
                                                            onclick="return confirm('¿Confirmas que configuraste el DNS y quieres activar el dominio?')">
                                                        <i class="fas fa-power-off"></i> Activar Dominio
                                                    </button>
                                                </form>

                                            @elseif($solicitud->estado === 'activo')
                                                <!-- Dominio activo -->
                                                <div class="alert alert-success mb-2 p-2">
                                                    <small><i class="fas fa-check-circle"></i> Dominio Activo</small>
                                                </div>
                                                <a href="https://{{ $solicitud->dominio_solicitado }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-info w-100">
                                                    <i class="fas fa-external-link-alt"></i> Ver Dominio
                                                </a>

                                            @else
                                                <!-- Estados finales: verificado_ocupado, cancelado -->
                                                <span class="text-muted"><i class="fas fa-ban"></i> Sin acciones</span>
                                            @endif
                                        </div>

                                        @if($solicitud->observaciones)
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-comment"></i>
                                                    {{ Str::limit($solicitud->observaciones, 50) }}
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($solicitudes->hasPages())
                    <div class="mt-3">
                        {{ $solicitudes->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Instrucciones -->
    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="fas fa-info-circle"></i> Proceso Manual de Compra de Dominios</h4>
        </div>
        <div class="card-body">
            <ol class="mb-0">
                <li><strong>Solicitud:</strong> Usuario solicita dominio → Recibes email automático</li>
                <li><strong>Verificación:</strong> Verificas disponibilidad en <a href="https://nic.cl/whois" target="_blank">NIC Chile WHOIS</a></li>
                <li><strong>Aprobación:</strong> Si disponible → Apruebas (usuario recibe email para pagar)</li>
                <li><strong>Pago:</strong> Usuario paga $20.000 por transferencia → Marcas como "Pagado"</li>
                <li><strong>Compra:</strong> Compras dominio en <a href="https://clientes.nic.cl" target="_blank">NIC Chile</a> (~$9.000) → Marcas como "Comprado"</li>
                <li><strong>Configuración:</strong> Configuras DNS (CNAME → sistemaapr.cl)</li>
                <li><strong>Activación:</strong> Activas dominio → Usuario recibe email de confirmación</li>
            </ol>
            <div class="mt-3 alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Importante:</strong> Costo cliente: $20.000 | Costo NIC: ~$9.000 | Ganancia: ~$11.000
            </div>
        </div>
    </div>
</div>

<!-- Modal para rechazar -->
<div class="modal fade" id="modalRechazar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle"></i>
                    Rechazar Solicitud
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRechazar" method="POST">
                @csrf
                <div class="modal-body">
                    <p>El dominio <strong id="dominioRechazar"></strong> NO está disponible.</p>
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo (opcional)</label>
                        <textarea class="form-control"
                                  id="motivo"
                                  name="motivo"
                                  rows="3"
                                  placeholder="Ej: El dominio ya está registrado por otra persona"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i>
                        Rechazar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .page-header {
        margin-bottom: 24px;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #64748b;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
    }

    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 16px;
    }

    .empty-state h3 {
        font-size: 1.25rem;
        color: #475569;
        margin-bottom: 8px;
    }

    .empty-state p {
        color: #64748b;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background: #f8fafc;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px;
    }

    .table tbody td {
        padding: 12px;
        vertical-align: middle;
    }

    .btn-group-vertical {
        width: 100%;
        gap: 4px;
    }

    .btn-group-vertical .btn {
        text-align: left;
        white-space: nowrap;
    }
</style>
@endsection

@section('scripts')
<script>
    function rechazarSolicitud(id) {
        const solicitud = @json($solicitudes);
        const sol = solicitud.data.find(s => s.id === id);

        if (sol) {
            document.getElementById('dominioRechazar').textContent = sol.dominio_solicitado;
            document.getElementById('formRechazar').action = `/superadmin/solicitudes-dominio/${id}/rechazar`;

            const modal = new bootstrap.Modal(document.getElementById('modalRechazar'));
            modal.show();
        }
    }
</script>
@endsection
