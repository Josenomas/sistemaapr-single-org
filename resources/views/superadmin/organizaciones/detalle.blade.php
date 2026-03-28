@extends('layouts.superadmin')

@section('title', 'Detalle de Organización')

@section('page-title')

@section('content')

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-building"></i> {{ $organizacion->nombre_apr }}
            </h1>
            <p class="text-muted">Detalle completo de la organización</p>
        </div>
        <div>
            <a href="{{ route('superadmin.organizaciones') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información General -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información General</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted">Nombre APR</label>
                            <p>{{ $organizacion->nombre_apr }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted">RUT</label>
                            <p>{{ $organizacion->rut }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted">Email Contacto</label>
                            <p>{{ $organizacion->email_contacto }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted">Teléfono</label>
                            <p>{{ $organizacion->telefono ?? 'No especificado' }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="fw-bold text-muted">Dirección</label>
                            <p>{{ $organizacion->direccion ?? 'No especificada' }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted">Comuna</label>
                            <p>{{ $organizacion->comuna ?? 'No especificada' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted">Región</label>
                            <p>{{ $organizacion->region ?? 'No especificada' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Estadísticas de Uso</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3 class="mb-0 text-primary">{{ $totalUsuarios }}</h3>
                                <small class="text-muted">Usuarios</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3 class="mb-0 text-success">{{ $totalSocios }}</h3>
                                <small class="text-muted">Socios</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3 class="mb-0 text-info">{{ $totalBoletas }}</h3>
                                <small class="text-muted">Boletas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3 class="mb-0 text-warning">${{ number_format($totalPagos, 0, ',', '.') }}</h3>
                                <small class="text-muted">Total Pagos</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usuarios Recientes -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Usuarios Recientes</h5>
                </div>
                <div class="card-body">
                    @if($usuariosRecientes->isEmpty())
                        <p class="text-muted text-center py-3">No hay usuarios registrados</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Fecha Creación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usuariosRecientes as $usuario)
                                        <tr>
                                            <td>{{ $usuario->nombre_completo }}</td>
                                            <td>{{ $usuario->email }}</td>
                                            <td><span class="badge bg-secondary">{{ $usuario->rol }}</span></td>
                                            <td>{{ $usuario->created_at ? $usuario->created_at->format('d/m/Y') : 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Socios Recientes -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-user-friends"></i> Socios Recientes</h5>
                </div>
                <div class="card-body">
                    @if($sociosRecientes->isEmpty())
                        <p class="text-muted text-center py-3">No hay socios registrados</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Número</th>
                                        <th>Nombre</th>
                                        <th>RUT</th>
                                        <th>Estado</th>
                                        <th>Fecha Registro</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sociosRecientes as $socio)
                                        <tr>
                                            <td>{{ $socio->numero_socio }}</td>
                                            <td>{{ $socio->nombre_completo }}</td>
                                            <td>{{ $socio->rut }}</td>
                                            <td><span class="badge bg-success">{{ $socio->estado }}</span></td>
                                            <td>{{ $socio->fecha_creacion ? \Carbon\Carbon::parse($socio->fecha_creacion)->format('d/m/Y') : 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Panel Lateral de Acciones -->
        <div class="col-md-4">
            <!-- Estado y Plan -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> Estado y Plan</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Plan Actual</label>
                        <p class="mb-0">
                            <span class="badge bg-primary fs-6">
                                {{ $organizacion->suscripcion->nombre ?? 'N/A' }}
                            </span>
                        </p>
                        <small class="text-muted">
                            ${{ number_format($organizacion->suscripcion->precio_mensual ?? 0, 0, ',', '.') }}/mes
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted">Estado Suscripción</label>
                        <p>
                            @php
                                $badgeClass = match($organizacion->estado_suscripcion) {
                                    'activa' => 'bg-success',
                                    'prueba' => 'bg-warning text-dark',
                                    'suspendida' => 'bg-danger',
                                    'expirada' => 'bg-secondary',
                                    'cancelada' => 'bg-dark',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ ucfirst($organizacion->estado_suscripcion) }}
                            </span>
                        </p>
                    </div>

                    @if($organizacion->estado_suscripcion === 'prueba')
                        <div class="mb-3">
                            <label class="fw-bold text-muted">Días de Prueba Restantes</label>
                            <p class="mb-0">{{ $organizacion->dias_prueba_restantes ?? 0 }} días</p>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="fw-bold text-muted">Estado General</label>
                        <p>
                            @if($organizacion->activo)
                                <span class="badge bg-success">Activa</span>
                            @else
                                <span class="badge bg-danger">Inactiva</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted">Fecha de Registro</label>
                        @if($organizacion->created_at)
                            <p class="mb-0">{{ $organizacion->created_at->format('d/m/Y H:i') }}</p>
                            <small class="text-muted">{{ $organizacion->created_at->diffForHumans() }}</small>
                        @else
                            <p class="mb-0 text-muted">No disponible</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Cambiar Plan -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-exchange-alt"></i> Cambiar Plan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.organizacion.cambiar-plan', $organizacion->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nuevo Plan</label>
                            <select name="id_suscripcion" class="form-select" required>
                                @foreach(\App\Models\Suscripcion::all() as $sus)
                                    <option value="{{ $sus->id }}" {{ $organizacion->id_suscripcion == $sus->id ? 'selected' : '' }}>
                                        {{ $sus->nombre }} - ${{ number_format($sus->precio_mensual, 0, ',', '.') }}/mes
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check"></i> Cambiar Plan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-tools"></i> Acciones</h5>
                </div>
                <div class="card-body">
                    @if($organizacion->activo)
                        <form action="{{ route('superadmin.organizacion.suspender', $organizacion->id) }}"
                              method="POST"
                              onsubmit="return confirm('¿Está seguro de suspender esta organización?')">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-pause"></i> Suspender Organización
                            </button>
                        </form>
                    @else
                        <form action="{{ route('superadmin.organizacion.activar', $organizacion->id) }}"
                              method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-play"></i> Activar Organización
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('superadmin.organizacion.eliminar', $organizacion->id) }}"
                          method="POST"
                          onsubmit="return confirm('¿Está seguro de eliminar esta organización? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Eliminar Organización
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
