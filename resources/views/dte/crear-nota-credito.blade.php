@extends('layouts.app')

@section('title', 'Crear Nota de Crédito - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-minus-circle"></i>
        Crear Nota de Crédito
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

    <!-- Formulario Nota de Crédito -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white;">
                <h3 class="card-title">
                    <i class="fas fa-minus-circle"></i>
                    Nota de Crédito
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Nota de Crédito:</strong> Documento que anula o reduce el valor de un DTE previamente emitido.
                </div>

                <form action="{{ route('dte.emitir-nota-credito') }}" method="POST" id="formNotaCredito">
                    @csrf
                    <input type="hidden" name="boleta_id" value="{{ $boleta->id }}">

                    <div class="form-group">
                        <label for="motivo" class="form-label required">Motivo de la Nota de Crédito</label>
                        <textarea class="form-control @error('motivo') is-invalid @enderror"
                                  id="motivo"
                                  name="motivo"
                                  rows="4"
                                  required
                                  placeholder="Ej: Anulación por error en facturación, Descuento por acuerdo comercial, etc.">{{ old('motivo') }}</textarea>
                        @error('motivo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">Explique claramente el motivo de la nota de crédito.</small>
                    </div>

                    <div class="form-group">
                        <label for="monto" class="form-label required">Monto de la Nota de Crédito</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number"
                                   class="form-control @error('monto') is-invalid @enderror"
                                   id="monto"
                                   name="monto"
                                   value="{{ old('monto', $boleta->total) }}"
                                   min="1"
                                   max="{{ $boleta->total }}"
                                   step="1"
                                   required>
                        </div>
                        @error('monto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">Máximo: {{ $boleta->total_formateado }}</small>
                    </div>

                    <div class="tipo-nota-options">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_nota" id="notaParcial" value="parcial" checked>
                            <label class="form-check-label" for="notaParcial">
                                <strong>Nota Parcial</strong>
                                <p class="mb-0">Reduce el valor del documento sin anularlo completamente.</p>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_nota" id="notaTotal" value="total">
                            <label class="form-check-label" for="notaTotal">
                                <strong>Anulación Total</strong>
                                <p class="mb-0">Anula completamente el documento original.</p>
                            </label>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Atención:</strong> Esta acción es irreversible. La nota de crédito será emitida al SII inmediatamente.
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-danger btn-lg">
                            <i class="fas fa-minus-circle"></i>
                            Emitir Nota de Crédito
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
        color: #ef4444;
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
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
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

    .tipo-nota-options {
        background: var(--gray-50);
        padding: 16px;
        border-radius: var(--radius);
        margin-bottom: 16px;
    }

    .form-check {
        margin-bottom: 12px;
        padding-left: 0;
    }

    .form-check:last-child {
        margin-bottom: 0;
    }

    .form-check-input {
        width: 20px;
        height: 20px;
        margin-right: 10px;
        vertical-align: middle;
        cursor: pointer;
    }

    .form-check-label {
        cursor: pointer;
        display: block;
        padding-left: 30px;
    }

    .form-check-label p {
        color: var(--gray-600);
        font-size: 0.875rem;
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

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .btn-danger:hover {
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
        const notaTotalRadio = document.getElementById('notaTotal');
        const notaParcialRadio = document.getElementById('notaParcial');
        const maxMonto = {{ $boleta->total }};

        // Cuando se selecciona anulación total, poner el monto máximo
        notaTotalRadio.addEventListener('change', function() {
            if (this.checked) {
                montoInput.value = maxMonto;
            }
        });

        // Cuando se selecciona nota parcial, limpiar el monto
        notaParcialRadio.addEventListener('change', function() {
            if (this.checked) {
                montoInput.value = '';
            }
        });

        // Confirmar antes de enviar
        document.getElementById('formNotaCredito').addEventListener('submit', function(e) {
            const monto = parseFloat(montoInput.value);
            const esTotal = monto >= maxMonto;

            let mensaje = esTotal
                ? '¿Está seguro de que desea ANULAR COMPLETAMENTE este documento?\n\n' +
                  'Se emitirá una Nota de Crédito por el monto total y el documento original quedará anulado.\n\n' +
                  'Esta acción es IRREVERSIBLE.'
                : '¿Está seguro de que desea emitir esta Nota de Crédito?\n\n' +
                  'Monto: $' + monto.toLocaleString('es-CL') + '\n\n' +
                  'Esta acción es IRREVERSIBLE.';

            if (!confirm(mensaje)) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
