<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta tu Cuenta - Sistema APR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #1e40af;
            --secondary: #10b981;
            --danger: #ef4444;
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
            background: linear-gradient(135deg, #230830 0%, #1B0C24 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .container {
            max-width: 500px;
            width: 100%;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 3rem 2.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
            text-decoration: none;
        }

        .logo:hover {
            color: var(--primary);
            text-decoration: none;
        }

        .logo i {
            font-size: 2.5rem;
        }

        .header h1 {
            font-size: 1.75rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .header p {
            color: var(--gray-600);
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--gray-300);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-600);
        }

        .input-icon .form-control {
            padding-left: 3rem;
        }

        .help-text {
            font-size: 0.85rem;
            color: var(--gray-600);
            margin-top: 0.5rem;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }

        .btn-back {
            width: 100%;
            background: var(--gray-200);
            color: var(--gray-700);
            padding: 0.875rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 1rem;
            text-decoration: none;
        }

        .btn-back:hover {
            background: var(--gray-300);
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
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
            font-size: 1.25rem;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert-error i {
            color: #ef4444;
            font-size: 1.25rem;
        }

        .info-box {
            background: var(--gray-50);
            border-left: 4px solid var(--primary);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .info-box h3 {
            font-size: 0.95rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-box ul {
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 0.9rem;
            color: var(--gray-600);
        }

        .info-box li {
            padding: 0.25rem 0;
            padding-left: 1.5rem;
            position: relative;
        }

        .info-box li:before {
            content: "•";
            position: absolute;
            left: 0.5rem;
            color: var(--primary);
            font-weight: bold;
        }

        @media (max-width: 640px) {
            body {
                padding: 1rem;
            }

            .card {
                padding: 2rem 1.5rem;
            }

            .header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <a href="{{ route('landing') }}" class="logo">
                    <i class="fas fa-tint"></i>
                    Sistema APR
                </a>
                <h1>Consulta tu Cuenta</h1>
                <p>Ingresa tu RUT para consultar tus boletas pendientes</p>
            </div>

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div class="info-box">
                <h3><i class="fas fa-info-circle"></i> ¿Qué puedes hacer aquí?</h3>
                <ul>
                    <li>Consultar tus boletas pendientes de pago</li>
                    <li>Ver el monto total adeudado</li>
                    <li>Pagar en línea con Flow (débito/crédito)</li>
                </ul>
            </div>

            <form action="{{ route('consulta.buscar') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="rut">RUT del Titular</label>
                    <div class="input-icon">
                        <i class="fas fa-id-card"></i>
                        <input
                            type="text"
                            id="rut"
                            name="rut"
                            class="form-control"
                            placeholder="Ejemplo: 12.345.678-9"
                            value="{{ old('rut') }}"
                            required
                            maxlength="12"
                        >
                    </div>
                    <div class="help-text">
                        Ingresa el RUT con puntos y guión (ej: 12.345.678-9)
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-search"></i>
                    Consultar Cuenta
                </button>
            </form>

            <a href="{{ route('landing') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Volver al Inicio
            </a>
        </div>
    </div>

    <script>
        // Auto-formatear RUT mientras se escribe
        const rutInput = document.getElementById('rut');

        rutInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9kK]/g, '');

            if (value.length > 1) {
                let body = value.slice(0, -1);
                let dv = value.slice(-1).toUpperCase();

                // Formatear con puntos
                body = body.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                e.target.value = body + '-' + dv;
            } else {
                e.target.value = value;
            }
        });
    </script>
</body>
</html>
