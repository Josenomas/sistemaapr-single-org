@extends('layouts.app')

@section('title', 'Editar Rendicion Mensual - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-edit"></i>
        Editar Rendicion Mensual
    </h2>
    <a href="{{ route('rendiciones-mensuales.show', $rendicion->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<form action="{{ route('rendiciones-mensuales.update', $rendicion->id) }}" method="POST" id="formRendicion">
    @csrf
    @method('PUT')

    <div class="card mb-3">
        <div class="card-header">
            <h3><i class="fas fa-calendar"></i> Informacion del Periodo</h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="mes" class="form-label required">Mes</label>
                    <select name="mes" id="mes" class="form-control @error('mes') is-invalid @enderror" required>
                        <option value="">Seleccione mes</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('mes', $rendicion->mes) == $i ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                    @error('mes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="anio" class="form-label required">Anio</label>
                    <input type="number" name="anio" id="anio" class="form-control @error('anio') is-invalid @enderror"
                           value="{{ old('anio', $rendicion->anio) }}" min="2020" max="2100" required>
                    @error('anio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="saldo_anterior" class="form-label required">Saldo Anterior</label>
                    <input type="number" name="saldo_anterior" id="saldo_anterior" class="form-control @error('saldo_anterior') is-invalid @enderror"
                           value="{{ old('saldo_anterior', $rendicion->saldo_anterior) }}" step="0.01" min="0" required>
                    @error('saldo_anterior')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="form-help">Saldo final del mes anterior</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-success-light">
            <h3><i class="fas fa-arrow-down"></i> Ingresos</h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="ingresos_consumo_agua">Consumo de Agua</label>
                    <input type="number" name="ingresos_consumo_agua" id="ingresos_consumo_agua" class="form-control ingreso-input"
                           value="{{ old('ingresos_consumo_agua', $rendicion->ingresos_consumo_agua) }}" step="0.01" min="0">
                </div>
                <div class="form-group col-md-6">
                    <label for="ingresos_subsidios">Subsidios</label>
                    <input type="number" name="ingresos_subsidios" id="ingresos_subsidios" class="form-control ingreso-input"
                           value="{{ old('ingresos_subsidios', $rendicion->ingresos_subsidios) }}" step="0.01" min="0">
                </div>
                <div class="form-group col-md-6">
                    <label for="ingresos_aportes_socios">Aportes de Socios</label>
                    <input type="number" name="ingresos_aportes_socios" id="ingresos_aportes_socios" class="form-control ingreso-input"
                           value="{{ old('ingresos_aportes_socios', $rendicion->ingresos_aportes_socios) }}" step="0.01" min="0">
                </div>
                <div class="form-group col-md-6">
                    <label for="ingresos_multas">Multas</label>
                    <input type="number" name="ingresos_multas" id="ingresos_multas" class="form-control ingreso-input"
                           value="{{ old('ingresos_multas', $rendicion->ingresos_multas) }}" step="0.01" min="0">
                </div>
                <div class="form-group col-md-6">
                    <label for="ingresos_incorporaciones">Incorporaciones</label>
                    <input type="number" name="ingresos_incorporaciones" id="ingresos_incorporaciones" class="form-control ingreso-input"
                           value="{{ old('ingresos_incorporaciones', $rendicion->ingresos_incorporaciones) }}" step="0.01" min="0">
                </div>
                <div class="form-group col-md-6">
                    <label for="ingresos_otros">Otros Ingresos</label>
                    <input type="number" name="ingresos_otros" id="ingresos_otros" class="form-control ingreso-input"
                           value="{{ old('ingresos_otros', $rendicion->ingresos_otros) }}" step="0.01" min="0">
                </div>
            </div>
            <div class="total-box bg-success-light">
                <strong>Total Ingresos:</strong>
                <span id="totalIngresos">$0</span>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-danger-light">
            <h3><i class="fas fa-arrow-up"></i> Egresos</h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="egresos_energia_electrica">Energia Electrica</label>
                    <input type="number" name="egresos_energia_electrica" id="egresos_energia_electrica" class="form-control egreso-input"
                           value="{{ old('egresos_energia_electrica', $rendicion->egresos_energia_electrica) }}" step="0.01" min="0">
                </div>
                <div class="form-group col-md-6">
                    <label for="egresos_productos_quimicos">Productos Quimicos</label>
                    <input type="number" name="egresos_productos_quimicos" id="egresos_productos_quimicos" class="form-control egreso-input"
                           value="{{ old('egresos_productos_quimicos', $rendicion->egresos_productos_quimicos) }}" step="0.01" min="0">
                </div>
                <div class="form-group col-md-6">
                    <label for="egresos_reparaciones">Reparaciones</label>
                    <input type="number" name="egresos_reparaciones" id="egresos_reparaciones" class="form-control egreso-input"
                           value="{{ old('egresos_reparaciones', $rendicion->egresos_reparaciones) }}" step="0.01" min="0">
                </div>
                <div class="form-group col-md-6">
                    <label for="egresos_remuneraciones">Remuneraciones</label>
                    <input type="number" name="egresos_remuneraciones" id="egresos_remuneraciones" class="form-control egreso-input"
                           value="{{ old('egresos_remuneraciones', $rendicion->egresos_remuneraciones) }}" step="0.01" min="0">
                </div>
                <div class="form-group col-md-6">
                    <label for="egresos_gastos_administrativos">Gastos Administrativos</label>
                    <input type="number" name="egresos_gastos_administrativos" id="egresos_gastos_administrativos" class="form-control egreso-input"
                           value="{{ old('egresos_gastos_administrativos', $rendicion->egresos_gastos_administrativos) }}" step="0.01" min="0">
                </div>
                <div class="form-group col-md-6">
                    <label for="egresos_otros">Otros Egresos</label>
                    <input type="number" name="egresos_otros" id="egresos_otros" class="form-control egreso-input"
                           value="{{ old('egresos_otros', $rendicion->egresos_otros) }}" step="0.01" min="0">
                </div>
            </div>
            <div class="total-box bg-danger-light">
                <strong>Total Egresos:</strong>
                <span id="totalEgresos">$0</span>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="saldo-final-box" id="saldoFinalBox">
                <h3>Saldo Final</h3>
                <div class="saldo-calculation">
                    <div><strong>Saldo Anterior:</strong> <span id="displaySaldoAnterior">$0</span></div>
                    <div class="text-success"><strong>+ Ingresos:</strong> <span id="displayIngresos">$0</span></div>
                    <div class="text-danger"><strong>- Egresos:</strong> <span id="displayEgresos">$0</span></div>
                    <hr>
                    <div class="saldo-final"><strong>= Saldo Final:</strong> <span id="displaySaldoFinal">$0</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="form-group">
                <label for="observaciones">Observaciones</label>
                <textarea name="observaciones" id="observaciones" class="form-control" rows="3">{{ old('observaciones', $rendicion->observaciones) }}</textarea>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i>
            Actualizar Rendicion
        </button>
        <a href="{{ route('rendiciones-mensuales.show', $rendicion->id) }}" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            Cancelar
        </a>
    </div>
</form>
@endsection

@section('styles')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 1.75rem; font-weight: 700; color: var(--dark); display: flex; align-items: center; gap: 12px; margin: 0; }
    .page-title i { color: var(--primary); }
    .card { background: var(--white); border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--gray-200); margin-bottom: 20px; }
    .card-header { padding: 16px 24px; border-bottom: 1px solid var(--gray-200); background: var(--gray-50); }
    .card-header h3 { margin: 0; font-size: 1.125rem; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .card-body { padding: 24px; }
    .bg-success-light { background: #d1fae5 !important; }
    .bg-danger-light { background: #fee2e2 !important; }
    .form-row { display: grid; grid-template-columns: repeat(12, 1fr); gap: 20px; margin-bottom: 20px; }
    .form-group { display: flex; flex-direction: column; }
    .col-md-12 { grid-column: span 12; }
    .col-md-6 { grid-column: span 6; }
    .col-md-4 { grid-column: span 4; }
    .form-label { font-weight: 600; color: var(--gray-700); margin-bottom: 8px; font-size: 0.875rem; }
    .form-label.required::after { content: ' *'; color: #ef4444; }
    .form-control { padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: var(--radius); font-size: 0.875rem; transition: all 0.2s; }
    .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    .form-control.is-invalid { border-color: #ef4444; }
    .invalid-feedback { color: #ef4444; font-size: 0.75rem; margin-top: 4px; }
    .form-help { color: var(--gray-500); font-size: 0.75rem; margin-top: 4px; }
    .total-box { padding: 12px 16px; border-radius: var(--radius); margin-top: 16px; font-size: 1.125rem; display: flex; justify-content: space-between; align-items: center; }
    .saldo-final-box { padding: 20px; background: var(--gray-50); border-radius: var(--radius); border: 2px solid var(--primary); }
    .saldo-final-box h3 { margin: 0 0 16px 0; color: var(--primary); }
    .saldo-calculation { font-size: 1rem; }
    .saldo-calculation > div { padding: 8px 0; }
    .saldo-final { font-size: 1.25rem; color: var(--primary); padding: 12px 0; }
    .text-success { color: #059669; }
    .text-danger { color: #dc2626; }
    .form-actions { display: flex; gap: 12px; margin-top: 24px; }
    .btn { padding: 10px 20px; border-radius: var(--radius); border: none; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
    .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
    .btn-secondary { background: var(--gray-200); color: var(--gray-700); }
    .btn-secondary:hover { background: var(--gray-300); }
    @media (max-width: 768px) {
        .form-row { grid-template-columns: 1fr; }
        .col-md-12, .col-md-6, .col-md-4 { grid-column: span 1; }
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ingresoInputs = document.querySelectorAll('.ingreso-input');
    const egresoInputs = document.querySelectorAll('.egreso-input');
    const saldoAnteriorInput = document.getElementById('saldo_anterior');

    function formatCurrency(value) {
        return '$' + parseFloat(value).toLocaleString('es-CL', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }

    function calcularTotales() {
        let totalIngresos = 0;
        ingresoInputs.forEach(input => {
            totalIngresos += parseFloat(input.value) || 0;
        });

        let totalEgresos = 0;
        egresoInputs.forEach(input => {
            totalEgresos += parseFloat(input.value) || 0;
        });

        const saldoAnterior = parseFloat(saldoAnteriorInput.value) || 0;
        const saldoFinal = saldoAnterior + totalIngresos - totalEgresos;

        document.getElementById('totalIngresos').textContent = formatCurrency(totalIngresos);
        document.getElementById('totalEgresos').textContent = formatCurrency(totalEgresos);
        document.getElementById('displaySaldoAnterior').textContent = formatCurrency(saldoAnterior);
        document.getElementById('displayIngresos').textContent = formatCurrency(totalIngresos);
        document.getElementById('displayEgresos').textContent = formatCurrency(totalEgresos);
        document.getElementById('displaySaldoFinal').textContent = formatCurrency(saldoFinal);

        const saldoFinalSpan = document.getElementById('displaySaldoFinal');
        if (saldoFinal < 0) {
            saldoFinalSpan.style.color = '#dc2626';
            document.getElementById('saldoFinalBox').style.borderColor = '#dc2626';
        } else {
            saldoFinalSpan.style.color = '#059669';
            document.getElementById('saldoFinalBox').style.borderColor = 'var(--primary)';
        }
    }

    ingresoInputs.forEach(input => input.addEventListener('input', calcularTotales));
    egresoInputs.forEach(input => input.addEventListener('input', calcularTotales));
    saldoAnteriorInput.addEventListener('input', calcularTotales);

    calcularTotales();
});
</script>
@endsection
