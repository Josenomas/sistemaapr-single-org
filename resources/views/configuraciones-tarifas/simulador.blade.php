@extends('layouts.app')

@section('title', 'Simulador de Tarifas')

@section('styles')
<style>
    .simulador-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .simulador-title {
        font-size: 1.75rem;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        margin: 0;
    }

    .simulador-title i {
        color: var(--primary);
    }

    .simulador-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    @media (max-width: 968px) {
        .simulador-container {
            grid-template-columns: 1fr;
        }
    }

    .calculator-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
        padding: 32px;
        border: 2px solid var(--primary-light);
    }

    .calculator-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .calculator-title i {
        color: var(--primary);
        font-size: 1.5rem;
    }

    .input-group {
        margin-bottom: 24px;
    }

    .input-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--dark);
        font-weight: 600;
        font-size: 0.875rem;
    }

    .input-wrapper {
        position: relative;
    }

    .input-wrapper i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary);
        font-size: 1.25rem;
    }

    .consumo-input {
        width: 100%;
        padding: 16px 16px 16px 48px;
        border: 2px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 1.5rem;
        font-weight: 600;
        transition: all 0.3s;
        text-align: center;
    }

    .consumo-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .btn-calcular {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        border-radius: var(--radius);
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .btn-calcular:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .btn-calcular:active {
        transform: translateY(0);
    }

    .results-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
        padding: 32px;
        border: 2px solid var(--success);
    }

    .results-hidden {
        display: none;
    }

    .result-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid var(--gray-100);
    }

    .result-item:last-child {
        border-bottom: none;
    }

    .result-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .result-label i {
        color: var(--primary);
    }

    .result-value {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--dark);
    }

    .result-total {
        background: linear-gradient(135deg, var(--success), #059669);
        color: white;
        padding: 24px;
        border-radius: var(--radius);
        margin-top: 24px;
        text-align: center;
    }

    .result-total-label {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-bottom: 8px;
    }

    .result-total-value {
        font-size: 2.5rem;
        font-weight: 700;
    }

    .tramos-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 24px;
        margin-top: 24px;
    }

    .tramos-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tramo-item {
        display: flex;
        justify-content: space-between;
        padding: 12px;
        background: var(--gray-50);
        border-radius: var(--radius);
        margin-bottom: 8px;
        border-left: 4px solid var(--primary);
    }

    .tramo-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .tramo-nombre {
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--dark);
    }

    .tramo-rango {
        font-size: 0.75rem;
        color: var(--gray-600);
    }

    .tramo-precio {
        font-size: 1rem;
        font-weight: 700;
        color: var(--primary);
        align-self: center;
    }

    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .alert-info {
        background: #dbeafe;
        color: #1e40af;
        padding: 16px;
        border-radius: var(--radius);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.875rem;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        padding: 16px;
        border-radius: var(--radius);
        margin-top: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.875rem;
        display: none;
    }
</style>
@endsection

