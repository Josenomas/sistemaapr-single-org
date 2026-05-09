<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- <meta name="robots" content="noindex, nofollow"> -->
    <link rel="canonical" href="{{ url()->current() }}">
    <title>@yield('title', 'Sistema APR')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Intro.js para tours guiados -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/introjs.min.css">

    @yield('styles')
    <style>
        :root {
            /* Colores personalizados de la organización */
            --primary: {{ $coloresTema['primario'] ?? '#2563eb' }};
            --primary-dark: {{ $coloresTema['primario_dark'] ?? '#1d4ed8' }};
            --primary-light: {{ $coloresTema['primario_light'] ?? '#dbeafe' }};
            --secondary: {{ $coloresTema['secundario'] ?? '#10b981' }};
            --info: #06b6d4;
            --light: #f8fafc;
            --dark: #1e293b;
            --gray: #64748b;
            --gray-50: #f9fafb;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --white: #ffffff;
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius: 0.5rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--dark);
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
        }

        header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            padding: 1rem 0;
            box-shadow: var(--shadow-lg);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: white;
        }

        .logo i {
            font-size: 1.75rem;
            color: #60a5fa;
        }

        .logo-img {
            height: 45px;
            max-width: 150px;
            object-fit: contain;
            background: white;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .logo h1 {
            font-size: 1.75rem;
            font-weight: 700;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--secondary), #0891b2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            font-size: 0.875rem;
            box-shadow: var(--shadow);
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-role {
            font-size: 0.75rem;
            opacity: 0.8;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 16px;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
        }

        main {
            padding: 32px 0;
        }

        .alert {
            padding: 14px 16px;
            border-radius: var(--radius);
            margin-bottom: 24px;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 8px;
            margin-right: 12px;
        }

        /* Campana de notificaciones */
        .notificaciones-dropdown {
            position: relative;
        }

        .help-btn {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            color: white;
            font-size: 1.25rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            margin-right: 10px;
        }

        .help-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.05);
        }

        .notif-bell {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            color: white;
            font-size: 1.25rem;
            transition: all 0.2s;
            text-decoration: none;
        }

        .notif-bell:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.05);
        }

        .notif-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: white;
            font-size: 0.625rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            animation: pulseNotif 2s infinite;
        }

        @keyframes pulseNotif {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            .header-content {
                gap: 0;
            }

            .user-menu {
                gap: 8px;
            }

            .user-info {
                display: none;
            }

            .logout-btn span {
                display: none;
            }

            .logout-btn {
                padding: 8px 12px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>

                <a href="{{ route('dashboard') }}" class="logo">
                    @php
                        $organizacion = null;
                        if (auth()->check() && auth()->user()->id_organizacion) {
                            $organizacion = \App\Models\Organizacion::find(auth()->user()->id_organizacion);
                        }
                    @endphp

                    @if($organizacion && $organizacion->logo)
                        <img src="{{ asset('storage/' . $organizacion->logo) }}"
                             alt="Logo {{ $organizacion->nombre_apr }}"
                             class="logo-img">
                    @else
                        <i class="fas fa-tint"></i>
                    @endif

                    <h1>{{ $organizacion ? $organizacion->nombre_apr : 'Sistema APR' }}</h1>
                </a>

                <div class="user-menu">
                    <!-- Campana de notificaciones -->
                    @php
                        $contadorNotificaciones = \App\Models\NotificacionSistema::query()
                            ->where(function ($q) use ($organizacion) {
                                if ($organizacion) {
                                    $q->where('id_organizacion', $organizacion->id)
                                      ->whereNull('id_usuario')
                                      ->orWhere('id_usuario', auth()->id());
                                } else {
                                    $q->where('id_usuario', auth()->id());
                                }
                            })
                            ->noLeidas()
                            ->count();
                    @endphp

                    <!-- Botón de Ayuda -->
                    <button onclick="abrirModalSoporte()" class="help-btn" title="¿Necesitas ayuda?">
                        <i class="fas fa-question-circle"></i>
                    </button>

                    <div class="notificaciones-dropdown">
                        <a href="{{ route('notificaciones-sistema.index') }}" class="notif-bell" id="notifBell">
                            <i class="fas fa-bell"></i>
                            @if($contadorNotificaciones > 0)
                                <span class="notif-badge">{{ $contadorNotificaciones > 9 ? '9+' : $contadorNotificaciones }}</span>
                            @endif
                        </a>
                    </div>

                    <div class="user-info">
                        <div class="user-avatar">
                            {{ auth()->user()->iniciales }}
                        </div>
                        <div class="user-details">
                            <div class="user-name">{{ auth()->user()->nombre_completo }}</div>
                            <div class="user-role">{{ ucfirst(auth()->user()->rol) }}</div>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Salir</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="main-wrapper">
        @include('components.sidebar')

        <main class="content-wrapper">
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

            @yield('content')
        </main>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Intro.js Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js"></script>

    <script>
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebar = document.querySelector('.sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            // Toggle sidebar
            function toggleSidebar() {
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
            }

            // Open sidebar when clicking hamburger menu
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', toggleSidebar);
            }

            // Close sidebar when clicking overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', toggleSidebar);
            }

            // Close sidebar when clicking a nav item (on mobile)
            const navItems = document.querySelectorAll('.sidebar .nav-item');
            navItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        toggleSidebar();
                    }
                });
            });
        });

        // ============================================
        // NOTIFICACIONES EN TIEMPO REAL CON SSE
        // ============================================
        @auth
        (function() {
            let lastNotifId = 0;
            let eventSource = null;

            // Cargar IDs ya mostradas desde localStorage
            let notificacionesMostradas = new Set(
                JSON.parse(localStorage.getItem('notificacionesMostradas') || '[]')
            );

            function startSSE() {
                if (eventSource) return; // Ya hay conexión activa

                eventSource = new EventSource('/notificaciones-sistema/stream?lastId=' + lastNotifId);

                eventSource.onmessage = function(event) {
                    const notif = JSON.parse(event.data);

                    // Solo mostrar si NO se ha mostrado antes
                    if (!notificacionesMostradas.has(notif.id)) {
                        notificacionesMostradas.add(notif.id);

                        // Guardar en localStorage para persistir entre páginas
                        localStorage.setItem('notificacionesMostradas',
                            JSON.stringify(Array.from(notificacionesMostradas)));

                        lastNotifId = notif.id;
                        mostrarNotificacionPopup(notif);
                    }
                };

                eventSource.onerror = function() {
                    if (eventSource) {
                        eventSource.close();
                        eventSource = null;
                    }
                };
            }

            function mostrarNotificacionPopup(notif) {
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
                    animation: slideInRight 0.3s ease-out;
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

                // Auto-cerrar después de 10 segundos
                setTimeout(() => {
                    popup.style.animation = 'slideOutRight 0.3s ease-in';
                    setTimeout(() => popup.remove(), 300);
                }, 10000);
            }

            // Agregar animaciones CSS
            if (!document.getElementById('sse-animations')) {
                const style = document.createElement('style');
                style.id = 'sse-animations';
                style.textContent = `
                    @keyframes slideInRight {
                        from { transform: translateX(400px); opacity: 0; }
                        to { transform: translateX(0); opacity: 1; }
                    }
                    @keyframes slideOutRight {
                        from { transform: translateX(0); opacity: 1; }
                        to { transform: translateX(400px); opacity: 0; }
                    }
                `;
                document.head.appendChild(style);
            }

            // Iniciar SSE cuando la página carga
            startSSE();

            // Reconectar cada 35 segundos (después del timeout del servidor)
            setInterval(() => {
                if (eventSource) {
                    eventSource.close();
                    eventSource = null;
                }
                startSSE();
            }, 35000);
        })();
        @endauth
    </script>

    <!-- Modal de Soporte Global -->
    <div id="modalSoporteGlobal" class="modal-soporte hidden">
        <div class="modal-soporte-overlay" onclick="cerrarModalSoporte()"></div>
        <div class="modal-soporte-content">
            <div class="modal-soporte-header">
                <h3><i class="fas fa-headset"></i> Solicitar Soporte</h3>
                <button onclick="cerrarModalSoporte()" class="modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-soporte-body">
                <!-- Información prellenada -->
                <div class="info-prellenada">
                    <div class="info-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="info-text">
                        <strong>Se enviará automáticamente:</strong>
                        <ul>
                            <li>Nombre de la organización</li>
                            <li>RUT y email de contacto</li>
                            <li>Usuario que reporta</li>
                        </ul>
                    </div>
                </div>

                <!-- Formulario -->
                <form id="formSoporteGlobal">
                    <div class="form-group">
                        <label for="asunto">Asunto *</label>
                        <input type="text" id="asunto" name="asunto" required
                               placeholder="Ej: Problema al generar boletas"
                               maxlength="200">
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción del problema *</label>
                        <textarea id="descripcion" name="descripcion" rows="6" required
                                  placeholder="Por favor describe el problema con el mayor detalle posible..."
                                  maxlength="2000"></textarea>
                        <small>Máximo 2000 caracteres</small>
                    </div>

                    <div class="modal-soporte-footer">
                        <button type="button" onclick="cerrarModalSoporte()" class="btn-cancelar">
                            Cancelar
                        </button>
                        <button type="submit" id="btnEnviarSoporte" class="btn-enviar">
                            <i class="fas fa-paper-plane"></i> Enviar Solicitud
                        </button>
                    </div>
                </form>

                <div id="mensajeResultadoSoporte" class="mensaje-resultado hidden"></div>
            </div>
        </div>
    </div>

    <style>
        .modal-soporte {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-soporte.hidden {
            display: none;
        }

        .modal-soporte-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .modal-soporte-content {
            position: relative;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-soporte-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 2px solid #e2e8f0;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            border-radius: 12px 12px 0 0;
        }

        .modal-soporte-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .modal-close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .modal-soporte-body {
            padding: 24px;
        }

        .info-prellenada {
            display: flex;
            gap: 12px;
            padding: 16px;
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            margin-bottom: 24px;
        }

        .info-icon {
            color: #3b82f6;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .info-text {
            color: #1e40af;
            font-size: 0.875rem;
        }

        .info-text strong {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9375rem;
        }

        .info-text ul {
            margin: 0;
            padding-left: 20px;
        }

        .info-text li {
            margin: 4px 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            color: #334155;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.9375rem;
            transition: all 0.2s;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-group small {
            display: block;
            margin-top: 6px;
            color: #64748b;
            font-size: 0.8125rem;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .modal-soporte-footer {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn-cancelar,
        .btn-enviar {
            flex: 1;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            font-size: 0.9375rem;
        }

        .btn-cancelar {
            background: #f1f5f9;
            color: #475569;
        }

        .btn-cancelar:hover {
            background: #e2e8f0;
        }

        .btn-enviar {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-enviar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-enviar:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .mensaje-resultado {
            margin-top: 16px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.9375rem;
        }

        .mensaje-resultado.hidden {
            display: none;
        }

        .mensaje-resultado.success {
            background: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
        }

        .mensaje-resultado.error {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #991b1b;
        }

        @media (max-width: 768px) {
            .modal-soporte-content {
                max-width: 100%;
                margin: 0 10px;
            }

            .modal-soporte-footer {
                flex-direction: column;
            }
        }
    </style>

    <script>
        function abrirModalSoporte() {
            document.getElementById('modalSoporteGlobal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function cerrarModalSoporte() {
            document.getElementById('modalSoporteGlobal').classList.add('hidden');
            document.getElementById('formSoporteGlobal').reset();
            document.getElementById('mensajeResultadoSoporte').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Manejar envío del formulario
        document.getElementById('formSoporteGlobal').addEventListener('submit', async function(e) {
            e.preventDefault();

            const btnEnviar = document.getElementById('btnEnviarSoporte');
            const mensajeResultado = document.getElementById('mensajeResultadoSoporte');
            const asunto = document.getElementById('asunto').value;
            const descripcion = document.getElementById('descripcion').value;

            btnEnviar.disabled = true;
            btnEnviar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
            mensajeResultado.classList.add('hidden');

            try {
                const response = await fetch('{{ route("suscripcion.enviar-soporte") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        mensaje_adicional: `ASUNTO: ${asunto}\n\n${descripcion}`
                    })
                });

                const data = await response.json();

                mensajeResultado.classList.remove('hidden');
                if (data.success) {
                    mensajeResultado.className = 'mensaje-resultado success';
                    mensajeResultado.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;

                    setTimeout(() => {
                        cerrarModalSoporte();
                    }, 2000);
                } else {
                    mensajeResultado.className = 'mensaje-resultado error';
                    mensajeResultado.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
                }
            } catch (error) {
                mensajeResultado.classList.remove('hidden');
                mensajeResultado.className = 'mensaje-resultado error';
                mensajeResultado.innerHTML = '<i class="fas fa-exclamation-circle"></i> Error al enviar la solicitud. Por favor intenta nuevamente.';
            } finally {
                btnEnviar.disabled = false;
                btnEnviar.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar Solicitud';
            }
        });

        // Cerrar modal con tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModalSoporte();
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
