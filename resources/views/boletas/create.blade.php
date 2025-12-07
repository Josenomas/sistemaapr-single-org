@extends('layouts.app')

@section('title', 'Nueva Boleta - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-file-invoice-dollar"></i>
        Crear Nueva Boleta
    </h2>
    <a href="{{ route('boletas.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Boleta</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('boletas.store') }}" method="POST">
            @csrf

            <!-- Información del Socio -->
            <div class="form-section">
                <h4 class="section-title">Información del Socio</h4>

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="id_socio" class="form-label required">Socio</label>
                        <select class="form-control @error('id_socio') is-invalid @enderror"
                                id="id_socio"
                                name="id_socio"
                                required>
                            <option value="">Seleccione un socio...</option>
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
                </div>
            </div>

            <!-- Período y Fechas -->
            <div class="form-section">
                <h4 class="section-title">Período y Fechas</h4>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="mes" class="form-label required">Mes</label>
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

                    <div class="form-group col-md-4">
                        <label for="fecha_emision" class="form-label required">Fecha de Emisión</label>
                        <input type="date"
                               class="form-control @error('fecha_emision') is-invalid @enderror"
                               id="fecha_emision"
                               name="fecha_emision"
                               value="{{ old('fecha_emision', date('Y-m-d')) }}"
                               required>
                        @error('fecha_emision')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label for="fecha_vencimiento" class="form-label required">Fecha de Vencimiento</label>
                        <input type="date"
                               class="form-control @error('fecha_vencimiento') is-invalid @enderror"
                               id="fecha_vencimiento"
                               name="fecha_vencimiento"
                               value="{{ old('fecha_vencimiento', date('Y-m-d', strtotime('+15 days'))) }}"
                               required>
                        @error('fecha_vencimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Consumo y Cargos -->
            <div class="form-section">
                <h4 class="section-title">Consumo y Cargos</h4>

                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="consumo_m3" class="form-label required">Consumo (m³)</label>
                        <input type="number"
                               class="form-control @error('consumo_m3') is-invalid @enderror"
                               id="consumo_m3"
                               name="consumo_m3"
                               value="{{ old('consumo_m3', 0) }}"
                               step="0.01"
                               min="0"
                               required>
                        @error('consumo_m3')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label for="cargo_fijo" class="form-label required">Cargo Fijo</label>
                        <input type="number"
                               class="form-control @error('cargo_fijo') is-invalid @enderror"
                               id="cargo_fijo"
                               name="cargo_fijo"
                               value="{{ old('cargo_fijo', 0) }}"
                               step="0.01"
                               min="0"
                               required>
                        @error('cargo_fijo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label for="cargo_consumo" class="form-label required">Cargo por Consumo</label>
                        <input type="number"
                               class="form-control @error('cargo_consumo') is-invalid @enderror"
                               id="cargo_consumo"
                               name="cargo_consumo"
                               value="{{ old('cargo_consumo', 0) }}"
                               step="0.01"
                               min="0"
                               required>
                        @error('cargo_consumo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label for="otros_cargos" class="form-label">Otros Cargos</label>
                        <input type="number"
                               class="form-control @error('otros_cargos') is-invalid @enderror"
                               id="otros_cargos"
                               name="otros_cargos"
                               value="{{ old('otros_cargos', 0) }}"
                               step="0.01"
                               min="0">
                        @error('otros_cargos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="descuentos" class="form-label">Descuentos</label>
                        <input type="number"
                               class="form-control @error('descuentos') is-invalid @enderror"
                               id="descuentos"
                               name="descuentos"
                               value="{{ old('descuentos', 0) }}"
                               step="0.01"
                               min="0">
                        @error('descuentos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-8">
                        <label class="form-label">Total Calculado</label>
                        <div class="total-display" id="totalDisplay">$0</div>
                    </div>
                </div>
            </div>

            <!-- Observaciones -->
            <div class="form-group">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control @error('observaciones') is-invalid @enderror"
                          id="observaciones"
                          name="observaciones"
                          rows="3"
                          placeholder="Notas adicionales sobre la boleta...">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar Boleta
                </button>
                <a href="{{ route('boletas.index') }}" class="btn btn-secondary">
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

    .form-section {
        margin-bottom: 32px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--gray-200);
    }

    .form-section:last-of-type {
        border-bottom: none;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 16px;
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

    .total-display {
        padding: 12px 14px;
        background: var(--gray-50);
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
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

    .col-md-3 {
        grid-column: span 1;
    }

    .col-md-4 {
        grid-column: span 1;
    }

    .col-md-8 {
        grid-column: span 2;
    }

    .col-md-12 {
        grid-column: 1 / -1;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .col-md-3,
        .col-md-4,
        .col-md-8,
        .col-md-12 {
            grid-column: span 1;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    // Calcular total automáticamente
    function calcularTotal() {
        const cargoFijo = parseFloat(document.getElementById('cargo_fijo').value) || 0;
        const cargoConsumo = parseFloat(document.getElementById('cargo_consumo').value) || 0;
        const otrosCargos = parseFloat(document.getElementById('otros_cargos').value) || 0;
        const descuentos = parseFloat(document.getElementById('descuentos').value) || 0;

        const total = (cargoFijo + cargoConsumo + otrosCargos) - descuentos;

        document.getElementById('totalDisplay').textContent = '$' + total.toLocaleString('es-CL');
    }

    // Agregar event listeners
    document.addEventListener('DOMContentLoaded', function() {
        ['cargo_fijo', 'cargo_consumo', 'otros_cargos', 'descuentos'].forEach(id => {
            document.getElementById(id).addEventListener('input', calcularTotal);
        });

        calcularTotal();
    });
</script>
@endsection
