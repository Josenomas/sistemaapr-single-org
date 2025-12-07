@extends('layouts.app')

@section('title', 'Registrar Sueldo - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-money-check-alt"></i>
        Registrar Sueldo
    </h2>
    <a href="{{ route('sueldos.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Sueldo</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('sueldos.store') }}">
            @csrf

            <div class="form-section">
                <h4 class="section-title">Datos del Funcionario</h4>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="id_funcionario" class="required">Funcionario</label>
                        <select id="id_funcionario" name="id_funcionario" class="form-control @error('id_funcionario') error @enderror" required>
                            <option value="">Seleccione un funcionario</option>
                            @foreach($funcionarios as $funcionario)
                                <option value="{{ $funcionario->id }}" {{ old('id_funcionario') == $funcionario->id ? 'selected' : '' }}>
                                    {{ $funcionario->nombre_completo }} - {{ $funcionario->cargo }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_funcionario')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">Período y Montos</h4>

                <div class="form-row">
                    <div class="form-group">
                        <label for="periodo" class="required">Período (Año-Mes)</label>
                        <input type="text" id="periodo" name="periodo" class="form-control @error('periodo') error @enderror"
                               value="{{ old('periodo') }}" placeholder="2024-01" pattern="\d{4}-\d{2}" required>
                        <small class="form-help">Formato: YYYY-MM (ejemplo: 2024-01 para Enero 2024)</small>
                        @error('periodo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="fecha_pago" class="required">Fecha de Pago</label>
                        <input type="date" id="fecha_pago" name="fecha_pago"
                               class="form-control @error('fecha_pago') error @enderror"
                               value="{{ old('fecha_pago') }}" required>
                        @error('fecha_pago')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="sueldo_base" class="required">Sueldo Base ($)</label>
                        <input type="number" id="sueldo_base" name="sueldo_base"
                               class="form-control @error('sueldo_base') error @enderror"
                               value="{{ old('sueldo_base', 0) }}" min="0" step="0.01" required
                               onchange="calcularTotal()">
                        @error('sueldo_base')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="bonos">Bonos ($)</label>
                        <input type="number" id="bonos" name="bonos"
                               class="form-control @error('bonos') error @enderror"
                               value="{{ old('bonos', 0) }}" min="0" step="0.01"
                               onchange="calcularTotal()">
                        @error('bonos')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="descuentos">Descuentos ($)</label>
                        <input type="number" id="descuentos" name="descuentos"
                               class="form-control @error('descuentos') error @enderror"
                               value="{{ old('descuentos', 0) }}" min="0" step="0.01"
                               onchange="calcularTotal()">
                        @error('descuentos')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Total Líquido ($)</label>
                        <div class="total-liquido-display" id="total_liquido_display">
                            $0
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">Información de Pago</h4>

                <div class="form-row">
                    <div class="form-group">
                        <label for="metodo_pago" class="required">Método de Pago</label>
                        <select id="metodo_pago" name="metodo_pago" class="form-control @error('metodo_pago') error @enderror" required>
                            <option value="">Seleccione un método</option>
                            <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="transferencia" {{ old('metodo_pago', 'transferencia') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            <option value="cheque" {{ old('metodo_pago') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        </select>
                        @error('metodo_pago')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="estado" class="required">Estado</label>
                        <select id="estado" name="estado" class="form-control @error('estado') error @enderror" required>
                            <option value="pendiente" {{ old('estado', 'pendiente') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="pagado" {{ old('estado') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                        </select>
                        @error('estado')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="comprobante">Número de Comprobante</label>
                        <input type="text" id="comprobante" name="comprobante"
                               class="form-control @error('comprobante') error @enderror"
                               value="{{ old('comprobante') }}" placeholder="Ej: TRANS-123456, CHQ-001">
                        @error('comprobante')
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
                                  rows="4">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Registrar Sueldo
                </button>
                <a href="{{ route('sueldos.index') }}" class="btn btn-secondary">
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

    .form-help {
        font-size: 0.75rem;
        color: var(--gray-500);
        margin-top: -4px;
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

    .total-liquido-display {
        padding: 10px 14px;
        border: 2px solid var(--primary);
        border-radius: var(--radius);
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
        background: var(--primary-light);
        text-align: center;
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

@section('scripts')
<script>
    function calcularTotal() {
        const sueldoBase = parseFloat(document.getElementById('sueldo_base').value) || 0;
        const bonos = parseFloat(document.getElementById('bonos').value) || 0;
        const descuentos = parseFloat(document.getElementById('descuentos').value) || 0;

        const totalLiquido = sueldoBase + bonos - descuentos;

        document.getElementById('total_liquido_display').textContent =
            '$' + totalLiquido.toLocaleString('es-CL', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    // Calcular total al cargar la página
    document.addEventListener('DOMContentLoaded', calcularTotal);
</script>
@endsection
