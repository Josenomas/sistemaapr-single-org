@extends('layouts.app')

@section('title', 'Editar Configuración Tarifaria')

@section('styles')
<style>
    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .form-title {
        font-size: 1.75rem;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        margin: 0;
    }

    .form-title i {
        color: var(--primary);
    }

    .form-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 32px;
        border: 1px solid var(--gray-200);
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--dark);
        font-weight: 600;
        font-size: 0.875rem;
    }

    .form-group .required {
        color: var(--danger);
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-control.error {
        border-color: var(--danger);
    }

    .error-message {
        color: var(--danger);
        font-size: 0.75rem;
        margin-top: 4px;
    }

    .form-help {
        font-size: 0.75rem;
        color: var(--gray-500);
        margin-top: 4px;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .checkbox-group input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-200);
    }

    .btn {
        padding: 12px 24px;
        border-radius: var(--radius);
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        font-size: 0.875rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .btn-secondary {
        background: var(--gray-100);
        color: var(--dark);
    }

    .btn-secondary:hover {
        background: var(--gray-200);
    }
</style>
@endsection

@section('content')
<div class="form-container">
    <div class="form-header">
        <h2 class="form-title">
            <i class="fas fa-edit"></i>
            Editar Configuración Tarifaria
        </h2>
    </div>

    <div class="form-card">
        <form action="{{ route('configuraciones-tarifas.update', $tarifa->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label for="tipo_cliente">
                        Tipo de Cliente <span class="required">*</span>
                    </label>
                    <select
                        id="tipo_cliente"
                        name="tipo_cliente"
                        class="form-control @error('tipo_cliente') error @enderror"
                        required
                    >
                        <option value="">Seleccione tipo de cliente</option>
                        <option value="residencial" {{ old('tipo_cliente', $tarifa->tipo_cliente) == 'residencial' ? 'selected' : '' }}>Residencial</option>
                        <option value="comercial" {{ old('tipo_cliente', $tarifa->tipo_cliente) == 'comercial' ? 'selected' : '' }}>Comercial</option>
                        <option value="industrial" {{ old('tipo_cliente', $tarifa->tipo_cliente) == 'industrial' ? 'selected' : '' }}>Industrial</option>
                    </select>
                    @error('tipo_cliente')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-help">Tipo de cliente al que aplica esta tarifa</div>
                </div>

                <div class="form-group">
                    <label for="nombre_tarifa">
                        Nombre de Tarifa <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="nombre_tarifa"
                        name="nombre_tarifa"
                        class="form-control @error('nombre_tarifa') error @enderror"
                        value="{{ old('nombre_tarifa', $tarifa->nombre_tarifa) }}"
                        placeholder="Ej: Tarifa Residencial 2025"
                        required
                    >
                    @error('nombre_tarifa')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-help">Nombre para agrupar los tramos de esta tarifa</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="nombre">
                        Nombre del Tramo <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        class="form-control @error('nombre') error @enderror"
                        value="{{ old('nombre', $tarifa->nombre) }}"
                        placeholder="Ej: Tramo 1 (0-10 m³)"
                        required
                    >
                    @error('nombre')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-help">Nombre descriptivo del tramo</div>
                </div>

                <div class="form-group">
                    <label for="orden">
                        Orden <span class="required">*</span>
                    </label>
                    <input
                        type="number"
                        id="orden"
                        name="orden"
                        class="form-control @error('orden') error @enderror"
                        value="{{ old('orden', $tarifa->orden) }}"
                        min="1"
                        required
                    >
                    @error('orden')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-help">Orden de aplicación del tramo</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="consumo_desde">
                        Consumo Desde (m³) <span class="required">*</span>
                    </label>
                    <input
                        type="number"
                        id="consumo_desde"
                        name="consumo_desde"
                        class="form-control @error('consumo_desde') error @enderror"
                        value="{{ old('consumo_desde', $tarifa->consumo_desde) }}"
                        step="0.01"
                        min="0"
                        required
                    >
                    @error('consumo_desde')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-help">Límite inferior del rango de consumo</div>
                </div>

                <div class="form-group">
                    <label for="consumo_hasta">
                        Consumo Hasta (m³)
                    </label>
                    <input
                        type="number"
                        id="consumo_hasta"
                        name="consumo_hasta"
                        class="form-control @error('consumo_hasta') error @enderror"
                        value="{{ old('consumo_hasta', $tarifa->consumo_hasta) }}"
                        step="0.01"
                        min="0"
                        placeholder="Dejar vacío para sin límite"
                    >
                    @error('consumo_hasta')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-help">Límite superior del rango (vacío = sin límite)</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="monto">
                        Monto ($) <span class="required">*</span>
                    </label>
                    <input
                        type="number"
                        id="monto"
                        name="monto"
                        class="form-control @error('monto') error @enderror"
                        value="{{ old('monto', $tarifa->monto) }}"
                        step="0.01"
                        min="0"
                        placeholder="Ej: 870"
                        required
                    >
                    @error('monto')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-help">Monto a cobrar en este tramo</div>
                </div>

                <div class="form-group">
                    <label for="cargo_fijo">
                        Cargo Fijo ($)
                    </label>
                    <input
                        type="number"
                        id="cargo_fijo"
                        name="cargo_fijo"
                        class="form-control @error('cargo_fijo') error @enderror"
                        value="{{ old('cargo_fijo', $tarifa->cargo_fijo) }}"
                        step="0.01"
                        min="0"
                        placeholder="Ej: 0"
                    >
                    @error('cargo_fijo')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-help">Cargo fijo adicional al monto del tramo</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="iva">
                        IVA (%)
                    </label>
                    <input
                        type="number"
                        id="iva"
                        name="iva"
                        class="form-control @error('iva') error @enderror"
                        value="{{ old('iva', $tarifa->iva) }}"
                        step="0.01"
                        min="0"
                        max="100"
                        placeholder="Ej: 19"
                    >
                    @error('iva')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-help">Porcentaje de IVA aplicado al subtotal</div>
                </div>

                <div class="form-group">
                    <label>Estado</label>
                    <div class="checkbox-group">
                        <input
                            type="checkbox"
                            id="activo"
                            name="activo"
                            value="1"
                            {{ old('activo', $tarifa->activo) ? 'checked' : '' }}
                        >
                        <label for="activo" style="margin: 0; font-weight: normal;">Activo</label>
                    </div>
                    <div class="form-help">Solo los tramos activos se usan para calcular boletas</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="vigente_desde">
                        Vigente Desde <span class="required">*</span>
                    </label>
                    <input
                        type="date"
                        id="vigente_desde"
                        name="vigente_desde"
                        class="form-control @error('vigente_desde') error @enderror"
                        value="{{ old('vigente_desde', $tarifa->vigente_desde ? $tarifa->vigente_desde->format('Y-m-d') : date('Y-m-d')) }}"
                        required
                    >
                    @error('vigente_desde')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-help">Fecha desde la cual esta tarifa está vigente</div>
                </div>

                <div class="form-group">
                    <label for="vigente_hasta">
                        Vigente Hasta
                    </label>
                    <input
                        type="date"
                        id="vigente_hasta"
                        name="vigente_hasta"
                        class="form-control @error('vigente_hasta') error @enderror"
                        value="{{ old('vigente_hasta', $tarifa->vigente_hasta ? $tarifa->vigente_hasta->format('Y-m-d') : '') }}"
                    >
                    @error('vigente_hasta')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-help">Fecha hasta la cual está vigente (dejar vacío si no tiene fin)</div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('configuraciones-tarifas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Actualizar Configuración
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
