<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de Cuenta - Sistema APR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #1e40af;
            --secondary: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
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
            background: linear-gradient(135deg, #6E049F 0%, #300246 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            margin-bottom: 2rem;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--gray-200);
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
            text-decoration: none;
        }

        .logo i {
            font-size: 2rem;
        }

        .header h1 {
            font-size: 1.75rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .socio-info {
            background: var(--gray-50);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .socio-info h2 {
            font-size: 1.25rem;
            color: var(--dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .socio-info h2 i {
            color: var(--primary);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 0.85rem;
            color: var(--gray-600);
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 1.05rem;
            color: var(--dark);
            font-weight: 600;
        }

        .alert {
            padding: 1.25rem;
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
            font-size: 2rem;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .alert-warning i {
            color: #f59e0b;
            font-size: 2rem;
        }

        .resumen-deuda {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .resumen-deuda h3 {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .resumen-deuda .monto {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .resumen-deuda .detalle {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .boletas-list {
            margin-bottom: 2rem;
        }

        .boletas-list h3 {
            font-size: 1.25rem;
            color: var(--dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .boleta-item {
            background: var(--gray-50);
            border-left: 4px solid var(--warning);
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }

        .boleta-item:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .boleta-item.vencida {
            border-left-color: var(--danger);
        }

        .boleta-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .boleta-numero {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--dark);
        }

        .boleta-estado {
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .estado-pendiente {
            background: #fef3c7;
            color: #92400e;
        }

        .estado-vencida {
            background: #fee2e2;
            color: #991b1b;
        }

        .boleta-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.75rem;
            font-size: 0.9rem;
        }

        .boleta-detail {
            display: flex;
            flex-direction: column;
        }

        .boleta-detail-label {
            color: var(--gray-600);
            font-size: 0.85rem;
        }

        .boleta-detail-value {
            color: var(--dark);
            font-weight: 600;
        }

        .monto-destacado {
            color: var(--danger);
            font-size: 1.1rem;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary), #059669);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
        }

        .btn-secondary {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .btn-secondary:hover {
            background: var(--gray-300);
        }

        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .payment-info {
            background: #dbeafe;
            border-left: 4px solid var(--primary);
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 2rem;
        }

        .payment-info h4 {
            font-size: 1rem;
            color: var(--dark);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .payment-info ul {
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 0.9rem;
            color: var(--gray-700);
        }

        .payment-info li {
            padding: 0.25rem 0;
            padding-left: 1.5rem;
            position: relative;
        }

        .payment-info li:before {
            content: "✓";
            position: absolute;
            left: 0.5rem;
            color: var(--primary);
            font-weight: bold;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .card {
                padding: 1.5rem;
            }

            .resumen-deuda .monto {
                font-size: 2.5rem;
            }

            .boleta-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
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
                <h1>Estado de Cuenta</h1>
            </div>

            <div class="socio-info">
                <h2>
                    <i class="fas fa-user"></i>
                    Información del Socio
                </h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Número de Socio</span>
                        <span class="info-value">{{ $socio->numero_socio }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nombre</span>
                        <span class="info-value">{{ $socio->nombre_completo }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">RUT</span>
                        <span class="info-value">{{ $socio->rut }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Dirección</span>
                        <span class="info-value">{{ $socio->direccion ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            @if($boletas->isEmpty())
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>¡Todo al día!</strong><br>
                        No registra pagos pendientes. Su cuenta está al día.
                    </div>
                </div>
            @else
                <div class="resumen-deuda">
                    <h3>Total Adeudado</h3>
                    <div class="monto">${{ number_format($totalDeuda, 0, ',', '.') }}</div>
                    <div class="detalle">{{ $boletas->count() }} {{ $boletas->count() == 1 ? 'boleta pendiente' : 'boletas pendientes' }}</div>
                </div>

                <div class="payment-info">
                    <h4>
                        <i class="fas fa-info-circle"></i>
                        Pago en Línea
                    </h4>
                    <ul>
                        <li>Paga de forma segura con tarjeta de débito o crédito</li>
                        <li>Procesado por Flow (pasarela de pagos certificada)</li>
                        <li>Recibe tu comprobante al instante</li>
                    </ul>
                </div>

                <div class="boletas-list">
                    <h3>
                        <i class="fas fa-file-invoice"></i>
                        Boletas Pendientes
                    </h3>

                    @foreach($boletas as $boleta)
                        <div class="boleta-item {{ $boleta->estado === 'vencida' ? 'vencida' : '' }}">
                            <div class="boleta-header">
                                <span class="boleta-numero">
                                    <i class="fas fa-hashtag"></i>
                                    {{ $boleta->numero_boleta }}
                                </span>
                                <span class="boleta-estado estado-{{ $boleta->estado }}">
                                    @if($boleta->estado === 'vencida')
                                        <i class="fas fa-exclamation-triangle"></i> VENCIDA
                                    @else
                                        <i class="fas fa-clock"></i> PENDIENTE
                                    @endif
                                </span>
                            </div>

                            <div class="boleta-details">
                                <div class="boleta-detail">
                                    <span class="boleta-detail-label">Período</span>
                                    <span class="boleta-detail-value">{{ $boleta->mes_texto }}</span>
                                </div>
                                <div class="boleta-detail">
                                    <span class="boleta-detail-label">Consumo</span>
                                    <span class="boleta-detail-value">{{ number_format($boleta->consumo_m3, 2) }} m³</span>
                                </div>
                                <div class="boleta-detail">
                                    <span class="boleta-detail-label">Fecha Vencimiento</span>
                                    <span class="boleta-detail-value">{{ \Carbon\Carbon::parse($boleta->fecha_vencimiento)->format('d/m/Y') }}</span>
                                </div>
                                <div class="boleta-detail">
                                    <span class="boleta-detail-label">Monto</span>
                                    <span class="boleta-detail-value monto-destacado">${{ number_format($boleta->total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <form action="{{ route('consulta.generar.pago') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_socio" value="{{ $socio->id }}">
                    <input type="hidden" name="boletas" value="{{ $boletas->pluck('id')->implode(',') }}">
                    <input type="hidden" name="monto_total" value="{{ $totalDeuda }}">

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-credit-card"></i>
                            Pagar ${{ number_format($totalDeuda, 0, ',', '.') }} con Flow
                        </button>
                        <a href="{{ route('consulta.pago') }}" class="btn btn-secondary">
                            <i class="fas fa-search"></i>
                            Nueva Consulta
                        </a>
                    </div>
                </form>
            @endif

            @if($boletas->isEmpty())
                <div class="actions">
                    <a href="{{ route('consulta.pago') }}" class="btn btn-secondary">
                        <i class="fas fa-search"></i>
                        Nueva Consulta
                    </a>
                    <a href="{{ route('landing') }}" class="btn btn-secondary">
                        <i class="fas fa-home"></i>
                        Volver al Inicio
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
