@extends('layouts.superadmin')

@section('title', 'Mi Perfil - Super Admin')
@section('page-title', 'Mi Perfil')

@section('content')
<div class="container-fluid">
    <!-- Mensajes de éxito/error -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Card de Información Personal -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Información Personal
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.perfil.actualizar') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user text-primary me-2"></i>
                                Nombre Completo
                            </label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $usuario->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                Correo Electrónico
                            </label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $usuario->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">
                            <i class="fas fa-key text-warning me-2"></i>
                            Cambiar Contraseña (Opcional)
                        </h6>
                        <p class="text-muted small mb-3">
                            Deja estos campos vacíos si no deseas cambiar tu contraseña.
                        </p>

                        <!-- Nueva Contraseña -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Nueva Contraseña
                            </label>
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   minlength="8">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Mínimo 8 caracteres
                            </small>
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                Confirmar Nueva Contraseña
                            </label>
                            <input type="password"
                                   class="form-control"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   minlength="8">
                        </div>

                        <!-- Botones -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Guardar Cambios
                            </button>
                            <a href="{{ route('superadmin.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card de Información de Cuenta -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Información de la Cuenta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tipo de Usuario</label>
                            <p class="mb-0 fw-bold">
                                <i class="fas fa-crown text-warning me-2"></i>
                                Super Administrador
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">ID de Usuario</label>
                            <p class="mb-0">{{ $usuario->id }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Cuenta Creada</label>
                            <p class="mb-0">{{ $usuario->created_at ? $usuario->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Última Actualización</label>
                            <p class="mb-0">{{ $usuario->updated_at ? $usuario->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: 1px solid var(--border);
        background: var(--dark-card);
    }

    .card-header {
        background: rgba(124, 58, 237, 0.05);
        border-bottom: 1px solid var(--border);
        color: var(--text-light);
    }

    .form-label {
        color: var(--text-light);
        font-weight: 500;
    }

    .form-control {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border);
        color: var(--text-light);
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.08);
        border-color: var(--primary);
        color: var(--text-light);
    }

    .text-muted {
        color: var(--text-muted) !important;
    }

    hr {
        border-color: var(--border);
        opacity: 0.3;
    }

    .btn-primary {
        background: var(--primary);
        border-color: var(--primary);
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
    }

    .btn-secondary {
        background: #6c757d;
        border-color: #6c757d;
    }

    /* Modo claro */
    body.light-mode .form-control {
        background: #ffffff;
        color: #1e293b;
    }

    body.light-mode .form-control:focus {
        background: #f8fafc;
        color: #1e293b;
    }

    body.light-mode .card-header {
        background: #f8fafc;
    }
</style>
@endsection
