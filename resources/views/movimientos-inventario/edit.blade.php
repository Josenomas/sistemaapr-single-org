@extends('layouts.app')

@section('title', 'Editar Movimiento - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-edit"></i>
        Editar Movimiento: {{ $movimiento->numero_movimiento }}
    </h2>
    <a href="{{ route('movimientos-inventario.show', $movimiento->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Movimiento</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('movimientos-inventario.update', $movimiento->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-row">
                <!-- Producto (No Editable) -->
                <div class="form-group col-md-4">
                    <label for="producto" class="form-label">Producto</label>
                    <input type="text"
                           class="form-control"
                           id="producto"
                           value="{{ $movimiento->producto->nombre }}"
                           disabled>
                    <small class="text-muted">No se puede modificar</small>
                </div>

                <!-- Tipo de Movimiento (No Editable) -->
                <div class="form-group col-md-4">
                    <label for="tipo_movimiento" class="form-label">Tipo de Movimiento</label>
                    <input type="text"
                           class="form-control"
                           id="tipo_movimiento"
                           value="{{ ucfirst($movimiento->tipo_movimiento) }}"
                           disabled>
                    <small class="text-muted">No se puede modificar</small>
                </div>

                <!-- Cantidad (No Editable) -->
                <div class="form-group col-md-4">
                    <label for="cantidad" class="form-label">Cantidad</label>
                    <input type="text"
                           class="form-control"
                           id="cantidad"
                           value="{{ number_format($movimiento->cantidad, 2) }}"
                           disabled>
                    <small class="text-muted">No se puede modificar</small>
                </div>
            </div>

            <div class="form-row">
                <!-- Motivo -->
                <div class="form-group col-md-4">
                    <label for="motivo" class="form-label">Motivo</label>
                    <input type="text"
                           class="form-control @error('motivo') is-invalid @enderror"
                           id="motivo"
                           name="motivo"
                           value="{{ old('motivo', $movimiento->motivo) }}">
                    @error('motivo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Destino -->
                <div class="form-group col-md-4">
                    <label for="destino" class="form-label">Destino</label>
                    <input type="text"
                           class="form-control @error('destino') is-invalid @enderror"
                           id="destino"
                           name="destino"
                           value="{{ old('destino', $movimiento->destino) }}">
                    @error('destino')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Documento de Referencia -->
                <div class="form-group col-md-4">
                    <label for="documento_referencia" class="form-label">Documento de Referencia</label>
                    <input type="text"
                           class="form-control @error('documento_referencia') is-invalid @enderror"
                           id="documento_referencia"
                           name="documento_referencia"
                           value="{{ old('documento_referencia', $movimiento->documento_referencia) }}">
                    @error('documento_referencia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Fecha del Movimiento -->
                <div class="form-group col-md-6">
                    <label for="fecha_movimiento" class="form-label">Fecha del Movimiento</label>
                    <input type="date"
                           class="form-control @error('fecha_movimiento') is-invalid @enderror"
                           id="fecha_movimiento"
                           name="fecha_movimiento"
                           value="{{ old('fecha_movimiento', date('Y-m-d', strtotime($movimiento->fecha_movimiento))) }}">
                    @error('fecha_movimiento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Responsable -->
                <div class="form-group col-md-6">
                    <label for="id_responsable" class="form-label">Responsable</label>
                    <select class="form-control @error('id_responsable') is-invalid @enderror"
                            id="id_responsable"
                            name="id_responsable">
                        <option value="">Seleccionar responsable</option>
                        @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}" {{ old('id_responsable', $movimiento->id_responsable) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
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
                          rows="3">{{ old('descripcion', $movimiento->descripcion) }}</textarea>
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
                          rows="3">{{ old('observaciones', $movimiento->observaciones) }}</textarea>
                @error('observaciones')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Actualizar Movimiento
                </button>
                <a href="{{ route('movimientos-inventario.show', $movimiento->id) }}" class="btn btn-secondary">
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

    .col-md-3 {
        grid-column: span 1;
    }

    .col-md-4 {
        grid-column: span 1;
    }

    .col-md-6 {
        grid-column: span 1;
    }

    .col-md-8 {
        grid-column: span 2;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .col-md-3,
        .col-md-4,
        .col-md-6,
        .col-md-8 {
            grid-column: span 1;
        }
    }
</style>
@endsection
