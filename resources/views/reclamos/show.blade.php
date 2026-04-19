@extends('layouts.superadmin')

@section('title', 'Reclamo ' . $reclamo->numero_reclamo . ' - Sistema APR')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-book text-danger"></i>
                        Reclamo {{ $reclamo->numero_reclamo }}
                    </h1>
                    <p class="text-muted mb-0">Detalles y gestión del reclamo</p>
                </div>
                <a href="{{ route('superadmin.reclamos.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Columna Izquierda: Detalles del Reclamo -->
        <div class="col-lg-8">
            <!-- Estado y Plazo -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Estado</h6>
                            @if($reclamo->estado === 'pendiente')
                                <span class="badge bg-warning text-dark fs-6">
                                    <i class="fas fa-clock"></i> Pendiente
                                </span>
                            @elseif($reclamo->estado === 'en_revision')
                                <span class="badge bg-info fs-6">
                                    <i class="fas fa-eye"></i> En Revisión
                                </span>
                            @elseif($reclamo->estado === 'resuelto')
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check-circle"></i> Resuelto
                                </span>
                            @else
                                <span class="badge bg-danger fs-6">
                                    <i class="fas fa-times-circle"></i> Rechazado
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Plazo de Respuesta</h6>
                            @if($reclamo->estado === 'pendiente' || $reclamo->estado === 'en_revision')
                                @php
                                    $diasTranscurridos = $reclamo->created_at->diffInDays(now());
                                    $diasRestantes = 5 - $diasTranscurridos;
                                @endphp
                                @if($diasRestantes > 2)
                                    <span class="badge bg-success fs-6">{{ $diasRestantes }} días restantes</span>
                                @elseif($diasRestantes > 0)
                                    <span class="badge bg-warning text-dark fs-6">{{ $diasRestantes }} días restantes</span>
                                @else
                                    <span class="badge bg-danger fs-6">¡Plazo vencido!</span>
                                @endif
                            @else
                                <p class="mb-0">Respondido el {{ $reclamo->fecha_respuesta->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datos del Reclamante -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-user text-primary"></i>
                        Datos del Reclamante
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-1">Nombre Completo</h6>
                            <p class="mb-0"><strong>{{ $reclamo->nombre_completo }}</strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-1">RUT</h6>
                            <p class="mb-0">{{ $reclamo->rut }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-1">Email</h6>
                            <p class="mb-0">
                                <a href="mailto:{{ $reclamo->email }}">{{ $reclamo->email }}</a>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-1">Teléfono</h6>
                            <p class="mb-0">{{ $reclamo->telefono ?? 'No proporcionado' }}</p>
                        </div>
                        @if($reclamo->direccion)
                        <div class="col-12 mb-3">
                            <h6 class="text-muted mb-1">Dirección</h6>
                            <p class="mb-0">{{ $reclamo->direccion }}</p>
                        </div>
                        @endif
                        @if($reclamo->id_organizacion && $reclamo->organizacion)
                        <div class="col-12">
                            <h6 class="text-muted mb-1">Organización Relacionada</h6>
                            <p class="mb-0">
                                <span class="badge bg-primary">{{ $reclamo->organizacion->nombre_apr ?? 'N/A' }}</span>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detalles del Reclamo -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt text-danger"></i>
                        Detalles del Reclamo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Tipo de Reclamo</h6>
                        <span class="badge bg-secondary">{{ $reclamo->tipo_reclamo_nombre }}</span>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Fecha de Registro</h6>
                        <p class="mb-0">{{ $reclamo->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Detalle del Reclamo</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $reclamo->detalle_reclamo }}
                        </div>
                    </div>
                    @if($reclamo->solucion_solicitada)
                    <div class="mb-0">
                        <h6 class="text-muted mb-1">Solución Solicitada</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $reclamo->solucion_solicitada }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Respuesta (si existe) -->
            @if($reclamo->respuesta)
            <div class="card border-0 shadow-sm mb-4 border-start border-4 {{ $reclamo->estado === 'resuelto' ? 'border-success' : 'border-danger' }}">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-reply text-{{ $reclamo->estado === 'resuelto' ? 'success' : 'danger' }}"></i>
                        Respuesta Enviada
                    </h5>
                </div>
                <div class="card-body">
                    <div class="p-3 bg-light rounded">
                        {{ $reclamo->respuesta }}
                    </div>
                    <hr>
                    <small class="text-muted">
                        <strong>Respondido por:</strong> {{ $reclamo->respondidoPor->nombre ?? 'Sistema' }}
                        {{ $reclamo->respondidoPor->apellido ?? '' }}<br>
                        <strong>Fecha:</strong> {{ $reclamo->fecha_respuesta->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
            @endif
        </div>

        <!-- Columna Derecha: Formulario de Respuesta -->
        <div class="col-lg-4">
            @if($reclamo->estado === 'pendiente' || $reclamo->estado === 'en_revision')
            <!-- Formulario de Respuesta -->
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-reply"></i>
                        Responder Reclamo
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.reclamos.responder', $reclamo->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Estado de la Respuesta <span class="text-danger">*</span></label>
                            <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                <option value="">Seleccionar...</option>
                                <option value="resuelto">✅ Resuelto</option>
                                <option value="rechazado">❌ Rechazado</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Respuesta <span class="text-danger">*</span></label>
                            <textarea name="respuesta"
                                      class="form-control @error('respuesta') is-invalid @enderror"
                                      rows="8"
                                      placeholder="Escribe una respuesta detallada y profesional..."
                                      required>{{ old('respuesta') }}</textarea>
                            <small class="text-muted">Mínimo 10 caracteres</small>
                            @error('respuesta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning" role="alert">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                <strong>Importante:</strong> Al enviar, se notificará al reclamante por email.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane"></i>
                            Enviar Respuesta
                        </button>
                    </form>
                </div>
            </div>
            @else
            <!-- Ya fue respondido -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>Reclamo ya respondido</h5>
                    <p class="text-muted mb-0">Este reclamo fue {{ $reclamo->estado === 'resuelto' ? 'resuelto' : 'rechazado' }} el {{ $reclamo->fecha_respuesta->format('d/m/Y') }}</p>
                </div>
            </div>
            @endif

            <!-- Metadatos Técnicos -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle"></i>
                        Metadatos Técnicos
                    </h6>
                </div>
                <div class="card-body">
                    <small class="d-block mb-2">
                        <strong>IP:</strong><br>
                        <code>{{ $reclamo->ip_address }}</code>
                    </small>
                    <small class="d-block">
                        <strong>Navegador:</strong><br>
                        <code style="font-size: 10px;">{{ $reclamo->user_agent }}</code>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
