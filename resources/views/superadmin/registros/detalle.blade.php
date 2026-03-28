@extends('layouts.superadmin')

@section('title', 'Detalle de Registro')

@section('page-title')

@section('content')

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-file-alt"></i> Detalle del Registro</h1>
            <p class="text-muted">Información completa del registro pendiente</p>
        </div>
        <div>
            <a href="{{ route('superadmin.registros.pendientes') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información del Registro -->
        <div class="col-md-8 mb-4">
            <!-- Información de la Organización -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-building"></i> Información de la Organización</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted">Nombre APR</label>
                            <p>{{ $registro->nombre_apr }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted">Slug</label>
                            <p><code>{{ $registro->slug }}</code></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted">RUT</label>
                            <p>{{ $registro->rut }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted">Email de Contacto</label>
                            <p>{{ $registro->email_contacto }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="fw-bold text-muted">Dirección</label>
                            <p>{{ $registro->direccion ?? 'No especificada' }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted">Comuna</label>
                            <p>{{ $registro->comuna ?? 'No especificada' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted">Región</label>
                            <p>{{ $registro->region ?? 'No especificada' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted">Teléfono</label>
                            <p>{{ $registro->telefono ?? 'No especificado' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Administrador -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie"></i> Información del Administrador</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted">Nombre</label>
                            <p>{{ $registro->admin_nombre }} {{ $registro->admin_apellido }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted">Email</label>
                            <p>{{ $registro->admin_email }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted">Teléfono</label>
                            <p>{{ $registro->admin_telefono ?? 'No especificado' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notas Adicionales -->
            @if($registro->notas)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-sticky-note"></i> Notas Adicionales</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $registro->notas }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Panel Lateral -->
        <div class="col-md-4">
            <!-- Estado del Registro -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Estado del Registro</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Estado</label>
                        <p>
                            @php
                                $badgeClass = match($registro->estado) {
                                    'pendiente' => 'bg-warning text-dark',
                                    'verificado' => 'bg-success',
                                    'rechazado' => 'bg-danger',
                                    'expirado' => 'bg-secondary',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} fs-6">
                                {{ ucfirst($registro->estado) }}
                            </span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted">Plan Deseado</label>
                        <p>
                            @if($registro->suscripcionDeseada)
                                <span class="badge bg-primary fs-6">
                                    {{ $registro->suscripcionDeseada->nombre }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    ${{ number_format($registro->suscripcionDeseada->precio_mensual, 0, ',', '.') }}/mes
                                </small>
                            @else
                                <span class="badge bg-secondary">Básico (default)</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted">Fecha de Registro</label>
                        <p class="mb-0">{{ $registro->created_at->format('d/m/Y H:i') }}</p>
                        <small class="text-muted">{{ $registro->created_at->diffForHumans() }}</small>
                    </div>

                    @if($registro->expira_en)
                        <div class="mb-3">
                            <label class="fw-bold text-muted">Fecha de Expiración</label>
                            @if($registro->haExpirado())
                                <p class="mb-0 text-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Expirado el {{ $registro->expira_en->format('d/m/Y H:i') }}
                                </p>
                            @else
                                <p class="mb-0">{{ $registro->expira_en->format('d/m/Y H:i') }}</p>
                                <small class="text-muted">{{ $registro->expira_en->diffForHumans() }}</small>
                            @endif
                        </div>
                    @endif

                    @if($registro->email_verificado_at)
                        <div class="mb-3">
                            <label class="fw-bold text-muted">Email Verificado</label>
                            <p class="mb-0 text-success">
                                <i class="fas fa-check-circle"></i>
                                {{ $registro->email_verificado_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    @endif

                    @if($registro->ip_registro)
                        <div class="mb-3">
                            <label class="fw-bold text-muted">IP de Registro</label>
                            <p class="mb-0"><code>{{ $registro->ip_registro }}</code></p>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="fw-bold text-muted">Token de Verificación</label>
                        <p class="mb-0">
                            <small><code>{{ substr($registro->token_verificacion, 0, 20) }}...</code></small>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            @if($registro->estado === 'pendiente')
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-tools"></i> Acciones</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.registros.aprobar', $registro->id) }}"
                              method="POST"
                              class="mb-2"
                              onsubmit="return confirm('¿Aprobar este registro y crear la organización automáticamente?')">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check"></i> Aprobar Registro
                            </button>
                        </form>

                        <form action="{{ route('superadmin.registros.rechazar', $registro->id) }}"
                              method="POST"
                              onsubmit="return confirm('¿Está seguro de rechazar este registro?')">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-times"></i> Rechazar Registro
                            </button>
                        </form>

                        <hr>

                        <div class="alert alert-info mb-0">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                <strong>Aprobar:</strong> Creará la organización y usuario admin automáticamente.
                                <br><br>
                                <strong>Rechazar:</strong> Marcará el registro como rechazado.
                            </small>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
