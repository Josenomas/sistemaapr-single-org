@extends('layouts.app')

@section('title', 'Importar Lecturas - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-file-upload"></i>
        Importar Lecturas Masivamente
    </h2>
    <a href="{{ route('lecturas.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cargar archivo CSV</h3>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
                @if(session('errores'))
                    <ul style="margin-top: 10px; margin-bottom: 0;">
                        @foreach(session('errores') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <div>
                <h4>Instrucciones:</h4>
                <ol>
                    <li>Descarga la plantilla CSV haciendo clic en el botón de abajo</li>
                    <li>Completa el archivo con tus datos (puedes usar Excel o Google Sheets)</li>
                    <li>Guarda el archivo como CSV (separado por comas)</li>
                    <li>Sube el archivo aquí</li>
                </ol>
                <p><strong>Formato del archivo:</strong></p>
                <ul>
                    <li><strong>numero_socio:</strong> Número del socio (ej: SOC-0001)</li>
                    <li><strong>mes:</strong> Formato YYYY-MM (ej: 2025-01)</li>
                    <li><strong>lectura_actual:</strong> Lectura en m³ (ej: 120.50)</li>
                    <li><strong>fecha_lectura:</strong> Formato DD/MM/YYYY (ej: 15/01/2025)</li>
                </ul>
            </div>
        </div>

        <div class="download-template">
            <a href="{{ route('lecturas.importar.plantilla') }}" class="btn btn-success">
                <i class="fas fa-download"></i>
                Descargar Plantilla CSV
            </a>
        </div>

        <form action="{{ route('lecturas.importar.procesar') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="archivo" class="form-label required">Archivo CSV</label>
                <input type="file"
                       class="form-control @error('archivo') is-invalid @enderror"
                       id="archivo"
                       name="archivo"
                       accept=".csv,.txt"
                       required>
                @error('archivo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text">Tamaño máximo: 2MB. Formatos permitidos: .csv, .txt</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i>
                    Cargar y Validar Archivo
                </button>
                <a href="{{ route('lecturas.index') }}" class="btn btn-secondary">
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

    .info-box {
        background: #e3f2fd;
        border-left: 4px solid #2196f3;
        padding: 16px 20px;
        margin-bottom: 24px;
        border-radius: 4px;
        display: flex;
        gap: 16px;
    }

    .info-box i {
        color: #2196f3;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .info-box h4 {
        margin: 0 0 8px 0;
        font-size: 1.1rem;
        color: #1976d2;
    }

    .info-box ol,
    .info-box ul {
        margin: 8px 0;
        padding-left: 20px;
    }

    .info-box li {
        margin: 4px 0;
        color: #0d47a1;
    }

    .download-template {
        margin-bottom: 32px;
        text-align: center;
        padding: 24px;
        background: var(--gray-50);
        border-radius: var(--radius);
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
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
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-text {
        display: block;
        margin-top: 4px;
        font-size: 0.8rem;
        color: var(--gray-500);
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
        background: linear-gradient(135deg, var(--primary), #1e40af);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-success {
        background: linear-gradient(135deg, var(--success), #059669);
        color: white;
    }

    .btn-success:hover {
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

    .alert {
        padding: 16px 20px;
        border-radius: var(--radius);
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .alert-danger i {
        color: #ef4444;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .alert ul {
        margin: 0;
        padding-left: 20px;
    }

    .alert li {
        margin: 4px 0;
    }
</style>
@endsection
