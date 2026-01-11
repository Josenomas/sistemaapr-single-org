@extends('layouts.app')

@section('title', 'Registrar Pago - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-plus-circle"></i>
        Registrar Nuevo Pago
    </h2>
    <a href="{{ route('pagos.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-file-invoice-dollar"></i>
            Información del Pago
        </h3>
    </div>
    <div class="card-body">
        <form action="{{ route('pagos.store') }}" method="POST" id="formPago">
            @csrf

            <div class="form-row">
                <!-- N° Recibo -->
                <div class="form-group col-md-4">
                    <label for="numero_recibo" class="form-label">N° Recibo</label>
                    <input type="text"
                           class="form-control"
                           id="numero_recibo"
                           value="{{ App\Models\Pago::generarNumeroRecibo() }}"
                           disabled
                           style="background: #f3f4f6; color: #6b7280;">
                    <small class="form-help">Se generará automáticamente</small>
                </div>

                <!-- Fecha de Pago -->
                <div class="form-group col-md-4">
                    <label for="fecha_pago" class="form-label required">Fecha de Pago</label>
                    <input type="date"
                           class="form-control @error('fecha_pago') is-invalid @enderror"
                           id="fecha_pago"
                           name="fecha_pago"
                           value="{{ old('fecha_pago', date('Y-m-d')) }}"
                           required>
                    @error('fecha_pago')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Búsqueda por RUT -->
            <div class="search-box">
                <h4><i class="fas fa-search"></i> Búsqueda Rápida por RUT</h4>
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="buscar_rut" class="form-label">RUT del Socio</label>
                        <input type="text"
                               class="form-control"
                               id="buscar_rut"
                               placeholder="Ej: 12345678-9"
                               maxlength="12">
                        <small class="form-help">Ingrese el RUT para buscar las boletas pendientes del socio</small>
                    </div>
                    <div class="form-group col-md-4" style="display: flex; align-items: flex-end;">
                        <button type="button" class="btn btn-primary" id="btnBuscarRut" style="width: 100%;">
                            <i class="fas fa-search"></i>
                            Buscar
                        </button>
                    </div>
                </div>
                <div id="resultadoBusqueda" style="display: none;"></div>
            </div>

            <div class="form-row">
                <!-- Boleta a Pagar -->
                <div class="form-group col-md-12">
                    <label for="id_boleta" class="form-label required">Boleta a Pagar</label>
                    <select class="form-control @error('id_boleta') is-invalid @enderror"
                            id="id_boleta"
                            name="id_boleta"
                            required>
                        <option value="">Seleccione una boleta</option>
                        @if($boleta)
                            @php
                                $saldoPendiente = $boleta->saldo_pendiente ?? $boleta->total;
                                $saldoFormateado = '$' . number_format($saldoPendiente, 0, ',', '.');
                                $totalPagado = $boleta->total_pagado ?? 0;
                            @endphp
                            <option value="{{ $boleta->id }}"
                                    selected
                                    data-socio="{{ $boleta->socio->nombre_completo }}"
                                    data-periodo="{{ $boleta->mes_texto }}"
                                    data-total="{{ $saldoPendiente }}"
                                    data-total-fmt="{{ $saldoFormateado }}"
                                    data-estado="{{ $boleta->estado_texto }}"
                                    data-vencimiento="{{ $boleta->fecha_vencimiento_formateada }}"
                                    data-total-pagado="{{ $totalPagado }}">
                                {{ $boleta->numero_boleta }} - {{ $boleta->socio->nombre_completo }} - {{ $boleta->mes_texto }} - {{ $saldoFormateado }}
                                @if($totalPagado > 0)
                                    (Abonado: ${{ number_format($totalPagado, 0, ',', '.') }})
                                @endif
                                @if($boleta->estado == 'vencida')
                                    (VENCIDA)
                                @endif
                            </option>
                        @else
                            @foreach($boletas as $b)
                                @php
                                    $saldoPendiente = $b->saldo_pendiente ?? $b->total;
                                    $saldoFormateado = '$' . number_format($saldoPendiente, 0, ',', '.');
                                    $totalPagado = $b->total_pagado ?? 0;
                                @endphp
                                <option value="{{ $b->id }}"
                                        data-socio="{{ $b->socio->nombre_completo }}"
                                        data-periodo="{{ $b->mes_texto }}"
                                        data-total="{{ $saldoPendiente }}"
                                        data-total-fmt="{{ $saldoFormateado }}"
                                        data-estado="{{ $b->estado_texto }}"
                                        data-vencimiento="{{ $b->fecha_vencimiento_formateada }}"
                                        data-total-pagado="{{ $totalPagado }}">
                                    {{ $b->numero_boleta }} - {{ $b->socio->nombre_completo }} - {{ $b->mes_texto }} - {{ $saldoFormateado }}
                                    @if($totalPagado > 0)
                                        (Abonado: ${{ number_format($totalPagado, 0, ',', '.') }})
                                    @endif
                                    @if($b->estado == 'vencida')
                                        (VENCIDA)
                                    @endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('id_boleta')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-help">Seleccione la boleta que desea pagar</small>
                </div>
            </div>

            <!-- Información de la boleta seleccionada -->
            <div id="infoBoleta" style="display: {{ $boleta ? 'block' : 'none' }};" class="info-box">
                <h4><i class="fas fa-info-circle"></i> Información de la Boleta</h4>
                <div class="info-grid-box">
                    <div><strong>Socio:</strong> <span id="info_socio">{{ $boleta->socio->nombre_completo ?? '' }}</span></div>
                    <div><strong>Período:</strong> <span id="info_periodo">{{ $boleta->mes_texto ?? '' }}</span></div>
                    <div><strong>Total:</strong> <span id="info_total">
                        @if($boleta)
                            @php
                                $saldoPendiente = $boleta->saldo_pendiente ?? $boleta->total;
                                $totalPagado = $boleta->total_pagado ?? 0;
                            @endphp
                            ${{ number_format($saldoPendiente, 0, ',', '.') }}
                            @if($totalPagado > 0)
                                <small style="display: block; color: #6b7280;">(Abonado: ${{ number_format($totalPagado, 0, ',', '.') }})</small>
                            @endif
                        @endif
                    </span></div>
                    <div><strong>Estado:</strong> <span id="info_estado">{{ $boleta->estado_texto ?? '' }}</span></div>
                    <div><strong>Vencimiento:</strong> <span id="info_vencimiento">{{ $boleta->fecha_vencimiento_formateada ?? '' }}</span></div>
                </div>
            </div>

            <div class="form-row">
                <!-- Tipo de Pago -->
                <div class="form-group col-md-12">
                    <label for="tipo_pago" class="form-label required">Tipo de Pago</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="tipo_pago" value="completo" checked id="tipo_completo">
                            <span class="radio-custom"></span>
                            <div class="radio-text">
                                <strong>Pago Completo</strong>
                                <small>Pagar el saldo pendiente completo</small>
                            </div>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="tipo_pago" value="parcial" id="tipo_parcial">
                            <span class="radio-custom"></span>
                            <div class="radio-text">
                                <strong>Pago Parcial (Abono)</strong>
                                <small>Pagar solo una parte del saldo pendiente</small>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- Monto Pagado -->
                <div class="form-group col-md-6">
                    <label for="monto_pagado" class="form-label required">Monto Pagado</label>
                    <input type="number"
                           class="form-control @error('monto_pagado') is-invalid @enderror"
                           id="monto_pagado"
                           name="monto_pagado"
                           value="{{ old('monto_pagado', $boleta->total ?? '') }}"
                           step="0.01"
                           min="0"
                           required>
                    @error('monto_pagado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-help" id="ayuda-monto">Ingrese el monto a pagar</small>
                </div>

                <!-- Método de Pago -->
                <div class="form-group col-md-6">
                    <label for="metodo_pago" class="form-label required">Método de Pago</label>
                    <select class="form-control @error('metodo_pago') is-invalid @enderror"
                            id="metodo_pago"
                            name="metodo_pago"
                            required>
                        <option value="">Seleccione</option>
                        <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                        <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                        <option value="cheque" {{ old('metodo_pago') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="debito" {{ old('metodo_pago') == 'debito' ? 'selected' : '' }}>Débito</option>
                        <option value="credito" {{ old('metodo_pago') == 'credito' ? 'selected' : '' }}>Crédito</option>
                    </select>
                    @error('metodo_pago')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Sección Flow para débito/crédito -->
            <div id="seccionFlow" style="display: none;" class="flow-section">
                <div class="alert alert-info">
                    <i class="fas fa-credit-card"></i>
                    <div>
                        <strong>Pago con Tarjeta (Flow)</strong>
                        <p>Genera un link de pago seguro para que el socio pueda pagar con tarjeta de débito o crédito a través de la plataforma Flow.</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="email_flow" class="form-label required">Email del Socio</label>
                        <input type="email"
                               class="form-control"
                               id="email_flow"
                               placeholder="correo@ejemplo.com">
                        <small class="form-help">Se enviará el link de pago a este correo</small>
                    </div>
                    <div class="form-group col-md-4" style="display: flex; align-items: flex-end;">
                        <button type="button" class="btn btn-success" id="btnGenerarLinkFlow" style="width: 100%;">
                            <i class="fas fa-link"></i>
                            Generar Link de Pago
                        </button>
                    </div>
                </div>

                <div id="linkGenerado" style="display: none;" class="link-box">
                    <h4><i class="fas fa-check-circle"></i> Link Generado Exitosamente</h4>
                    <p>Comparte este link con el socio para que realice el pago:</p>
                    <div class="link-display">
                        <input type="text" id="linkPago" class="form-control" readonly>
                        <button type="button" class="btn btn-secondary" onclick="copiarLink()">
                            <i class="fas fa-copy"></i> Copiar
                        </button>
                        <a href="#" id="abrirLink" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt"></i> Abrir
                        </a>
                    </div>
                </div>
            </div>

            <div class="form-row" id="seccionComprobante">
                <!-- N° Comprobante -->
                <div class="form-group col-md-12">
                    <label for="numero_comprobante" class="form-label">N° Comprobante</label>
                    <input type="text"
                           class="form-control @error('numero_comprobante') is-invalid @enderror"
                           id="numero_comprobante"
                           name="numero_comprobante"
                           value="{{ old('numero_comprobante') }}"
                           maxlength="100"
                           placeholder="Número de transferencia, cheque, etc.">
                    @error('numero_comprobante')
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
                              rows="3">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar Pago
                </button>
                <a href="{{ route('pagos.index') }}" class="btn btn-secondary">
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
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-title i {
        color: var(--primary);
    }

    .card-body {
        padding: 24px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .col-md-12 { grid-column: span 12; }
    .col-md-6 { grid-column: span 6; }
    .col-md-4 { grid-column: span 4; }
    .col-md-3 { grid-column: span 3; }

    .form-label {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
        font-size: 0.875rem;
    }

    .form-label.required::after {
        content: ' *';
        color: #ef4444;
    }

    .form-control {
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
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
        border-color: #ef4444;
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 4px;
    }

    .form-help {
        color: var(--gray-500);
        font-size: 0.75rem;
        margin-top: 4px;
    }

    .search-box {
        background: #f0fdf4;
        border: 2px solid #10b981;
        border-radius: var(--radius);
        padding: 16px;
        margin-bottom: 20px;
    }

    .search-box h4 {
        margin: 0 0 12px 0;
        color: #059669;
        font-size: 1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-box {
        background: #eff6ff;
        border: 1px solid #3b82f6;
        border-radius: var(--radius);
        padding: 16px;
        margin-bottom: 20px;
    }

    .info-box h4 {
        margin: 0 0 12px 0;
        color: #1e40af;
        font-size: 1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-grid-box {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        font-size: 0.875rem;
    }

    .info-grid-box div {
        color: var(--dark);
    }

    .info-grid-box strong {
        color: var(--gray-600);
    }

    .radio-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 12px;
    }

    .radio-option {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        border: 2px solid var(--gray-300);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
        background: var(--white);
    }

    .radio-option:hover {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .radio-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .radio-option input[type="radio"]:checked ~ .radio-custom {
        border-color: var(--primary);
        background: var(--primary);
    }

    .radio-option input[type="radio"]:checked ~ .radio-custom::after {
        display: block;
    }

    .radio-option input[type="radio"]:checked ~ .radio-text strong {
        color: var(--primary);
    }

    .radio-custom {
        width: 20px;
        height: 20px;
        border: 2px solid var(--gray-400);
        border-radius: 50%;
        transition: all 0.2s;
        position: relative;
        flex-shrink: 0;
    }

    .radio-custom::after {
        content: '';
        position: absolute;
        display: none;
        top: 3px;
        left: 3px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: white;
    }

    .radio-text {
        flex: 1;
    }

    .radio-text strong {
        display: block;
        font-size: 0.95rem;
        color: var(--dark);
        margin-bottom: 2px;
    }

    .radio-text small {
        color: var(--gray-600);
        font-size: 0.75rem;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
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

    .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .flow-section {
        background: #f0fdf4;
        border: 2px dashed #10b981;
        border-radius: var(--radius);
        padding: 20px;
        margin-bottom: 20px;
    }

    .alert {
        padding: 16px;
        border-radius: var(--radius);
        margin-bottom: 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .alert-info {
        background: #eff6ff;
        border: 1px solid #3b82f6;
        color: #1e40af;
    }

    .alert i {
        font-size: 1.5rem;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .alert div {
        flex: 1;
    }

    .alert strong {
        display: block;
        margin-bottom: 4px;
        font-size: 1rem;
    }

    .alert p {
        margin: 0;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .link-box {
        background: white;
        border: 1px solid #10b981;
        border-radius: var(--radius);
        padding: 16px;
        margin-top: 16px;
    }

    .link-box h4 {
        margin: 0 0 8px 0;
        color: #059669;
        font-size: 1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .link-box p {
        margin: 0 0 12px 0;
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .link-display {
        display: flex;
        gap: 8px;
    }

    .link-display input {
        flex: 1;
        font-family: monospace;
        font-size: 0.875rem;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .col-md-12,
        .col-md-6,
        .col-md-4,
        .col-md-3 {
            grid-column: span 1;
        }
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-descargar PDF si viene de registro exitoso
    @if(session('download_recibo'))
    window.open('{{ route("pagos.descargar-recibo", session("download_recibo")) }}', '_blank');
    @endif

    const selectBoleta = document.getElementById('id_boleta');
    const infoBoleta = document.getElementById('infoBoleta');
    const montoInput = document.getElementById('monto_pagado');
    const tipoCompleto = document.getElementById('tipo_completo');
    const tipoParcial = document.getElementById('tipo_parcial');
    const ayudaMonto = document.getElementById('ayuda-monto');
    const metodoPago = document.getElementById('metodo_pago');
    const seccionFlow = document.getElementById('seccionFlow');
    const seccionComprobante = document.getElementById('seccionComprobante');
    const btnGenerarLinkFlow = document.getElementById('btnGenerarLinkFlow');
    const emailFlow = document.getElementById('email_flow');
    const linkGenerado = document.getElementById('linkGenerado');
    const formPago = document.getElementById('formPago');
    const buscarRutInput = document.getElementById('buscar_rut');
    const btnBuscarRut = document.getElementById('btnBuscarRut');
    const resultadoBusqueda = document.getElementById('resultadoBusqueda');

    let totalBoleta = 0;
    let boletaSeleccionada = null;

    // Búsqueda por RUT
    btnBuscarRut.addEventListener('click', async function() {
        const rut = buscarRutInput.value.trim();

        if (!rut) {
            alert('Por favor ingrese un RUT');
            buscarRutInput.focus();
            return;
        }

        // Deshabilitar botón
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Buscando...';

        try {
            const response = await fetch('{{ route("pagos.buscarPorRut") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ rut: rut })
            });

            const data = await response.json();

            if (data.success) {
                // Limpiar select de boletas
                selectBoleta.innerHTML = '<option value="">Seleccione una boleta</option>';

                if (data.boletas.length > 0) {
                    // Agregar boletas del socio
                    data.boletas.forEach(boleta => {
                        const option = document.createElement('option');
                        option.value = boleta.id;

                        // Usar saldo pendiente si existe, sino usar total
                        const saldoPendiente = boleta.saldo_pendiente !== undefined ? boleta.saldo_pendiente : boleta.total;
                        const saldoFormateado = new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP' }).format(saldoPendiente);

                        option.setAttribute('data-socio', boleta.socio_nombre);
                        option.setAttribute('data-periodo', boleta.mes_texto);
                        option.setAttribute('data-total', saldoPendiente);
                        option.setAttribute('data-total-fmt', saldoFormateado);
                        option.setAttribute('data-estado', boleta.estado_texto);
                        option.setAttribute('data-vencimiento', boleta.fecha_vencimiento_formateada);
                        option.setAttribute('data-total-pagado', boleta.total_pagado || 0);

                        let textoEstado = boleta.estado === 'vencida' ? ' (VENCIDA)' : '';
                        let textoParcial = boleta.total_pagado > 0 ? ' (Abonado: ' + new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP' }).format(boleta.total_pagado) + ')' : '';
                        option.textContent = `${boleta.numero_boleta} - ${boleta.socio_nombre} - ${boleta.mes_texto} - ${saldoFormateado}${textoParcial}${textoEstado}`;

                        selectBoleta.appendChild(option);
                    });

                    // Mostrar resultado
                    resultadoBusqueda.style.display = 'block';
                    resultadoBusqueda.innerHTML = `
                        <div class="alert" style="background: #d1fae5; border: 1px solid #10b981; color: #065f46; margin-top: 12px;">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Socio encontrado: ${data.socio.nombre_completo}</strong>
                                <p style="margin: 4px 0 0 0;">Se encontraron ${data.boletas.length} boleta(s) pendiente(s). Seleccione una para continuar.</p>
                            </div>
                        </div>
                    `;

                    // Auto-seleccionar primera boleta si solo hay una
                    if (data.boletas.length === 1) {
                        selectBoleta.selectedIndex = 1;
                        selectBoleta.dispatchEvent(new Event('change'));
                    }
                } else {
                    // No hay boletas pendientes
                    resultadoBusqueda.style.display = 'block';
                    resultadoBusqueda.innerHTML = `
                        <div class="alert" style="background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; margin-top: 12px;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Socio encontrado: ${data.socio.nombre_completo}</strong>
                                <p style="margin: 4px 0 0 0;">Este socio no tiene boletas pendientes de pago.</p>
                            </div>
                        </div>
                    `;
                }
            } else {
                // No se encontró el socio
                resultadoBusqueda.style.display = 'block';
                resultadoBusqueda.innerHTML = `
                    <div class="alert" style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; margin-top: 12px;">
                        <i class="fas fa-times-circle"></i>
                        <div>
                            <strong>Socio no encontrado</strong>
                            <p style="margin: 4px 0 0 0;">${data.message}</p>
                        </div>
                    </div>
                `;

                // Limpiar select
                selectBoleta.innerHTML = '<option value="">Seleccione una boleta</option>';
            }
        } catch (error) {
            alert('Error al buscar el socio: ' + error.message);
            resultadoBusqueda.style.display = 'none';
        } finally {
            // Rehabilitar botón
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-search"></i> Buscar';
        }
    });

    // Buscar al presionar Enter en el campo RUT
    buscarRutInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            btnBuscarRut.click();
        }
    });

    // Manejar cambio de boleta
    selectBoleta.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        boletaSeleccionada = this.value;

        if (this.value) {
            const socio = option.getAttribute('data-socio');
            const periodo = option.getAttribute('data-periodo');
            const totalFmt = option.getAttribute('data-total-fmt');
            const totalNum = option.getAttribute('data-total');
            const estado = option.getAttribute('data-estado');
            const vencimiento = option.getAttribute('data-vencimiento');

            if (socio) {
                document.getElementById('info_socio').textContent = socio;
                document.getElementById('info_periodo').textContent = periodo;
                document.getElementById('info_total').textContent = totalFmt;
                document.getElementById('info_estado').textContent = estado;
                document.getElementById('info_vencimiento').textContent = vencimiento;

                totalBoleta = parseFloat(totalNum);

                // Autocompletar monto si está en modo completo
                if (tipoCompleto.checked) {
                    montoInput.value = totalNum;
                    montoInput.setAttribute('readonly', 'readonly');
                    ayudaMonto.textContent = 'Monto total de la boleta';
                } else {
                    montoInput.removeAttribute('readonly');
                    montoInput.setAttribute('max', totalNum);
                    ayudaMonto.textContent = `Ingrese un monto entre $1 y ${totalFmt}`;
                }

                infoBoleta.style.display = 'block';
            }
        } else {
            infoBoleta.style.display = 'none';
            totalBoleta = 0;
            boletaSeleccionada = null;
        }
    });

    // Manejar cambio de tipo de pago
    tipoCompleto.addEventListener('change', function() {
        if (this.checked && totalBoleta > 0) {
            montoInput.value = totalBoleta;
            montoInput.setAttribute('readonly', 'readonly');
            ayudaMonto.textContent = 'Monto total de la boleta';
        }
    });

    tipoParcial.addEventListener('change', function() {
        if (this.checked) {
            montoInput.removeAttribute('readonly');
            montoInput.value = '';
            if (totalBoleta > 0) {
                montoInput.setAttribute('max', totalBoleta);
                const totalFmt = '$' + totalBoleta.toLocaleString('es-CL');
                ayudaMonto.textContent = `Ingrese un monto entre $1 y ${totalFmt}`;
            } else {
                ayudaMonto.textContent = 'Ingrese el monto a pagar';
            }
            montoInput.focus();
        }
    });

    // Validar que el monto parcial no exceda el total
    montoInput.addEventListener('input', function() {
        if (tipoParcial.checked && totalBoleta > 0) {
            const monto = parseFloat(this.value);
            if (monto > totalBoleta) {
                this.value = totalBoleta;
                ayudaMonto.textContent = 'El monto no puede exceder el total de la boleta';
                ayudaMonto.style.color = '#ef4444';
            } else {
                const totalFmt = '$' + totalBoleta.toLocaleString('es-CL');
                ayudaMonto.textContent = `Ingrese un monto entre $1 y ${totalFmt}`;
                ayudaMonto.style.color = '#64748b';
            }
        }
    });

    // Manejar cambio de método de pago
    metodoPago.addEventListener('change', function() {
        const metodo = this.value;

        if (metodo === 'debito' || metodo === 'credito') {
            // Mostrar sección Flow
            seccionFlow.style.display = 'block';
            seccionComprobante.style.display = 'none';
            linkGenerado.style.display = 'none';

            // Deshabilitar el botón de guardar pago
            formPago.querySelector('button[type="submit"]').disabled = true;
            formPago.querySelector('button[type="submit"]').style.opacity = '0.5';
        } else {
            // Ocultar sección Flow
            seccionFlow.style.display = 'none';
            seccionComprobante.style.display = 'block';

            // Habilitar el botón de guardar pago
            formPago.querySelector('button[type="submit"]').disabled = false;
            formPago.querySelector('button[type="submit"]').style.opacity = '1';
        }
    });

    // Generar link de pago Flow
    btnGenerarLinkFlow.addEventListener('click', async function() {
        const email = emailFlow.value;

        if (!email) {
            alert('Por favor ingrese un email válido');
            emailFlow.focus();
            return;
        }

        if (!boletaSeleccionada) {
            alert('Por favor seleccione una boleta');
            selectBoleta.focus();
            return;
        }

        // Deshabilitar botón
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';

        try {
            const response = await fetch('{{ route("pagos.generarLinkFlow") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id_boleta: boletaSeleccionada,
                    email: email
                })
            });

            const data = await response.json();

            if (data.success) {
                // Mostrar link generado
                document.getElementById('linkPago').value = data.url;
                document.getElementById('abrirLink').href = data.url;
                linkGenerado.style.display = 'block';

                alert('Link de pago generado exitosamente. El socio recibirá un email con el link de pago.');
            } else {
                alert('Error al generar el link de pago: ' + data.message);
            }
        } catch (error) {
            alert('Error al generar el link de pago: ' + error.message);
        } finally {
            // Rehabilitar botón
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-link"></i> Generar Link de Pago';
        }
    });
});

// Función para copiar link
function copiarLink() {
    const linkInput = document.getElementById('linkPago');
    linkInput.select();
    document.execCommand('copy');
    alert('Link copiado al portapapeles');
}
</script>
@endsection
