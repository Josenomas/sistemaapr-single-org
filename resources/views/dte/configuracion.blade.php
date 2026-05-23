@extends('layouts.app')

@section('title', 'Configuración DTE - Facturación Electrónica')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-file-invoice"></i>
        Configuración de Facturación Electrónica (DTE)
    </h1>
    <p class="page-subtitle">Configure los datos de su organización para emitir Documentos Tributarios Electrónicos</p>
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
        <h3><i class="fas fa-cog"></i> Configuración LibreDTE</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('dte.guardar-configuracion') }}" method="POST" id="formConfigDTE">
            @csrf

            <!-- Información del Emisor -->
            <div class="form-section">
                <h4><i class="fas fa-building"></i> Datos del Emisor</h4>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="rut_emisor">RUT Emisor <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="rut_emisor"
                                   name="rut_emisor"
                                   value="{{ old('rut_emisor', $config->rut_emisor ?? '') }}"
                                   placeholder="12.345.678-9"
                                   required>
                            @error('rut_emisor')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="razon_social">Razón Social <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="razon_social"
                                   name="razon_social"
                                   value="{{ old('razon_social', $config->razon_social ?? '') }}"
                                   placeholder="APR Ejemplo S.A."
                                   required>
                            @error('razon_social')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="giro">Giro <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="giro"
                                   name="giro"
                                   value="{{ old('giro', $config->giro ?? 'Servicios de Agua Potable Rural') }}"
                                   placeholder="Servicios de Agua Potable Rural"
                                   required>
                            @error('giro')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="direccion_casa_matriz">Dirección Casa Matriz <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="direccion_casa_matriz"
                                   name="direccion_casa_matriz"
                                   value="{{ old('direccion_casa_matriz', $config->direccion_casa_matriz ?? '') }}"
                                   placeholder="Av. Principal 123"
                                   required>
                            @error('direccion_casa_matriz')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="comuna">Comuna <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="comuna"
                                   name="comuna"
                                   value="{{ old('comuna', $config->comuna ?? '') }}"
                                   placeholder="Panguipulli"
                                   required>
                            @error('comuna')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ciudad">Ciudad <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="ciudad"
                                   name="ciudad"
                                   value="{{ old('ciudad', $config->ciudad ?? '') }}"
                                   placeholder="Panguipulli"
                                   required>
                            @error('ciudad')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="text"
                                   class="form-control"
                                   id="telefono"
                                   name="telefono"
                                   value="{{ old('telefono', $config->telefono ?? '') }}"
                                   placeholder="+56912345678">
                            @error('telefono')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email_contacto">Email de Contacto <span class="text-danger">*</span></label>
                            <input type="email"
                                   class="form-control"
                                   id="email_contacto"
                                   name="email_contacto"
                                   value="{{ old('email_contacto', $config->email_contacto ?? '') }}"
                                   placeholder="contacto@aprejemplo.cl"
                                   required>
                            @error('email_contacto')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración LibreDTE -->
            <div class="form-section">
                <h4><i class="fas fa-link"></i> Conexión LibreDTE</h4>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>¿Cómo obtener el Hash de API?</strong><br>
                    1. Ingresa a tu cuenta en <a href="https://libredte.cl" target="_blank">LibreDTE.cl</a><br>
                    2. Ve a <strong>Configuración > Usuarios > API Token</strong><br>
                    3. Copia el token y pégalo aquí
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="libredte_hash">Hash de API LibreDTE <span class="text-danger">*</span></label>
                            <input type="password"
                                   class="form-control"
                                   id="libredte_hash"
                                   name="libredte_hash"
                                   value="{{ old('libredte_hash', $config->libredte_hash ?? '') }}"
                                   placeholder="Tu hash de API de LibreDTE"
                                   required>
                            <small class="text-muted">Este token se mantiene privado y seguro</small>
                            @error('libredte_hash')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ambiente">Ambiente <span class="text-danger">*</span></label>
                            <select class="form-control" id="ambiente" name="ambiente" required>
                                <option value="certificacion" {{ old('ambiente', $config->ambiente ?? 'certificacion') == 'certificacion' ? 'selected' : '' }}>
                                    Certificación (Pruebas)
                                </option>
                                <option value="produccion" {{ old('ambiente', $config->ambiente ?? '') == 'produccion' ? 'selected' : '' }}>
                                    Producción (Real)
                                </option>
                            </select>
                            <small class="text-muted">Usa Certificación para pruebas</small>
                            @error('ambiente')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="libredte_url">URL de LibreDTE</label>
                            <input type="url"
                                   class="form-control"
                                   id="libredte_url"
                                   name="libredte_url"
                                   value="{{ old('libredte_url', $config->libredte_url ?? 'https://libredte.cl') }}"
                                   placeholder="https://libredte.cl">
                            <small class="text-muted">Normalmente no necesitas cambiar esto</small>
                            @error('libredte_url')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label><br>
                            <button type="button" class="btn btn-secondary btn-block" id="btnVerificarConexion">
                                <i class="fas fa-plug"></i> Verificar Conexión
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado -->
            @if($config && $config->exists)
            <div class="form-section">
                <h4><i class="fas fa-chart-line"></i> Estado Actual</h4>
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value">{{ $config->folio_boleta_actual }}</div>
                                <div class="stat-label">Boletas Emitidas</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value">{{ $config->folio_factura_actual }}</div>
                                <div class="stat-label">Facturas Emitidas</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-server"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value">{{ ucfirst($config->ambiente) }}</div>
                                <div class="stat-label">Ambiente</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card {{ $config->activo ? 'stat-success' : 'stat-danger' }}">
                            <div class="stat-icon">
                                <i class="fas {{ $config->activo ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value">{{ $config->activo ? 'Activo' : 'Inactivo' }}</div>
                                <div class="stat-label">Estado</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Botones -->
            <div class="form-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Configuración
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.form-section {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid var(--border);
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section h4 {
    color: var(--text-light);
    margin-bottom: 20px;
    font-size: 1.1rem;
}

.stat-card {
    background: var(--dark-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-card.stat-success {
    border-color: #10b981;
    background: rgba(16, 185, 129, 0.1);
}

.stat-card.stat-danger {
    border-color: #ef4444;
    background: rgba(239, 68, 68, 0.1);
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
    font-weight: 600;
    color: var(--text-light);
    line-height: 1.2;
}

.stat-label {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 4px;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid var(--border);
}

.alert a {
    color: inherit;
    text-decoration: underline;
}
</style>

<script>
document.getElementById('btnVerificarConexion').addEventListener('click', function() {
    const btn = this;
    const originalHTML = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';

    fetch('{{ route('dte.verificar-conexion') }}')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('✅ ' + data.message);
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            alert('❌ Error al verificar conexión: ' + error.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        });
});
</script>
@endsection
