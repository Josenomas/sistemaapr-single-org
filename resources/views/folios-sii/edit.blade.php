@extends('layouts.app')

@section('title', 'Editar Folio SII')

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

    .form-control[readonly] {
        background-color: var(--gray-100);
        cursor: not-allowed;
    }

    .invalid-feedback {
        color: #ef4444;
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
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-info {
        background-color: #dbeafe;
        border-color: #bfdbfe;
        color: #1e40af;
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
        <i class="fas fa-edit"></i>
        Editar Folio #{{ $folio->id }}
    </h2>
    <a href="{{ route('folios-sii.show', $folio->id) }}" class="btn btn-secondary">
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
            <form action="{{ route('folios-sii.update', $folio->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Solo se pueden modificar la fecha de vencimiento, estado y observaciones.
                    El rango de folios no puede ser modificado.
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Documento (No editable)</label>
                        <input type="text" class="form-control" value="{{ strtoupper(str_replace('_', ' ', $folio->tipo_documento)) }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Rango de Folios (No editable)</label>
                        <input type="text" class="form-control"
                               value="{{ number_format($folio->folio_desde, 0, ',', '.') }} - {{ number_format($folio->folio_hasta, 0, ',', '.') }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label required">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror"
                               value="{{ old('fecha_vencimiento', $folio->fecha_vencimiento->format('Y-m-d')) }}" required>
                        @error('fecha_vencimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label required">Estado</label>
                        <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                            <option value="activo" {{ old('estado', $folio->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="agotado" {{ old('estado', $folio->estado) == 'agotado' ? 'selected' : '' }}>Agotado</option>
                            <option value="vencido" {{ old('estado', $folio->estado) == 'vencido' ? 'selected' : '' }}>Vencido</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror"
                              rows="3">{{ old('observaciones', $folio->observaciones) }}</textarea>
                    @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <a href="{{ route('folios-sii.show', $folio->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
