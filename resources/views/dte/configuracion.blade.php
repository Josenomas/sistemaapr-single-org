@extends('layouts.app')

@section('title', 'Configuración DTE - Facturación Electrónica')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-file-invoice-dollar"></i>
        Configuración de Facturación Electrónica
    </h2>
    <div class="page-actions">
        <button type="button" class="btn btn-info" onclick="mostrarAyuda()">
            <i class="fas fa-question-circle"></i>
            Ayuda
        </button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    {{ session('error') }}
</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Datos del Emisor</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('dte.guardar-configuracion') }}" method="POST" id="formConfigDTE" enctype="multipart/form-data">
            @csrf

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="rut_emisor" class="form-label required">RUT Emisor</label>
                    <input type="text"
                           class="form-control @error('rut_emisor') is-invalid @enderror"
                           id="rut_emisor"
                           name="rut_emisor"
                           value="{{ old('rut_emisor', $config->rut_emisor ?? '') }}"
                           placeholder="12.345.678-9"
                           required>
                    @error('rut_emisor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-8">
                    <label for="razon_social" class="form-label required">Razón Social</label>
                    <input type="text"
                           class="form-control @error('razon_social') is-invalid @enderror"
                           id="razon_social"
                           name="razon_social"
                           value="{{ old('razon_social', $config->razon_social ?? '') }}"
                           placeholder="APR Ejemplo S.A."
                           required>
                    @error('razon_social')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="giro" class="form-label required">Giro</label>
                    <input type="text"
                           class="form-control @error('giro') is-invalid @enderror"
                           id="giro"
                           name="giro"
                           value="{{ old('giro', $config->giro ?? 'Servicios de Agua Potable Rural') }}"
                           placeholder="Servicios de Agua Potable Rural"
                           required>
                    @error('giro')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="direccion_casa_matriz" class="form-label required">Dirección Casa Matriz</label>
                    <input type="text"
                           class="form-control @error('direccion_casa_matriz') is-invalid @enderror"
                           id="direccion_casa_matriz"
                           name="direccion_casa_matriz"
                           value="{{ old('direccion_casa_matriz', $config->direccion_casa_matriz ?? '') }}"
                           placeholder="Av. Principal 123"
                           required>
                    @error('direccion_casa_matriz')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="comuna" class="form-label required">Comuna</label>
                    <input type="text"
                           class="form-control @error('comuna') is-invalid @enderror"
                           id="comuna"
                           name="comuna"
                           value="{{ old('comuna', $config->comuna ?? '') }}"
                           placeholder="Panguipulli"
                           required>
                    @error('comuna')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="ciudad" class="form-label required">Ciudad</label>
                    <input type="text"
                           class="form-control @error('ciudad') is-invalid @enderror"
                           id="ciudad"
                           name="ciudad"
                           value="{{ old('ciudad', $config->ciudad ?? '') }}"
                           placeholder="Panguipulli"
                           required>
                    @error('ciudad')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="email_contacto" class="form-label required">Email de Contacto</label>
                    <input type="email"
                           class="form-control @error('email_contacto') is-invalid @enderror"
                           id="email_contacto"
                           name="email_contacto"
                           value="{{ old('email_contacto', $config->email_contacto ?? '') }}"
                           placeholder="contacto@aprejemplo.cl"
                           required>
                    @error('email_contacto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row" style="margin-top: 30px; padding-top: 24px; border-top: 1px solid var(--gray-200);">
                <div class="form-group col-md-6">
                    <label for="proveedor_dte" class="form-label required">Proveedor de Facturación</label>
                    <select class="form-control @error('proveedor_dte') is-invalid @enderror" id="proveedor_dte" name="proveedor_dte" required onchange="toggleProveedorFields()">
                        <option value="libredte" {{ old('proveedor_dte', $config->proveedor_dte ?? 'libredte') == 'libredte' ? 'selected' : '' }}>
                            LibreDTE ($40.000/mes)
                        </option>
                        <option value="simpleapi" {{ old('proveedor_dte', $config->proveedor_dte ?? '') == 'simpleapi' ? 'selected' : '' }}>
                            SimpleAPI (GRATIS hasta 500/mes) ⭐
                        </option>
                    </select>
                    <small class="form-text text-muted">
                        💡 <strong>Tip:</strong> SimpleAPI es gratis hasta 500 documentos/mes
                    </small>
                    @error('proveedor_dte')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="ambiente" class="form-label required">Ambiente de Emisión</label>
                    <select class="form-control @error('ambiente') is-invalid @enderror" id="ambiente" name="ambiente" required onchange="toggleAmbienteFields()">
                        <option value="certificacion" {{ old('ambiente', $config->ambiente ?? 'certificacion') == 'certificacion' ? 'selected' : '' }}>
                            🧪 Certificación (Pruebas) - Recomendado para testing
                        </option>
                        <option value="produccion" {{ old('ambiente', $config->ambiente ?? '') == 'produccion' ? 'selected' : '' }}>
                            ✅ Producción (Real) - DTEs válidos ante el SII
                        </option>
                    </select>
                    <small class="form-text">
                        <strong>Certificación:</strong> Para pruebas sin validez tributaria.
                        <strong>Producción:</strong> DTEs reales con validez legal.
                    </small>
                    @error('ambiente')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Credenciales de PRODUCCIÓN -->
            <div id="credenciales-produccion" style="display: none;">
                <div class="ambiente-section produccion">
                    <div class="ambiente-header">
                        <i class="fas fa-shield-alt"></i>
                        <h4>Credenciales de Producción</h4>
                        <span class="badge badge-success">Producción Real</span>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="libredte_hash" class="form-label required">Hash de API LibreDTE (Producción)</label>
                            <input type="password"
                                   class="form-control @error('libredte_hash') is-invalid @enderror"
                                   id="libredte_hash"
                                   name="libredte_hash"
                                   value="{{ old('libredte_hash', $config->libredte_hash ?? '') }}"
                                   placeholder="Tu hash de API de LibreDTE para producción">
                            <small class="form-text">Obtén tu token en LibreDTE.cl → Configuración → Usuarios → API Token</small>
                            @error('libredte_hash')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="libredte_url" class="form-label">URL de API LibreDTE (Producción)</label>
                            <input type="url"
                                   class="form-control @error('libredte_url') is-invalid @enderror"
                                   id="libredte_url"
                                   name="libredte_url"
                                   value="{{ old('libredte_url', $config->libredte_url ?? 'https://libredte.cl') }}"
                                   placeholder="https://libredte.cl">
                            <small class="form-text">Por defecto: https://libredte.cl</small>
                            @error('libredte_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credenciales de CERTIFICACIÓN -->
            <div id="credenciales-certificacion" style="display: none;">
                <div class="ambiente-section certificacion">
                    <div class="ambiente-header">
                        <i class="fas fa-flask"></i>
                        <h4>Credenciales de Certificación</h4>
                        <span class="badge badge-warning">Ambiente de Pruebas</span>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="libredte_hash_certificacion" class="form-label">Hash de API LibreDTE (Certificación)</label>
                            <input type="password"
                                   class="form-control @error('libredte_hash_certificacion') is-invalid @enderror"
                                   id="libredte_hash_certificacion"
                                   name="libredte_hash_certificacion"
                                   value="{{ old('libredte_hash_certificacion', $config->libredte_hash_certificacion ?? '') }}"
                                   placeholder="Tu hash de API para ambiente de certificación">
                            <small class="form-text">Token de certificación de LibreDTE (opcional si tienes cuenta separada)</small>
                            @error('libredte_hash_certificacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="libredte_url_certificacion" class="form-label">URL de API LibreDTE (Certificación)</label>
                            <input type="url"
                                   class="form-control @error('libredte_url_certificacion') is-invalid @enderror"
                                   id="libredte_url_certificacion"
                                   name="libredte_url_certificacion"
                                   value="{{ old('libredte_url_certificacion', $config->libredte_url_certificacion ?? '') }}"
                                   placeholder="https://certificacion.libredte.cl">
                            <small class="form-text">URL del ambiente de certificación (opcional)</small>
                            @error('libredte_url_certificacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certificado Digital -->
            <div class="form-row" style="margin-top: 30px; padding-top: 24px; border-top: 1px solid var(--gray-200);">
                <div class="col-md-12">
                    <h4 style="margin-bottom: 16px;">
                        <i class="fas fa-certificate"></i>
                        Certificado Digital (.pfx/.p12)
                    </h4>
                    <p class="text-muted" style="margin-bottom: 20px;">
                        <i class="fas fa-info-circle"></i>
                        El certificado digital es <strong>obligatorio para SimpleAPI</strong> y opcional para LibreDTE.
                        Debe ser emitido por el SII y estar vigente.
                    </p>
                </div>

                <div class="form-group col-md-8">
                    <label for="certificado_digital" class="form-label">
                        Subir Certificado Digital
                        @if($config && $config->proveedor_dte == 'simpleapi')
                            <span class="badge badge-danger">Obligatorio</span>
                        @else
                            <span class="badge badge-secondary">Opcional</span>
                        @endif
                    </label>
                    <input type="file"
                           class="form-control-file @error('certificado_digital') is-invalid @enderror"
                           id="certificado_digital"
                           name="certificado_digital"
                           accept=".pfx,.p12">
                    <small class="form-text text-muted">
                        <i class="fas fa-file-upload"></i>
                        Formatos aceptados: .pfx, .p12 (máx. 2MB)
                        @if($config && $config->certificado_digital)
                            <br><i class="fas fa-check-circle text-success"></i> <strong>Certificado ya cargado</strong>
                        @endif
                    </small>
                    @error('certificado_digital')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="certificado_password" class="form-label">Contraseña del Certificado</label>
                    <input type="password"
                           class="form-control @error('certificado_password') is-invalid @enderror"
                           id="certificado_password"
                           name="certificado_password"
                           placeholder="Contraseña del .pfx"
                           autocomplete="new-password">
                    <small class="form-text text-muted">
                        Solo ingresar si subes un nuevo certificado
                    </small>
                    @error('certificado_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Archivos CAF (Código de Autorización de Folios) -->
            <div class="form-row" style="margin-top: 30px; padding-top: 24px; border-top: 1px solid var(--gray-200);">
                <div class="col-md-12">
                    <h4 style="margin-bottom: 16px;">
                        <i class="fas fa-file-code"></i>
                        Archivos CAF (Código de Autorización de Folios)
                    </h4>
                    <p class="text-muted" style="margin-bottom: 20px;">
                        <i class="fas fa-info-circle"></i>
                        Los archivos CAF contienen los folios autorizados por el SII para emitir documentos electrónicos.
                        Descárgalos desde el sitio del SII (<a href="https://maullin.sii.cl/cvc_cgi/dte/of_solicita_folios" target="_blank">Certificación</a> o <a href="https://palena.sii.cl/cvc_cgi/dte/of_solicita_folios" target="_blank">Producción</a>).
                    </p>
                </div>

                <!-- CAF Boleta Electrónica (39) -->
                <div class="form-group col-md-6">
                    <label for="caf_boleta_39" class="form-label">
                        <i class="fas fa-receipt"></i>
                        CAF Boleta Electrónica (Tipo 39)
                        @if($config && $config->proveedor_dte == 'simpleapi')
                            <span class="badge badge-warning">Recomendado</span>
                        @endif
                    </label>
                    <input type="file"
                           class="form-control-file @error('caf_boleta_39') is-invalid @enderror"
                           id="caf_boleta_39"
                           name="caf_boleta_39"
                           accept=".xml">
                    <small class="form-text text-muted">
                        <i class="fas fa-file-code"></i>
                        Archivo XML del SII
                        @if($config && $config->caf_boleta_39)
                            <br><i class="fas fa-check-circle text-success"></i>
                            <strong>CAF cargado</strong> - Folios {{ $config->caf_boleta_desde }} a {{ $config->caf_boleta_hasta }}
                            @if($config->caf_boleta_vencimiento)
                                (Vence: {{ $config->caf_boleta_vencimiento->format('d/m/Y') }})
                            @endif
                        @endif
                    </small>
                    @error('caf_boleta_39')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- CAF Factura Electrónica (33) -->
                <div class="form-group col-md-6">
                    <label for="caf_factura_33" class="form-label">
                        <i class="fas fa-file-invoice"></i>
                        CAF Factura Electrónica (Tipo 33)
                        <span class="badge badge-secondary">Opcional</span>
                    </label>
                    <input type="file"
                           class="form-control-file @error('caf_factura_33') is-invalid @enderror"
                           id="caf_factura_33"
                           name="caf_factura_33"
                           accept=".xml">
                    <small class="form-text text-muted">
                        <i class="fas fa-file-code"></i>
                        Archivo XML del SII (solo si emites facturas)
                        @if($config && $config->caf_factura_33)
                            <br><i class="fas fa-check-circle text-success"></i>
                            <strong>CAF cargado</strong> - Folios {{ $config->caf_factura_desde }} a {{ $config->caf_factura_hasta }}
                            @if($config->caf_factura_vencimiento)
                                (Vence: {{ $config->caf_factura_vencimiento->format('d/m/Y') }})
                            @endif
                        @endif
                    </small>
                    @error('caf_factura_33')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- CAF Nota de Crédito (61) -->
                <div class="form-group col-md-6">
                    <label for="caf_nota_credito_61" class="form-label">
                        <i class="fas fa-file-minus"></i>
                        CAF Nota de Crédito (Tipo 61)
                        <span class="badge badge-secondary">Opcional</span>
                    </label>
                    <input type="file"
                           class="form-control-file @error('caf_nota_credito_61') is-invalid @enderror"
                           id="caf_nota_credito_61"
                           name="caf_nota_credito_61"
                           accept=".xml">
                    <small class="form-text text-muted">
                        <i class="fas fa-file-code"></i>
                        Archivo XML del SII (solo si necesitas anular documentos)
                        @if($config && $config->caf_nota_credito_61)
                            <br><i class="fas fa-check-circle text-success"></i> <strong>CAF cargado</strong>
                        @endif
                    </small>
                    @error('caf_nota_credito_61')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- CAF Nota de Débito (56) -->
                <div class="form-group col-md-6">
                    <label for="caf_nota_debito_56" class="form-label">
                        <i class="fas fa-file-plus"></i>
                        CAF Nota de Débito (Tipo 56)
                        <span class="badge badge-secondary">Opcional</span>
                    </label>
                    <input type="file"
                           class="form-control-file @error('caf_nota_debito_56') is-invalid @enderror"
                           id="caf_nota_debito_56"
                           name="caf_nota_debito_56"
                           accept=".xml">
                    <small class="form-text text-muted">
                        <i class="fas fa-file-code"></i>
                        Archivo XML del SII (solo si necesitas emitir cargos adicionales)
                        @if($config && $config->caf_nota_debito_56)
                            <br><i class="fas fa-check-circle text-success"></i> <strong>CAF cargado</strong>
                        @endif
                    </small>
                    @error('caf_nota_debito_56')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar Configuración
                </button>
                <button type="button" class="btn btn-info" id="btnVerificarConexion">
                    <i class="fas fa-plug"></i>
                    Verificar Conexión
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Estadísticas DTE -->
@if($config && $config->exists)
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Estado de Facturación Electrónica</h3>
    </div>
    <div class="card-body">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $config->folio_boleta_actual }}</div>
                    <div class="stat-label">Boletas Emitidas</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $config->folio_factura_actual }}</div>
                    <div class="stat-label">Facturas Emitidas</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-server"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ ucfirst($config->ambiente) }}</div>
                    <div class="stat-label">Ambiente Activo</div>
                </div>
            </div>
            <div class="stat-card {{ $config->activo ? 'stat-success' : 'stat-danger' }}">
                <div class="stat-icon">
                    <i class="fas {{ $config->activo ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $config->activo ? 'Activo' : 'Inactivo' }}</div>
                    <div class="stat-label">Estado DTE</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

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

    .page-actions {
        display: flex;
        gap: 12px;
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

    .btn-info {
        background: #17a2b8;
        color: white;
    }

    .btn-info:hover {
        background: #138496;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    select.form-control {
        cursor: pointer;
    }

    .col-md-4 {
        grid-column: span 1;
    }

    .col-md-8 {
        grid-column: span 2;
    }

    .col-md-12 {
        grid-column: span 3;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .stat-card {
        background: var(--gray-50);
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-card.stat-success {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.05);
    }

    .stat-card.stat-danger {
        border-color: #ef4444;
        background: rgba(239, 68, 68, 0.05);
    }

    .stat-icon {
        font-size: 2rem;
        color: var(--primary);
        opacity: 0.8;
    }

    .stat-success .stat-icon {
        color: #10b981;
    }

    .stat-danger .stat-icon {
        color: #ef4444;
    }

    .stat-info {
        flex: 1;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        line-height: 1.2;
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--gray-600);
        margin-top: 4px;
    }

    .ambiente-section {
        margin-top: 20px;
        padding: 20px;
        border-radius: var(--radius);
        border: 2px solid var(--gray-200);
        background: var(--gray-50);
    }

    .ambiente-section.produccion {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.05);
    }

    .ambiente-section.certificacion {
        border-color: #f59e0b;
        background: rgba(245, 158, 11, 0.05);
    }

    .ambiente-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--gray-200);
    }

    .ambiente-header i {
        font-size: 1.5rem;
    }

    .ambiente-section.produccion .ambiente-header i {
        color: #10b981;
    }

    .ambiente-section.certificacion .ambiente-header i {
        color: #f59e0b;
    }

    .ambiente-header h4 {
        flex: 1;
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-success {
        background: #10b981;
        color: white;
    }

    .badge-warning {
        background: #f59e0b;
        color: white;
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

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .page-actions {
            width: 100%;
        }
    }
</style>

<script>
// Toggle proveedor fields
function toggleProveedorFields() {
    const proveedor = document.getElementById('proveedor_dte').value;
    const credencialesProduccion = document.getElementById('credenciales-produccion');
    const credencialesCertificacion = document.getElementById('credenciales-certificacion');

    // SimpleAPI no usa hash de LibreDTE, solo certificado digital
    if (proveedor === 'simpleapi') {
        // Ocultar secciones de LibreDTE (hash)
        if (credencialesProduccion) credencialesProduccion.style.display = 'none';
        if (credencialesCertificacion) credencialesCertificacion.style.display = 'none';

        // Hacer no obligatorios los hash
        const hashProd = document.getElementById('libredte_hash');
        const hashCert = document.getElementById('libredte_hash_certificacion');
        if (hashProd) hashProd.required = false;
        if (hashCert) hashCert.required = false;
    } else {
        // LibreDTE: mostrar según ambiente
        toggleAmbienteFields();
    }
}

// Toggle ambiente fields on load and change
function toggleAmbienteFields() {
    const proveedor = document.getElementById('proveedor_dte').value;

    // Solo aplicar lógica si es LibreDTE
    if (proveedor !== 'libredte') return;

    const ambiente = document.getElementById('ambiente').value;
    const credencialesProduccion = document.getElementById('credenciales-produccion');
    const credencialesCertificacion = document.getElementById('credenciales-certificacion');

    if (ambiente === 'produccion') {
        credencialesProduccion.style.display = 'block';
        credencialesCertificacion.style.display = 'none';
        // Hacer obligatorio el hash de producción
        document.getElementById('libredte_hash').required = true;
        document.getElementById('libredte_hash_certificacion').required = false;
    } else {
        credencialesProduccion.style.display = 'none';
        credencialesCertificacion.style.display = 'block';
        // Hacer opcional el hash de producción en certificación
        document.getElementById('libredte_hash').required = false;
        document.getElementById('libredte_hash_certificacion').required = false;
    }
}

// Execute on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleProveedorFields();
    toggleAmbienteFields();
});

