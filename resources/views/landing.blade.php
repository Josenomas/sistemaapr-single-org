<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APR Pitrelahue - Agua Potable Rural</title>
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
            background: transparent;
            color: var(--primary);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: 2px solid var(--primary);
        }

        .btn-login:hover {
            background: var(--primary);
            color: white;
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

        /* Sections */
        section {
            padding: 100px 2rem;
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

        .content-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
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

        .card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .card p {
            color: var(--gray-600);
            line-height: 1.8;
        }

        /* Info Cards */
        .info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            border-radius: 16px;
            color: white;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .info-card h3 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: white;
        }

        .info-card p {
            opacity: 0.9;
            font-size: 0.95rem;
            color: white;
        }

        /* Contact Form */
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

        /* Footer */
        .footer {
            background: var(--dark);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }

        .footer p {
            margin-bottom: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .section-title h2 {
                font-size: 1.8rem;
            }

            .content-grid {
                grid-template-columns: 1fr;
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
                APR Pitrelahue
            </a>
            <ul class="nav-links">
                <li><a href="#nosotros">Nosotros</a></li>
                <li><a href="#servicios">Servicios</a></li>
                <li><a href="{{ route('conoce.boleta') }}">Consulta tu Boleta</a></li>
                <li><a href="#contact">Contacto</a></li>
            </ul>
            <div>
                <a href="{{ route('login') }}" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    Acceso Socios
                </a>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>💧 APR Pitrelahue</h1>
            <p>Agua Potable Rural de calidad para nuestra comunidad. Comprometidos con el servicio y bienestar de nuestros socios.</p>
            <div class="cta-buttons">
                <a href="{{ route('conoce.boleta') }}" class="btn-primary">
                    <i class="fas fa-file-invoice"></i>
                    Consulta tu Boleta
                </a>
                <a href="#contact" class="btn-secondary">
                    <i class="fas fa-phone"></i>
                    Contáctanos
                </a>
            </div>
        </div>
    </section>

    <!-- Nosotros Section -->
    <section id="nosotros" style="background: var(--gray-50);">
        <div class="section-title">
            <h2>Quiénes Somos</h2>
            <p>APR Pitrelahue - Compromiso con nuestra comunidad</p>
        </div>
        <div class="content-grid">
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Organización Comunitaria</h3>
                <p>Somos una organización sin fines de lucro, conformada por socios de nuestra comunidad, dedicada a proveer agua potable de calidad.</p>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-tint"></i>
                </div>
                <h3>Agua de Calidad</h3>
                <p>Garantizamos agua potable segura y de calidad, cumpliendo con todas las normativas sanitarias vigentes.</p>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3>Compromiso</h3>
                <p>Trabajamos día a día para mejorar nuestro servicio y satisfacer las necesidades de nuestros socios.</p>
            </div>
        </div>
    </section>

    <!-- Servicios Section -->
    <section id="servicios" style="background: white;">
        <div class="section-title">
            <h2>Nuestros Servicios</h2>
            <p>Servicios que ofrecemos a nuestros socios</p>
        </div>
        <div class="content-grid">
            <div class="info-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">💧</div>
                <h3>Suministro de Agua Potable</h3>
                <p>Servicio continuo de agua potable de calidad para toda nuestra comunidad</p>
            </div>
            <div class="info-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🔧</div>
                <h3>Mantención y Reparaciones</h3>
                <p>Atención de emergencias y mantención preventiva de nuestro sistema</p>
            </div>
            <div class="info-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📱</div>
                <h3>Gestión en Línea</h3>
                <p>Consulta tu boleta, pagos e información de consumo desde nuestra plataforma digital</p>
            </div>
        </div>
    </section>

    <!-- Información Section -->
    <section style="background: var(--gray-50);">
        <div class="section-title">
            <h2>Información Importante</h2>
            <p>Datos relevantes para nuestros socios</p>
        </div>
        <div class="content-grid">
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Horario de Atención</h3>
                <p><strong>Lunes a Viernes:</strong> 9:00 AM - 17:00 PM<br>
                <strong>Emergencias:</strong> 24/7</p>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <h3>Contacto</h3>
                <p><strong>Teléfono:</strong> +56 9 1234 5678<br>
                <strong>Email:</strong> contacto@aprpitrelahue.cl</p>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h3>Ubicación</h3>
                <p><strong>Dirección:</strong> Pitrelahue<br>
                <strong>Comuna:</strong> Puerto Montt<br>
                <strong>Región:</strong> Los Lagos</p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" style="background: white;">
        <div class="section-title">
            <h2>Contáctanos</h2>
            <p>¿Tienes alguna consulta o sugerencia? Escríbenos</p>
        </div>
        <div class="contact-content">
            @if(session('success'))
                <div style="padding: 1rem; background: #d1fae5; color: #065f46; border-radius: 8px; margin-bottom: 2rem; text-align: center;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="padding: 1rem; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 2rem; text-align: center;">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
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
                    <label for="mensaje">Mensaje</label>
                    <textarea id="mensaje" name="mensaje" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    Enviar Mensaje
                </button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p><strong>APR Pitrelahue</strong> - Agua Potable Rural</p>
        <p>Pitrelahue, Puerto Montt - Región de Los Lagos</p>
        <p>&copy; 2026 Todos los derechos reservados</p>
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
