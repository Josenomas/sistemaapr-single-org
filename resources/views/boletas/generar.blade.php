@extends('layouts.app')

@section('title', 'Generar Boletas - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-plus-circle"></i>
        Generar Boletas Masivas
    </h2>
    <div class="header-actions">
        <button type="button" id="startTourBtn" class="btn btn-info" title="Iniciar tutorial">
            <i class="fas fa-question-circle"></i>
            Ayuda
        </button>
        <a href="{{ route('boletas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<!-- Alertas -->
@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        {{ session('warning') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Generación Masiva de Boletas</h3>
    </div>
    <div class="card-body">
        <div class="info-box" data-intro="Lee esta información importante antes de generar boletas. El proceso es automático pero requiere que las lecturas del mes estén registradas." data-step="1">
            <i class="fas fa-info-circle"></i>
            <div>
                <h4>Información Importante</h4>
                <p>Este proceso generará automáticamente boletas para todos los socios activos del mes seleccionado.</p>
                <ul>
                    <li>Se generarán boletas basadas en las últimas lecturas registradas</li>
                    <li>El cálculo incluirá cargo fijo y cargo por consumo</li>
                    <li>Las boletas se generarán con estado "Pendiente"</li>
                    <li>No se pueden generar boletas si ya existen para el mes seleccionado</li>
                </ul>
            </div>
        </div>

        <form action="{{ route('boletas.storeGenerar') }}" method="POST" onsubmit="return confirm('¿Está seguro de generar las boletas para el mes seleccionado? Esta acción no se puede deshacer.');">
            @csrf

            <div class="form-group" data-intro="Selecciona el mes para el cual quieres generar las boletas. Asegúrate de que todas las lecturas de ese mes ya estén registradas." data-step="2">
                <label for="mes" class="form-label required">Mes a Generar</label>
                <input type="month"
                       class="form-control @error('mes') is-invalid @enderror"
                       id="mes"
                       name="mes"
                       value="{{ old('mes', $mesActual) }}"
                       required>
                @error('mes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Seleccione el mes para el cual desea generar las boletas</small>
            </div>

            <div class="warning-box" data-intro="⚠️ IMPORTANTE: Si faltan lecturas, el sistema te mostrará una advertencia con la lista de socios sin lectura. Debes registrarlas antes de generar." data-step="3">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Advertencia:</strong> Asegúrese de haber registrado todas las lecturas del mes antes de generar las boletas. Una vez generadas, no podrá regenerarlas automáticamente.
                </div>
            </div>

            <div class="form-actions" data-intro="Click aquí para iniciar el proceso de generación masiva. El sistema validará las lecturas y generará todas las boletas automáticamente." data-step="4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-cogs"></i>
                    Generar Boletas
                </button>
                <a href="{{ route('boletas.index') }}" class="btn btn-secondary">
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
        background: #dbeafe;
        border: 1px solid #93c5fd;
        border-radius: var(--radius);
        padding: 20px;
        margin-bottom: 32px;
        display: flex;
        gap: 16px;
    }

    .info-box i {
        font-size: 24px;
        color: #1e40af;
        flex-shrink: 0;
    }

    .info-box h4 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e3a8a;
        margin: 0 0 8px 0;
    }

    .info-box p {
        margin: 0 0 12px 0;
        color: #1e3a8a;
    }

    .info-box ul {
        margin: 0;
        padding-left: 20px;
        color: #1e40af;
    }

    .info-box li {
        margin-bottom: 4px;
    }

    .warning-box {
        background: #fef3c7;
        border: 1px solid #fbbf24;
        border-radius: var(--radius);
        padding: 16px;
        margin-top: 24px;
        display: flex;
        gap: 12px;
        align-items: start;
    }

    .warning-box i {
        font-size: 20px;
        color: #92400e;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .warning-box div {
        color: #78350f;
        font-size: 0.95rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 24px;
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
        max-width: 400px;
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

    .text-muted {
        color: var(--gray-500);
        font-size: 0.875rem;
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

    .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
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
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .btn-info {
        background: #06b6d4;
        color: white;
    }

    .header-actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    /* Estilos personalizados para Intro.js */
    .custom-tooltip {
        max-width: 400px;
    }

    .introjs-tooltip {
        border-radius: 12px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2) !important;
    }

    .introjs-button {
        border-radius: 6px !important;
        padding: 8px 16px !important;
        font-weight: 600 !important;
        text-shadow: none !important;
    }

    .introjs-nextbutton {
        background: var(--primary) !important;
        border: none !important;
    }

    .introjs-prevbutton {
        background: var(--gray-500) !important;
        border: none !important;
    }

    .introjs-skipbutton {
        color: var(--gray-600) !important;
    }

    .introjs-donebutton {
        background: var(--success) !important;
        border: none !important;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar el tour
        const intro = introJs();
        intro.setOptions({
            nextLabel: 'Siguiente',
            prevLabel: 'Anterior',
            doneLabel: 'Finalizar',
            skipLabel: 'Salir',
            showProgress: true,
            showBullets: false,
            exitOnOverlayClick: false,
            disableInteraction: true,
            tooltipClass: 'custom-tooltip'
        });

        // Botón para iniciar el tour
        document.getElementById('startTourBtn').addEventListener('click', function() {
            intro.start();
        });

        // Mostrar tour automáticamente solo la primera vez
        const tourShown = localStorage.getItem('generarBoletasTourShown');
        if (!tourShown) {
            setTimeout(function() {
                intro.start();
                localStorage.setItem('generarBoletasTourShown', 'true');
            }, 500);
        }

        // SSE para notificaciones en tiempo real cuando se generan boletas
        let lastNotifId = 0;
        let eventSource = null;
        let isGenerating = false;

        // Detectar cuando se envía el formulario de generación
        document.querySelector('form').addEventListener('submit', function(e) {
            isGenerating = true;
            startSSE();
        });

        function startSSE() {
            if (eventSource) return; // Ya hay conexión activa

            eventSource = new EventSource(`/notificaciones-sistema/stream?lastId=${lastNotifId}`);

            eventSource.onmessage = function(event) {
                const notif = JSON.parse(event.data);
                lastNotifId = notif.id;

                // Mostrar notificación popup
                mostrarNotificacion(notif);

                // Cerrar conexión después de recibir notificación
                eventSource.close();
                eventSource = null;
                isGenerating = false;
            };

            eventSource.onerror = function() {
                eventSource.close();
                eventSource = null;
                isGenerating = false;
            };

            // Timeout de 35 segundos
            setTimeout(() => {
                if (eventSource) {
                    eventSource.close();
                    eventSource = null;
                    isGenerating = false;
                }
            }, 35000);
        }

        function mostrarNotificacion(notif) {
            const colores = {
                'success': '#10b981',
                'info': '#06b6d4',
                'warning': '#f59e0b',
                'danger': '#ef4444'
            };

            const iconos = {
                'success': 'fa-check-circle',
                'info': 'fa-info-circle',
                'warning': 'fa-exclamation-triangle',
                'danger': 'fa-exclamation-circle'
            };

            const popup = document.createElement('div');
            popup.style.cssText = `
                position: fixed;
                top: 80px;
                right: 20px;
                background: white;
                border-left: 4px solid ${colores[notif.color] || '#06b6d4'};
                border-radius: 8px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                padding: 20px;
                max-width: 400px;
                z-index: 9999;
                animation: slideIn 0.3s ease-out;
            `;

            popup.innerHTML = `
                <div style="display: flex; gap: 12px; align-items: start;">
                    <i class="fas ${iconos[notif.color] || 'fa-bell'}" style="font-size: 24px; color: ${colores[notif.color] || '#06b6d4'}; margin-top: 2px;"></i>
                    <div style="flex: 1;">
                        <h4 style="margin: 0 0 8px 0; font-size: 1.1rem; font-weight: 600; color: #1f2937;">${notif.titulo}</h4>
                        <p style="margin: 0; color: #6b7280; font-size: 0.95rem;">${notif.mensaje}</p>
                        ${notif.url ? `<a href="${notif.url}" style="display: inline-block; margin-top: 12px; color: ${colores[notif.color] || '#06b6d4'}; font-weight: 600; text-decoration: none;">Ver detalles →</a>` : ''}
                    </div>
                    <button onclick="this.closest('div[style*=fixed]').remove()" style="background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 20px; padding: 0; line-height: 1;">×</button>
                </div>
            `;

            document.body.appendChild(popup);

            // Auto-cerrar después de 8 segundos
            setTimeout(() => {
                popup.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => popup.remove(), 300);
            }, 8000);
        }

        // Agregar animaciones CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(400px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(400px); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    });
</script>
@endsection
