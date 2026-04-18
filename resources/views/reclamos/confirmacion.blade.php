<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reclamo Registrado - Sistema APR</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .success-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 50px 40px;
            text-align: center;
        }

        .success-header i {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: checkmark 0.6s ease-in-out;
        }

        @keyframes checkmark {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
            }
        }

        .success-header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .success-header p {
            opacity: 0.9;
            font-size: 1.05rem;
        }

        .content {
            padding: 40px;
        }

        .reclamo-number {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 30px;
        }

        .reclamo-number .label {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .reclamo-number .number {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .info-box h3 {
            color: #28a745;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .info-box p {
            color: #495057;
            line-height: 1.7;
            margin-bottom: 10px;
        }

        .info-box ul {
            margin: 15px 0 15px 25px;
            color: #495057;
        }

        .info-box ul li {
            margin-bottom: 8px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-row .label {
            color: #6c757d;
            font-weight: 500;
        }

        .detail-row .value {
            color: #212529;
            font-weight: 600;
            text-align: right;
        }

        .actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #e9ecef;
        }

        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: #667eea;
            color: white;
            flex: 1;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        @media print {
            body {
                background: white;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-header">
            <i class="fas fa-check-circle"></i>
            <h1>¡Reclamo Registrado Exitosamente!</h1>
            <p>Hemos recibido tu reclamo correctamente</p>
        </div>

        <div class="content">
            <div class="reclamo-number">
                <div class="label">Tu número de reclamo es:</div>
                <div class="number">{{ $reclamo->numero_reclamo }}</div>
            </div>

            <div class="info-box">
                <h3><i class="fas fa-info-circle"></i> ¿Qué sigue ahora?</h3>
                <p><strong>Hemos enviado un email de confirmación</strong> a {{ $reclamo->email }} con los detalles de tu reclamo.</p>
                <p><strong>Plazo de respuesta:</strong> Máximo 5 días hábiles desde {{ $reclamo->created_at->format('d/m/Y') }}</p>
                <ul>
                    <li>Revisaremos tu reclamo detalladamente</li>
                    <li>Te enviaremos una respuesta por email</li>
                    <li>Guarda tu número de reclamo para hacer seguimiento</li>
                </ul>
            </div>

            <div class="detail-row">
                <span class="label">Fecha de registro:</span>
                <span class="value">{{ $reclamo->created_at->format('d/m/Y H:i') }}</span>
            </div>

            <div class="detail-row">
                <span class="label">Tipo de reclamo:</span>
                <span class="value">{{ $reclamo->tipo_reclamo_nombre }}</span>
            </div>

            <div class="detail-row">
                <span class="label">Email de contacto:</span>
                <span class="value">{{ $reclamo->email }}</span>
            </div>

            <div class="actions">
                <a href="/" class="btn btn-primary">
                    <i class="fas fa-home"></i>
                    Volver al Inicio
                </a>
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i>
                    Imprimir
                </button>
            </div>
        </div>
    </div>
</body>
</html>
