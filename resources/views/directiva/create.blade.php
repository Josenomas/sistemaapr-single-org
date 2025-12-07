@extends('layouts.app')

@section('title', 'Nuevo Miembro Directiva - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-plus-circle"></i>
        Registrar Nuevo Miembro de Directiva
    </h2>
    <a href="{{ route('directiva.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Miembro de Directiva</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('directiva.store') }}" method="POST">
            @csrf

            <div class="form-row">
                <!-- Socio -->
                <div class="form-group col-md-6">
                    <label for="id_socio" class="form-label required">Socio</label>
                    <select class="form-control @error('id_socio') is-invalid @enderror"
                            id="id_socio"
                            name="id_socio"
                            required>
                        <option value="">Seleccione un socio...</option>
                        @foreach($socios as $socio)
                            <option value="{{ $socio->id }}" {{ old('id_socio') == $socio->id ? 'selected' : '' }}>
                                {{ $socio->nombre_completo }} - RUT: {{ $socio->rut }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_socio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Cargo -->
                <div class="form-group col-md-6">
                    <label for="cargo" class="form-label required">Cargo</label>
                    <select class="form-control @error('cargo') is-invalid @enderror"
                            id="cargo"
                            name="cargo"
                            required>
                        <option value="">Seleccione...</option>
                        <option value="presidente" {{ old('cargo') == 'presidente' ? 'selected' : '' }}>Presidente</option>
                        <option value="vicepresidente" {{ old('cargo') == 'vicepresidente' ? 'selected' : '' }}>Vicepresidente</option>
                        <option value="secretario" {{ old('cargo') == 'secretario' ? 'selected' : '' }}>Secretario</option>
                        <option value="tesorero" {{ old('cargo') == 'tesorero' ? 'selected' : '' }}>Tesorero</option>
                        <option value="director" {{ old('cargo') == 'director' ? 'selected' : '' }}>Director</option>
                        <option value="vocal" {{ old('cargo') == 'vocal' ? 'selected' : '' }}>Vocal</option>
                        <option value="suplente" {{ old('cargo') == 'suplente' ? 'selected' : '' }}>Suplente</option>
                    </select>
                    @error('cargo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Periodo -->
                <div class="form-group col-md-6">
                    <label for="periodo" class="form-label required">Periodo</label>
                    <input type="text"
                           class="form-control @error('periodo') is-invalid @enderror"
                           id="periodo"
                           name="periodo"
                           value="{{ old('periodo') }}"
                           placeholder="Ej: 2024-2026"
                           maxlength="20"
                           required>
                    <small class="text-muted">Formato sugerido: YYYY-YYYY</small>
                    @error('periodo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Estado -->
                <div class="form-group col-md-6">
                    <label for="estado" class="form-label required">Estado</label>
                    <select class="form-control @error('estado') is-invalid @enderror"
                            id="estado"
                            name="estado"
                            required>
                        <option value="">Seleccione...</option>
                        <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }} selected>Activo</option>
                        <option value="finalizado" {{ old('estado') == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                        <option value="renunciado" {{ old('estado') == 'renunciado' ? 'selected' : '' }}>Renunciado</option>
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Fecha Inicio -->
                <div class="form-group col-md-6">
                    <label for="fecha_inicio" class="form-label required">Fecha de Inicio</label>
                    <input type="date"
                           class="form-control @error('fecha_inicio') is-invalid @enderror"
                           id="fecha_inicio"
                           name="fecha_inicio"
                           value="{{ old('fecha_inicio') }}"
                           required>
                    @error('fecha_inicio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Fecha Término -->
                <div class="form-group col-md-6">
                    <label for="fecha_termino" class="form-label">Fecha de Término</label>
                    <input type="date"
                           class="form-control @error('fecha_termino') is-invalid @enderror"
                           id="fecha_termino"
                           name="fecha_termino"
                           value="{{ old('fecha_termino') }}">
                    <small class="text-muted">Opcional. Dejar vacío si aún está en el cargo</small>
                    @error('fecha_termino')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Acta de Nombramiento -->
                <div class="form-group col-md-12">
                    <label for="acta_nombramiento" class="form-label">Acta de Nombramiento</label>
                    <input type="text"
                           class="form-control @error('acta_nombramiento') is-invalid @enderror"
                           id="acta_nombramiento"
                           name="acta_nombramiento"
                           value="{{ old('acta_nombramiento') }}"
                           placeholder="Ej: Acta N° 12/2024"
                           maxlength="100">
                    <small class="text-muted">Número o referencia del acta de nombramiento</small>
                    @error('acta_nombramiento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Observaciones -->
                <div class="form-group col-md-12">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea class="form-control @error('observaciones') is-invalid @enderror"
                              id="observaciones"
                              name="observaciones"
                              rows="4"
                              placeholder="Observaciones adicionales (opcional)">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Registrar Miembro
                </button>
                <a href="{{ route('directiva.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        padding-bottom: 16px;
        border-bottom: 2px solid var(--gray-200);
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--gray-800);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title i {
        color: var(--primary);
    }

    .card {
        background: var(--white);
        border-radius: 8px;
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
        color: var(--gray-800);
        margin: 0;
    }

    .card-body {
        padding: 24px;
    }

    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        flex: 1;
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
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: 6px;
        font-size: 0.875rem;
        transition: all 0.2s;
        background: var(--white);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-control.is-invalid {
        border-color: var(--danger);
    }

    .invalid-feedback {
        color: var(--danger);
        font-size: 0.875rem;
        margin-top: 4px;
    }

    .text-muted {
        color: var(--gray-500);
        font-size: 0.75rem;
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
        padding: 10px 24px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-secondary {
        background: var(--gray-500);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-600);
    }

    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }
    }
</style>
