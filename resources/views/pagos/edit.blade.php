@extends('layouts.app')

@section('title', 'Editar Pago - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-edit"></i>
        Editar Pago
    </h2>
    <a href="{{ route('pagos.show', $pago->id) }}" class="btn btn-secondary">
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
        <form action="{{ route('pagos.update', $pago->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-row">
                <!-- N° Recibo -->
                <div class="form-group col-md-4">
                    <label for="numero_recibo" class="form-label">N° Recibo</label>
                    <input type="text"
                           class="form-control"
                           id="numero_recibo"
                           value="{{ $pago->numero_recibo }}"
                           disabled
                           style="background: #f3f4f6; color: #6b7280;">
                </div>

                <!-- Fecha de Pago -->
                <div class="form-group col-md-4">
                    <label for="fecha_pago" class="form-label required">Fecha de Pago</label>
                    <input type="date"
                           class="form-control @error('fecha_pago') is-invalid @enderror"
                           id="fecha_pago"
                           name="fecha_pago"
                           value="{{ old('fecha_pago', $pago->fecha_pago->format('Y-m-d')) }}"
                           required>
                    @error('fecha_pago')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Boleta -->
                <div class="form-group col-md-12">
                    <label for="id_boleta" class="form-label required">Boleta</label>
                    <select class="form-control @error('id_boleta') is-invalid @enderror"
                            id="id_boleta"
                            name="id_boleta"
                            required>
                        @foreach($boletas as $boleta)
                            <option value="{{ $boleta->id }}"
                                    {{ old('id_boleta', $pago->id_boleta) == $boleta->id ? 'selected' : '' }}>
                                {{ $boleta->numero_boleta }} - {{ $boleta->socio->nombre_completo }} - {{ $boleta->mes_texto }} - {{ $boleta->total_formateado }}
                                @if($boleta->estado == 'vencida')
                                    (VENCIDA)
                                @elseif($boleta->estado == 'pagada')
                                    (PAGADA)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('id_boleta')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                           value="{{ old('monto_pagado', $pago->monto_pagado) }}"
                           step="0.01"
                           min="0"
                           required>
                    @error('monto_pagado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Método de Pago -->
                <div class="form-group col-md-6">
                    <label for="metodo_pago" class="form-label required">Método de Pago</label>
                    <select class="form-control @error('metodo_pago') is-invalid @enderror"
                            id="metodo_pago"
                            name="metodo_pago"
                            required>
                        <option value="">Seleccione</option>
                        <option value="efectivo" {{ old('metodo_pago', $pago->metodo_pago) == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                        <option value="transferencia" {{ old('metodo_pago', $pago->metodo_pago) == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                        <option value="cheque" {{ old('metodo_pago', $pago->metodo_pago) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="debito" {{ old('metodo_pago', $pago->metodo_pago) == 'debito' ? 'selected' : '' }}>Débito</option>
                        <option value="credito" {{ old('metodo_pago', $pago->metodo_pago) == 'credito' ? 'selected' : '' }}>Crédito</option>
                    </select>
                    @error('metodo_pago')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- N° Comprobante -->
                <div class="form-group col-md-12">
                    <label for="numero_comprobante" class="form-label">N° Comprobante</label>
                    <input type="text"
                           class="form-control @error('numero_comprobante') is-invalid @enderror"
                           id="numero_comprobante"
                           name="numero_comprobante"
                           value="{{ old('numero_comprobante', $pago->numero_comprobante) }}"
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
                              rows="3">{{ old('observaciones', $pago->observaciones) }}</textarea>
                    @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Actualizar Pago
                </button>
                <a href="{{ route('pagos.show', $pago->id) }}" class="btn btn-secondary">
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
