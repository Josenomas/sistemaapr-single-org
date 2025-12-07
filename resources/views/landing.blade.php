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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 2rem;
            text-align: center;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: white;
            color: var(--primary);
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
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

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .nav-links {
                display: none;
            }

            .pricing-card.featured {
                transform: scale(1);
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
            <ul class="nav-links">
                <li><a href="#features">Características</a></li>
                <li><a href="#modules">Módulos</a></li>
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
            <h1>💧 Gestión Integral para tu APR</h1>
            <p>Sistema completo de administración para Agua Potable Rural. Controla socios, lecturas, boletas, pagos y más en una sola plataforma moderna y fácil de usar.</p>
            <div class="cta-buttons">
                <a href="#contact" class="btn-primary">
                    <i class="fas fa-rocket"></i>
                    Solicitar Demo
                </a>
                <a href="#features" class="btn-secondary">
                    Ver Características
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
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Soporte Técnico</h3>
                <p>Equipo de soporte disponible para ayudarte cuando lo necesites.</p>
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section id="modules" class="modules">
        <div class="section-title">
            <h2>Módulos Incluidos</h2>
            <p>Todo lo que necesitas en un solo sistema</p>
        </div>
        <div class="modules-grid">
            <div class="module-item">
                <i class="fas fa-users"></i>
                <strong>Gestión de Socios</strong>
            </div>
            <div class="module-item">
                <i class="fas fa-tachometer-alt"></i>
                <strong>Lecturas de Medidores</strong>
            </div>
            <div class="module-item">
                <i class="fas fa-file-invoice-dollar"></i>
                <strong>Generación de Boletas</strong>
            </div>
            <div class="module-item">
                <i class="fas fa-dollar-sign"></i>
                <strong>Control de Pagos</strong>
            </div>
            <div class="module-item">
                <i class="fas fa-bell"></i>
                <strong>Notificaciones</strong>
            </div>
            <div class="module-item">
                <i class="fas fa-boxes"></i>
                <strong>Inventario</strong>
            </div>
            <div class="module-item">
                <i class="fas fa-shopping-cart"></i>
                <strong>Compras</strong>
            </div>
            <div class="module-item">
                <i class="fas fa-tools"></i>
                <strong>Trabajos Realizados</strong>
            </div>
            <div class="module-item">
                <i class="fas fa-calendar-check"></i>
                <strong>Asistencias</strong>
            </div>
            <div class="module-item">
                <i class="fas fa-user-tie"></i>
                <strong>Directiva</strong>
            </div>
            <div class="module-item">
                <i class="fas fa-ticket-alt"></i>
                <strong>Mesa de Ayuda</strong>
            </div>
            <div class="module-item">
                <i class="fas fa-history"></i>
                <strong>Actividad Reciente</strong>
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
        <p>&copy; 2025 Todos los derechos reservados</p>
        <div class="social-links">
            <a href="https://facebook.com/tuapr" target="_blank" rel="noopener noreferrer" title="Síguenos en Facebook">
                <i class="fab fa-facebook"></i>
            </a>
            <a href="https://twitter.com/tuapr" target="_blank" rel="noopener noreferrer" title="Síguenos en Twitter">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="https://instagram.com/tuapr" target="_blank" rel="noopener noreferrer" title="Síguenos en Instagram">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://linkedin.com/company/tuapr" target="_blank" rel="noopener noreferrer" title="Síguenos en LinkedIn">
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
            });
        });
    </script>
</body>
</html>
