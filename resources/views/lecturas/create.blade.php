@extends('layouts.app')

@section('title', 'Nueva Lectura - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-plus"></i>
        Registrar Nueva Lectura
    </h2>
    <a href="{{ route('lecturas.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Lectura</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('lecturas.store') }}" method="POST">
            @csrf

            <div class="form-row">
                <!-- Socio -->
                <div class="form-group col-md-4">
                    <label for="id_socio" class="form-label required">Socio</label>
                    <select class="form-control @error('id_socio') is-invalid @enderror"
                            id="id_socio"
                            name="id_socio"
                            required>
                        <option value="">Seleccione un socio</option>
                        @foreach($socios as $socio)
                            <option value="{{ $socio->id }}" {{ old('id_socio') == $socio->id ? 'selected' : '' }}>
                                {{ $socio->numero_socio }} - {{ $socio->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_socio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Mes/Año -->
                <div class="form-group col-md-4">
                    <label for="mes" class="form-label required">Mes/Año</label>
                    <input type="month"
                           class="form-control @error('mes') is-invalid @enderror"
                           id="mes"
                           name="mes"
                           value="{{ old('mes', date('Y-m')) }}"
                           required>
                    @error('mes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Fecha de Lectura -->
                <div class="form-group col-md-4">
                    <label for="fecha_lectura" class="form-label required">Fecha de Lectura</label>
                    <input type="date"
                           class="form-control @error('fecha_lectura') is-invalid @enderror"
                           id="fecha_lectura"
                           name="fecha_lectura"
                           value="{{ old('fecha_lectura', date('Y-m-d')) }}"
                           required>
                    @error('fecha_lectura')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Lectura Anterior -->
                <div class="form-group col-md-4">
                    <label for="lectura_anterior" class="form-label required">Lectura Anterior (m³)</label>
                    <input type="number"
                           class="form-control @error('lectura_anterior') is-invalid @enderror"
                           id="lectura_anterior"
                           name="lectura_anterior"
                           value="{{ old('lectura_anterior', 0) }}"
                           min="0"
                           step="0.01"
                           required>
                    @error('lectura_anterior')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Lectura Actual -->
                <div class="form-group col-md-4">
                    <label for="lectura_actual" class="form-label required">Lectura Actual (m³)</label>
                    <input type="number"
                           class="form-control @error('lectura_actual') is-invalid @enderror"
                           id="lectura_actual"
                           name="lectura_actual"
                           value="{{ old('lectura_actual') }}"
                           min="0"
                           step="0.01"
                           required>
                    @error('lectura_actual')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Consumo -->
                <div class="form-group col-md-4">
                    <label for="consumo" class="form-label">Consumo (m³)</label>
                    <input type="number"
                           class="form-control"
                           id="consumo"
                           name="consumo"
                           value="{{ old('consumo', 0) }}"
                           min="0"
                           step="0.01"
                           readonly>
                    <small class="form-text">Se calcula automáticamente</small>
                </div>
            </div>

            <!-- Observaciones -->
            <div class="form-group">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control @error('observaciones') is-invalid @enderror"
                          id="observaciones"
                          name="observaciones"
                          rows="3"
                          placeholder="Notas adicionales sobre la lectura...">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar Lectura
                </button>
                <a href="{{ route('lecturas.index') }}" class="btn btn-secondary">
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

    .form-text {
        color: var(--gray-500);
        font-size: 0.8rem;
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const lecturaAnterior = document.getElementById('lectura_anterior');
    const lecturaActual = document.getElementById('lectura_actual');
    const consumo = document.getElementById('consumo');

    function calcularConsumo() {
        const anterior = parseFloat(lecturaAnterior.value) || 0;
        const actual = parseFloat(lecturaActual.value) || 0;
        const resultado = Math.max(0, actual - anterior);
        consumo.value = resultado.toFixed(2);
    }

    lecturaAnterior.addEventListener('input', calcularConsumo);
    lecturaActual.addEventListener('input', calcularConsumo);
});
</script>
@endsection
