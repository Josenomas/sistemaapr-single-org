@extends('layouts.app')

@section('title', 'Editar Boleta - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-edit"></i>
        Editar Boleta: {{ $boleta->numero_boleta }}
    </h2>
    <a href="{{ route('boletas.show', $boleta->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Boleta</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('boletas.update', $boleta->id) }}" method="POST">
            @csrf
            @method('PUT')

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
                                <option value="{{ $socio->id }}" {{ old('id_socio', $boleta->id_socio) == $socio->id ? 'selected' : '' }}>
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
                               value="{{ old('mes', $boleta->mes) }}"
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
                               value="{{ old('fecha_emision', $boleta->fecha_emision?->format('Y-m-d')) }}"
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
                               value="{{ old('fecha_vencimiento', $boleta->fecha_vencimiento?->format('Y-m-d')) }}"
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
                               value="{{ old('consumo_m3', $boleta->consumo_m3) }}"
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
                               value="{{ old('cargo_fijo', $boleta->cargo_fijo) }}"
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
                               value="{{ old('cargo_consumo', $boleta->cargo_consumo) }}"
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
                               value="{{ old('otros_cargos', $boleta->otros_cargos) }}"
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
                               value="{{ old('descuentos', $boleta->descuentos) }}"
                               step="0.01"
                               min="0">
                        @error('descuentos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label for="estado" class="form-label required">Estado</label>
                        <select class="form-control @error('estado') is-invalid @enderror"
                                id="estado"
                                name="estado"
                                required>
                            <option value="pendiente" {{ old('estado', $boleta->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="vencida" {{ old('estado', $boleta->estado) == 'vencida' ? 'selected' : '' }}>Vencida</option>
                            <option value="anulada" {{ old('estado', $boleta->estado) == 'anulada' ? 'selected' : '' }}>Anulada</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">No se puede cambiar a "Pagada" desde aquí</small>
                    </div>

                    <div class="form-group col-md-4">
                        <label class="form-label">Total Calculado</label>
                        <div class="total-display" id="totalDisplay">{{ $boleta->total_formateado }}</div>
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
                          placeholder="Notas adicionales sobre la boleta...">{{ old('observaciones', $boleta->observaciones) }}</textarea>
                @error('observaciones')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Datos Factura Electrónica (Opcional) -->
            <div class="form-section factura-section">
                <div class="section-header-factura" onclick="toggleFacturaFields()">
                    <h4 class="section-title">
                        <i class="fas fa-file-invoice" style="color: #3b82f6;"></i>
                        Datos para Factura Electrónica (Opcional)
                        <i class="fas fa-chevron-down toggle-icon" id="toggleIcon"></i>
                    </h4>
                    <p class="section-description">
                        Completar solo si desea emitir una <strong>Factura Electrónica (tipo 33)</strong> en lugar de Boleta.
                        Si deja vacío, se emitirá Boleta Electrónica (tipo 39).
                    </p>
                </div>

                <div class="factura-fields" id="facturaFields" style="display: {{ old('rut_receptor', $boleta->rut_receptor) ? 'block' : 'none' }};">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="rut_receptor" class="form-label">RUT Receptor</label>
                            <input type="text"
                                   class="form-control @error('rut_receptor') is-invalid @enderror"
                                   id="rut_receptor"
                                   name="rut_receptor"
                                   value="{{ old('rut_receptor', $boleta->rut_receptor) }}"
                                   placeholder="12.345.678-9"
                                   maxlength="12">
                            <small class="form-text">Si completa este campo, se emitirá Factura Electrónica</small>
                            @error('rut_receptor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="razon_social_receptor" class="form-label">Razón Social</label>
                            <input type="text"
                                   class="form-control @error('razon_social_receptor') is-invalid @enderror"
                                   id="razon_social_receptor"
                                   name="razon_social_receptor"
                                   value="{{ old('razon_social_receptor', $boleta->razon_social_receptor) }}"
                                   placeholder="Empresa S.A."
                                   maxlength="255">
                            @error('razon_social_receptor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="giro_receptor" class="form-label">Giro</label>
                            <input type="text"
                                   class="form-control @error('giro_receptor') is-invalid @enderror"
                                   id="giro_receptor"
                                   name="giro_receptor"
                                   value="{{ old('giro_receptor', $boleta->giro_receptor) }}"
                                   placeholder="Comercio al por menor"
                                   maxlength="255">
                            @error('giro_receptor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-5">
                            <label for="direccion_receptor" class="form-label">Dirección</label>
                            <input type="text"
                                   class="form-control @error('direccion_receptor') is-invalid @enderror"
                                   id="direccion_receptor"
                                   name="direccion_receptor"
                                   value="{{ old('direccion_receptor', $boleta->direccion_receptor) }}"
                                   placeholder="Calle 123"
                                   maxlength="255">
                            @error('direccion_receptor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-3">
                            <label for="comuna_receptor" class="form-label">Comuna</label>
                            <input type="text"
                                   class="form-control @error('comuna_receptor') is-invalid @enderror"
                                   id="comuna_receptor"
                                   name="comuna_receptor"
                                   value="{{ old('comuna_receptor', $boleta->comuna_receptor) }}"
                                   placeholder="Santiago"
                                   maxlength="100">
                            @error('comuna_receptor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Actualizar Boleta
                </button>
                <a href="{{ route('boletas.show', $boleta->id) }}" class="btn btn-secondary">
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

    .text-muted {
        color: var(--gray-500);
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

    /* Sección Factura Electrónica */
    .factura-section {
        background: #f8faff;
        border: 2px dashed #3b82f6;
        border-radius: var(--radius);
        padding: 20px;
        margin-bottom: 24px;
    }

    .section-header-factura {
        cursor: pointer;
        user-select: none;
    }

    .section-header-factura:hover {
        opacity: 0.8;
    }

    .section-header-factura .section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .toggle-icon {
        margin-left: auto;
        font-size: 0.9rem;
        transition: transform 0.3s ease;
        color: #3b82f6;
    }

    .toggle-icon.rotated {
        transform: rotate(180deg);
    }

    .section-description {
        color: var(--gray-600);
        font-size: 0.875rem;
        margin: 0;
        padding-left: 32px;
    }

    .factura-fields {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #3b82f680;
    }

    .form-text {
        display: block;
        margin-top: 4px;
        font-size: 0.8rem;
        color: #3b82f6;
        font-weight: 500;
    }

    .col-md-3 {
        grid-column: span 1;
    }

    .col-md-4 {
        grid-column: span 1;
    }

    .col-md-5 {
        grid-column: span 1;
    }

    .col-md-6 {
        grid-column: span 1;
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
        .col-md-5,
        .col-md-6,
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

    // Toggle para mostrar/ocultar campos de factura
    function toggleFacturaFields() {
        const fields = document.getElementById('facturaFields');
        const icon = document.getElementById('toggleIcon');

        if (fields.style.display === 'none') {
            fields.style.display = 'block';
            icon.classList.add('rotated');
        } else {
            fields.style.display = 'none';
            icon.classList.remove('rotated');
        }
    }

    // Formatear RUT mientras se escribe
    function formatearRut(input) {
        let valor = input.value.replace(/[^0-9kK]/g, '');

        if (valor.length > 1) {
            const dv = valor.slice(-1).toUpperCase();
            let numero = valor.slice(0, -1);

            // Agregar puntos
            numero = numero.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            input.value = numero + '-' + dv;
        } else {
            input.value = valor;
        }
    }

    // Validar RUT chileno
    function validarRut(rut) {
        rut = rut.replace(/[^0-9kK]/g, '');

        if (rut.length < 2) return false;

        const dv = rut.slice(-1).toUpperCase();
        const numero = rut.slice(0, -1);

        let suma = 0;
        let multiplicador = 2;

        for (let i = numero.length - 1; i >= 0; i--) {
            suma += parseInt(numero[i]) * multiplicador;
            multiplicador = multiplicador === 7 ? 2 : multiplicador + 1;
        }

        const resto = suma % 11;
        const dvCalculado = 11 - resto;

        let dvEsperado;
        if (dvCalculado === 11) dvEsperado = '0';
        else if (dvCalculado === 10) dvEsperado = 'K';
        else dvEsperado = dvCalculado.toString();

        return dv === dvEsperado;
    }

    // Agregar event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Calcular total
        ['cargo_fijo', 'cargo_consumo', 'otros_cargos', 'descuentos'].forEach(id => {
            document.getElementById(id).addEventListener('input', calcularTotal);
        });
        calcularTotal();

        // Formatear y validar RUT
        const rutInput = document.getElementById('rut_receptor');
        if (rutInput) {
            rutInput.addEventListener('input', function() {
                formatearRut(this);
            });

            rutInput.addEventListener('blur', function() {
                if (this.value.trim() !== '') {
                    const esValido = validarRut(this.value);
                    if (!esValido) {
                        this.classList.add('is-invalid');
                        if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.style.display = 'block';
                            feedback.textContent = 'RUT inválido';
                            this.parentNode.appendChild(feedback);
                        }
                    } else {
                        this.classList.remove('is-invalid');
                        const feedback = this.parentNode.querySelector('.invalid-feedback');
                        if (feedback && feedback.textContent === 'RUT inválido') {
                            feedback.remove();
                        }
                    }
                }
            });
        }

        // Auto-expandir si hay datos de factura o errores
        const hasFacturaData = {{ old('rut_receptor', $boleta->rut_receptor) ? 'true' : 'false' }};
        const hasErrors = {{ ($errors->has('rut_receptor') || $errors->has('razon_social_receptor') || $errors->has('giro_receptor') || $errors->has('direccion_receptor') || $errors->has('comuna_receptor')) ? 'true' : 'false' }};

        if (hasFacturaData || hasErrors) {
            const icon = document.getElementById('toggleIcon');
            if (icon) {
                icon.classList.add('rotated');
            }
        }
    });
</script>
@endsection
