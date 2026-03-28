<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Sistema APR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --gray: #64748b;
            --gray-200: #e2e8f0;
            --gray-700: #334155;
            --success: #10b981;
            --danger: #ef4444;
            --white: #ffffff;
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --radius: 0.5rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #6b46c1 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .reset-container {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 480px;
            padding: 48px 40px;
        }

        .reset-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .logo-icon i {
            font-size: 2.5rem;
            color: white;
        }

        .reset-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-700);
            margin-bottom: 12px;
        }

        .reset-subtitle {
            font-size: 0.95rem;
            color: var(--gray);
            line-height: 1.6;
        }

        .info-box {
            background: #fef3c7;
            border: 1px solid #fde68a;
            border-radius: var(--radius);
            padding: 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .info-box i {
            color: #d97706;
            font-size: 1.25rem;
            margin-top: 2px;
        }

        .info-box p {
            font-size: 0.875rem;
            color: var(--gray-700);
            line-height: 1.6;
            margin: 0;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--gray-700);
            font-weight: 600;
            font-size: 0.875rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            font-size: 1.125rem;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius);
            font-size: 1rem;
            transition: all 0.3s;
            font-family: inherit;
        }

        .password-input {
            padding-right: 48px;
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: var(--primary);
        }

        .toggle-password i {
            font-size: 1.125rem;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .form-input:disabled {
            background: #f9fafb;
            cursor: not-allowed;
        }

        .alert {
            padding: 14px 16px;
            border-radius: var(--radius);
            margin-bottom: 24px;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .password-requirements {
            background: #f9fafb;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 12px 16px;
            margin-bottom: 24px;
        }

        .password-requirements h4 {
            font-size: 0.875rem;
            color: var(--gray-700);
            margin-bottom: 8px;
            font-weight: 600;
        }

        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .password-requirements li {
            font-size: 0.875rem;
            color: var(--gray);
            padding: 4px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .password-requirements li i {
            color: var(--primary);
            font-size: 0.75rem;
        }

        .btn-primary {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .reset-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--gray-200);
            color: var(--gray);
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <div class="logo-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h1 class="reset-title">Nueva Contraseña</h1>
            <p class="reset-subtitle">Ingresa tu nueva contraseña para restablecer el acceso a tu cuenta</p>
        </div>

        <div class="info-box">
            <i class="fas fa-shield-alt"></i>
            <p>
                Por seguridad, este enlace expirará en 60 minutos desde que fue generado.
            </p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <label for="email_display" class="form-label">Email</label>
                <div class="input-wrapper">
                    <input
                        type="email"
                        id="email_display"
                        class="form-input"
                        value="{{ $email }}"
                        disabled
                    >
                    <i class="fas fa-envelope input-icon"></i>
                </div>
            </div>

            <div class="password-requirements">
                <h4>Requisitos de la contraseña:</h4>
                <ul>
                    <li><i class="fas fa-check-circle"></i> Mínimo 6 caracteres</li>
                    <li><i class="fas fa-check-circle"></i> Las contraseñas deben coincidir</li>
                </ul>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Nueva Contraseña</label>
                <div class="input-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input password-input"
                        placeholder="Ingrese su nueva contraseña"
                        required
                        autofocus
                    >
                    <i class="fas fa-lock input-icon"></i>
                    <button type="button" class="toggle-password" onclick="togglePassword('password', 'toggleIcon1')">
                        <i class="fas fa-eye" id="toggleIcon1"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                <div class="input-wrapper">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-input password-input"
                        placeholder="Confirme su nueva contraseña"
                        required
                    >
                    <i class="fas fa-lock input-icon"></i>
                    <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation', 'toggleIcon2')">
                        <i class="fas fa-eye" id="toggleIcon2"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-check"></i>
                Restablecer Contraseña
            </button>
        </form>

        <div class="reset-footer">
            <p>&copy; {{ date('Y') }} Sistema APR - Agua Potable Rural</p>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
