<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Sistema APR')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f3f4f6;
            padding: 40px 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: @yield('header-color', 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)');
            padding: 40px 30px;
            text-align: center;
            color: white;
            position: relative;
        }

        .logo-container {
            margin-bottom: 20px;
        }

        .logo-container img {
            max-width: 120px;
            max-height: 80px;
            border-radius: 8px;
            background: white;
            padding: 10px;
        }

        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .email-header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.95;
        }

        .email-body {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .content-section {
            margin-bottom: 30px;
        }

        .alert-box {
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
            border-left: 4px solid;
        }

        .alert-info {
            background: #dbeafe;
            border-color: #3b82f6;
            color: #1e40af;
        }

        .alert-warning {
            background: #fef3c7;
            border-color: #f59e0b;
            color: #92400e;
        }

        .alert-success {
            background: #d1fae5;
            border-color: #059669;
            color: #065f46;
        }

        .alert-danger {
            background: #fee2e2;
            border-color: #dc2626;
            color: #991b1b;
        }

        .alert-box h2 {
            margin: 0 0 10px 0;
            font-size: 20px;
        }

        .alert-box p {
            margin: 0;
            font-size: 15px;
            line-height: 1.6;
        }

        .info-card {
            background: #f9fafb;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 30px;
            border: 1px solid #e5e7eb;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 14px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 14px;
        }

        .info-value {
            color: #1f2937;
            font-weight: 600;
            font-size: 15px;
        }

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            padding: 16px 40px;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            margin: 10px 0;
            text-align: center;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .btn-success {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
        }

        .btn-center {
            text-align: center;
            margin: 30px 0;
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
            margin: 30px 0;
        }

        .text-muted {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
        }

        .text-center {
            text-align: center;
        }

        .email-footer {
            background: #f9fafb;
            padding: 30px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }

        .email-footer p {
            margin: 8px 0;
        }

        .email-footer strong {
            color: #1f2937;
        }

        .social-links {
            margin: 20px 0;
        }

        .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #6b7280;
            text-decoration: none;
        }

        .footer-note {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 15px;
            font-style: italic;
        }

        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 20px 10px;
            }

            .email-header {
                padding: 30px 20px;
            }

            .email-header h1 {
                font-size: 24px;
            }

            .email-body {
                padding: 30px 20px;
            }

            .info-row {
                flex-direction: column;
                gap: 5px;
            }

            .btn {
                display: block;
                width: 100%;
            }
        }
    </style>
    @yield('extra-styles')
</head>
<body>
    <div class="email-wrapper">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td align="center">
                    <div class="email-container">
                        <!-- Header -->
                        <div class="email-header">
                            @if(isset($organizacion) && $organizacion->logo)
                            <div class="logo-container">
                                <img src="{{ asset('storage/' . $organizacion->logo) }}" alt="{{ $organizacion->nombre_apr }}">
                            </div>
                            @endif

                            <h1>@yield('email-title')</h1>
                            <p>@yield('email-subtitle', 'Sistema APR - Gestión de Agua Potable')</p>
                        </div>

                        <!-- Body -->
                        <div class="email-body">
                            @yield('content')
                        </div>

                        <!-- Footer -->
                        <div class="email-footer">
                            @if(isset($organizacion))
                                <p><strong>{{ $organizacion->nombre_apr }}</strong></p>
                                @if($organizacion->email_contacto)
                                <p>📧 {{ $organizacion->email_contacto }}</p>
                                @endif
                                @if($organizacion->telefono_contacto)
                                <p>📞 {{ $organizacion->telefono_contacto }}</p>
                                @endif
                            @else
                                <p><strong>Sistema APR</strong></p>
                                <p>Gestión integral para organizaciones de agua potable rural</p>
                            @endif

                            <div class="divider"></div>

                            <p class="footer-note">
                                Este es un email automático, por favor no responder directamente.
                            </p>

                            @yield('footer-extra')
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
