@extends('layouts.superadmin')

@section('title', 'Configuración DTE - Super Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-cog"></i>
            Configuración DTE - {{ $organizacion->nombre_apr }}
        </h2>
        <a href="{{ route('superadmin.configuracion-dte') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
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
        <form action="{{ route('superadmin.configuracion-dte.guardar', $organizacion->id) }}" method="POST" id="formConfigDTE" enctype="multipart/form-data">
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
                        <option value="simpleapi" {{ old('proveedor_dte', $config->proveedor_dte ?? '') == 'simpleapi' ? 'selected' : '' }}>
                            SimpleAPI (GRATIS hasta 500/mes)
                        </option>
                        <option value="simplefactura" {{ old('proveedor_dte', $config->proveedor_dte ?? 'simplefactura') == 'simplefactura' ? 'selected' : '' }}>
                            SimpleFactura (ChileSystems) ⭐ RECOMENDADO
                        </option>
                    </select>
                    <small class="form-text text-muted">
                        💡 <strong>Tip:</strong> SimpleFactura tiene SDK oficial y mejor documentación
                    </small>
                    @error('proveedor_dte')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="ambiente" class="form-label required">Ambiente de Emisión</label>
                    <select class="form-control @error('ambiente') is-invalid @enderror" id="ambiente" name="ambiente" required>
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

            <!-- Credenciales SimpleAPI -->
            <div id="credenciales-simpleapi" style="display: none;">
                <div class="ambiente-section simpleapi">
                    <div class="ambiente-header">
                        <i class="fas fa-key"></i>
                        <h4>Credenciales SimpleAPI</h4>
                        <span class="badge badge-info">GRATIS hasta 500 DTEs/mes</span>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="simpleapi_token" class="form-label required">Token de API SimpleAPI</label>
                            <input type="password"
                                   class="form-control @error('simpleapi_token') is-invalid @enderror"
                                   id="simpleapi_token"
                                   name="simpleapi_token"
                                   value="{{ old('simpleapi_token', $config->simpleapi_token ?? '') }}"
                                   placeholder="Tu token de API de SimpleAPI">
                            <small class="form-text">
                                Obtén tu token en: <a href="https://www.simpleapi.cl" target="_blank">www.simpleapi.cl</a> → Panel → API Token
                            </small>
                            @error('simpleapi_token')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credenciales SimpleFactura -->
            <div id="credenciales-simplefactura" style="display: none;">
                <div class="ambiente-section simplefactura">
                    <div class="ambiente-header">
                        <i class="fas fa-star"></i>
                        <h4>Credenciales SimpleFactura (ChileSystems)</h4>
                        <span class="badge badge-success">⭐ RECOMENDADO</span>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="simplefactura_usuario" class="form-label required">Usuario SimpleFactura</label>
                            <input type="email"
                                   class="form-control @error('simplefactura_usuario') is-invalid @enderror"
                                   id="simplefactura_usuario"
                                   name="simplefactura_usuario"
                                   value="{{ old('simplefactura_usuario', $config->simplefactura_usuario ?? '') }}"
                                   placeholder="usuario@tuempresa.com">
                            <small class="form-text">Email de tu cuenta SimpleFactura</small>
                            @error('simplefactura_usuario')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="simplefactura_password" class="form-label required">Contraseña SimpleFactura</label>
                            <input type="password"
                                   class="form-control @error('simplefactura_password') is-invalid @enderror"
                                   id="simplefactura_password"
                                   name="simplefactura_password"
                                   value="{{ old('simplefactura_password', $config->simplefactura_password ?? '') }}"
                                   placeholder="Tu contraseña de SimpleFactura">
                            <small class="form-text">
                                Regístrate en: <a href="https://www.simplefactura.cl" target="_blank">www.simplefactura.cl</a>
                            </small>
                            @error('simplefactura_password')
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
                        El certificado digital es <strong>obligatorio</strong>. Debe ser emitido por el SII y estar vigente.
                    </p>
                </div>

                <div class="form-group col-md-8">
                    <label for="certificado_digital" class="form-label">
                        Subir Certificado Digital
                        @if($config && in_array($config->proveedor_dte, ['simpleapi', 'simplefactura']))
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

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar Configuración
                </button>
                <button type="button" class="btn btn-info" id="btnVerificarConexion">
                    <i class="fas fa-plug"></i>
                    Verificar Conexión
                </button>
                <a href="{{ route('superadmin.configuracion-dte') }}" class="btn btn-secondary">
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

    // Ocultar todas las secciones de credenciales primero
    const credencialesSimpleapi = document.getElementById('credenciales-simpleapi');
    const credencialesSimplefactura = document.getElementById('credenciales-simplefactura');

    if (credencialesSimpleapi) credencialesSimpleapi.style.display = 'none';
    if (credencialesSimplefactura) credencialesSimplefactura.style.display = 'none';

    // Resetear required en todos los campos
    const simpleapiToken = document.getElementById('simpleapi_token');
    const simplefacturaUsuario = document.getElementById('simplefactura_usuario');
    const simplefacturaPassword = document.getElementById('simplefactura_password');

    if (simpleapiToken) simpleapiToken.required = false;
    if (simplefacturaUsuario) simplefacturaUsuario.required = false;
    if (simplefacturaPassword) simplefacturaPassword.required = false;

    // Mostrar sección según proveedor
    if (proveedor === 'simpleapi') {
        if (credencialesSimpleapi) credencialesSimpleapi.style.display = 'block';
        if (simpleapiToken) simpleapiToken.required = true;
    } else if (proveedor === 'simplefactura') {
        if (credencialesSimplefactura) credencialesSimplefactura.style.display = 'block';
        if (simplefacturaUsuario) simplefacturaUsuario.required = true;
        if (simplefacturaPassword) simplefacturaPassword.required = true;
    }
}

// Execute on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleProveedorFields();
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

            <p><strong>Proveedores disponibles:</strong></p>
            <ul>
                <li><strong>SimpleAPI:</strong> Gratis hasta 500 DTEs/mes</li>
                <li><strong>SimpleFactura (ChileSystems):</strong> ⭐ Recomendado - SDK oficial y mejor documentación</li>
            </ul>

            <p><strong>Pasos para configurar:</strong></p>
            <ol>
                <li>Completa los datos del emisor (RUT, razón social, dirección, etc.)</li>
                <li>Selecciona el proveedor de facturación</li>
                <li>Ingresa las credenciales correspondientes</li>
                <li>Sube el certificado digital (.pfx/.p12)</li>
                <li>Selecciona el ambiente (usa Certificación para pruebas)</li>
                <li>Haz clic en "Verificar Conexión" para comprobar</li>
                <li>Guarda la configuración</li>
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
