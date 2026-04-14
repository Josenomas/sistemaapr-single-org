@extends('layouts.app')

@section('title', 'Solicitar Pago Manual')

@section('styles')
<style>
    .page-header {
        margin-bottom: 24px;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0 0 8px 0;
    }

    .page-title i {
        color: var(--primary);
    }

    .breadcrumb {
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .breadcrumb a {
        color: var(--primary);
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        margin-bottom: 24px;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    .card-header h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-body {
        padding: 24px;
    }

    .info-box {
        background: #dbeafe;
        border-left: 4px solid var(--primary);
        padding: 16px 20px;
        border-radius: var(--radius);
        margin-bottom: 24px;
    }

    .info-box h4 {
        font-size: 1rem;
        font-weight: 600;
        color: #1e40af;
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-box p {
        margin: 0;
        color: #1e40af;
        font-size: 0.875rem;
        line-height: 1.6;
    }

    .info-box ul {
        margin: 8px 0 0 20px;
        color: #1e40af;
        font-size: 0.875rem;
    }

    .pago-detail {
        background: var(--gray-50);
        border-radius: var(--radius);
        padding: 20px;
        margin-bottom: 24px;
    }

    .pago-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .pago-detail-item label {
        display: block;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--gray-600);
        font-weight: 600;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }

    .pago-detail-item .value {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 8px;
        font-size: 0.875rem;
    }

    .form-group label .required {
        color: #ef4444;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    }

    .form-control.is-invalid {
        border-color: #ef4444;
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 4px;
        display: block;
    }

    .file-upload-box {
        border: 2px dashed var(--gray-300);
        border-radius: var(--radius);
        padding: 24px;
        text-align: center;
        transition: all 0.2s;
        cursor: pointer;
        background: var(--gray-50);
    }

    .file-upload-box:hover {
        border-color: var(--primary);
        background: #f5f3ff;
    }

    .file-upload-box.drag-over {
        border-color: var(--primary);
        background: #f5f3ff;
    }

    .file-upload-box i {
        font-size: 3rem;
        color: var(--gray-400);
        margin-bottom: 12px;
    }

    .file-upload-box p {
        margin: 0;
        color: var(--gray-600);
        font-size: 0.875rem;
    }

    .file-upload-box .file-name {
        margin-top: 12px;
        padding: 8px 16px;
        background: var(--primary);
        color: white;
        border-radius: var(--radius);
        display: inline-block;
        font-weight: 600;
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

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .btn-secondary {
        background: var(--gray-600);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-700);
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-200);
    }

    @media (max-width: 768px) {
        .pago-detail-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .form-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-file-invoice-dollar"></i>
        Solicitar Pago Manual
    </h2>
    <div class="breadcrumb">
        <a href="{{ route('organizacion.pagos-suscripcion') }}">Historial de Pagos</a>
        <span> / </span>
        <span>Solicitar Pago Manual</span>
    </div>
</div>

<div class="info-box">
    <h4><i class="fas fa-info-circle"></i> Instrucciones</h4>
    <p>Para registrar un pago manual por transferencia o depósito, sigue estos pasos:</p>
    <ul>
        <li>Realiza la transferencia a la cuenta bancaria de Sistema APR</li>
        <li>Guarda el comprobante de transferencia (foto o PDF)</li>
        <li>Completa el formulario con los datos de la transferencia</li>
        <li>Adjunta el comprobante</li>
        <li>Tu solicitud será revisada y aprobada por el administrador</li>
    </ul>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-receipt"></i> Detalle del Pago</h3>
    </div>
    <div class="card-body">
        <div class="pago-detail">
            <div class="pago-detail-grid">
                <div class="pago-detail-item">
                    <label>Plan</label>
                    <div class="value">{{ $pago->suscripcion->nombre }}</div>
                </div>
                <div class="pago-detail-item">
                    <label>Monto a Pagar</label>
                    <div class="value">${{ number_format($pago->monto, 0, ',', '.') }}</div>
                </div>
                <div class="pago-detail-item">
                    <label>Período</label>
                    <div class="value">
                        {{ $pago->periodo_inicio->format('d/m/Y') }} - {{ $pago->periodo_fin->format('d/m/Y') }}
                    </div>
                </div>
                <div class="pago-detail-item">
                    <label>Vencimiento</label>
                    <div class="value">{{ $pago->fecha_vencimiento->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-edit"></i> Datos de la Transferencia</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('organizacion.solicitud-pago-manual.store', $pago->id) }}" method="POST" enctype="multipart/form-data" id="formPagoManual">
            @csrf

            <div class="form-group">
                <label>
                    Comprobante de Transferencia <span class="required">*</span>
                    <small style="color: var(--gray-600); font-weight: normal;">(PDF, JPG, PNG - Máx. 5MB)</small>
                </label>
                <div class="file-upload-box" id="fileUploadBox">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p><strong>Haz clic para seleccionar</strong> o arrastra el archivo aquí</p>
                    <p class="file-name" id="fileName" style="display: none;"></p>
                </div>
                <input type="file" name="comprobante" id="comprobante" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" required>
                @error('comprobante')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label>Número de Operación/Transferencia <span class="required">*</span></label>
                    <input type="text" name="numero_operacion" class="form-control @error('numero_operacion') is-invalid @enderror"
                           value="{{ old('numero_operacion') }}" placeholder="Ej: 12345678" required>
                    @error('numero_operacion')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Banco Origen <span class="required">*</span></label>
                    <select name="banco_origen" class="form-control @error('banco_origen') is-invalid @enderror" required>
                        <option value="">Seleccionar banco</option>
                        <option value="Banco de Chile" {{ old('banco_origen') == 'Banco de Chile' ? 'selected' : '' }}>Banco de Chile</option>
                        <option value="Banco Estado" {{ old('banco_origen') == 'Banco Estado' ? 'selected' : '' }}>Banco Estado</option>
                        <option value="BCI" {{ old('banco_origen') == 'BCI' ? 'selected' : '' }}>BCI</option>
                        <option value="Banco Santander" {{ old('banco_origen') == 'Banco Santander' ? 'selected' : '' }}>Banco Santander</option>
                        <option value="Banco Scotiabank" {{ old('banco_origen') == 'Banco Scotiabank' ? 'selected' : '' }}>Banco Scotiabank</option>
                        <option value="Banco Itaú" {{ old('banco_origen') == 'Banco Itaú' ? 'selected' : '' }}>Banco Itaú</option>
                        <option value="Banco Security" {{ old('banco_origen') == 'Banco Security' ? 'selected' : '' }}>Banco Security</option>
                        <option value="Banco Falabella" {{ old('banco_origen') == 'Banco Falabella' ? 'selected' : '' }}>Banco Falabella</option>
                        <option value="Banco Ripley" {{ old('banco_origen') == 'Banco Ripley' ? 'selected' : '' }}>Banco Ripley</option>
                        <option value="Banco Consorcio" {{ old('banco_origen') == 'Banco Consorcio' ? 'selected' : '' }}>Banco Consorcio</option>
                        <option value="Coopeuch" {{ old('banco_origen') == 'Coopeuch' ? 'selected' : '' }}>Coopeuch</option>
                        <option value="Otro" {{ old('banco_origen') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('banco_origen')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label>Fecha de Transferencia <span class="required">*</span></label>
                    <input type="date" name="fecha_transferencia" class="form-control @error('fecha_transferencia') is-invalid @enderror"
                           value="{{ old('fecha_transferencia') }}" max="{{ date('Y-m-d') }}" required>
                    @error('fecha_transferencia')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Monto Transferido <span class="required">*</span></label>
                    <input type="number" name="monto" class="form-control @error('monto') is-invalid @enderror"
                           value="{{ old('monto', $pago->monto) }}" min="0" step="1" placeholder="Ej: 15000" required>
                    @error('monto')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Notas Adicionales <small style="color: var(--gray-600); font-weight: normal;">(Opcional)</small></label>
                <textarea name="notas" class="form-control @error('notas') is-invalid @enderror"
                          rows="3" placeholder="Información adicional sobre el pago...">{{ old('notas') }}</textarea>
                @error('notas')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('organizacion.pagos-suscripcion') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary" id="btnSubmit">
                    <i class="fas fa-paper-plane"></i> Enviar Solicitud
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileUploadBox = document.getElementById('fileUploadBox');
    const fileInput = document.getElementById('comprobante');
    const fileName = document.getElementById('fileName');

    // Click para seleccionar archivo
    fileUploadBox.addEventListener('click', () => {
        fileInput.click();
    });

    // Cuando se selecciona un archivo
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileName.textContent = this.files[0].name;
            fileName.style.display = 'inline-block';
        }
    });

    // Drag and drop
    fileUploadBox.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUploadBox.classList.add('drag-over');
    });

    fileUploadBox.addEventListener('dragleave', () => {
        fileUploadBox.classList.remove('drag-over');
    });

    fileUploadBox.addEventListener('drop', (e) => {
        e.preventDefault();
        fileUploadBox.classList.remove('drag-over');

        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            fileName.textContent = e.dataTransfer.files[0].name;
            fileName.style.display = 'inline-block';
        }
    });

    // Validación antes de enviar
    document.getElementById('formPagoManual').addEventListener('submit', function(e) {
        const btnSubmit = document.getElementById('btnSubmit');
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    });
});
</script>
@endsection
