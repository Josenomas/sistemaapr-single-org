@extends('layouts.app')

@section('title', 'Editar Funcionario - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-user-tie"></i>
        Editar Funcionario
    </h2>
    <a href="{{ route('funcionarios.show', $funcionario->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Funcionario</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('funcionarios.update', $funcionario->id) }}">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h4 class="section-title">Datos Personales</h4>

                <div class="form-row">
                    <div class="form-group">
                        <label for="rut" class="required">RUT</label>
                        <input type="text" id="rut" name="rut" class="form-control @error('rut') error @enderror"
                               value="{{ old('rut', $funcionario->rut) }}" placeholder="12.345.678-9" required>
                        @error('rut')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="nombre" class="required">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') error @enderror"
                               value="{{ old('nombre', $funcionario->nombre) }}" required>
                        @error('nombre')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="apellido_paterno" class="required">Apellido Paterno</label>
                        <input type="text" id="apellido_paterno" name="apellido_paterno"
                               class="form-control @error('apellido_paterno') error @enderror"
                               value="{{ old('apellido_paterno', $funcionario->apellido_paterno) }}" required>
                        @error('apellido_paterno')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="apellido_materno">Apellido Materno</label>
                        <input type="text" id="apellido_materno" name="apellido_materno"
                               class="form-control @error('apellido_materno') error @enderror"
                               value="{{ old('apellido_materno', $funcionario->apellido_materno) }}">
                        @error('apellido_materno')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">Información Laboral</h4>

                <div class="form-row">
                    <div class="form-group">
                        <label for="cargo" class="required">Cargo</label>
                        <input type="text" id="cargo" name="cargo" class="form-control @error('cargo') error @enderror"
                               value="{{ old('cargo', $funcionario->cargo) }}" placeholder="Ej: Operador, Secretaria, etc." required>
                        @error('cargo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="estado" class="required">Estado</label>
                        <select id="estado" name="estado" class="form-control @error('estado') error @enderror" required>
                            <option value="activo" {{ old('estado', $funcionario->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ old('estado', $funcionario->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            <option value="licencia" {{ old('estado', $funcionario->estado) == 'licencia' ? 'selected' : '' }}>Licencia</option>
                        </select>
                        @error('estado')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_ingreso" class="required">Fecha de Ingreso</label>
                        <input type="date" id="fecha_ingreso" name="fecha_ingreso"
                               class="form-control @error('fecha_ingreso') error @enderror"
                               value="{{ old('fecha_ingreso', $funcionario->fecha_ingreso->format('Y-m-d')) }}" required>
                        @error('fecha_ingreso')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="fecha_termino">Fecha de Término</label>
                        <input type="date" id="fecha_termino" name="fecha_termino"
                               class="form-control @error('fecha_termino') error @enderror"
                               value="{{ old('fecha_termino', $funcionario->fecha_termino ? $funcionario->fecha_termino->format('Y-m-d') : '') }}">
                        @error('fecha_termino')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">Contacto</h4>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" class="form-control @error('telefono') error @enderror"
                               value="{{ old('telefono', $funcionario->telefono) }}" placeholder="+56 9 1234 5678">
                        @error('telefono')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') error @enderror"
                               value="{{ old('email', $funcionario->email) }}" placeholder="ejemplo@correo.com">
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="direccion">Dirección</label>
                        <input type="text" id="direccion" name="direccion" class="form-control @error('direccion') error @enderror"
                               value="{{ old('direccion', $funcionario->direccion) }}">
                        @error('direccion')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">Observaciones</h4>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="observaciones">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" class="form-control @error('observaciones') error @enderror"
                                  rows="4">{{ old('observaciones', $funcionario->observaciones) }}</textarea>
                        @error('observaciones')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Actualizar Funcionario
                </button>
                <a href="{{ route('funcionarios.show', $funcionario->id) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .page-title i {
        color: var(--primary);
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 2px solid var(--gray-200);
        background: var(--gray-50);
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .card-body {
        padding: 24px;
    }

    .form-section {
        margin-bottom: 32px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--gray-200);
    }

    .form-section:last-of-type {
        border-bottom: none;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 20px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--primary);
        display: inline-block;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row:last-child {
        margin-bottom: 0;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
    }

    .form-group label.required::after {
        content: ' *';
        color: var(--danger);
    }

    .form-control {
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .form-control.error {
        border-color: var(--danger);
    }

    .error-message {
        font-size: 0.75rem;
        color: var(--danger);
        margin-top: 4px;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid var(--gray-200);
    }

    .btn {
        padding: 10px 20px;
        border-radius: var(--radius);
        border: none;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--gray-600);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-700);
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
