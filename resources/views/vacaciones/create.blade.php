@extends('layouts.app')

@section('title', 'Nueva Vacación - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-plus-circle"></i>
        Registrar Nueva Vacación
    </h2>
    <a href="{{ route('vacaciones.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Vacación</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('vacaciones.store') }}" method="POST">
            @csrf

            <!-- Funcionario y Periodo -->
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="id_funcionario" class="form-label required">Funcionario</label>
                    <select name="id_funcionario" id="id_funcionario" class="form-control @error('id_funcionario') is-invalid @enderror" required>
                        <option value="">Seleccione un funcionario</option>
                        @foreach($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}" {{ old('id_funcionario') == $funcionario->id ? 'selected' : '' }}>
                                {{ $funcionario->nombre }} {{ $funcionario->apellido_paterno }} - {{ $funcionario->cargo }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_funcionario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="periodo" class="form-label required">Periodo (Año)</label>
                    <input type="text" name="periodo" id="periodo" class="form-control @error('periodo') is-invalid @enderror"
                           value="{{ old('periodo', date('Y')) }}" required maxlength="4" placeholder="Ej: 2024">
                    @error('periodo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Fechas -->
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="fecha_inicio" class="form-label required">Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio"
                           class="form-control @error('fecha_inicio') is-invalid @enderror"
                           value="{{ old('fecha_inicio') }}" required>
                    @error('fecha_inicio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="fecha_termino" class="form-label required">Fecha de Término</label>
                    <input type="date" name="fecha_termino" id="fecha_termino"
                           class="form-control @error('fecha_termino') is-invalid @enderror"
                           value="{{ old('fecha_termino') }}" required>
                    @error('fecha_termino')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="dias_habiles" class="form-label required">Días Hábiles</label>
                    <input type="number" name="dias_habiles" id="dias_habiles"
                           class="form-control @error('dias_habiles') is-invalid @enderror"
                           value="{{ old('dias_habiles') }}" required min="1" placeholder="Ej: 15">
                    @error('dias_habiles')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Tipo, Estado y Suplente -->
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="tipo" class="form-label required">Tipo de Vacación</label>
                    <select name="tipo" id="tipo" class="form-control @error('tipo') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        <option value="legales" {{ old('tipo') == 'legales' ? 'selected' : '' }}>Legales</option>
                        <option value="progresivas" {{ old('tipo') == 'progresivas' ? 'selected' : '' }}>Progresivas</option>
                        <option value="administrativas" {{ old('tipo') == 'administrativas' ? 'selected' : '' }}>Administrativas</option>
                        <option value="sin_goce" {{ old('tipo') == 'sin_goce' ? 'selected' : '' }}>Sin Goce de Sueldo</option>
                    </select>
                    @error('tipo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="estado" class="form-label required">Estado</label>
                    <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        <option value="solicitada" {{ old('estado', 'solicitada') == 'solicitada' ? 'selected' : '' }}>Solicitada</option>
                        <option value="aprobada" {{ old('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                        <option value="rechazada" {{ old('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                        <option value="en_curso" {{ old('estado') == 'en_curso' ? 'selected' : '' }}>En Curso</option>
                        <option value="finalizada" {{ old('estado') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                        <option value="cancelada" {{ old('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="suplente" class="form-label">Funcionario Suplente</label>
                    <select name="suplente" id="suplente" class="form-control @error('suplente') is-invalid @enderror">
                        <option value="">Sin asignar</option>
                        @foreach($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}" {{ old('suplente') == $funcionario->id ? 'selected' : '' }}>
                                {{ $funcionario->nombre }} {{ $funcionario->apellido_paterno }}
                            </option>
                        @endforeach
                    </select>
                    @error('suplente')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Aprobación -->
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="fecha_solicitud" class="form-label required">Fecha de Solicitud</label>
                    <input type="date" name="fecha_solicitud" id="fecha_solicitud"
                           class="form-control @error('fecha_solicitud') is-invalid @enderror"
                           value="{{ old('fecha_solicitud', date('Y-m-d')) }}" required>
                    @error('fecha_solicitud')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="id_aprobador" class="form-label">Aprobado Por</label>
                    <select name="id_aprobador" id="id_aprobador" class="form-control @error('id_aprobador') is-invalid @enderror">
                        <option value="">Sin asignar</option>
                        @foreach($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}" {{ old('id_aprobador') == $funcionario->id ? 'selected' : '' }}>
                                {{ $funcionario->nombre }} {{ $funcionario->apellido_paterno }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_aprobador')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="fecha_aprobacion" class="form-label">Fecha de Aprobación</label>
                    <input type="date" name="fecha_aprobacion" id="fecha_aprobacion"
                           class="form-control @error('fecha_aprobacion') is-invalid @enderror"
                           value="{{ old('fecha_aprobacion') }}">
                    @error('fecha_aprobacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Observaciones -->
            <div class="form-group">
                <label for="motivo_rechazo" class="form-label">Motivo de Rechazo</label>
                <textarea name="motivo_rechazo" id="motivo_rechazo" rows="2"
                          class="form-control @error('motivo_rechazo') is-invalid @enderror"
                          placeholder="Solo si fue rechazada">{{ old('motivo_rechazo') }}</textarea>
                @error('motivo_rechazo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea name="observaciones" id="observaciones" rows="3"
                          class="form-control @error('observaciones') is-invalid @enderror"
                          placeholder="Notas adicionales sobre la vacación...">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar Vacación
                </button>
                <a href="{{ route('vacaciones.index') }}" class="btn btn-secondary">
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
        border-bottom: 1px solid var(--gray-200);
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

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
        font-size: 0.875rem;
    }

    .form-label.required::after {
        content: ' *';
        color: var(--danger);
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.95rem;
        transition: all 0.2s;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .form-control.is-invalid {
        border-color: var(--danger);
    }

    .invalid-feedback {
        color: var(--danger);
        font-size: 0.875rem;
        margin-top: 4px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-200);
    }

    .btn {
        padding: 12px 24px;
        border-radius: var(--radius);
        border: none;
        font-weight: 600;
        font-size: 0.95rem;
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
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-secondary:hover {
        background: var(--gray-300);
    }

    select.form-control {
        cursor: pointer;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    .col-md-4 {
        grid-column: span 1;
    }

    .col-md-8 {
        grid-column: span 2;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .col-md-4,
        .col-md-8 {
            grid-column: span 1;
        }
    }
</style>
@endsection
