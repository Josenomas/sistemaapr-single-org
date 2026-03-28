@extends('layouts.app')

@section('title', 'Nueva Noticia - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-plus-circle"></i>
        Nueva Noticia
    </h2>
    <a href="{{ route('noticias.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Noticia</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('noticias.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-row">
                <!-- Título -->
                <div class="form-group col-md-8">
                    <label for="titulo" class="form-label required">Título</label>
                    <input type="text"
                           class="form-control @error('titulo') is-invalid @enderror"
                           id="titulo"
                           name="titulo"
                           value="{{ old('titulo') }}"
                           placeholder="Ingrese el título de la noticia"
                           required
                           autofocus>
                    @error('titulo')
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
                        <option value="borrador" {{ old('estado', 'borrador') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                        <option value="publicada" {{ old('estado') == 'publicada' ? 'selected' : '' }}>Publicada</option>
                        <option value="archivada" {{ old('estado') == 'archivada' ? 'selected' : '' }}>Archivada</option>
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Categoría -->
                <div class="form-group col-md-4">
                    <label for="categoria" class="form-label">Categoría</label>
                    <select class="form-control @error('categoria') is-invalid @enderror"
                            id="categoria"
                            name="categoria">
                        <option value="">Sin categoría</option>
                        <option value="Aviso" {{ old('categoria') == 'Aviso' ? 'selected' : '' }}>Aviso</option>
                        <option value="Evento" {{ old('categoria') == 'Evento' ? 'selected' : '' }}>Evento</option>
                        <option value="Mantenimiento" {{ old('categoria') == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                        <option value="Corte" {{ old('categoria') == 'Corte' ? 'selected' : '' }}>Corte</option>
                        <option value="Reunión" {{ old('categoria') == 'Reunión' ? 'selected' : '' }}>Reunión</option>
                        <option value="Otro" {{ old('categoria') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('categoria')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Fecha de Publicación -->
                <div class="form-group col-md-4">
                    <label for="fecha_publicacion" class="form-label">Fecha de Publicación</label>
                    <input type="date"
                           class="form-control @error('fecha_publicacion') is-invalid @enderror"
                           id="fecha_publicacion"
                           name="fecha_publicacion"
                           value="{{ old('fecha_publicacion', date('Y-m-d')) }}">
                    @error('fecha_publicacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Destacada -->
                <div class="form-group col-md-4">
                    <label class="form-label">Opciones</label>
                    <div class="checkbox-wrapper">
                        <label class="checkbox-item">
                            <input type="checkbox" name="destacada" value="1" {{ old('destacada') ? 'checked' : '' }}>
                            <span><i class="fas fa-star text-warning"></i> Marcar como destacada</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- Resumen -->
                <div class="form-group col-md-12">
                    <label for="resumen" class="form-label">Resumen breve</label>
                    <textarea class="form-control @error('resumen') is-invalid @enderror"
                              id="resumen"
                              name="resumen"
                              rows="2"
                              placeholder="Descripción corta que aparecerá en el listado (máximo 500 caracteres)">{{ old('resumen') }}</textarea>
                    @error('resumen')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text">Máximo 500 caracteres</small>
                </div>
            </div>

            <div class="form-row">
                <!-- Contenido -->
                <div class="form-group col-md-12">
                    <label for="contenido" class="form-label required">Contenido</label>
                    <textarea class="form-control @error('contenido') is-invalid @enderror"
                              id="contenido"
                              name="contenido"
                              rows="12"
                              placeholder="Escriba el contenido completo de la noticia"
                              required>{{ old('contenido') }}</textarea>
                    @error('contenido')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Imagen Destacada -->
                <div class="form-group col-md-12">
                    <label for="imagen_destacada" class="form-label">Imagen Destacada</label>
                    <input type="file"
                           class="form-control @error('imagen_destacada') is-invalid @enderror"
                           id="imagen_destacada"
                           name="imagen_destacada"
                           accept="image/*">
                    @error('imagen_destacada')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text">JPG, PNG, GIF (máx. 2MB)</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Crear Noticia
                </button>
                <a href="{{ route('noticias.index') }}" class="btn btn-secondary">
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

    .form-text {
        color: var(--gray-500);
        font-size: 0.8rem;
        margin-top: 4px;
    }

    .checkbox-wrapper {
        padding: 10px 0;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 8px;
        border-radius: var(--radius);
    }

    .checkbox-item input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .checkbox-item span {
        font-weight: 500;
        color: var(--gray-700);
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
    }

    .col-md-4 {
        grid-column: span 1;
    }

    .col-md-8 {
        grid-column: span 2;
    }

    .col-md-12 {
        grid-column: 1 / -1;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .col-md-4,
        .col-md-8,
        .col-md-12 {
            grid-column: span 1;
        }
    }
</style>
@endsection
