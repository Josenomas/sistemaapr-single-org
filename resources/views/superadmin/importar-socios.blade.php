@extends('layouts.superadmin')

@section('title', 'Importar Socios - ' . $organizacion->nombre_apr)

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-file-excel"></i>
        Importar Socios Masivamente
    </h1>
    <div class="breadcrumb">
        <a href="{{ route('superadmin.organizaciones') }}">Organizaciones</a>
        <span> / </span>
        <a href="{{ route('superadmin.organizacion.ver', $organizacion->id) }}">{{ $organizacion->nombre_apr }}</a>
        <span> / </span>
        <span>Importar Socios</span>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}

    @if(session('errores') && count(session('errores')) > 0)
        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(0,0,0,0.1);">
            <p style="margin-bottom: 10px;"><strong>¿Importación con errores?</strong> Puedes eliminar todos los socios de esta organización y volver a intentar:</p>
            @php
                $totalSocios = \App\Models\Socio::where('id_organizacion', $organizacion->id)->count();
            @endphp
            <form action="{{ route('superadmin.organizacion.eliminar-todos-socios', $organizacion->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('⚠️ ¿Eliminar TODOS los {{ $totalSocios }} socios de esta organización? Esta acción NO se puede deshacer.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash-alt"></i>
                    Eliminar todos los socios de esta organización
                </button>
            </form>
        </div>
    @endif
</div>

@if(session('errores') && count(session('errores')) > 0)
    <div class="alert alert-warning">
        <h4><i class="fas fa-exclamation-triangle"></i> Advertencias durante la importación:</h4>
        <ul style="margin: 10px 0 0 20px;">
            @foreach(array_slice(session('errores'), 0, 10) as $error)
                <li>{{ $error }}</li>
            @endforeach
            @if(count(session('errores')) > 10)
                <li><em>... y {{ count(session('errores')) - 10 }} errores más</em></li>
            @endif
        </ul>
    </div>
@endif
@endif

@if(session('error'))
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    {{ session('error') }}
</div>
@endif

<!-- Información de la Organización -->
<div class="card mb-4">
    <div class="card-header">
        <h3><i class="fas fa-building"></i> Organización</h3>
    </div>
    <div class="card-body">
        <div class="org-info">
            <div class="info-item">
                <span class="label">Nombre:</span>
                <span class="value">{{ $organizacion->nombre_apr }}</span>
            </div>
            <div class="info-item">
                <span class="label">Slug:</span>
                <span class="value">{{ $organizacion->slug }}</span>
            </div>
            <div class="info-item">
                <span class="label">Plan:</span>
                <span class="value"><span class="badge bg-primary">{{ $organizacion->suscripcion->nombre }}</span></span>
            </div>
        </div>
    </div>
</div>

<!-- Instrucciones -->
<div class="card mb-4">
    <div class="card-header">
        <h3><i class="fas fa-info-circle"></i> Instrucciones de Importación</h3>
    </div>
    <div class="card-body">
        <ol class="instructions">
            <li>
                <strong>Descarga la plantilla de Excel</strong>
                <p>Haz clic en el botón de abajo para descargar la plantilla con los campos requeridos y ejemplos.</p>
                <a href="{{ route('superadmin.importar-socios.plantilla') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-download"></i> Descargar Plantilla Excel
                </a>
            </li>
            <li>
                <strong>Llena la plantilla con los datos de los socios</strong>
                <p>Completa todas las filas con la información de cada socio. Los campos obligatorios son: RUT, Nombre y Apellido Paterno.</p>
            </li>
            <li>
                <strong>Sube el archivo Excel</strong>
                <p>Una vez completada la plantilla, súbela usando el formulario de abajo.</p>
            </li>
        </ol>

        <div class="info-box">
            <h4><i class="fas fa-list-check"></i> Campos Disponibles</h4>
            <div class="campos-grid">
                <div class="campo">
                    <strong>rut</strong> <span class="required">*</span>
                    <small>Ej: 12345678-9</small>
                </div>
                <div class="campo">
                    <strong>nombre</strong> <span class="required">*</span>
                    <small>Ej: Juan</small>
                </div>
                <div class="campo">
                    <strong>apellido_paterno</strong> <span class="required">*</span>
                    <small>Ej: Pérez</small>
                </div>
                <div class="campo">
                    <strong>apellido_materno</strong>
                    <small>Ej: González</small>
                </div>
                <div class="campo">
                    <strong>email</strong>
                    <small>Ej: juan@email.com</small>
                </div>
                <div class="campo">
                    <strong>telefono</strong>
                    <small>Ej: +56912345678</small>
                </div>
                <div class="campo">
                    <strong>direccion</strong>
                    <small>Ej: Av. Principal 123</small>
                </div>
                <div class="campo">
                    <strong>comuna</strong>
                    <small>Ej: Santiago</small>
                </div>
                <div class="campo">
                    <strong>region</strong>
                    <small>Ej: Metropolitana</small>
                </div>
                <div class="campo">
                    <strong>numero_medidor</strong>
                    <small>Ej: MED-001</small>
                </div>
                <div class="campo">
                    <strong>sector</strong>
                    <small>Ej: Sector A</small>
                </div>
                <div class="campo">
                    <strong>rol_avaluo</strong>
                    <small>Ej: 12345-001</small>
                </div>
                <div class="campo">
                    <strong>estado</strong>
                    <small>activo/suspendido</small>
                </div>
                <div class="campo">
                    <strong>exento_iva</strong>
                    <small>si/no</small>
                </div>
            </div>
            <p class="mt-3"><span class="required">*</span> = Campo obligatorio</p>
        </div>
    </div>
