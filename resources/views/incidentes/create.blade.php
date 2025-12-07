@extends('layouts.app')

@section('title', 'Reportar Incidente - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-exclamation-triangle"></i>
        Reportar Nuevo Incidente
    </h2>
    <div class="header-actions">
        <a href="{{ route('incidentes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('incidentes.store') }}" method="POST">
            @csrf

            <div class="form-row">
                <!-- Tipo de Incidente -->
                <div class="form-group col-md-6">
                    <label for="tipo" class="form-label required">Tipo de Incidente</label>
                    <select class="form-control @error('tipo') is-invalid @enderror"
                            id="tipo"
                            name="tipo"
                            required>
                        <option value="">Seleccione un tipo...</option>
                        <option value="fuga" {{ old('tipo') == 'fuga' ? 'selected' : '' }}>Fuga de Agua</option>
                        <option value="corte" {{ old('tipo') == 'corte' ? 'selected' : '' }}>Corte de Suministro</option>
                        <option value="baja_presion" {{ old('tipo') == 'baja_presion' ? 'selected' : '' }}>Baja Presión</option>
                        <option value="contaminacion" {{ old('tipo') == 'contaminacion' ? 'selected' : '' }}>Contaminación</option>
                        <option value="otro" {{ old('tipo') == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('tipo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Prioridad -->
                <div class="form-group col-md-6">
                    <label for="prioridad" class="form-label required">Prioridad</label>
                    <select class="form-control @error('prioridad') is-invalid @enderror"
                            id="prioridad"
                            name="prioridad"
                            required>
                        <option value="">Seleccione prioridad...</option>
                        <option value="baja" {{ old('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                        <option value="media" {{ old('prioridad') == 'media' ? 'selected' : '' }}>Media</option>
                        <option value="alta" {{ old('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                        <option value="critica" {{ old('prioridad') == 'critica' ? 'selected' : '' }}>Crítica</option>
                    </select>
                    @error('prioridad')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Ubicación -->
                <div class="form-group col-md-8">
                    <label for="ubicacion" class="form-label required">Ubicación</label>
                    <input type="text"
                           class="form-control @error('ubicacion') is-invalid @enderror"
                           id="ubicacion"
                           name="ubicacion"
                           value="{{ old('ubicacion') }}"
                           placeholder="Ej: Calle Principal #123"
                           required>
                    @error('ubicacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Sector -->
                <div class="form-group col-md-4">
                    <label for="sector" class="form-label">Sector</label>
                    <input type="text"
                           class="form-control @error('sector') is-invalid @enderror"
                           id="sector"
                           name="sector"
                           value="{{ old('sector') }}"
                           placeholder="Ej: Centro, Norte, Sur">
                    @error('sector')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Descripción -->
            <div class="form-group">
                <label for="descripcion" class="form-label required">Descripción del Incidente</label>
                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                          id="descripcion"
                          name="descripcion"
                          rows="4"
                          placeholder="Describa detalladamente el incidente..."
                          required>{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <!-- Socio que Reporta -->
                <div class="form-group col-md-6">
                    <label for="id_socio_reporta" class="form-label">Socio que Reporta</label>
                    <select class="form-control @error('id_socio_reporta') is-invalid @enderror"
                            id="id_socio_reporta"
                            name="id_socio_reporta">
                        <option value="">Seleccione un socio (opcional)...</option>
                        @foreach($socios as $socio)
                            <option value="{{ $socio->id }}" {{ old('id_socio_reporta') == $socio->id ? 'selected' : '' }}>
                                {{ $socio->numero_socio }} - {{ $socio->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_socio_reporta')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Usuario Asignado -->
                <div class="form-group col-md-6">
                    <label for="id_usuario_asignado" class="form-label">Asignar a</label>
                    <select class="form-control @error('id_usuario_asignado') is-invalid @enderror"
                            id="id_usuario_asignado"
                            name="id_usuario_asignado">
                        <option value="">Sin asignar...</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ old('id_usuario_asignado') == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->nombre_completo }} ({{ ucfirst($usuario->rol) }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_usuario_asignado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Observaciones -->
            <div class="form-group">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control @error('observaciones') is-invalid @enderror"
                          id="observaciones"
                          name="observaciones"
                          rows="3"
                          placeholder="Observaciones adicionales...">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Reportar Incidente
                </button>
                <a href="{{ route('incidentes.index') }}" class="btn btn-secondary">
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
        color: #f59e0b;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
    }

    .card-body {
        padding: 32px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .col-md-4 { grid-column: span 4; }
    .col-md-6 { grid-column: span 6; }
    .col-md-8 { grid-column: span 8; }
    .col-md-12 { grid-column: span 12; }

    .form-label {
        display: block;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-label.required::after {
        content: '*';
        color: #ef4444;
        margin-left: 4px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--gray-300);
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-control.is-invalid {
        border-color: #ef4444;
    }

    .invalid-feedback {
        display: block;
        color: #ef4444;
        font-size: 13px;
        margin-top: 4px;
    }

    textarea.form-control {
        resize: vertical;
        font-family: inherit;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-200);
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
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-secondary:hover {
        background: var(--gray-300);
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .header-actions {
            width: 100%;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .col-md-4,
        .col-md-6,
        .col-md-8,
        .col-md-12 {
            grid-column: span 1;
        }
    }
</style>
@endsection