@section('content')
<div class="simulador-wrapper">
    <div class="simulador-header">
        <h2 class="simulador-title">
            <i class="fas fa-calculator"></i>
            Simulador de Tarifas
        </h2>
        <a href="{{ route('configuraciones-tarifas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>

    <div class="alert-info">
        <i class="fas fa-info-circle"></i>
        <span>Ingresa los metros cúbicos de agua consumidos para calcular el monto total a pagar según las tarifas configuradas.</span>
    </div>

    <div class="simulador-container">
        <!-- Calculadora -->
        <div>
            <div class="calculator-card">
                <div class="calculator-title">
                    <i class="fas fa-tint"></i>
                    Ingresa el Consumo
                </div>

                <form id="calculatorForm">
                    <div class="input-group">
                        <label for="tipo_cliente">Tipo de Cliente</label>
                        <select
                            id="tipo_cliente"
                            name="tipo_cliente"
                            class="consumo-input"
                            required
                            style="padding: 16px 16px 16px 48px;"
                        >
                            <option value="">Seleccione tipo</option>
                            <option value="residencial">Residencial</option>
                            <option value="comercial">Comercial</option>
                            <option value="industrial">Industrial</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="consumo">Metros Cúbicos (m³)</label>
                        <div class="input-wrapper">
                            <i class="fas fa-tint"></i>
                            <input
                                type="number"
                                id="consumo"
                                name="consumo"
                                class="consumo-input"
                                placeholder="0.00"
                                step="0.01"
                                min="0"
                                required
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn-calcular">
                        <i class="fas fa-calculator"></i>
                        Calcular Tarifa
                    </button>

                    <div class="alert-error" id="errorAlert">
                        <i class="fas fa-exclamation-circle"></i>
                        <span id="errorMessage"></span>
                    </div>
                </form>
            </div>

            <!-- Tramos Tarifarios -->
            <div class="tramos-card">
                <div class="tramos-title">
                    <i class="fas fa-list"></i>
                    Tramos Tarifarios Vigentes
                </div>
                @foreach($tarifas as $tarifa)
                    <div class="tramo-item">
                        <div class="tramo-info">
                            <div class="tramo-nombre">{{ $tarifa->nombre }}</div>
                            <div class="tramo-rango">{{ $tarifa->rango_descripcion }}</div>
                        </div>
                        <div class="tramo-precio">${{ number_format($tarifa->monto, 0, ',', '.') }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Resultados -->
        <div class="results-card results-hidden" id="resultsCard">
            <div class="calculator-title">
                <i class="fas fa-receipt"></i>
                Detalle del Cálculo
            </div>

            <div class="result-item">
                <div class="result-label">
                    <i class="fas fa-tint"></i>
                    Consumo
                </div>
                <div class="result-value" id="resultConsumo">-</div>
            </div>

            <div class="result-item">
                <div class="result-label">
                    <i class="fas fa-layer-group"></i>
                    Tramo Aplicado
                </div>
                <div class="result-value" id="resultTramo">-</div>
            </div>

            <div class="result-item">
                <div class="result-label">
                    <i class="fas fa-chart-bar"></i>
                    Rango
                </div>
                <div class="result-value" id="resultRango">-</div>
            </div>

            <!-- Desglose por Tramos -->
            <div id="desgloseTramos" style="display: none; margin: 20px 0; padding: 15px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #3b82f6;">
                <div style="font-weight: 600; color: #1e40af; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-layer-group"></i>
                    Desglose por Tramos
                </div>
                <div id="desgloseLista"></div>
            </div>

            <div class="result-item">
                <div class="result-label">
                    <i class="fas fa-dollar-sign"></i>
                    Monto por Consumo
                </div>
                <div class="result-value" id="resultMontoConsumo">-</div>
            </div>

            <div class="result-item">
                <div class="result-label">
                    <i class="fas fa-plus-circle"></i>
                    Cargo Fijo
                </div>
                <div class="result-value" id="resultCargoFijo">-</div>
            </div>

            <div class="result-item">
                <div class="result-label">
                    <i class="fas fa-equals"></i>
                    Subtotal
                </div>
                <div class="result-value" id="resultSubtotal">-</div>
            </div>

            <div class="result-item">
                <div class="result-label">
                    <i class="fas fa-percentage"></i>
                    IVA (<span id="resultPorcentajeIva">0</span>%)
                </div>
                <div class="result-value" id="resultIva">-</div>
            </div>

            <div class="result-total">
                <div class="result-total-label">TOTAL A PAGAR</div>
                <div class="result-total-value" id="resultTotal">$0</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('calculatorForm');
    const resultsCard = document.getElementById('resultsCard');
    const errorAlert = document.getElementById('errorAlert');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const tipoCliente = document.getElementById('tipo_cliente').value;
        const consumo = document.getElementById('consumo').value;

        if (!tipoCliente) {
            showError('Por favor selecciona un tipo de cliente.');
            return;
        }

        if (!consumo || consumo < 0) {
            showError('Por favor ingresa un consumo válido.');
            return;
        }

        calcularTarifa(tipoCliente, consumo);
    });

    function calcularTarifa(tipoCliente, consumo) {
        // Mostrar loading
        form.classList.add('loading');
        errorAlert.style.display = 'none';

        // Realizar petición AJAX
        fetch('{{ route('configuraciones-tarifas.calcular') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tipo_cliente: tipoCliente,
                consumo: consumo
            })
        })
        .then(response => response.json())
        .then(data => {
            form.classList.remove('loading');

            if (data.success) {
                mostrarResultados(data.data);
            } else {
                showError(data.message || 'Error al calcular la tarifa.');
            }
        })
        .catch(error => {
            form.classList.remove('loading');
            showError('Error de conexión. Por favor intenta nuevamente.');
            console.error('Error:', error);
        });
    }

    function mostrarResultados(data) {
        document.getElementById('resultConsumo').textContent = data.consumo + ' m³';
        document.getElementById('resultTramo').textContent = data.nombre_tarifa + ' - ' + data.tramo;
        document.getElementById('resultRango').textContent = data.rango + ' (' + data.tipo_cliente + ')';
        document.getElementById('resultMontoConsumo').textContent = data.cargo_consumo_formateado;
        document.getElementById('resultCargoFijo').textContent = data.cargo_fijo_formateado;
        document.getElementById('resultSubtotal').textContent = data.monto_base_formateado;
        document.getElementById('resultPorcentajeIva').textContent = data.porcentaje_iva;
        document.getElementById('resultIva').textContent = data.monto_iva_formateado;
        document.getElementById('resultTotal').textContent = data.total_formateado;

        // Mostrar desglose por tramos si existe
        const desgloseContainer = document.getElementById('desgloseTramos');
        const desgloseLista = document.getElementById('desgloseLista');

        if (data.desglose_tramos && data.desglose_tramos.length > 0) {
            let html = '';
            data.desglose_tramos.forEach(function(tramo) {
                html += `
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #374151;">${tramo.nombre}</div>
                            <div style="font-size: 0.813rem; color: #6b7280;">
                                ${tramo.consumo_m3.toFixed(2)} m³ × $${tramo.precio_unitario.toLocaleString('es-CL')}
                            </div>
                        </div>
                        <div style="font-weight: 600; color: #1e40af; text-align: right;">
                            ${tramo.monto_formateado}
                        </div>
                    </div>
                `;
            });
            desgloseLista.innerHTML = html;
            desgloseContainer.style.display = 'block';
        } else {
            desgloseContainer.style.display = 'none';
        }

        resultsCard.classList.remove('results-hidden');

        // Smooth scroll to results
        resultsCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function showError(message) {
        document.getElementById('errorMessage').textContent = message;
        errorAlert.style.display = 'flex';
        resultsCard.classList.add('results-hidden');
    }
});
</script>
@endsection
