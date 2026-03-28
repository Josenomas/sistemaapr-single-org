@extends('layouts.superadmin')

@section('title', 'Gestión de Dominios Personalizados')
@section('page-title', 'Dominios Personalizados')

@section('content')
<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-md-2 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted mb-1 small">Total</p>
                <h4 class="mb-0">{{ $stats['total'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card border-0 shadow-sm h-100 border-start border-warning border-3">
            <div class="card-body">
                <p class="text-muted mb-1 small">Verificados DNS</p>
                <h4 class="mb-0 text-warning">{{ $stats['verificado_dns'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card border-0 shadow-sm h-100 border-start border-success border-3">
            <div class="card-body">
                <p class="text-muted mb-1 small">Aprobados</p>
                <h4 class="mb-0 text-success">{{ $stats['activo_aprobado'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card border-0 shadow-sm h-100 border-start border-info border-3">
            <div class="card-body">
                <p class="text-muted mb-1 small">Pendientes Config.</p>
                <h4 class="mb-0 text-info">{{ $stats['pendiente_configuracion'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card border-0 shadow-sm h-100 border-start border-danger border-3">
            <div class="card-body">
                <p class="text-muted mb-1 small">Rechazados</p>
                <h4 class="mb-0 text-danger">{{ $stats['rechazado'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card border-0 shadow-sm h-100 border-start border-secondary border-3">
            <div class="card-body">
                <p class="text-muted mb-1 small">Suspendidos</p>
                <h4 class="mb-0 text-secondary">{{ $stats['suspendido'] }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Alerts -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Tabla de Dominios -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">
            <i class="fas fa-globe"></i> Listado de Dominios Personalizados
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Organización</th>
                        <th>Dominio</th>
                        <th>Plan</th>
                        <th>Estado</th>
                        <th>Fecha Solicitud</th>
                        <th>Fecha Verificación</th>
                        <th>Aprobado Por</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dominios as $org)
                    <tr>
                        <td>
                            <strong>{{ $org->nombre }}</strong><br>
                            <small class="text-muted">{{ $org->rut }}</small>
                        </td>
                        <td>
                            <a href="https://{{ $org->dominio_personalizado }}" target="_blank" class="text-decoration-none">
                                {{ $org->dominio_personalizado }}
                                <i class="fas fa-external-link-alt fa-xs"></i>
                            </a>
                            @if($org->observaciones_dominio)
                            <br><small class="text-warning">
                                <i class="fas fa-info-circle"></i> {{ Str::limit($org->observaciones_dominio, 50) }}
                            </small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $org->suscripcion->nombre_mostrar ?? 'N/A' }}</span>
                        </td>
                        <td>{!! $org->badge_estado_dominio !!}</td>
                        <td>
                            @if($org->fecha_solicitud_dominio)
                                <small>{{ $org->fecha_solicitud_dominio->format('d/m/Y') }}</small><br>
                                <small class="text-muted">{{ $org->fecha_solicitud_dominio->format('H:i') }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($org->fecha_verificacion_dns)
                                <small>{{ $org->fecha_verificacion_dns->format('d/m/Y') }}</small><br>
                                <small class="text-muted">{{ $org->fecha_verificacion_dns->format('H:i') }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($org->aprobador)
                                <small>{{ $org->aprobador->nombre }} {{ $org->aprobador->apellido }}</small><br>
                                @if($org->fecha_aprobacion_dominio)
                                <small class="text-muted">{{ $org->fecha_aprobacion_dominio->format('d/m/Y H:i') }}</small>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                @if($org->estado_dominio_personalizado === 'verificado_dns')
                                    <!-- Aprobar -->
                                    <form action="{{ route('superadmin.dominios.aprobar', $org->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" title="Aprobar Dominio"
                                                onclick="return confirm('¿Aprobar el dominio {{ $org->dominio_personalizado }}?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <!-- Rechazar -->
                                    <button type="button" class="btn btn-danger btn-sm" title="Rechazar Dominio"
                                            data-bs-toggle="modal" data-bs-target="#rechazarModal{{ $org->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif

                                @if(in_array($org->estado_dominio_personalizado, ['verificado_dns', 'activo_aprobado']))
                                    <!-- Suspender -->
                                    <button type="button" class="btn btn-warning btn-sm" title="Suspender Dominio"
                                            data-bs-toggle="modal" data-bs-target="#suspenderModal{{ $org->id }}">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @endif

                                <!-- Ver Detalles -->
                                <button type="button" class="btn btn-info btn-sm" title="Ver Detalles DNS"
                                        data-bs-toggle="modal" data-bs-target="#detallesModal{{ $org->id }}">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Rechazar -->
                    <div class="modal fade" id="rechazarModal{{ $org->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('superadmin.dominios.rechazar', $org->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Rechazar Dominio</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>¿Estás seguro de rechazar el dominio <strong>{{ $org->dominio_personalizado }}</strong>?</p>
                                        <div class="mb-3">
                                            <label class="form-label">Motivo del rechazo <span class="text-danger">*</span></label>
                                            <textarea name="motivo" class="form-control" rows="3" required
                                                      placeholder="Ej: Dominio no cumple con políticas de uso, contenido inapropiado, etc."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-times"></i> Rechazar Dominio
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Suspender -->
                    <div class="modal fade" id="suspenderModal{{ $org->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('superadmin.dominios.suspender', $org->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Suspender Dominio</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Advertencia:</strong> Al suspender este dominio, dejará de funcionar inmediatamente.
                                        </div>
                                        <p>Suspender: <strong>{{ $org->dominio_personalizado }}</strong></p>
                                        <div class="mb-3">
                                            <label class="form-label">Motivo de la suspensión <span class="text-danger">*</span></label>
                                            <textarea name="motivo" class="form-control" rows="3" required
                                                      placeholder="Ej: Violación de términos de servicio, contenido inapropiado, etc."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-ban"></i> Suspender Dominio
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Detalles DNS -->
                    <div class="modal fade" id="detallesModal{{ $org->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Detalles DNS - {{ $org->dominio_personalizado }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="200">Organización:</th>
                                            <td>{{ $org->nombre }}</td>
                                        </tr>
                                        <tr>
                                            <th>RUT:</th>
                                            <td>{{ $org->rut }}</td>
                                        </tr>
                                        <tr>
                                            <th>Dominio:</th>
                                            <td>{{ $org->dominio_personalizado }}</td>
                                        </tr>
                                        <tr>
                                            <th>Estado:</th>
                                            <td>{!! $org->badge_estado_dominio !!}</td>
                                        </tr>
                                        <tr>
                                            <th>Fecha Solicitud:</th>
                                            <td>{{ $org->fecha_solicitud_dominio ? $org->fecha_solicitud_dominio->format('d/m/Y H:i') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Fecha Verificación DNS:</th>
                                            <td>{{ $org->fecha_verificacion_dns ? $org->fecha_verificacion_dns->format('d/m/Y H:i') : '-' }}</td>
                                        </tr>
                                        @if($org->fecha_aprobacion_dominio)
                                        <tr>
                                            <th>Fecha Aprobación:</th>
                                            <td>{{ $org->fecha_aprobacion_dominio->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @endif
                                        @if($org->aprobador)
                                        <tr>
                                            <th>Aprobado Por:</th>
                                            <td>{{ $org->aprobador->nombre }} {{ $org->aprobador->apellido }} ({{ $org->aprobador->email }})</td>
                                        </tr>
                                        @endif
                                        @if($org->observaciones_dominio)
                                        <tr>
                                            <th>Observaciones:</th>
                                            <td>{{ $org->observaciones_dominio }}</td>
                                        </tr>
                                        @endif
                                    </table>

                                    @if($org->detalles_verificacion_dns)
                                    <hr>
                                    <h6>Detalles Técnicos de Verificación DNS:</h6>
                                    <pre class="bg-light p-3 rounded" style="font-size: 0.85rem;">{{ $org->detalles_verificacion_dns }}</pre>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fas fa-globe fa-3x mb-3 d-block"></i>
                            No hay dominios personalizados registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($dominios->hasPages())
    <div class="card-footer bg-white">
        {{ $dominios->links() }}
    </div>
    @endif
</div>

<style>
.btn-group-sm > .btn {
    margin-right: 2px;
}
</style>
@endsection
