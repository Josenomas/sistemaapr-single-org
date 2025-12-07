@extends('layouts.app')

@section('title', 'Registrar Corte de Suministro - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-plug"></i>
        Registrar Corte de Suministro
    </h2>
    <a href="{{ route('cortes.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Corte</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('cortes.store') }}">
            @csrf

            <div class="form-section">
                <h4 class="section-title">Datos del Socio</h4>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="id_socio" class="required">Socio</label>
                        <select id="id_socio" name="id_socio" class="form-control @error('id_socio') error @enderror" required>
                            <option value="">Seleccione un socio</option>
                            @foreach($socios as $socio)
                                <option value="{{ $socio->id }}" {{ old('id_socio') == $socio->id ? 'selected' : '' }}>
                                    {{ $socio->numero_socio }} - {{ $socio->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_socio')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">Información del Corte</h4>

                <div class="form-row">
                    <div class="form-group">
                        <label for="motivo" class="required">Motivo del Corte</label>
                        <select id="motivo" name="motivo" class="form-control @error('motivo') error @enderror" required>
                            <option value="">Seleccione un motivo</option>
                            <option value="morosidad" {{ old('motivo', 'morosidad') == 'morosidad' ? 'selected' : '' }}>Morosidad</option>
                            <option value="solicitud_socio" {{ old('motivo') == 'solicitud_socio' ? 'selected' : '' }}>Solicitud del Socio</option>
                            <option value="mantenimiento" {{ old('motivo') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            <option value="otro" {{ old('motivo') == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('motivo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="fecha_corte" class="required">Fecha de Corte</label>
                        <input type="date" id="fecha_corte" name="fecha_corte"
                               class="form-control @error('fecha_corte') error @enderror"
                               value="{{ old('fecha_corte', date('Y-m-d')) }}" required>
                        @error('fecha_corte')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="estado" class="required">Estado</label>
                        <select id="estado" name="estado" class="form-control @error('estado') error @enderror" required>
                            <option value="pendiente" {{ old('estado', 'pendiente') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="ejecutado" {{ old('estado') == 'ejecutado' ? 'selected' : '' }}>Ejecutado</option>
                            <option value="cancelado" {{ old('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                        @error('estado')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="monto_adeudado">Monto Adeudado ($)</label>
                        <input type="number" id="monto_adeudado" name="monto_adeudado"
                               class="form-control @error('monto_adeudado') error @enderror"
                               value="{{ old('monto_adeudado', 0) }}" min="0" step="0.01">
                        @error('monto_adeudado')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control @error('descripcion') error @enderror"
                                  rows="3">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">Información de Ejecución</h4>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="id_ejecutor">Funcionario Ejecutor</label>
                        <select id="id_ejecutor" name="id_ejecutor" class="form-control @error('id_ejecutor') error @enderror">
                            <option value="">Sin asignar</option>
                            @foreach($funcionarios as $funcionario)
                                <option value="{{ $funcionario->id }}" {{ old('id_ejecutor') == $funcionario->id ? 'selected' : '' }}>
                                    {{ $funcionario->nombre_completo }} - {{ $funcionario->cargo }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_ejecutor')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">Observaciones</h4>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="observaciones">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" class="form-control @error('observaciones') error @enderror"
                                  rows="4">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Registrar Corte
                </button>
                <a href="{{ route('cortes.index') }}" class="btn btn-secondary">
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
        border-bottom: 2px solid var(--gray-200);
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

    .form-section {
        margin-bottom: 32px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--gray-200);
    }

    .form-section:last-of-type {
        border-bottom: none;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 20px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--primary);
        display: inline-block;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row:last-child {
        margin-bottom: 0;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
    }

    .form-group label.required::after {
        content: ' *';
        color: var(--danger);
    }

    .form-control {
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .form-control.error {
        border-color: var(--danger);
    }

    .error-message {
        font-size: 0.75rem;
        color: var(--danger);
        margin-top: 4px;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid var(--gray-200);
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
        background: var(--gray-600);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-700);
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
