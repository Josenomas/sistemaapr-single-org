@extends('layouts.superadmin')

@section('title', 'Registros Pendientes')

@section('page-title')

@section('content')

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-user-clock"></i> Registros Pendientes de Verificación</h1>
            <p class="text-muted">Organizaciones esperando verificar su email</p>
        </div>
        <div>
            <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Tabla de Registros -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Listado de Registros Pendientes ({{ $registros->total() }})</h5>
        </div>
        <div class="card-body p-0">
            @if($registros->isEmpty())
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-check-circle fa-3x mb-3 text-success" style="opacity: 0.3;"></i>
                    <p>No hay registros pendientes de verificación</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Organización</th>
                                <th>Admin</th>
                                <th>Plan Deseado</th>
                                <th>Fecha Registro</th>
                                <th>Expira</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registros as $registro)
                                <tr>
                                    <td>{{ $registro->id }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $registro->nombre_apr }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-id-card"></i> {{ $registro->rut }}<br>
                                                <i class="fas fa-envelope"></i> {{ $registro->email_contacto }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $registro->admin_nombre }} {{ $registro->admin_apellido }}
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-envelope"></i> {{ $registro->admin_email }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($registro->suscripcionDeseada)
                                            <span class="badge bg-primary">
                                                {{ $registro->suscripcionDeseada->nombre }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Básico (default)</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $registro->created_at->format('d/m/Y H:i') }}</small>
                                        <br>
                                        <small class="text-muted">{{ $registro->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        @if($registro->expira_en)
                                            @if($registro->haExpirado())
                                                <span class="badge bg-danger">Expirado</span>
                                            @else
                                                <small>{{ $registro->expira_en->format('d/m/Y H:i') }}</small>
                                                <br>
                                                <small class="text-muted">{{ $registro->expira_en->diffForHumans() }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">Sin expiración</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('superadmin.registros.ver', $registro->id) }}"
                                               class="btn btn-info"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <form action="{{ route('superadmin.registros.aprobar', $registro->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Aprobar este registro y crear la organización?')">
                                                @csrf
                                                <button type="submit" class="btn btn-success" title="Aprobar">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('superadmin.registros.rechazar', $registro->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Está seguro de rechazar este registro?')">
                                                @csrf
                                                <button type="submit" class="btn btn-danger" title="Rechazar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if($registros->hasPages())
            <div class="card-footer bg-white">
                {{ $registros->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
