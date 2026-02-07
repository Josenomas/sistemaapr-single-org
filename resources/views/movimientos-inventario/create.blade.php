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
        <form action="{{ route('movimientos-inventario.store') }}" method="POST" id="form-movimiento">
            @csrf

            <!-- Información General del Movimiento -->
            <div class="seccion-header">
                <h3><i class="fas fa-info-circle"></i> Información General</h3>
            </div>

            <div class="form-row">
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
                              rows="2"
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

            <!-- Productos -->
            <div class="seccion-header" style="margin-top: 30px;">
                <h3><i class="fas fa-box"></i> Productos</h3>
                <button type="button" class="btn btn-success btn-sm" id="btn-agregar-producto">
                    <i class="fas fa-plus"></i> Agregar Producto
                </button>
            </div>

            <div id="productos-container">
                <!-- Los productos se agregarán aquí dinámicamente -->
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

    .seccion-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        background: linear-gradient(90deg, #f1f5f9 0%, #ffffff 100%);
        border-left: 4px solid var(--primary);
        border-radius: 6px;
        margin-bottom: 20px;
    }
    .seccion-header h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .seccion-header i { color: var(--primary); }

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
    .btn-success { background: #10b981; color: white; }
    .btn-success:hover { background: #059669; }
    .btn-danger { background: #ef4444; color: white; }
    .btn-danger:hover { background: #dc2626; }
    .btn-sm { padding: 8px 16px; font-size: 0.8rem; }

    /* Productos */
    .producto-item {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        position: relative;
    }
    .producto-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    .producto-item-numero {
        font-weight: 700;
        color: var(--primary);
        font-size: 1rem;
    }
    .producto-item-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 15px;
    }
    .stock-info-box {
        background: #eff6ff;
        border: 1px solid #3b82f6;
        border-radius: 6px;
        padding: 10px;
        margin-top: 8px;
        font-size: 0.85rem;
        color: #1e40af;
        font-weight: 600;
    }

    #productos-container:empty::before {
        content: 'No hay productos agregados. Haga clic en "Agregar Producto" para comenzar.';
        display: block;
        text-align: center;
        padding: 40px;
        color: var(--gray-500);
        font-style: italic;
        background: #f8fafc;
        border: 2px dashed #e2e8f0;
        border-radius: 8px;
    }
</style>
@endsection

@section('scripts')
<script>
    // Datos de productos desde PHP
    const productosData = {!! json_encode($productos->map(function($p) {
        return [
            'id' => $p->id,
            'nombre' => $p->nombre,
            'stock' => $p->cantidad_actual,
            'unidad' => $p->unidad_medida
        ];
    })) !!};

    let productoCounter = 0;

    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipo_movimiento');
        const destinoGroup = document.getElementById('destino-group');
        const btnAgregar = document.getElementById('btn-agregar-producto');
        const container = document.getElementById('productos-container');

        // Mostrar/ocultar destino según tipo
        tipoSelect.addEventListener('change', function() {
            if (this.value === 'salida') {
                destinoGroup.style.display = 'flex';
            } else {
                destinoGroup.style.display = 'none';
            }

            // Actualizar info de stock en todos los productos
            actualizarTodosLosStocks();
        });

        // Agregar primer producto al cargar
        agregarProducto();

        // Botón agregar producto
        btnAgregar.addEventListener('click', function() {
            agregarProducto();
        });

        function agregarProducto() {
            productoCounter++;

            const productoHTML = `
                <div class="producto-item" data-index="${productoCounter}">
                    <div class="producto-item-header">
                        <span class="producto-item-numero">Producto #${productoCounter}</span>
                        <button type="button" class="btn btn-danger btn-sm btn-eliminar-producto">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                    <div class="producto-item-grid">
                        <div class="form-group">
                            <label>Producto: *</label>
                            <select name="productos[${productoCounter}][id_producto]" class="form-control producto-select" required>
                                <option value="">Seleccione un producto</option>
                                ${productosData.map(p => `
                                    <option value="${p.id}" data-stock="${p.stock}" data-unidad="${p.unidad}">
                                        ${p.nombre} - Stock: ${p.stock} ${p.unidad}
                                    </option>
                                `).join('')}
                            </select>
                            <div class="stock-info-box" style="display: none;"></div>
                        </div>
                        <div class="form-group">
                            <label>Cantidad: *</label>
                            <input type="number"
                                   name="productos[${productoCounter}][cantidad]"
                                   class="form-control cantidad-input"
                                   step="0.01"
                                   min="0.01"
                                   required>
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', productoHTML);

            // Agregar eventos al nuevo producto
            const nuevoProducto = container.lastElementChild;
            const selectProducto = nuevoProducto.querySelector('.producto-select');
            const btnEliminar = nuevoProducto.querySelector('.btn-eliminar-producto');

            selectProducto.addEventListener('change', function() {
                actualizarStockInfo(nuevoProducto);
            });

            btnEliminar.addEventListener('click', function() {
                if (container.children.length > 1) {
                    nuevoProducto.remove();
                    renumerarProductos();
                } else {
                    alert('Debe haber al menos un producto');
                }
            });
        }

        function actualizarStockInfo(productoItem) {
            const select = productoItem.querySelector('.producto-select');
            const cantidadInput = productoItem.querySelector('.cantidad-input');
            const stockBox = productoItem.querySelector('.stock-info-box');
            const tipo = tipoSelect.value;

            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption.value && tipo) {
                const stock = parseFloat(selectedOption.dataset.stock);
                const unidad = selectedOption.dataset.unidad;

                stockBox.style.display = 'block';

                if (tipo === 'salida') {
                    stockBox.textContent = `Stock disponible: ${stock} ${unidad}`;
                    stockBox.style.background = '#fef3c7';
                    stockBox.style.borderColor = '#f59e0b';
                    stockBox.style.color = '#92400e';
                    cantidadInput.max = stock;
                } else if (tipo === 'ajuste') {
                    stockBox.textContent = `Stock actual: ${stock} ${unidad}. Ingrese el nuevo stock total.`;
                    stockBox.style.background = '#dbeafe';
                    stockBox.style.borderColor = '#3b82f6';
                    stockBox.style.color = '#1e40af';
                    cantidadInput.removeAttribute('max');
                } else {
                    stockBox.textContent = `Stock actual: ${stock} ${unidad}`;
                    stockBox.style.background = '#d1fae5';
                    stockBox.style.borderColor = '#10b981';
                    stockBox.style.color = '#065f46';
                    cantidadInput.removeAttribute('max');
                }
            } else {
                stockBox.style.display = 'none';
            }
        }

        function actualizarTodosLosStocks() {
            const productos = container.querySelectorAll('.producto-item');
            productos.forEach(producto => {
                actualizarStockInfo(producto);
            });
        }

        function renumerarProductos() {
            const productos = container.querySelectorAll('.producto-item');
            productos.forEach((producto, index) => {
                producto.querySelector('.producto-item-numero').textContent = `Producto #${index + 1}`;
            });
        }

        // Validación antes de enviar
        document.getElementById('form-movimiento').addEventListener('submit', function(e) {
            if (container.children.length === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un producto');
                return false;
            }
        });

        // Inicializar destino
        if (tipoSelect.value === 'salida') {
            destinoGroup.style.display = 'flex';
        } else {
            destinoGroup.style.display = 'none';
        }
    });
</script>
@endsection
