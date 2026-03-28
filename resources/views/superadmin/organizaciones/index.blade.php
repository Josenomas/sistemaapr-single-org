@extends('layouts.superadmin')

@section('title', 'Gestión de Organizaciones')

@section('page-title')

@section('content')

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-building"></i> Gestión de Organizaciones</h1>
            <p class="text-muted">Administrar todas las organizaciones del sistema</p>
        </div>
        <div>
            <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('superadmin.organizaciones') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="busqueda" class="form-control"
                               placeholder="Nombre, RUT, email..."
                               value="{{ request('busqueda') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Plan</label>
                        <select name="id_suscripcion" class="form-select">
                            <option value="">Todos</option>
                            @foreach($suscripciones as $sus)
                                <option value="{{ $sus->id }}" {{ request('id_suscripcion') == $sus->id ? 'selected' : '' }}>
                                    {{ $sus->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado Suscripción</label>
                        <select name="estado_suscripcion" class="form-select">
                            <option value="">Todos</option>
                            <option value="prueba" {{ request('estado_suscripcion') == 'prueba' ? 'selected' : '' }}>Prueba</option>
                            <option value="activa" {{ request('estado_suscripcion') == 'activa' ? 'selected' : '' }}>Activa</option>
                            <option value="suspendida" {{ request('estado_suscripcion') == 'suspendida' ? 'selected' : '' }}>Suspendida</option>
                            <option value="cancelada" {{ request('estado_suscripcion') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                            <option value="expirada" {{ request('estado_suscripcion') == 'expirada' ? 'selected' : '' }}>Expirada</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="activo" class="form-select">
                            <option value="">Todos</option>
                            <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activas</option>
                            <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivas</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('superadmin.organizaciones') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Organizaciones -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Listado de Organizaciones ({{ $organizaciones->total() }})</h5>
        </div>
        <div class="card-body p-0">
            @if($organizaciones->isEmpty())
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-building fa-3x mb-3" style="opacity: 0.3;"></i>
                    <p>No hay organizaciones registradas con los filtros aplicados</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Organización</th>
                                <th>Plan</th>
                                <th>Estado Suscripción</th>
                                <th>Estado</th>
                                <th>Fecha Registro</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($organizaciones as $org)
                                <tr>
                                    <td>{{ $org->id }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $org->nombre_apr }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-id-card"></i> {{ $org->rut }}<br>
                                                <i class="fas fa-envelope"></i> {{ $org->email_contacto }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $org->suscripcion->nombre ?? 'N/A' }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            ${{ number_format($org->suscripcion->precio_mensual ?? 0, 0, ',', '.') }}/mes
                                        </small>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match($org->estado_suscripcion) {
                                                'activa' => 'bg-success',
                                                'prueba' => 'bg-warning text-dark',
                                                'suspendida' => 'bg-danger',
                                                'expirada' => 'bg-secondary',
                                                'cancelada' => 'bg-dark',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($org->estado_suscripcion) }}
                                        </span>
                                        @if($org->estado_suscripcion === 'prueba')
                                            <br>
                                            <small class="text-muted">
                                                {{ $org->dias_prueba_restantes ?? 0 }} días restantes
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($org->activo)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> Activa
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle"></i> Inactiva
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $org->created_at->format('d/m/Y') }}</small>
                                        <br>
                                        <small class="text-muted">{{ $org->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('superadmin.organizacion.ver', $org->id) }}"
                                               class="btn btn-info"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($org->activo)
                                                <form action="{{ route('superadmin.organizacion.suspender', $org->id) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('¿Está seguro de suspender esta organización?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-warning" title="Suspender">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('superadmin.organizacion.activar', $org->id) }}"
                                                      method="POST"
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success" title="Activar">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('superadmin.organizacion.eliminar', $org->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Está seguro de eliminar esta organización? Esta acción no se puede deshacer.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
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
        @if($organizaciones->hasPages())
            <div class="card-footer bg-white">
                {{ $organizaciones->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
