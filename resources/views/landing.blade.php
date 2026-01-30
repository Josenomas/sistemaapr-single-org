<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema APR - Gestión Integral de Agua Potable Rural</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #1e40af;
            --secondary: #10b981;
            --dark: #1f2937;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: var(--dark);
            line-height: 1.6;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }

        .logo i {
            font-size: 2rem;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--gray-700);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .btn-login {
            background: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        /* Hero Section */
        .hero {
            margin-top: 80px;
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('/Logo1.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: white;
            padding: 100px 2rem;
            text-align: center;
            position: relative;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8), 0 0 20px rgba(0, 0, 0, 0.6);
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.8), 0 0 15px rgba(0, 0, 0, 0.6);
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            border: 2px solid white;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: white;
            color: var(--primary);
        }

        /* Features Section */
        .features {
            padding: 100px 2rem;
            background: var(--gray-50);
        }

        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .section-title p {
            font-size: 1.1rem;
            color: var(--gray-600);
        }

        .features-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .feature-card p {
            color: var(--gray-600);
            line-height: 1.8;
        }

        /* Modules Section */
        .modules {
            padding: 100px 2rem;
            background: white;
        }

        .modules-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .module-item {
            padding: 1.5rem;
            background: var(--gray-50);
            border-radius: 8px;
            border-left: 4px solid var(--primary);
            transition: all 0.3s;
        }

        .module-item:hover {
            background: var(--primary);
            color: white;
            transform: translateX(8px);
        }

        .module-item i {
            margin-right: 8px;
            color: var(--primary);
        }

        .module-item:hover i {
            color: white;
        }

        /* Pricing Section */
        .pricing {
            padding: 100px 2rem;
            background: var(--gray-50);
        }

        .pricing-cards {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .pricing-card {
            background: white;
            padding: 3rem 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s;
        }

        .pricing-card.featured {
            border: 3px solid var(--primary);
            transform: scale(1.05);
        }

        .pricing-card:hover {
            transform: translateY(-8px) scale(1.05);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .pricing-card h3 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .price {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary);
            margin: 1.5rem 0;
        }

        .price span {
            font-size: 1.2rem;
            color: var(--gray-600);
        }

        .features-list {
            list-style: none;
            margin: 2rem 0;
            text-align: left;
        }

        .features-list li {
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .features-list i {
            color: var(--secondary);
            margin-right: 8px;
        }

        /* Contact Section */
        .contact {
            padding: 100px 2rem;
            background: white;
        }

        .contact-content {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-300);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-success i {
            color: #10b981;
            font-size: 1.5rem;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert-error i {
            color: #ef4444;
            font-size: 1.5rem;
        }

        /* Footer */
        .footer {
            background: var(--dark);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }

        .footer p {
            margin-bottom: 1rem;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1.5rem;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-4px);
        }

        /* Hamburger Menu */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary);
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav {
                padding: 1rem;
            }

            .logo {
                font-size: 1.2rem;
            }

            .logo i {
                font-size: 1.5rem;
            }

            .menu-toggle {
                display: block;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                flex-direction: column;
                padding: 1rem;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                gap: 0;
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links li {
                padding: 0.75rem 0;
                border-bottom: 1px solid var(--gray-200);
            }

            .btn-login {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .hero {
                padding: 80px 1.5rem;
                margin-top: 60px;
            }

            .hero-content {
                padding: 0 0.5rem;
            }

            .hero h1 {
                font-size: 1.8rem;
                margin-bottom: 1.5rem;
                line-height: 1.3;
            }

            .hero p {
                font-size: 0.95rem;
                margin-bottom: 2rem;
                line-height: 1.6;
            }

            .cta-buttons {
                flex-direction: column;
                gap: 1rem;
                padding: 0 1rem;
            }

            .btn-primary,
            .btn-secondary {
                width: 100%;
                padding: 1rem 1.5rem;
                font-size: 1rem;
                justify-content: center;
            }

            .features,
            .modules,
            .pricing,
            .contact {
                padding: 60px 1.5rem;
            }

            .section-title h2 {
                font-size: 1.8rem;
            }

            .section-title p {
                font-size: 1rem;
            }

            .features-grid,
            .modules-grid,
            .pricing-cards {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .pricing-card.featured {
                transform: scale(1);
            }

            .pricing-card:hover {
                transform: translateY(-8px) scale(1);
            }

            /* Before & After Section */
            section[style*="padding: 100px"] {
                padding: 60px 1.5rem !important;
            }

            div[style*="grid-template-columns: repeat(auto-fit, minmax(500px, 1fr))"] {
                grid-template-columns: 1fr !important;
                gap: 2rem !important;
            }

            div[style*="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr))"] {
                grid-template-columns: 1fr !important;
                gap: 2rem !important;
            }

            div[style*="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))"] {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }

            /* Ajustar tamaños de fuente en móvil */
            div[style*="font-size: 4rem"] {
                font-size: 3rem !important;
            }

            div[style*="font-size: 3rem"][style*="font-weight: bold"] {
                font-size: 2.5rem !important;
            }

            h3[style*="font-size: 2rem"] {
                font-size: 1.5rem !important;
            }

            h3[style*="font-size: 1.8rem"] {
                font-size: 1.4rem !important;
            }

            p[style*="font-size: 1.1rem"],
            li[style*="font-size: 1.05rem"] {
                font-size: 0.95rem !important;
            }

            /* Reducir padding de las cards */
            div[style*="padding: 3rem"] {
                padding: 2rem !important;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="nav">
            <a href="{{ route('landing') }}" class="logo">
                <i class="fas fa-tint"></i>
                Sistema APR
            </a>
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="nav-links" id="navLinks">
                <li><a href="#features">Características</a></li>
                <li><a href="{{ route('conoce.boleta') }}">Conoce tu Boleta</a></li>
                <li><a href="{{ route('consulta.pago') }}">Pagar Cuenta</a></li>
                <li><a href="#pricing">Precios</a></li>
                <li><a href="#contact">Contacto</a></li>
            </ul>
            <a href="{{ route('login') }}" class="btn-login">
                <i class="fas fa-sign-in-alt"></i>
                Iniciar Sesión
            </a>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>💧 Gestión Integral para tu SSR
            <p>Sistema completo de administración para Agua Potable Rural. Controla socios, lecturas, boletas, pagos y más en una sola plataforma moderna y fácil de usar.</p>
            <div class="cta-buttons">
                <a href="{{ route('consulta.pago') }}" class="btn-primary">
                    <i class="fas fa-credit-card"></i>
                    Pagar mi Cuenta
                </a>
                <a href="{{ route('conoce.boleta') }}" class="btn-secondary">
                    <i class="fas fa-file-invoice"></i>
                    Conoce tu Boleta
                </a>
                <a href="#contact" class="btn-secondary">
                    <i class="fas fa-rocket"></i>
                    Contáctanos
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="section-title">
            <h2>¿Por qué elegir Sistema APR?</h2>
            <p>Una solución completa diseñada específicamente para la gestión de agua potable rural</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3>Rápido y Eficiente</h3>
                <p>Automatiza tareas repetitivas y ahorra tiempo en la gestión diaria de tu APR. Genera boletas masivas en segundos.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Seguro y Confiable</h3>
                <p>Tus datos están protegidos con tecnología de seguridad de última generación. Backups automáticos incluidos.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Acceso desde Cualquier Lugar</h3>
                <p>Diseño responsive que funciona perfectamente en computadores, tablets y celulares.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Reportes y Estadísticas</h3>
                <p>Visualiza el estado de tu APR con gráficos y reportes detallados en tiempo real.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3>Notificaciones Automáticas</h3>
                <p>Envío automático de boletas y recordatorios por email a tus socios.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <h3>Gestión de Inventario</h3>
                <p>Control completo del inventario de materiales, herramientas y equipos. Registro de entradas, salidas, movimientos y stock disponible en tiempo real.</p>
            </div>
        </div>
    </section>

    <!-- Before & After Section -->
    <section style="padding: 100px 2rem; background: #1f2937; color: white;">
        <div class="section-title" style="color: white;">
            <h2>Del Trabajo Manual a la Gestión Centralizada</h2>
            <p style="color: rgba(255, 255, 255, 0.8);">Transforma tu forma de trabajar</p>
        </div>
        <div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 3rem; align-items: center;">
            <!-- Antes: Trabajo Manual -->
            <div style="background: rgba(220, 38, 38, 0.1); border: 2px solid #dc2626; padding: 3rem; border-radius: 12px;">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📋</div>
                    <h3 style="font-size: 2rem; color: #fca5a5; margin-bottom: 1rem;">Antes: Trabajo Manual</h3>
                </div>
                <ul style="list-style: none; padding: 0; font-size: 1.05rem; line-height: 2;">
                    <li style="padding: 10px 0; border-bottom: 1px solid rgba(220, 38, 38, 0.3); display: flex; align-items: flex-start; gap: 12px;">
                        <span style="color: #dc2626; font-size: 1.3rem;">✗</span>
                        <span>Libros de registro en papel, difíciles de buscar y actualizar</span>
                    </li>
                    <li style="padding: 10px 0; border-bottom: 1px solid rgba(220, 38, 38, 0.3); display: flex; align-items: flex-start; gap: 12px;">
                        <span style="color: #dc2626; font-size: 1.3rem;">✗</span>
                        <span>Cálculos manuales de boletas propensos a errores</span>
                    </li>
                    <li style="padding: 10px 0; border-bottom: 1px solid rgba(220, 38, 38, 0.3); display: flex; align-items: flex-start; gap: 12px;">
                        <span style="color: #dc2626; font-size: 1.3rem;">✗</span>
                        <span>Información dispersa en múltiples cuadernos y archivos</span>
                    </li>
                    <li style="padding: 10px 0; border-bottom: 1px solid rgba(220, 38, 38, 0.3); display: flex; align-items: flex-start; gap: 12px;">
                        <span style="color: #dc2626; font-size: 1.3rem;">✗</span>
                        <span>Horas perdidas buscando datos de socios o pagos anteriores</span>
                    </li>
                    <li style="padding: 10px 0; border-bottom: 1px solid rgba(220, 38, 38, 0.3); display: flex; align-items: flex-start; gap: 12px;">
                        <span style="color: #dc2626; font-size: 1.3rem;">✗</span>
                        <span>Riesgo de pérdida de información por daño o extravío</span>
                    </li>
                    <li style="padding: 10px 0; display: flex; align-items: flex-start; gap: 12px;">
                        <span style="color: #dc2626; font-size: 1.3rem;">✗</span>
                        <span>Reportes y estadísticas elaborados manualmente cada mes</span>
                    </li>
                </ul>
            </div>

            <!-- Después: Sistema Centralizado -->
            <div style="background: linear-gradient(135deg, #10b981, #059669); padding: 3rem; border-radius: 12px; box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🚀</div>
                    <h3 style="font-size: 2rem; margin-bottom: 1rem;">Ahora: Sistema Centralizado</h3>
                </div>
                <ul style="list-style: none; padding: 0; font-size: 1.05rem; line-height: 2;">
                    <li style="padding: 10px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.2); display: flex; align-items: flex-start; gap: 12px;">
                        <span style="color: #d1fae5; font-size: 1.3rem;">✓</span>
                        <span><strong>Todo en un solo lugar:</strong> Accede a toda tu información desde cualquier dispositivo</span>
                    </li>
                    <li style="padding: 10px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.2); display: flex; align-items: flex-start; gap: 12px;">
                        <span style="color: #d1fae5; font-size: 1.3rem;">✓</span>
                        <span><strong>Automatización total:</strong> Genera boletas masivas en segundos, sin errores</span>
                    </li>
                    <li style="padding: 10px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.2); display: flex; align-items: flex-start; gap: 12px;">
                        <span style="color: #d1fae5; font-size: 1.3rem;">✓</span>
                        <span><strong>Búsqueda instantánea:</strong> Encuentra cualquier dato en milisegundos</span>
                    </li>
                    <li style="padding: 10px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.2); display: flex; align-items: flex-start; gap: 12px;">
                        <span style="color: #d1fae5; font-size: 1.3rem;">✓</span>
                        <span><strong>Respaldo automático:</strong> Tu información siempre segura y disponible</span>
                    </li>
                    <li style="padding: 10px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.2); display: flex; align-items: flex-start; gap: 12px;">
                        <span style="color: #d1fae5; font-size: 1.3rem;">✓</span>
                        <span><strong>Reportes en tiempo real:</strong> Visualiza estadísticas al instante con gráficos</span>
                    </li>
                    <li style="padding: 10px 0; display: flex; align-items: flex-start; gap: 12px;">
                        <span style="color: #d1fae5; font-size: 1.3rem;">✓</span>
                        <span><strong>Ahorra tiempo y dinero:</strong> Reduce horas de trabajo administrativo</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Estadísticas de impacto -->
        <div style="max-width: 1000px; margin: 4rem auto 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; text-align: center;">
            <div style="background: rgba(255, 255, 255, 0.1); padding: 2rem; border-radius: 12px;">
                <div style="font-size: 3rem; font-weight: bold; color: #10b981; margin-bottom: 0.5rem;">80%</div>
                <div style="font-size: 1rem; opacity: 0.9;">Menos tiempo en tareas administrativas</div>
            </div>
            <div style="background: rgba(255, 255, 255, 0.1); padding: 2rem; border-radius: 12px;">
                <div style="font-size: 3rem; font-weight: bold; color: #10b981; margin-bottom: 0.5rem;">100%</div>
                <div style="font-size: 1rem; opacity: 0.9;">Información centralizada y accesible</div>
            </div>
            <div style="background: rgba(255, 255, 255, 0.1); padding: 2rem; border-radius: 12px;">
                <div style="font-size: 3rem; font-weight: bold; color: #10b981; margin-bottom: 0.5rem;">0</div>
                <div style="font-size: 1rem; opacity: 0.9;">Errores de cálculo en boletas</div>
            </div>
            <div style="background: rgba(255, 255, 255, 0.1); padding: 2rem; border-radius: 12px;">
                <div style="font-size: 3rem; font-weight: bold; color: #10b981; margin-bottom: 0.5rem;">24/7</div>
                <div style="font-size: 1rem; opacity: 0.9;">Acceso desde cualquier lugar</div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section id="mision-vision" style="padding: 100px 2rem; background: white;">
        <div class="section-title">
            <h2>Misión y Visión</h2>
            <p>Nuestro compromiso con el agua potable rural</p>
        </div>
        <div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 3rem;">
            <!-- Misión -->
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 3rem; border-radius: 12px; color: white; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🎯</div>
                <h3 style="font-size: 1.8rem; margin-bottom: 1.5rem;">Nuestra Misión</h3>
                <p style="font-size: 1.1rem; line-height: 1.8; opacity: 0.95;">
                    Crear una plataforma <strong>fácil de usar e intuitiva</strong> que simplifique la gestión diaria de los sistemas de agua potable rural.
                    Diseñamos cada función pensando en la <strong>experiencia del usuario</strong>, eliminando la complejidad y permitiendo que cualquier persona,
                    sin conocimientos técnicos avanzados, pueda administrar eficientemente su APR. Nuestro objetivo es que la tecnología sea un <strong>aliado accesible</strong>,
                    no una barrera, democratizando la gestión del agua en zonas rurales.
                </p>
            </div>
            <!-- Visión -->
            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 3rem; border-radius: 12px; color: white; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🌟</div>
                <h3 style="font-size: 1.8rem; margin-bottom: 1.5rem;">Nuestra Visión</h3>
                <p style="font-size: 1.1rem; line-height: 1.8; opacity: 0.95;">
                    Ser reconocidos como la plataforma más <strong>intuitiva y amigable</strong> para la gestión de agua potable rural,
                    donde la <strong>simplicidad y la eficiencia</strong> se encuentran. Aspiramos a que cada APR en Chile pueda gestionar sus operaciones
                    con <strong>confianza y autonomía</strong>, utilizando una herramienta que se adapta a sus necesidades reales,
                    con una <strong>interfaz clara</strong>, <strong>flujos de trabajo naturales</strong> y un <strong>diseño pensado para humanos</strong>, no para expertos en tecnología.
                </p>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing">
        <div class="section-title">
            <h2>Planes y Precios</h2>
            <p>Elige el plan que mejor se adapte a tu APR</p>
        </div>
        <div class="pricing-cards">
            <div class="pricing-card">
                <h3>Básico</h3>
                <div class="price">$20.000<span>/mes</span></div>
                <ul class="features-list">
                    <li><i class="fas fa-check"></i> Hasta 100 socios</li>
                    <li><i class="fas fa-check"></i> Módulos básicos</li>
                    <li><i class="fas fa-check"></i> 1 usuario administrador</li>
                    <li><i class="fas fa-check"></i> Soporte por email</li>
                    <li><i class="fas fa-check"></i> Actualizaciones incluidas</li>
                </ul>
                <a href="#contact" class="btn-primary">Contratar</a>
            </div>

            <div class="pricing-card featured">
                <h3>Profesional</h3>
                <div class="price">$30.000<span>/mes</span></div>
                <ul class="features-list">
                    <li><i class="fas fa-check"></i> Hasta 500 socios</li>
                    <li><i class="fas fa-check"></i> Todos los módulos</li>
                    <li><i class="fas fa-check"></i> 5 usuarios</li>
                    <li><i class="fas fa-check"></i> Soporte prioritario</li>
                    <li><i class="fas fa-check"></i> Notificaciones por email</li>
                    <li><i class="fas fa-check"></i> Reportes avanzados</li>
                </ul>
                <a href="#contact" class="btn-primary">Contratar</a>
            </div>

            <div class="pricing-card">
                <h3>Enterprise</h3>
                <div class="price">Consultar</div>
                <ul class="features-list">
                    <li><i class="fas fa-check"></i> Socios ilimitados</li>
                    <li><i class="fas fa-check"></i> Todos los módulos</li>
                    <li><i class="fas fa-check"></i> Usuarios ilimitados</li>
                    <li><i class="fas fa-check"></i> Soporte 24/7</li>
                    <li><i class="fas fa-check"></i> Personalización</li>
                    <li><i class="fas fa-check"></i> Integración API</li>
                </ul>
                <a href="#contact" class="btn-primary">Contactar</a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="section-title">
            <h2>¿Listo para comenzar?</h2>
            <p>Contáctanos y te ayudaremos a digitalizar tu APR</p>
        </div>
        <div class="contact-content">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('contacto.enviar') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="apr">Nombre de tu APR</label>
                    <input type="text" id="apr" name="apr" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="mensaje">Mensaje</label>
                    <textarea id="mensaje" name="mensaje" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    Enviar Consulta
                </button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p><strong>Sistema APR</strong> - Gestión Integral de Agua Potable Rural</p>
        <p>&copy; 2026 Todos los derechos reservados</p>
        <div class="social-links">
            <a href="https://facebook.com/sistemaapr" target="_blank" rel="noopener noreferrer" title="Síguenos en Facebook">
                <i class="fab fa-facebook"></i>
            </a>
            <a href="https://twitter.com/sistemaapr" target="_blank" rel="noopener noreferrer" title="Síguenos en Twitter">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="https://instagram.com/sistemaapr" target="_blank" rel="noopener noreferrer" title="Síguenos en Instagram">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://linkedin.com/company/sistemaapr" target="_blank" rel="noopener noreferrer" title="Síguenos en LinkedIn">
                <i class="fab fa-linkedin"></i>
            </a>
        </div>
    </footer>

    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
                // Cerrar menú móvil después de hacer clic
                document.getElementById('navLinks').classList.remove('active');
            });
        });

        // Toggle menú móvil
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('navLinks').classList.toggle('active');
        });

        // Cerrar menú al hacer clic fuera
        document.addEventListener('click', function(event) {
            const nav = document.getElementById('navLinks');
            const toggle = document.getElementById('menuToggle');

            if (!nav.contains(event.target) && !toggle.contains(event.target)) {
                nav.classList.remove('active');
            }
        });
    </script>
</body>
</html>
