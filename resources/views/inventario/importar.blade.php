@extends('layouts.app')

@section('title', 'Importar Inventario - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-file-excel"></i>
        Importar Productos Masivamente
    </h2>
    <div class="header-actions">
        <a href="{{ route('inventario.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver al Inventario
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('errores'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-times-circle"></i> Errores encontrados:</strong>
        <ul class="mb-0 mt-2">
            @foreach(session('errores') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-info-circle"></i>
            Instrucciones
        </h5>
    </div>
    <div class="card-body">
        <div class="instrucciones">
            <h6><i class="fas fa-clipboard-list"></i> Pasos para importar productos:</h6>
            <ol>
                <li>
                    <strong>Descarga la plantilla Excel</strong> usando el botón de abajo
                </li>
                <li>
                    <strong>Completa los datos de tus productos</strong> en el archivo:
                    <ul>
                        <li>Los campos con fondo <span class="badge-azul">AZUL</span> son obligatorios</li>
                        <li>Los campos con fondo <span class="badge-gris">GRIS</span> son opcionales</li>
                    </ul>
                </li>
                <li>
                    <strong>Campos requeridos:</strong>
                    <ul>
                        <li><code>Nombre</code> - Nombre del producto</li>
                        <li><code>Categoría</code> - materiales, equipos, herramientas, insumos, quimicos, repuestos, otro</li>
                        <li><code>Unidad de Medida</code> - unidad, kg, litros, metros, cajas, etc.</li>
                        <li><code>Cantidad Actual</code> - Stock actual del producto</li>
                        <li><code>Cantidad Mínima</code> - Stock mínimo antes de alerta</li>
                    </ul>
                </li>
                <li>
                    <strong>Guarda el archivo Excel</strong> en tu computador
                </li>
                <li>
                    <strong>Sube el archivo</strong> usando el formulario de abajo
                </li>
            </ol>

            <div class="alert alert-info mt-3">
                <i class="fas fa-lightbulb"></i>
                <strong>Nota:</strong> El código de producto se genera automáticamente. El sistema determinará el estado (disponible, bajo stock, agotado) basado en las cantidades ingresadas.
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('inventario.importar.plantilla') }}" class="btn btn-success btn-lg">
                <i class="fas fa-download"></i>
                Descargar Plantilla Excel
            </a>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-upload"></i>
            Subir Archivo con Productos
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('inventario.importar.procesar') }}" method="POST" enctype="multipart/form-data" id="formImportar">
            @csrf

            <div class="upload-zone" id="uploadZone">
                <input type="file" name="archivo" id="archivo" accept=".xlsx,.xls" required hidden>
                <label for="archivo" class="upload-label">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span class="upload-text">
                        Arrastra tu archivo Excel aquí<br>
                        o haz clic para seleccionar
                    </span>
                    <span class="upload-hint">Formatos aceptados: .xlsx, .xls (Máximo 10 MB)</span>
                </label>
                <div class="file-info" id="fileInfo" style="display: none;">
                    <i class="fas fa-file-excel text-success"></i>
                    <span id="fileName"></span>
                    <button type="button" class="btn-remove" id="btnRemove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            @error('archivo')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror

            <div class="form-actions mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-upload"></i>
                    Importar Productos
                </button>
                <a href="{{ route('inventario.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid var(--gray-200);
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--gray-800);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title i {
        color: #10b981;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .card {
        background: var(--white);
        border-radius: 8px;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        margin-bottom: 24px;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-title i {
        color: var(--primary);
    }

    .card-body {
        padding: 24px;
    }

    .instrucciones h6 {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .instrucciones ol {
        margin-left: 20px;
    }

    .instrucciones li {
        margin-bottom: 12px;
        line-height: 1.6;
    }

    .instrucciones ul {
        margin-top: 8px;
        margin-left: 20px;
    }

    .instrucciones code {
        background: var(--gray-100);
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.875rem;
        color: #dc2626;
    }

    .badge-azul {
        background: #dbeafe;
        color: #1e40af;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .badge-gris {
        background: #f3f4f6;
        color: #6b7280;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .upload-zone {
        border: 2px dashed var(--gray-300);
        border-radius: 12px;
        padding: 48px 24px;
        text-align: center;
        transition: all 0.3s;
        background: var(--gray-50);
    }

    .upload-zone.dragover {
        border-color: var(--primary);
        background: #eff6ff;
    }

    .upload-label {
        cursor: pointer;
        display: block;
    }

    .upload-label i {
        font-size: 3rem;
        color: var(--primary);
        margin-bottom: 16px;
        display: block;
    }

    .upload-text {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-700);
        display: block;
        margin-bottom: 8px;
    }

    .upload-hint {
        font-size: 0.875rem;
        color: var(--gray-500);
        display: block;
    }

    .file-info {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        font-size: 1.125rem;
    }

    .file-info i {
        font-size: 2rem;
    }

    .btn-remove {
        background: #ef4444;
        color: white;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-remove:hover {
        background: #dc2626;
        transform: scale(1.1);
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
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

    .btn-lg {
        padding: 12px 24px;
        font-size: 1rem;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--gray-500);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-600);
    }

    .btn-success {
        background: #10b981;
        color: white;
    }

    .btn-success:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .form-actions {
        display: flex;
        gap: 12px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-200);
    }

    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 24px;
        border: 1px solid;
        position: relative;
    }

    .alert-success {
        background: #d1fae5;
        border-color: #10b981;
        color: #065f46;
    }

    .alert-danger {
        background: #fee2e2;
        border-color: #ef4444;
        color: #991b1b;
    }

    .alert-warning {
        background: #fef3c7;
        border-color: #f59e0b;
        color: #92400e;
    }

    .alert-info {
        background: #dbeafe;
        border-color: #3b82f6;
        color: #1e40af;
    }

    .btn-close {
        position: absolute;
        top: 12px;
        right: 12px;
        background: transparent;
        border: none;
        font-size: 1.25rem;
        cursor: pointer;
        opacity: 0.5;
    }

    .btn-close:hover {
        opacity: 1;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-lg {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('archivo');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const uploadLabel = document.querySelector('.upload-label');
    const btnRemove = document.getElementById('btnRemove');

    // Drag & Drop
    uploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });

    uploadZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
    });

    uploadZone.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadZone.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            mostrarArchivo(files[0]);
        }
    });

    // Click para seleccionar archivo
    fileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            mostrarArchivo(e.target.files[0]);
        }
    });

    // Remover archivo
    btnRemove.addEventListener('click', function(e) {
        e.stopPropagation();
        fileInput.value = '';
        uploadLabel.style.display = 'block';
        fileInfo.style.display = 'none';
    });

    function mostrarArchivo(file) {
        fileName.textContent = file.name;
        uploadLabel.style.display = 'none';
        fileInfo.style.display = 'flex';
    }
});
</script>
@endsection
