<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Sistema APR</title>
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

        .forgot-container {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 480px;
            padding: 48px 40px;
        }

        .forgot-header {
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

        .forgot-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-700);
            margin-bottom: 12px;
        }

        .forgot-subtitle {
            font-size: 0.95rem;
            color: var(--gray);
            line-height: 1.6;
        }

        .info-box {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: var(--radius);
            padding: 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .info-box i {
            color: var(--primary);
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

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
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

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
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

        .btn-secondary {
            width: 100%;
            padding: 14px 24px;
            background: white;
            color: var(--gray-700);
            border: 2px solid var(--gray-200);
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 12px;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: var(--gray-200);
        }

        .forgot-footer {
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
    <div class="forgot-container">
        <div class="forgot-header">
            <div class="logo-icon">
                <i class="fas fa-key"></i>
            </div>
            <h1 class="forgot-title">Recuperar Contraseña</h1>
            <p class="forgot-subtitle">Ingresa tu email y te enviaremos un enlace para restablecer tu contraseña</p>
        </div>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <p>
                Te enviaremos un correo con instrucciones para crear una nueva contraseña.
                El enlace será válido por 60 minutos.
            </p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <div class="input-wrapper">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        placeholder="correo@ejemplo.com"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                    <i class="fas fa-envelope input-icon"></i>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-paper-plane"></i>
                Enviar Enlace de Recuperación
            </button>

            <a href="{{ route('login') }}" class="btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Volver al Login
            </a>
        </form>

        <div class="forgot-footer">
            <p>&copy; {{ date('Y') }} Sistema APR - Agua Potable Rural</p>
        </div>
    </div>
</body>
</html>
