@extends('layouts.app')

@section('title', 'Editar Compra - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-edit"></i>
        Editar Compra
    </h2>
    <a href="{{ route('compras.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Compra</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('compras.update', $compra->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Número y Fecha -->
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="numero_compra" class="form-label required">Número de Compra</label>
                    <input type="text" name="numero_compra" id="numero_compra"
                           class="form-control @error('numero_compra') is-invalid @enderror"
                           value="{{ old('numero_compra', $compra->numero_compra) }}" required readonly>
                    @error('numero_compra')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="fecha_compra" class="form-label required">Fecha de Compra</label>
                    <input type="date" name="fecha_compra" id="fecha_compra"
                           class="form-control @error('fecha_compra') is-invalid @enderror"
                           value="{{ old('fecha_compra', $compra->fecha_compra->format('Y-m-d')) }}" required>
                    @error('fecha_compra')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="tipo_compra" class="form-label required">Tipo de Compra</label>
                    <select name="tipo_compra" id="tipo_compra" class="form-control @error('tipo_compra') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        <option value="materiales" {{ old('tipo_compra', $compra->tipo_compra) == 'materiales' ? 'selected' : '' }}>Materiales</option>
                        <option value="equipos" {{ old('tipo_compra', $compra->tipo_compra) == 'equipos' ? 'selected' : '' }}>Equipos</option>
                        <option value="herramientas" {{ old('tipo_compra', $compra->tipo_compra) == 'herramientas' ? 'selected' : '' }}>Herramientas</option>
                        <option value="insumos" {{ old('tipo_compra', $compra->tipo_compra) == 'insumos' ? 'selected' : '' }}>Insumos</option>
                        <option value="servicios" {{ old('tipo_compra', $compra->tipo_compra) == 'servicios' ? 'selected' : '' }}>Servicios</option>
                        <option value="otro" {{ old('tipo_compra', $compra->tipo_compra) == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('tipo_compra')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Proveedor -->
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="proveedor" class="form-label required">Proveedor</label>
                    <input type="text" name="proveedor" id="proveedor"
                           class="form-control @error('proveedor') is-invalid @enderror"
                           value="{{ old('proveedor', $compra->proveedor) }}" required placeholder="Nombre del proveedor">
                    @error('proveedor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="rut_proveedor" class="form-label">RUT Proveedor</label>
                    <input type="text" name="rut_proveedor" id="rut_proveedor"
                           class="form-control @error('rut_proveedor') is-invalid @enderror"
                           value="{{ old('rut_proveedor', $compra->rut_proveedor) }}" placeholder="12.345.678-9">
                    @error('rut_proveedor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Descripción -->
            <div class="form-group">
                <label for="descripcion" class="form-label required">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="3"
                          class="form-control @error('descripcion') is-invalid @enderror"
                          required placeholder="Descripción detallada de la compra">{{ old('descripcion', $compra->descripcion) }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Cantidad y Precio -->
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="cantidad" class="form-label required">Cantidad</label>
                    <input type="number" name="cantidad" id="cantidad" step="0.01"
                           class="form-control @error('cantidad') is-invalid @enderror"
                           value="{{ old('cantidad', $compra->cantidad) }}" required min="0.01" placeholder="1.00">
                    @error('cantidad')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                    <input type="text" name="unidad_medida" id="unidad_medida"
                           class="form-control @error('unidad_medida') is-invalid @enderror"
                           value="{{ old('unidad_medida', $compra->unidad_medida) }}" placeholder="Ej: unidades, kg, metros">
                    @error('unidad_medida')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="precio_unitario" class="form-label required">Precio Unitario</label>
                    <input type="number" name="precio_unitario" id="precio_unitario" step="0.01"
                           class="form-control @error('precio_unitario') is-invalid @enderror"
                           value="{{ old('precio_unitario', $compra->precio_unitario) }}" required min="0" placeholder="0.00">
                    @error('precio_unitario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- IVA -->
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="iva" class="form-label">IVA</label>
                    <input type="number" name="iva" id="iva" step="0.01"
                           class="form-control @error('iva') is-invalid @enderror"
                           value="{{ old('iva', $compra->iva) }}" min="0" placeholder="0.00">
                    @error('iva')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="metodo_pago" class="form-label required">Método de Pago</label>
                    <select name="metodo_pago" id="metodo_pago" class="form-control @error('metodo_pago') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        <option value="efectivo" {{ old('metodo_pago', $compra->metodo_pago) == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                        <option value="transferencia" {{ old('metodo_pago', $compra->metodo_pago) == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                        <option value="cheque" {{ old('metodo_pago', $compra->metodo_pago) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="credito" {{ old('metodo_pago', $compra->metodo_pago) == 'credito' ? 'selected' : '' }}>Crédito</option>
                    </select>
                    @error('metodo_pago')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="estado" class="form-label required">Estado</label>
                    <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        <option value="pendiente" {{ old('estado', $compra->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="pagada" {{ old('estado', $compra->estado) == 'pagada' ? 'selected' : '' }}>Pagada</option>
                        <option value="anulada" {{ old('estado', $compra->estado) == 'anulada' ? 'selected' : '' }}>Anulada</option>
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Factura y Pago -->
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="numero_factura" class="form-label">Número de Factura</label>
                    <input type="text" name="numero_factura" id="numero_factura"
                           class="form-control @error('numero_factura') is-invalid @enderror"
                           value="{{ old('numero_factura', $compra->numero_factura) }}" placeholder="Ej: 001234">
                    @error('numero_factura')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="fecha_pago" class="form-label">Fecha de Pago</label>
                    <input type="date" name="fecha_pago" id="fecha_pago"
                           class="form-control @error('fecha_pago') is-invalid @enderror"
                           value="{{ old('fecha_pago', $compra->fecha_pago ? $compra->fecha_pago->format('Y-m-d') : '') }}">
                    @error('fecha_pago')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="id_responsable" class="form-label">Responsable</label>
                    <select name="id_responsable" id="id_responsable" class="form-control @error('id_responsable') is-invalid @enderror">
                        <option value="">Sin asignar</option>
                        @foreach($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}" {{ old('id_responsable', $compra->id_responsable) == $funcionario->id ? 'selected' : '' }}>
                                {{ $funcionario->nombre }} {{ $funcionario->apellido_paterno }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_responsable')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Observaciones -->
            <div class="form-group">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea name="observaciones" id="observaciones" rows="3"
                          class="form-control @error('observaciones') is-invalid @enderror"
                          placeholder="Notas adicionales sobre la compra...">{{ old('observaciones', $compra->observaciones) }}</textarea>
                @error('observaciones')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Actualizar Compra
                </button>
                <a href="{{ route('compras.index') }}" class="btn btn-secondary">
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

    .invalid-feedback {
        color: var(--danger);
        font-size: 0.875rem;
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

    .col-md-4 {
        grid-column: span 1;
    }

    .col-md-8 {
        grid-column: span 2;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .col-md-4,
        .col-md-8 {
            grid-column: span 1;
        }
    }
</style>
@endsection
