<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema APR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Elementos decorativos de fondo */
        body::before,
        body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            opacity: 0.1;
            z-index: 0;
        }

        body::before {
            width: 500px;
            height: 500px;
            background: white;
            top: -200px;
            right: -100px;
        }

        body::after {
            width: 300px;
            height: 300px;
            background: white;
            bottom: -100px;
            left: -50px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            padding: 0;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 50px 40px;
            text-align: center;
            color: white;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 1;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.95;
            font-weight: 500;
        }

        .trial-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.25);
            padding: 8px 20px;
            border-radius: 50px;
            margin-top: 15px;
            font-weight: 600;
            font-size: 0.95rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .form-wrapper {
            padding: 50px 40px;
        }

        .form-section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 1.4rem;
            color: #1f2937;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title::before {
            content: '';
            width: 4px;
            height: 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }

        .section-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.9rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            position: relative;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
            font-size: 0.95rem;
        }

        .required {
            color: #ef4444;
            margin-left: 2px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.95rem;
            z-index: 1;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f9fafb;
            color: #1f2937;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .has-icon {
            padding-left: 44px;
        }

        .is-invalid {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .is-invalid:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .invalid-feedback {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .invalid-feedback::before {
            content: '\f06a';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            align-items: start;
            gap: 12px;
            font-size: 0.95rem;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-danger::before {
            content: '\f071';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: #ef4444;
        }

        .checkbox-wrapper {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }

        .checkbox-wrapper:hover {
            background: white;
            border-color: #667eea;
        }

        .checkbox-wrapper label {
            display: flex;
            align-items: start;
            gap: 12px;
            cursor: pointer;
            font-weight: 500;
            color: #374151;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            margin-top: 2px;
            accent-color: #667eea;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .login-link {
            text-align: center;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #f3f4f6;
            color: #6b7280;
            font-size: 0.95rem;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .login-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        /* Animación de entrada */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            animation: slideUp 0.6s ease;
        }

        @media (max-width: 768px) {
            body {
                padding: 20px 15px;
            }

            .container {
                border-radius: 16px;
            }

            .header {
                padding: 40px 25px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .form-wrapper {
                padding: 35px 25px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div class="logo-icon">
                    <i class="fas fa-tint"></i>
                </div>
                <h1>Sistema APR</h1>
                <p>Gestión integral para tu organización de agua potable</p>
                <div class="trial-badge">
                    <i class="fas fa-gift"></i> 30 días gratis - Sin tarjeta de crédito
                </div>
            </div>
        </div>

        <div class="form-wrapper">
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('registro.procesar') }}" method="POST">
                @csrf

                <div class="form-section">
                    <h3 class="section-title">
                        <span class="section-icon"><i class="fas fa-building"></i></span>
                        Datos de la Organización
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nombre APR <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-building input-icon"></i>
                                <input type="text" name="nombre_apr" class="form-control has-icon @error('nombre_apr') is-invalid @enderror" value="{{ old('nombre_apr') }}" placeholder="APR Los Jardines" required>
                            </div>
                            @error('nombre_apr')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>RUT <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-id-card input-icon"></i>
                                <input type="text" name="rut" class="form-control has-icon @error('rut') is-invalid @enderror" value="{{ old('rut') }}" placeholder="12.345.678-9" required>
                            </div>
                            @error('rut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" name="email_contacto" class="form-control has-icon @error('email_contacto') is-invalid @enderror" value="{{ old('email_contacto') }}" placeholder="contacto@apr.cl" required>
                            </div>
                            @error('email_contacto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="tel" name="telefono" class="form-control has-icon" value="{{ old('telefono') }}" placeholder="+56 9 1234 5678">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3 class="section-title">
                        <span class="section-icon"><i class="fas fa-user-shield"></i></span>
                        Datos del Administrador
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nombre <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="admin_nombre" class="form-control has-icon @error('admin_nombre') is-invalid @enderror" value="{{ old('admin_nombre') }}" placeholder="Juan" required>
                            </div>
                            @error('admin_nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Apellido <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="admin_apellido" class="form-control has-icon @error('admin_apellido') is-invalid @enderror" value="{{ old('admin_apellido') }}" placeholder="Pérez" required>
                            </div>
                            @error('admin_apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" name="admin_email" class="form-control has-icon @error('admin_email') is-invalid @enderror" value="{{ old('admin_email') }}" placeholder="admin@apr.cl" required>
                            </div>
                            @error('admin_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="tel" name="admin_telefono" class="form-control has-icon" value="{{ old('admin_telefono') }}" placeholder="+56 9 1234 5678">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Contraseña <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" name="password" class="form-control has-icon @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres" required>
                            </div>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Confirmar Contraseña <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" name="password_confirmation" class="form-control has-icon" placeholder="Repite tu contraseña" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="checkbox-wrapper">
                    <label>
                        <input type="checkbox" name="acepta_terminos" value="1" required {{ old('acepta_terminos') ? 'checked' : '' }}>
                        <span>
                            Acepto los <a href="{{ route('terminos.condiciones') }}" target="_blank" style="color: #667eea; text-decoration: none; font-weight: 600;">términos y condiciones</a> <span class="required">*</span>
                        </span>
                    </label>
                    @error('acepta_terminos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="checkbox-wrapper">
                    <label>
                        <input type="checkbox" name="acepta_privacidad" value="1" required {{ old('acepta_privacidad') ? 'checked' : '' }}>
                        <span>
                            He leído y acepto la <a href="{{ route('politicas.privacidad') }}" target="_blank" style="color: #667eea; text-decoration: none; font-weight: 600;">política de privacidad</a> y autorizo el tratamiento de mis datos personales <span class="required">*</span>
                        </span>
                    </label>
                    @error('acepta_privacidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-rocket"></i> Crear Cuenta Gratis
                </button>

                <div class="login-link">
                    ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
