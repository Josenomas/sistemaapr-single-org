@extends('layouts.app')

@section('title', 'Nuevo Movimiento - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-plus"></i>
        Nuevo Movimiento de Inventario
    </h2>
    <a href="{{ route('movimientos-inventario.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('movimientos-inventario.store') }}" method="POST">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="id_producto">Producto: *</label>
                    <select id="id_producto" name="id_producto" class="form-control" required>
                        <option value="">Seleccione un producto</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}"
                                    data-stock="{{ $producto->cantidad_actual }}"
                                    data-unidad="{{ $producto->unidad_medida }}"
                                    {{ old('id_producto') == $producto->id ? 'selected' : '' }}>
                                {{ $producto->nombre }} - Stock: {{ $producto->cantidad_actual }} {{ $producto->unidad_medida }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_producto')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tipo_movimiento">Tipo de Movimiento: *</label>
                    <select id="tipo_movimiento" name="tipo_movimiento" class="form-control" required>
                        <option value="">Seleccione el tipo</option>
                        <option value="entrada" {{ old('tipo_movimiento') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                        <option value="salida" {{ old('tipo_movimiento') == 'salida' ? 'selected' : '' }}>Salida</option>
                        <option value="ajuste" {{ old('tipo_movimiento') == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                    </select>
                    @error('tipo_movimiento')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="cantidad">Cantidad: *</label>
                    <input type="number"
                           id="cantidad"
                           name="cantidad"
                           class="form-control"
                           value="{{ old('cantidad') }}"
                           step="0.01"
                           min="0.01"
                           required>
                    @error('cantidad')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <small id="stock-info" class="form-text"></small>
                </div>

                <div class="form-group">
                    <label for="fecha_movimiento">Fecha del Movimiento: *</label>
                    <input type="date"
                           id="fecha_movimiento"
                           name="fecha_movimiento"
                           class="form-control"
                           value="{{ old('fecha_movimiento', date('Y-m-d')) }}"
                           required>
                    @error('fecha_movimiento')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group full-width">
                    <label for="motivo">Motivo: *</label>
                    <input type="text"
                           id="motivo"
                           name="motivo"
                           class="form-control"
                           value="{{ old('motivo') }}"
                           placeholder="Ej: Compra de materiales, Uso en trabajo, etc."
                           maxlength="200"
                           required>
                    @error('motivo')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" id="destino-group">
                    <label for="destino">Destino:</label>
                    <input type="text"
                           id="destino"
                           name="destino"
                           class="form-control"
                           value="{{ old('destino') }}"
                           placeholder="Lugar o persona que recibe"
                           maxlength="200">
                    @error('destino')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="documento_referencia">Documento Referencia:</label>
                    <input type="text"
                           id="documento_referencia"
                           name="documento_referencia"
                           class="form-control"
                           value="{{ old('documento_referencia') }}"
                           placeholder="N° Factura, Orden de Trabajo, etc."
                           maxlength="100">
                    @error('documento_referencia')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="id_responsable">Responsable:</label>
                    <select id="id_responsable" name="id_responsable" class="form-control">
                        <option value="">Ninguno</option>
                        @foreach($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}" {{ old('id_responsable') == $funcionario->id ? 'selected' : '' }}>
                                {{ $funcionario->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_responsable')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group full-width">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion"
                              name="descripcion"
                              class="form-control"
                              rows="3"
                              placeholder="Detalles adicionales del movimiento">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group full-width">
                    <label for="observaciones">Observaciones:</label>
                    <textarea id="observaciones"
                              name="observaciones"
                              class="form-control"
                              rows="2"
                              placeholder="Observaciones adicionales">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Registrar Movimiento
                </button>
                <a href="{{ route('movimientos-inventario.index') }}" class="btn btn-secondary">
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
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 1.75rem; font-weight: 700; color: var(--dark); display: flex; align-items: center; gap: 12px; margin: 0; }
    .page-title i { color: var(--primary); }
    .card { background: var(--white); border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--gray-200); }
    .card-body { padding: 24px; }
    .form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .form-group { display: flex; flex-direction: column; }
    .form-group.full-width { grid-column: span 2; }
    .form-group label { font-weight: 600; color: var(--gray-700); margin-bottom: 6px; font-size: 0.875rem; }
    .form-control { padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: var(--radius); font-size: 0.875rem; transition: all 0.2s; }
    .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
    .form-text { font-size: 0.75rem; color: var(--gray-600); margin-top: 4px; }
    .error-message { color: #ef4444; font-size: 0.75rem; margin-top: 4px; }
    .form-actions { margin-top: 24px; display: flex; gap: 10px; }
    .btn { padding: 10px 20px; border-radius: var(--radius); border: none; font-weight: 600; font-size: 0.875rem; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
    .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
    .btn-secondary { background: var(--gray-500); color: white; }
    .btn-secondary:hover { background: var(--gray-600); }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productoSelect = document.getElementById('id_producto');
        const tipoSelect = document.getElementById('tipo_movimiento');
        const cantidadInput = document.getElementById('cantidad');
        const stockInfo = document.getElementById('stock-info');
        const destinoGroup = document.getElementById('destino-group');

        function updateStockInfo() {
            const selectedOption = productoSelect.options[productoSelect.selectedIndex];
            if (selectedOption.value && tipoSelect.value) {
                const stock = selectedOption.dataset.stock;
                const unidad = selectedOption.dataset.unidad;

                if (tipoSelect.value === 'salida') {
                    stockInfo.textContent = `Stock disponible: ${stock} ${unidad}`;
                    stockInfo.style.color = '#059669';
                    cantidadInput.max = stock;
                    destinoGroup.style.display = 'flex';
                } else if (tipoSelect.value === 'ajuste') {
                    stockInfo.textContent = `Stock actual: ${stock} ${unidad}. Ingrese el nuevo stock total.`;
                    stockInfo.style.color = '#0284c7';
                    cantidadInput.removeAttribute('max');
                    destinoGroup.style.display = 'none';
                } else {
                    stockInfo.textContent = `Stock actual: ${stock} ${unidad}`;
                    stockInfo.style.color = '#64748b';
                    cantidadInput.removeAttribute('max');
                    destinoGroup.style.display = 'none';
                }
            } else {
                stockInfo.textContent = '';
                destinoGroup.style.display = 'none';
            }
        }

        productoSelect.addEventListener('change', updateStockInfo);
        tipoSelect.addEventListener('change', updateStockInfo);

        updateStockInfo();
    });
</script>
@endsection
