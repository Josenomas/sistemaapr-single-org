@extends('layouts.app')

@section('title', 'Editar Organización - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-edit"></i>
        Editar Organización
    </h2>
    <a href="{{ route('organizacion.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Organización</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('organizacion.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-row">
                <!-- Nombre APR -->
                <div class="form-group col-md-6">
                    <label for="nombre_apr" class="form-label required">Nombre de la APR</label>
                    <input type="text"
                           class="form-control @error('nombre_apr') is-invalid @enderror"
                           id="nombre_apr"
                           name="nombre_apr"
                           value="{{ old('nombre_apr', $organizacion->nombre_apr) }}"
                           required>
                    @error('nombre_apr')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- RUT -->
                <div class="form-group col-md-6">
                    <label for="rut" class="form-label required">RUT</label>
                    <input type="text"
                           class="form-control @error('rut') is-invalid @enderror"
                           id="rut"
                           name="rut"
                           value="{{ old('rut', $organizacion->rut) }}"
                           placeholder="12.345.678-9"
                           required>
                    @error('rut')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Dirección -->
                <div class="form-group col-md-12">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text"
                           class="form-control @error('direccion') is-invalid @enderror"
                           id="direccion"
                           name="direccion"
                           value="{{ old('direccion', $organizacion->direccion) }}">
                    @error('direccion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Teléfono -->
                <div class="form-group col-md-6">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text"
                           class="form-control @error('telefono') is-invalid @enderror"
                           id="telefono"
                           name="telefono"
                           value="{{ old('telefono', $organizacion->telefono) }}"
                           placeholder="+56 9 1234 5678">
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group col-md-6">
                    <label for="email_contacto" class="form-label">Email de Contacto</label>
                    <input type="email"
                           class="form-control @error('email_contacto') is-invalid @enderror"
                           id="email_contacto"
                           name="email_contacto"
                           value="{{ old('email_contacto', $organizacion->email_contacto) }}"
                           placeholder="contacto@apr.cl">
                    @error('email_contacto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Logo -->
                <div class="form-group col-md-12">
                    <label for="logo" class="form-label">Logo de la Organización</label>
                    @if($organizacion->logo)
                        <div class="logo-current">
                            <img src="{{ asset('storage/' . $organizacion->logo) }}"
                                 alt="Logo actual"
                                 class="logo-preview">
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminarLogo()">
                                <i class="fas fa-trash"></i> Eliminar Logo
                            </button>
                        </div>
                    @endif
                    <input type="file"
                           class="form-control @error('logo') is-invalid @enderror"
                           id="logo"
                           name="logo"
                           accept="image/*">
                    <small class="form-text">
                        Formatos permitidos: JPG, PNG, SVG. Tamaño máximo: 2MB
                    </small>
                    @error('logo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Formulario oculto para eliminar logo -->
            <form id="deleteLogoForm" action="{{ route('organizacion.deleteLogo') }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>

            <!-- Dominio Personalizado (solo Enterprise) -->
            @if($organizacion->suscripcion && $organizacion->suscripcion->permite_dominio_personalizado)
            <div class="domain-section">
                <h4 class="section-title">
                    <i class="fas fa-globe"></i>
                    Dominio Personalizado
                    <span class="badge-enterprise">Enterprise</span>
                </h4>
                <p class="section-description">
                    Configura un dominio personalizado para tu organización. El dominio debe estar previamente registrado y configurado con los DNS correctos.
                </p>

                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>Subdominio actual:</strong> <code>{{ $organizacion->slug }}.sistemaapr.cl</code><br>
                        @if($organizacion->dominio_personalizado)
                            <strong>Dominio personalizado:</strong> <code>{{ $organizacion->dominio_personalizado }}</code>
                            <br>
                            <strong>Estado:</strong> {!! $organizacion->badge_estado_dominio !!}
                            @if($organizacion->fecha_verificacion_dns)
                                <br><small><i class="fas fa-clock"></i> Verificado: {{ $organizacion->fecha_verificacion_dns->format('d/m/Y H:i') }}</small>
                            @endif
                            @if($organizacion->observaciones_dominio)
                                <br><small style="color: #d97706;"><i class="fas fa-exclamation-triangle"></i> {{ $organizacion->observaciones_dominio }}</small>
                            @endif
                        @else
                            <small>Aún no has configurado un dominio personalizado</small>
                        @endif
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="dominio_personalizado" class="form-label">Dominio Personalizado</label>
                        <input type="text"
                               class="form-control @error('dominio_personalizado') is-invalid @enderror"
                               id="dominio_personalizado"
                               name="dominio_personalizado"
                               value="{{ old('dominio_personalizado', $organizacion->dominio_personalizado) }}"
                               placeholder="www.aprnombre.cl">
                        <small class="form-text">
                            <i class="fas fa-lightbulb"></i>
                            Ingresa solo el dominio (ejemplo: www.aprnombre.cl). No incluyas http:// o https://
                        </small>
                        @error('dominio_personalizado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="warning-box">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Importante:</strong> Antes de configurar tu dominio personalizado, debes:
                        <ol>
                            <li>Tener el dominio registrado a tu nombre</li>
                            <li>Configurar un registro CNAME apuntando a <code>sistemaapr.cl</code></li>
                            <li>Esperar a que los cambios DNS se propaguen (puede tomar hasta 48 horas)</li>
                        </ol>
                        <a href="#" onclick="mostrarInstruccionesDNS(); return false;" class="link-help">
                            <i class="fas fa-question-circle"></i> Ver instrucciones detalladas
                        </a>
                    </div>
                </div>

                @if($organizacion->dominio_personalizado && in_array($organizacion->estado_dominio_personalizado, ['pendiente_configuracion', 'verificado_dns']))
                    <div style="margin-top: 16px;">
                        <form action="{{ route('organizacion.reverificar-dns') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-info btn-sm">
                                <i class="fas fa-sync-alt"></i>
                                Re-verificar DNS
                            </button>
                        </form>
                        <small class="form-text">
                            Si ya configuraste el DNS, haz clic para verificar nuevamente.
                        </small>
                    </div>
                @endif
            </div>
            @endif

            <!-- Personalización -->
            <div class="customization-section">
                <h4 class="section-title">
                    <i class="fas fa-palette"></i>
                    Personalización de Colores
                </h4>
                <p class="section-description">
                    Personaliza los colores del sistema para que coincidan con la identidad visual de tu organización.
                    Los cambios se aplicarán en toda la interfaz del sistema (botones, encabezados, enlaces, etc.).
                </p>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="color_primario" class="form-label">Color Primario</label>
                        <div class="color-picker-group">
                            <input type="color"
                                   class="color-input @error('color_primario') is-invalid @enderror"
                                   id="color_primario"
                                   name="color_primario"
                                   value="{{ old('color_primario', $organizacion->color_primario ?? '#5e0a85') }}">
                            <input type="text"
                                   class="form-control color-text"
                                   id="color_primario_text"
                                   value="{{ old('color_primario', $organizacion->color_primario ?? '#5e0a85') }}"
                                   readonly>
                        </div>
                        @error('color_primario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="color_secundario" class="form-label">Color Secundario</label>
                        <div class="color-picker-group">
                            <input type="color"
                                   class="color-input @error('color_secundario') is-invalid @enderror"
                                   id="color_secundario"
                                   name="color_secundario"
                                   value="{{ old('color_secundario', $organizacion->color_secundario ?? '#10b981') }}">
                            <input type="text"
                                   class="form-control color-text"
                                   id="color_secundario_text"
                                   value="{{ old('color_secundario', $organizacion->color_secundario ?? '#10b981') }}"
                                   readonly>
                        </div>
                        @error('color_secundario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar Cambios
                </button>
                <button type="button" class="btn btn-info" onclick="previsualizarColores()">
                    <i class="fas fa-eye"></i>
                    Vista Previa
                </button>
                <a href="{{ route('organizacion.index') }}" class="btn btn-secondary">
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
        display: block;
    }

    .form-text {
        color: var(--gray-500);
        font-size: 0.8rem;
        margin-top: 4px;
    }

    .logo-current {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
        padding: 16px;
        background: var(--gray-50);
        border-radius: var(--radius);
        border: 1px solid var(--gray-200);
    }

    .logo-preview {
        max-width: 200px;
        max-height: 100px;
        border: 1px solid var(--gray-300);
        padding: 0.5rem;
        border-radius: var(--radius);
        background: white;
    }

    .btn-sm {
        padding: 8px 16px;
        font-size: 0.875rem;
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .domain-section {
        background: #f0fdf4;
        border: 2px solid #10b981;
        border-radius: var(--radius);
        padding: 20px;
        margin-bottom: 20px;
    }

    .badge-enterprise {
        display: inline-block;
        background: linear-gradient(135deg, #9333ea, #7c3aed);
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-left: 8px;
    }

    .info-box {
        background: #e0f2fe;
        border: 1px solid #38bdf8;
        border-radius: var(--radius);
        padding: 14px;
        margin-bottom: 16px;
        display: flex;
        gap: 12px;
        font-size: 0.9rem;
    }

    .info-box i {
        color: #0284c7;
        font-size: 1.2rem;
        margin-top: 2px;
    }

    .info-box code {
        background: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        color: #0c4a6e;
        font-weight: 600;
    }

    .warning-box {
        background: #fef3c7;
        border: 1px solid #fbbf24;
        border-radius: var(--radius);
        padding: 14px;
        display: flex;
        gap: 12px;
        font-size: 0.9rem;
    }

    .warning-box i {
        color: #d97706;
        font-size: 1.2rem;
        margin-top: 2px;
    }

    .warning-box ol {
        margin: 8px 0 8px 20px;
        padding: 0;
    }

    .warning-box li {
        margin: 4px 0;
    }

    .warning-box code {
        background: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        color: #78350f;
        font-weight: 600;
    }

    .link-help {
        color: #0369a1;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .link-help:hover {
        text-decoration: underline;
    }

    .customization-section {
        background: #f0f9ff;
        border: 2px solid #3b82f6;
        border-radius: var(--radius);
        padding: 20px;
        margin-bottom: 20px;
    }

    .section-title {
        color: #1e40af;
        font-size: 1rem;
        font-weight: 600;
        margin: 0 0 12px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-description {
        color: #475569;
        font-size: 0.9rem;
        margin: 0 0 20px 0;
        line-height: 1.6;
    }

    .color-picker-group {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .color-input {
        width: 80px;
        height: 45px;
        padding: 4px;
        cursor: pointer;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
    }

    .color-text {
        flex: 1;
        background-color: var(--gray-100);
        font-family: monospace;
        font-weight: 600;
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
        background: linear-gradient(135deg, #06b6d4, #0891b2);
        color: white;
    }

    .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .alert {
        padding: 12px 16px;
        border-radius: var(--radius);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #10b981;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #ef4444;
    }

    .col-md-6 {
        grid-column: span 1;
    }

    .col-md-12 {
        grid-column: span 2;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .col-md-6,
        .col-md-12 {
            grid-column: span 1;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sincronizar color picker primario con input de texto
        const colorPrimario = document.getElementById('color_primario');
        const colorPrimarioText = document.getElementById('color_primario_text');

        colorPrimario.addEventListener('input', function() {
            colorPrimarioText.value = this.value;
        });

        // Sincronizar color picker secundario con input de texto
        const colorSecundario = document.getElementById('color_secundario');
        const colorSecundarioText = document.getElementById('color_secundario_text');

        colorSecundario.addEventListener('input', function() {
            colorSecundarioText.value = this.value;
        });
    });

    // Función para previsualizar colores
    function previsualizarColores() {
        const colorPrimario = document.getElementById('color_primario').value;
        const colorSecundario = document.getElementById('color_secundario').value;

        // Calcular color primario oscuro
        const colorPrimarioDark = adjustBrightness(colorPrimario, -40);

        // Aplicar temporalmente los colores al documento
        document.documentElement.style.setProperty('--primary', colorPrimario);
        document.documentElement.style.setProperty('--primary-dark', colorPrimarioDark);
        document.documentElement.style.setProperty('--secondary', colorSecundario);

        // Mostrar mensaje
        alert('Vista previa aplicada. Los colores se han aplicado temporalmente a esta página.\n\nPara hacer los cambios permanentes, haz clic en "Guardar Cambios".\n\nRecarga la página para volver a los colores originales.');
    }

    // Función para ajustar brillo de color hexadecimal
    function adjustBrightness(hex, steps) {
        hex = hex.replace('#', '');
        let r = parseInt(hex.substring(0, 2), 16);
        let g = parseInt(hex.substring(2, 4), 16);
        let b = parseInt(hex.substring(4, 6), 16);

        r = Math.max(0, Math.min(255, r + steps));
        g = Math.max(0, Math.min(255, g + steps));
        b = Math.max(0, Math.min(255, b + steps));

        return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    }

    // Función para confirmar eliminación de logo
    function confirmarEliminarLogo() {
        if (confirm('¿Estás seguro de que deseas eliminar el logo de la organización?\n\nEsta acción no se puede deshacer.')) {
            document.getElementById('deleteLogoForm').submit();
        }
    }

    // Función para mostrar instrucciones DNS
    function mostrarInstruccionesDNS() {
        const mensaje = `
═══════════════════════════════════════════════════════
  INSTRUCCIONES PARA CONFIGURAR DOMINIO PERSONALIZADO
═══════════════════════════════════════════════════════

📋 PASOS A SEGUIR:

1️⃣ REGISTRAR TU DOMINIO
   • Adquiere tu dominio en un registrador (NIC Chile, GoDaddy, etc.)
   • Ejemplo: aprnombre.cl o www.aprnombre.cl

2️⃣ CONFIGURAR DNS (en tu proveedor de dominio)
   • Tipo: CNAME
   • Host/Nombre: www (o @ para dominio raíz)
   • Destino/Valor: sistemaapr.cl
   • TTL: 3600 (1 hora) o el valor por defecto

   EJEMPLO DE CONFIGURACIÓN:
   ┌─────────────────────────────────────────┐
   │ Tipo:   CNAME                           │
   │ Host:   www                             │
   │ Valor:  sistemaapr.cl                   │
   │ TTL:    3600                            │
   └─────────────────────────────────────────┘

3️⃣ ESPERAR PROPAGACIÓN DNS
   • Tiempo: 1-48 horas (usualmente 2-4 horas)
   • Puedes verificar con: https://dnschecker.org

4️⃣ CONFIGURAR EN EL SISTEMA
   • Ingresa tu dominio en este formulario
   • Ejemplo: www.aprnombre.cl
   • NO incluyas http:// o https://

5️⃣ VERIFICACIÓN
   • Una vez guardado, accede a tu dominio
   • El sistema redirigirá automáticamente

⚠️  IMPORTANTE:
   • Solo disponible en plan Enterprise
   • El dominio debe apuntar a sistemaapr.cl
   • Los cambios DNS pueden tardar hasta 48 horas

📞 SOPORTE:
   Si necesitas ayuda, contacta a soporte@sistemaapr.cl
        `;

        alert(mensaje);
    }
</script>
@endsection
