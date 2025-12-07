@extends('layouts.app')

@section('title', 'Nuevo Giro Bancario - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-plus-circle"></i>
        Registrar Nuevo Giro Bancario
    </h2>
    <a href="{{ route('giros-bancarios.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Giro Bancario</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('giros-bancarios.store') }}" method="POST">
            @csrf

            <div class="form-row">
                <!-- Número de Giro (autogenerado) -->
                <div class="form-group col-md-6">
                    <label for="numero_giro" class="form-label">N° Giro</label>
                    <input type="text"
                           class="form-control"
                           id="numero_giro"
                           value="(Se generará automáticamente)"
                           disabled>
                    <small class="text-muted">El número se asignará al guardar</small>
                </div>

                <!-- Banco -->
                <div class="form-group col-md-6">
                    <label for="banco" class="form-label required">Banco</label>
                    <input type="text"
                           class="form-control @error('banco') is-invalid @enderror"
                           id="banco"
                           name="banco"
                           value="{{ old('banco') }}"
                           placeholder="Ej: Banco de Chile"
                           required>
                    @error('banco')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Número de Cuenta -->
                <div class="form-group col-md-6">
                    <label for="numero_cuenta" class="form-label required">Número de Cuenta</label>
                    <input type="text"
                           class="form-control @error('numero_cuenta') is-invalid @enderror"
                           id="numero_cuenta"
                           name="numero_cuenta"
                           value="{{ old('numero_cuenta') }}"
                           placeholder="1234567890"
                           required>
                    @error('numero_cuenta')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tipo de Cuenta -->
                <div class="form-group col-md-6">
                    <label for="tipo_cuenta" class="form-label required">Tipo de Cuenta</label>
                    <select class="form-control @error('tipo_cuenta') is-invalid @enderror"
                            id="tipo_cuenta"
                            name="tipo_cuenta"
                            required>
                        <option value="">Seleccione...</option>
                        <option value="corriente" {{ old('tipo_cuenta') == 'corriente' ? 'selected' : '' }}>Cuenta Corriente</option>
                        <option value="vista" {{ old('tipo_cuenta') == 'vista' ? 'selected' : '' }}>Cuenta Vista</option>
                        <option value="ahorro" {{ old('tipo_cuenta') == 'ahorro' ? 'selected' : '' }}>Cuenta de Ahorro</option>
                    </select>
                    @error('tipo_cuenta')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Beneficiario -->
                <div class="form-group col-md-6">
                    <label for="beneficiario" class="form-label required">Beneficiario</label>
                    <input type="text"
                           class="form-control @error('beneficiario') is-invalid @enderror"
                           id="beneficiario"
                           name="beneficiario"
                           value="{{ old('beneficiario') }}"
                           placeholder="Nombre completo del beneficiario"
                           required>
                    @error('beneficiario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- RUT Beneficiario -->
                <div class="form-group col-md-6">
                    <label for="rut_beneficiario" class="form-label">RUT Beneficiario</label>
                    <input type="text"
                           class="form-control @error('rut_beneficiario') is-invalid @enderror"
                           id="rut_beneficiario"
                           name="rut_beneficiario"
                           value="{{ old('rut_beneficiario') }}"
                           placeholder="12.345.678-9">
                    @error('rut_beneficiario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Monto -->
                <div class="form-group col-md-6">
                    <label for="monto" class="form-label required">Monto</label>
                    <input type="number"
                           class="form-control @error('monto') is-invalid @enderror"
                           id="monto"
                           name="monto"
                           value="{{ old('monto') }}"
                           min="1"
                           step="0.01"
                           placeholder="0.00"
                           required>
                    @error('monto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Fecha de Emisión -->
                <div class="form-group col-md-6">
                    <label for="fecha_emision" class="form-label required">Fecha de Emisión</label>
                    <input type="date"
                           class="form-control @error('fecha_emision') is-invalid @enderror"
                           id="fecha_emision"
                           name="fecha_emision"
                           value="{{ old('fecha_emision', date('Y-m-d')) }}"
                           required>
                    @error('fecha_emision')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Concepto -->
                <div class="form-group col-md-6">
                    <label for="concepto" class="form-label required">Concepto</label>
                    <input type="text"
                           class="form-control @error('concepto') is-invalid @enderror"
                           id="concepto"
                           name="concepto"
                           value="{{ old('concepto') }}"
                           placeholder="Motivo del giro bancario"
                           required>
                    @error('concepto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Estado -->
                <div class="form-group col-md-6">
                    <label for="estado" class="form-label required">Estado</label>
                    <select class="form-control @error('estado') is-invalid @enderror"
                            id="estado"
                            name="estado"
                            required>
                        <option value="">Seleccione...</option>
                        <option value="emitido" {{ old('estado', 'emitido') == 'emitido' ? 'selected' : '' }}>Emitido</option>
                        <option value="pagado" {{ old('estado') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                        <option value="anulado" {{ old('estado') == 'anulado' ? 'selected' : '' }}>Anulado</option>
                        <option value="vencido" {{ old('estado') == 'vencido' ? 'selected' : '' }}>Vencido</option>
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Método de Entrega -->
                <div class="form-group col-md-6">
                    <label for="metodo_entrega" class="form-label required">Método de Entrega</label>
                    <select class="form-control @error('metodo_entrega') is-invalid @enderror"
                            id="metodo_entrega"
                            name="metodo_entrega"
                            required>
                        <option value="">Seleccione...</option>
                        <option value="retiro_sucursal" {{ old('metodo_entrega') == 'retiro_sucursal' ? 'selected' : '' }}>Retiro en Sucursal</option>
                        <option value="transferencia" {{ old('metodo_entrega') == 'transferencia' ? 'selected' : '' }}>Transferencia Bancaria</option>
                        <option value="cheque" {{ old('metodo_entrega') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                    @error('metodo_entrega')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Número de Comprobante -->
                <div class="form-group col-md-6">
                    <label for="numero_comprobante" class="form-label">Número de Comprobante</label>
                    <input type="text"
                           class="form-control @error('numero_comprobante') is-invalid @enderror"
                           id="numero_comprobante"
                           name="numero_comprobante"
                           value="{{ old('numero_comprobante') }}"
                           placeholder="Opcional">
                    @error('numero_comprobante')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Responsable -->
                <div class="form-group col-md-6">
                    <label for="id_responsable" class="form-label">Responsable</label>
                    <select class="form-control @error('id_responsable') is-invalid @enderror"
                            id="id_responsable"
                            name="id_responsable">
                        <option value="">Seleccione...</option>
                        @foreach($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}" {{ old('id_responsable') == $funcionario->id ? 'selected' : '' }}>
                                {{ $funcionario->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_responsable')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Descripción -->
            <div class="form-group">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                          id="descripcion"
                          name="descripcion"
                          rows="3"
                          placeholder="Detalles adicionales del giro...">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Observaciones -->
            <div class="form-group">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control @error('observaciones') is-invalid @enderror"
                          id="observaciones"
                          name="observaciones"
                          rows="3"
                          placeholder="Notas adicionales...">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar Giro
                </button>
                <a href="{{ route('giros-bancarios.index') }}" class="btn btn-secondary">
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

    .form-control:disabled {
        background: var(--gray-100);
        cursor: not-allowed;
    }

    .invalid-feedback {
        color: var(--danger);
        font-size: 0.875rem;
        margin-top: 4px;
    }

    .text-muted {
        color: var(--gray-500);
        font-size: 0.75rem;
        margin-top: 4px;
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

    .col-md-6 {
        grid-column: span 1;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .col-md-6 {
            grid-column: span 1;
        }
    }
</style>
@endsection