</div>

<!-- Formulario de Importación -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-upload"></i> Subir Archivo Excel</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('superadmin.importar-socios.procesar', $organizacion->id) }}"
              method="POST"
              enctype="multipart/form-data"
              id="formImportar">
            @csrf

            <div class="upload-area" id="uploadArea">
                <i class="fas fa-file-excel"></i>
                <h4>Arrastra tu archivo Excel aquí</h4>
                <p>o haz clic para seleccionar</p>
                <p class="file-types">Formatos aceptados: .xlsx, .xls, .csv (Máx. 10MB)</p>
                <input type="file"
                       name="archivo"
                       id="archivo"
                       accept=".xlsx,.xls,.csv"
                       required
                       style="display: none;">
                <div class="file-info" id="fileInfo" style="display: none;">
                    <i class="fas fa-file-excel text-success"></i>
                    <span id="fileName"></span>
                    <button type="button" class="btn-remove" onclick="removeFile()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            @error('archivo')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <div class="form-actions">
                <a href="{{ route('superadmin.organizacion.ver', $organizacion->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary" id="btnImportar">
                    <i class="fas fa-upload"></i> Importar Socios
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .breadcrumb {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-top: 8px;
    }

    .breadcrumb a {
        color: var(--primary);
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .org-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }

    .info-item {
        display: flex;
        gap: 8px;
    }

    .info-item .label {
        font-weight: 600;
        color: var(--text-muted);
        min-width: 60px;
    }

    .info-item .value {
        color: var(--text-light);
    }

    .instructions {
        padding-left: 20px;
        color: var(--text-light);
    }

    .instructions li {
        margin-bottom: 20px;
    }

    .instructions strong {
        color: var(--text-light);
        font-size: 1.05rem;
    }

    .instructions p {
        margin: 8px 0;
        color: var(--text-muted);
    }

    .info-box {
        background: rgba(124, 58, 237, 0.1);
        border-left: 4px solid var(--primary);
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .info-box h4 {
        color: var(--text-light);
        margin: 0 0 16px 0;
        font-size: 1.05rem;
    }

    .campos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
        margin-bottom: 12px;
    }

    .campo {
        padding: 8px;
        background: var(--dark-card);
        border-radius: 6px;
    }

    .campo strong {
        color: var(--text-light);
        display: block;
        margin-bottom: 4px;
    }

    .campo small {
        color: var(--text-muted);
        font-size: 0.8rem;
    }

    .required {
        color: #ef4444;
        font-weight: bold;
    }

    .upload-area {
        border: 2px dashed var(--border);
        border-radius: 12px;
        padding: 60px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: var(--dark-lighter);
    }

    .upload-area:hover {
        border-color: var(--primary);
        background: rgba(124, 58, 237, 0.05);
    }

    .upload-area.drag-over {
        border-color: var(--primary);
        background: rgba(124, 58, 237, 0.1);
    }

    .upload-area i.fa-file-excel {
        font-size: 4rem;
        color: var(--primary);
        margin-bottom: 16px;
    }

    .upload-area h4 {
        color: var(--text-light);
        margin: 0 0 8px 0;
    }

    .upload-area p {
        color: var(--text-muted);
        margin: 4px 0;
    }

    .upload-area .file-types {
        font-size: 0.85rem;
        margin-top: 12px;
    }

    .file-info {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 20px;
        background: rgba(16, 185, 129, 0.1);
        border-radius: 8px;
        margin-top: 20px;
    }

    .file-info i {
        font-size: 2rem;
    }

    .file-info span {
        font-weight: 600;
        color: var(--text-light);
    }

    .btn-remove {
        background: var(--danger);
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.875rem;
    }

    .btn-remove:hover {
        background: #dc2626;
    }

    .error-message {
        color: #ef4444;
        margin-top: 12px;
        font-size: 0.875rem;
    }

    .form-actions {
        margin-top: 24px;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .alert-warning {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .alert h4 {
        margin: 0 0 8px 0;
    }

    .alert ul {
        margin: 0;
    }

    @media (max-width: 768px) {
        .campos-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .form-actions .btn {
            width: 100%;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('archivo');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const btnImportar = document.getElementById('btnImportar');

    // Click para seleccionar archivo
    uploadArea.addEventListener('click', (e) => {
        if (!e.target.classList.contains('btn-remove') && e.target.tagName !== 'I') {
            fileInput.click();
        }
    });

    // Cuando se selecciona un archivo
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            showFile(this.files[0]);
        }
    });

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('drag-over');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('drag-over');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');

        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            showFile(e.dataTransfer.files[0]);
        }
    });

    function showFile(file) {
        fileName.textContent = file.name;
        fileInfo.style.display = 'flex';
        uploadArea.querySelector('i.fa-file-excel').style.display = 'none';
        uploadArea.querySelector('h4').style.display = 'none';
        uploadArea.querySelectorAll('p').forEach(p => p.style.display = 'none');
    }

    // Validación antes de enviar
    document.getElementById('formImportar').addEventListener('submit', function() {
        btnImportar.disabled = true;
        btnImportar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importando...';
    });
});

function removeFile() {
    document.getElementById('archivo').value = '';
    document.getElementById('fileInfo').style.display = 'none';
    const uploadArea = document.getElementById('uploadArea');
    uploadArea.querySelector('i.fa-file-excel').style.display = 'block';
    uploadArea.querySelector('h4').style.display = 'block';
    uploadArea.querySelectorAll('p').forEach(p => p.style.display = 'block');
}
</script>
@endsection
