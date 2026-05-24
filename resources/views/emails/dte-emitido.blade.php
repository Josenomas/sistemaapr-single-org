<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $boleta->tipo_documento }} N° {{ $boleta->folio_sii }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            line-height: 1.6;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }

        .message {
            color: #666;
            margin-bottom: 30px;
            font-size: 15px;
        }

        .document-info {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }

        .document-info h3 {
            color: #667eea;
            font-size: 16px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            color: #333;
            font-weight: 600;
        }

        .total-row {
            background: white;
            margin-top: 10px;
            padding: 15px;
            border-radius: 4px;
        }

        .total-row .info-value {
            color: #667eea;
            font-size: 20px;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }

        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 13px;
        }

        .footer-links {
            margin-top: 15px;
        }

        .footer-links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 10px;
        }

        .divider {
            height: 1px;
            background: #e9ecef;
            margin: 30px 0;
        }

        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }

            .header {
                padding: 30px 20px;
            }

            .content {
                padding: 30px 20px;
            }

            .info-row {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $organizacion->nombre }}</h1>
            <p>{{ $boleta->tipo_documento }} Electrónico N° {{ $boleta->folio_sii }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Estimado/a {{ $boleta->socio->nombre_completo }},
            </div>

            <div class="message">
                @if($boleta->esNotaCredito())
                    Le informamos que se ha emitido una <strong>Nota de Crédito Electrónica</strong> asociada a su documento.
                @elseif($boleta->esNotaDebito())
                    Le informamos que se ha emitido una <strong>Nota de Débito Electrónica</strong> asociada a su documento.
                @else
                    Le informamos que se ha emitido su <strong>{{ $boleta->tipo_documento }}</strong> correspondiente al servicio de agua potable.
                @endif
            </div>

            <!-- Document Info -->
            <div class="document-info">
                <h3>
                    📄 Detalles del Documento
                </h3>

                <div class="info-row">
                    <span class="info-label">Tipo de Documento:</span>
                    <span class="info-value">
                        @if($boleta->tipo_dte == 33)
                            <span class="badge badge-info">Factura Electrónica</span>
                        @elseif($boleta->tipo_dte == 39)
                            <span class="badge badge-success">Boleta Electrónica</span>
                        @elseif($boleta->tipo_dte == 61)
                            <span class="badge badge-danger">Nota de Crédito</span>
                        @elseif($boleta->tipo_dte == 56)
                            <span class="badge badge-warning">Nota de Débito</span>
                        @endif
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Folio SII:</span>
                    <span class="info-value">{{ $boleta->folio_sii }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Número de Boleta:</span>
                    <span class="info-value">{{ $boleta->numero_boleta }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Fecha de Emisión:</span>
                    <span class="info-value">{{ $boleta->fecha_emision_dte->format('d/m/Y H:i') }}</span>
                </div>

                @if(!$boleta->esNota())
                    <div class="info-row">
                        <span class="info-label">Período:</span>
                        <span class="info-value">{{ $boleta->mes_texto }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Consumo:</span>
                        <span class="info-value">{{ $boleta->consumo_m3 }} m³</span>
                    </div>

                    @if(!$boleta->fecha_vencimiento->isPast())
                        <div class="info-row">
                            <span class="info-label">Fecha de Vencimiento:</span>
                            <span class="info-value">{{ $boleta->fecha_vencimiento->format('d/m/Y') }}</span>
                        </div>
                    @endif
                @else
                    <div class="info-row">
                        <span class="info-label">Motivo:</span>
                        <span class="info-value">{{ $boleta->motivo_nota }}</span>
                    </div>
                @endif

                <div class="total-row">
                    <div class="info-row">
                        <span class="info-label" style="font-size: 16px;">Total:</span>
                        <span class="info-value">{{ $boleta->esNota() ? '$' . number_format($boleta->monto_nota, 0, ',', '.') : $boleta->total_formateado }}</span>
                    </div>
                </div>
            </div>

            @if(!$boleta->esNota())
                <div class="divider"></div>

                <div class="message">
                    El PDF con el timbre electrónico está adjunto a este correo. Puede descargarlo y conservarlo para sus registros.
                </div>

                @if($boleta->estado !== 'pagada')
                    <p style="color: #666; font-size: 14px; margin-top: 20px;">
                        💡 <strong>Recuerde:</strong> Puede realizar el pago en nuestras oficinas o a través de los medios de pago habilitados.
                    </p>
                @endif
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <strong>{{ $organizacion->nombre }}</strong><br>
            @if($organizacion->direccion)
                {{ $organizacion->direccion }}<br>
            @endif
            @if($organizacion->telefono)
                Teléfono: {{ $organizacion->telefono }}<br>
            @endif
            @if($organizacion->email)
                Email: {{ $organizacion->email }}
            @endif

            <div class="footer-links">
                <p style="font-size: 12px; color: #999; margin-top: 20px;">
                    Este es un correo automático, por favor no responder.<br>
                    Documento tributario electrónico emitido conforme a la normativa vigente del SII.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
