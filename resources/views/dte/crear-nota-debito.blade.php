@extends('layouts.app')

@section('title', 'Crear Nota de Débito - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-plus-circle"></i>
        Crear Nota de Débito
    </h2>
    <a href="{{ route('dte.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="row">
    <!-- Información del Documento Original -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white;">
                <h3 class="card-title">
                    <i class="fas fa-file-invoice"></i>
                    Documento Original
                </h3>
            </div>
            <div class="card-body">
                <table class="info-table">
                    <tr>
                        <th>Tipo DTE:</th>
                        <td>{!! $boleta->tipo_documento_badge !!}</td>
                    </tr>
                    <tr>
                        <th>Folio SII:</th>
                        <td><strong>{{ $boleta->folio_sii }}</strong></td>
                    </tr>
                    <tr>
                        <th>Número Boleta:</th>
                        <td>{{ $boleta->numero_boleta }}</td>
                    </tr>
                    <tr>
                        <th>Socio:</th>
                        <td>{{ $boleta->socio->numero_socio }} - {{ $boleta->socio->nombre_completo }}</td>
                    </tr>
                    <tr>
                        <th>Fecha Emisión:</th>
                        <td>{{ $boleta->fecha_emision_dte->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Total Original:</th>
                        <td><strong class="text-primary" style="font-size: 1.2rem;">{{ $boleta->total_formateado }}</strong></td>
                    </tr>
                    <tr>
                        <th>Estado DTE:</th>
                        <td>{!! $boleta->estado_dte_badge !!}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Formulario Nota de Débito -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">
                <h3 class="card-title">
                    <i class="fas fa-plus-circle"></i>
                    Nota de Débito
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Nota de Débito:</strong> Documento que incrementa el valor de un DTE previamente emitido (cargos adicionales, intereses, etc.).
                </div>

                <form action="{{ route('dte.emitir-nota-debito') }}" method="POST" id="formNotaDebito">
                    @csrf
                    <input type="hidden" name="boleta_id" value="{{ $boleta->id }}">

                    <div class="form-group">
                        <label for="motivo" class="form-label required">Motivo de la Nota de Débito</label>
                        <textarea class="form-control @error('motivo') is-invalid @enderror"
                                  id="motivo"
                                  name="motivo"
                                  rows="4"
                                  required
                                  placeholder="Ej: Intereses por mora, Cargo adicional por servicio, Recargo por consumo excesivo, etc.">{{ old('motivo') }}</textarea>
                        @error('motivo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">Explique claramente el motivo de la nota de débito.</small>
                    </div>

                    <div class="form-group">
                        <label for="monto" class="form-label required">Monto del Cargo Adicional</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number"
                                   class="form-control @error('monto') is-invalid @enderror"
                                   id="monto"
                                   name="monto"
                                   value="{{ old('monto') }}"
                                   min="1"
                                   step="1"
                                   required>
                        </div>
                        @error('monto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">Ingrese el monto del cargo adicional a aplicar.</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-calculator"></i>
                        <div>
                            <strong>Nuevo Total:</strong>
                            <div style="font-size: 1.1rem; margin-top: 8px;">
                                <span id="nuevoTotal">{{ $boleta->total_formateado }}</span>
                            </div>
                            <small class="text-muted">Total Original: {{ $boleta->total_formateado }}</small>
                        </div>
                    </div>

                    <div class="ejemplos-box">
                        <strong><i class="fas fa-lightbulb"></i> Ejemplos de uso:</strong>
                        <ul>
                            <li>Intereses por pago fuera de plazo</li>
                            <li>Recargos por consumo excesivo</li>
                            <li>Cargos administrativos</li>
                            <li>Servicios adicionales no incluidos</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Atención:</strong> Esta acción es irreversible. La nota de débito será emitida al SII inmediatamente y aumentará el monto a cobrar al socio.
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-plus-circle"></i>
                            Emitir Nota de Débito
                        </button>
                        <a href="{{ route('dte.dashboard') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
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

    .row {
        margin: 0 -12px;
    }

    .col-md-6 {
        padding: 0 12px;
        margin-bottom: 24px;
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        height: 100%;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: white;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-body {
        padding: 24px;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table tr {
        border-bottom: 1px solid var(--gray-200);
    }

    .info-table tr:last-child {
        border-bottom: none;
    }

    .info-table th {
        padding: 12px 0;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
        width: 40%;
    }

    .info-table td {
        padding: 12px 0;
        color: var(--gray-900);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
        display: block;
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
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
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
        display: block;
        margin-top: 4px;
        font-size: 0.8rem;
        color: var(--gray-500);
    }

    .input-group {
        display: flex;
    }

    .input-group-text {
        background: var(--gray-100);
        border: 2px solid var(--gray-200);
        border-right: none;
        padding: 10px 14px;
        border-radius: var(--radius) 0 0 var(--radius);
        font-weight: 600;
        color: var(--gray-700);
    }

    .input-group .form-control {
        border-radius: 0 var(--radius) var(--radius) 0;
    }

    .ejemplos-box {
        background: #f0f9ff;
        border: 1px solid #3b82f6;
        border-radius: var(--radius);
        padding: 16px;
        margin-bottom: 16px;
    }

    .ejemplos-box strong {
        color: #1e40af;
        display: block;
        margin-bottom: 8px;
    }

    .ejemplos-box ul {
        margin: 0;
        padding-left: 20px;
        color: #1e40af;
    }

    .ejemplos-box li {
        margin-bottom: 4px;
    }

    .alert {
        padding: 12px 16px;
        border-radius: var(--radius);
        margin-bottom: 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .alert-info {
        background: #dbeafe;
        border: 1px solid #3b82f6;
        color: #1e40af;
    }

    .alert-warning {
        background: #fef3c7;
        border: 1px solid #f59e0b;
        color: #92400e;
    }

    .alert i {
        margin-top: 2px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
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

    .btn-lg {
        padding: 14px 28px;
        font-size: 1rem;
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .btn-warning:hover {
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

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    @media (max-width: 768px) {
        .col-md-6 {
            width: 100%;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            justify-content: center;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const montoInput = document.getElementById('monto');
        const nuevoTotalSpan = document.getElementById('nuevoTotal');
        const totalOriginal = {{ $boleta->total }};

        // Actualizar el nuevo total cuando cambia el monto
        montoInput.addEventListener('input', function() {
            const cargo = parseFloat(this.value) || 0;
            const nuevoTotal = totalOriginal + cargo;

            nuevoTotalSpan.textContent = '$' + nuevoTotal.toLocaleString('es-CL');
        });

        // Confirmar antes de enviar
        document.getElementById('formNotaDebito').addEventListener('submit', function(e) {
            const monto = parseFloat(montoInput.value);
            const nuevoTotal = totalOriginal + monto;

            const mensaje = '¿Está seguro de que desea emitir esta Nota de Débito?\n\n' +
                            'Cargo adicional: $' + monto.toLocaleString('es-CL') + '\n' +
                            'Total original: $' + totalOriginal.toLocaleString('es-CL') + '\n' +
                            'Nuevo total: $' + nuevoTotal.toLocaleString('es-CL') + '\n\n' +
                            'Esta acción es IRREVERSIBLE.';

            if (!confirm(mensaje)) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