document.getElementById('btnVerificarConexion').addEventListener('click', function() {
    const btn = this;
    const originalHTML = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';

    fetch('{{ route('dte.verificar-conexion') }}')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: '¡Conexión Exitosa!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'Entendido'
                });
            } else {
                Swal.fire({
                    title: 'Error de Conexión',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Error al verificar conexión: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        });
});

function mostrarAyuda() {
    const ayuda = `
        <div style="text-align: left;">
            <h4 style="margin-top: 0;">📄 Configuración de Facturación Electrónica</h4>

            <p><strong>¿Qué es LibreDTE?</strong></p>
            <p>LibreDTE es un servicio que permite emitir Documentos Tributarios Electrónicos (DTE)
            válidos ante el SII (Servicio de Impuestos Internos de Chile).</p>

            <p><strong>Pasos para configurar:</strong></p>
            <ol>
                <li>Completa los datos del emisor (RUT, razón social, dirección, etc.)</li>
                <li>Obtén tu Hash de API desde LibreDTE.cl</li>
                <li>Selecciona el ambiente (usa Certificación para pruebas)</li>
                <li>Haz clic en "Verificar Conexión" para comprobar</li>
                <li>Guarda la configuración</li>
            </ol>

            <p><strong>¿Cómo obtener el Hash de API?</strong></p>
            <ol>
                <li>Ingresa a <a href="https://libredte.cl" target="_blank">LibreDTE.cl</a></li>
                <li>Ve a Configuración → Usuarios → API Token</li>
                <li>Copia el token completo</li>
                <li>Pégalo en el campo "Hash de API LibreDTE"</li>
            </ol>

            <p style="background: #fff3cd; padding: 10px; border-radius: 5px; margin-top: 15px;">
                <strong>⚠️ Importante:</strong> Usa el ambiente de Certificación primero para hacer pruebas.
                Solo cambia a Producción cuando estés seguro de que todo funciona correctamente.
            </p>
        </div>
    `;

    Swal.fire({
        title: 'Ayuda',
        html: ayuda,
        icon: 'info',
        width: '600px',
        confirmButtonText: 'Entendido'
    });
}
</script>
@endsection
