<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            let checkInterval = null;

            function startSSE() {
                if (eventSource) return; // Ya hay conexión activa

                eventSource = new EventSource('/notificaciones-sistema/stream?lastId=' + lastNotifId);

                eventSource.onmessage = function(event) {
                    const notif = JSON.parse(event.data);
                    lastNotifId = notif.id;
                    mostrarNotificacionPopup(notif);
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

    @yield('scripts')
</body>
</html>
