@extends('layouts.app')

@section('title', 'Editar Producto - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-edit"></i>
        Editar Producto: {{ $producto->nombre }}
    </h2>
    <a href="{{ route('inventario.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Producto</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('inventario.update', $producto->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-row">
                <!-- Código de Producto -->
                <div class="form-group col-md-4">
                    <label for="codigo_producto" class="form-label required">Código de Producto</label>
                    <input type="text"
                           class="form-control @error('codigo_producto') is-invalid @enderror"
                           id="codigo_producto"
                           name="codigo_producto"
                           value="{{ old('codigo_producto', $producto->codigo_producto) }}"
                           required>
                    @error('codigo_producto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Nombre -->
                <div class="form-group col-md-8">
                    <label for="nombre" class="form-label required">Nombre del Producto</label>
                    <input type="text"
                           class="form-control @error('nombre') is-invalid @enderror"
                           id="nombre"
                           name="nombre"
                           value="{{ old('nombre', $producto->nombre) }}"
                           required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Categoría -->
                <div class="form-group col-md-4">
                    <label for="categoria" class="form-label required">Categoría</label>
                    <select class="form-control @error('categoria') is-invalid @enderror"
                            id="categoria"
                            name="categoria"
                            required>
                        <option value="">Seleccione...</option>
                        <option value="materiales" {{ old('categoria', $producto->categoria) == 'materiales' ? 'selected' : '' }}>Materiales</option>
                        <option value="equipos" {{ old('categoria', $producto->categoria) == 'equipos' ? 'selected' : '' }}>Equipos</option>
                        <option value="herramientas" {{ old('categoria', $producto->categoria) == 'herramientas' ? 'selected' : '' }}>Herramientas</option>
                        <option value="insumos" {{ old('categoria', $producto->categoria) == 'insumos' ? 'selected' : '' }}>Insumos</option>
                        <option value="quimicos" {{ old('categoria', $producto->categoria) == 'quimicos' ? 'selected' : '' }}>Químicos</option>
                        <option value="repuestos" {{ old('categoria', $producto->categoria) == 'repuestos' ? 'selected' : '' }}>Repuestos</option>
                        <option value="otro" {{ old('categoria', $producto->categoria) == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('categoria')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Unidad de Medida -->
                <div class="form-group col-md-4">
                    <label for="unidad_medida" class="form-label required">Unidad de Medida</label>
                    <input type="text"
                           class="form-control @error('unidad_medida') is-invalid @enderror"
                           id="unidad_medida"
                           name="unidad_medida"
                           value="{{ old('unidad_medida', $producto->unidad_medida) }}"
                           placeholder="Ej: kg, L, m, unidad"
                           required>
                    @error('unidad_medida')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Precio Unitario -->
                <div class="form-group col-md-4">
                    <label for="precio_unitario" class="form-label">Precio Unitario</label>
                    <input type="number"
                           class="form-control @error('precio_unitario') is-invalid @enderror"
                           id="precio_unitario"
                           name="precio_unitario"
                           value="{{ old('precio_unitario', $producto->precio_unitario) }}"
                           step="0.01"
                           min="0"
                           placeholder="0.00">
                    @error('precio_unitario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Cantidad Actual -->
                <div class="form-group col-md-4">
                    <label for="cantidad_actual" class="form-label required">Cantidad Actual</label>
                    <input type="number"
                           class="form-control @error('cantidad_actual') is-invalid @enderror"
                           id="cantidad_actual"
                           name="cantidad_actual"
                           value="{{ old('cantidad_actual', $producto->cantidad_actual) }}"
                           step="0.01"
                           min="0"
                           required>
                    @error('cantidad_actual')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Cantidad Mínima -->
                <div class="form-group col-md-4">
                    <label for="cantidad_minima" class="form-label required">Cantidad Mínima</label>
                    <input type="number"
                           class="form-control @error('cantidad_minima') is-invalid @enderror"
                           id="cantidad_minima"
                           name="cantidad_minima"
                           value="{{ old('cantidad_minima', $producto->cantidad_minima) }}"
                           step="0.01"
                           min="0"
                           required>
                    @error('cantidad_minima')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Cantidad Máxima -->
                <div class="form-group col-md-4">
                    <label for="cantidad_maxima" class="form-label">Cantidad Máxima</label>
                    <input type="number"
                           class="form-control @error('cantidad_maxima') is-invalid @enderror"
                           id="cantidad_maxima"
                           name="cantidad_maxima"
                           value="{{ old('cantidad_maxima', $producto->cantidad_maxima) }}"
                           step="0.01"
                           min="0"
                           placeholder="Opcional">
                    @error('cantidad_maxima')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Ubicación -->
                <div class="form-group col-md-4">
                    <label for="ubicacion" class="form-label">Ubicación</label>
                    <input type="text"
                           class="form-control @error('ubicacion') is-invalid @enderror"
                           id="ubicacion"
                           name="ubicacion"
                           value="{{ old('ubicacion', $producto->ubicacion) }}"
                           placeholder="Ej: Bodega A, Estante 3">
                    @error('ubicacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Proveedor -->
                <div class="form-group col-md-4">
                    <label for="proveedor" class="form-label">Proveedor</label>
                    <input type="text"
                           class="form-control @error('proveedor') is-invalid @enderror"
                           id="proveedor"
                           name="proveedor"
                           value="{{ old('proveedor', $producto->proveedor) }}"
                           placeholder="Nombre del proveedor habitual">
                    @error('proveedor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Estado -->
                <div class="form-group col-md-4">
                    <label for="estado" class="form-label required">Estado</label>
                    <select class="form-control @error('estado') is-invalid @enderror"
                            id="estado"
                            name="estado"
                            required>
                        <option value="disponible" {{ old('estado', $producto->estado) == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="agotado" {{ old('estado', $producto->estado) == 'agotado' ? 'selected' : '' }}>Agotado</option>
                        <option value="bajo_stock" {{ old('estado', $producto->estado) == 'bajo_stock' ? 'selected' : '' }}>Bajo Stock</option>
                        <option value="descontinuado" {{ old('estado', $producto->estado) == 'descontinuado' ? 'selected' : '' }}>Descontinuado</option>
                    </select>
                    @error('estado')
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
                          placeholder="Descripción detallada del producto...">{{ old('descripcion', $producto->descripcion) }}</textarea>
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
                          placeholder="Notas adicionales sobre el producto...">{{ old('observaciones', $producto->observaciones) }}</textarea>
                @error('observaciones')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Actualizar Producto
                </button>
                <a href="{{ route('inventario.index') }}" class="btn btn-secondary">
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
