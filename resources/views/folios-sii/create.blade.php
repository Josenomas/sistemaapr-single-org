@extends('layouts.app')

@section('title', 'Nuevo Folio SII')

@section('styles')
<style>
.required::after {
    content: " *";
    color: #dc3545;
}

.card {
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.alert-info {
    background-color: #e0f2fe;
    border-color: #bae6fd;
    color: #075985;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-plus-circle"></i> Cargar Nuevo Folio SII</h1>
        <a href="{{ route('folios-sii.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card">
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
