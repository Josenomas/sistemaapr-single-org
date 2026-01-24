@extends('layouts.app')

@section('title', 'Nuevo Folio SII')

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

    .row {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 20px;
    }

    .col-md-12 { grid-column: span 12; }
    .col-md-6 { grid-column: span 6; }
    .col-md-4 { grid-column: span 4; }
    .col-md-3 { grid-column: span 3; }

    .mb-3 {
        margin-bottom: 20px;
    }

    .mt-4 {
        margin-top: 24px;
    }

    .form-label {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
        font-size: 0.875rem;
        display: block;
    }

    .form-label.required::after {
        content: ' *';
        color: #ef4444;
    }

    .form-control, .form-select {
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
        background: var(--white);
        width: 100%;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #ef4444;
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 4px;
        display: block;
    }

    .text-muted, small.text-muted {
        color: var(--gray-500);
        font-size: 0.75rem;
        margin-top: 4px;
        display: block;
    }

    .d-flex {
        display: flex;
    }

    .gap-2 {
        gap: 12px;
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

    .alert {
        padding: 16px;
        border-radius: var(--radius);
        border: 1px solid;
    }

    .alert-info {
        background-color: #dbeafe;
        border-color: #bfdbfe;
        color: #1e40af;
    }

    .alert-info h5 {
        margin: 0 0 8px 0;
        font-size: 1rem;
        font-weight: 600;
    }

    .alert-info p {
        margin: 0;
        font-size: 0.875rem;
    }

    @media (max-width: 768px) {
        .row {
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

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-plus-circle"></i>
        Cargar Nuevo Folio SII
    </h2>
    <a href="{{ route('folios-sii.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-file-invoice"></i>
            Información del Folio
        </h3>
    </div>
    <div class="card-body">
            <form action="{{ route('folios-sii.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label required">Tipo de Documento</label>
                        <select name="tipo_documento" class="form-select @error('tipo_documento') is-invalid @enderror" required>
                            <option value="">Seleccionar...</option>
                            <option value="boleta" {{ old('tipo_documento') == 'boleta' ? 'selected' : '' }}>Boleta Electrónica</option>
                            <option value="factura" {{ old('tipo_documento') == 'factura' ? 'selected' : '' }}>Factura Electrónica</option>
                            <option value="nota_credito" {{ old('tipo_documento') == 'nota_credito' ? 'selected' : '' }}>Nota de Crédito</option>
                            <option value="nota_debito" {{ old('tipo_documento') == 'nota_debito' ? 'selected' : '' }}>Nota de Débito</option>
                        </select>
                        @error('tipo_documento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label required">Folio Desde</label>
                        <input type="number" name="folio_desde" class="form-control @error('folio_desde') is-invalid @enderror"
                               value="{{ old('folio_desde') }}" placeholder="Ej: 1" min="1" required>
                        @error('folio_desde')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Número inicial del rango de folios</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label required">Folio Hasta</label>
                        <input type="number" name="folio_hasta" class="form-control @error('folio_hasta') is-invalid @enderror"
                               value="{{ old('folio_hasta') }}" placeholder="Ej: 1000" min="1" required>
                        @error('folio_hasta')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Número final del rango de folios</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label required">Fecha de Autorización SII</label>
                        <input type="date" name="fecha_autorizacion" class="form-control @error('fecha_autorizacion') is-invalid @enderror"
                               value="{{ old('fecha_autorizacion') }}" required>
                        @error('fecha_autorizacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label required">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror"
                               value="{{ old('fecha_vencimiento') }}" required>
                        @error('fecha_vencimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Fecha límite de uso del CAF</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">CAF XML (Opcional)</label>
                    <textarea name="caf_xml" class="form-control @error('caf_xml') is-invalid @enderror"
                              rows="6" placeholder="Pegar aquí el contenido XML del archivo CAF del SII...">{{ old('caf_xml') }}</textarea>
                    @error('caf_xml')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Código de Autorización de Folios proporcionado por el SII
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror"
                              rows="3" placeholder="Notas adicionales...">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Folio
                    </button>
                    <a href="{{ route('folios-sii.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-info mt-4">
        <h5><i class="fas fa-info-circle"></i> ¿Qué es un CAF?</h5>
        <p class="mb-0">
            El <strong>Código de Autorización de Folios (CAF)</strong> es un archivo XML proporcionado por el SII
            que autoriza el uso de un rango de folios para la emisión de documentos tributarios electrónicos.
        </p>
    </div>
</div>

<script>
// Calcular automáticamente cantidad de folios
document.addEventListener('DOMContentLoaded', function() {
    const folioDesde = document.querySelector('[name="folio_desde"]');
    const folioHasta = document.querySelector('[name="folio_hasta"]');

    function calcularFolios() {
        const desde = parseInt(folioDesde.value) || 0;
        const hasta = parseInt(folioHasta.value) || 0;

        if (desde > 0 && hasta > 0 && hasta >= desde) {
            const total = hasta - desde + 1;
            console.log(`Total folios: ${total}`);
        }
    }

    folioDesde.addEventListener('input', calcularFolios);
    folioHasta.addEventListener('input', calcularFolios);
});
</script>
@endsection
