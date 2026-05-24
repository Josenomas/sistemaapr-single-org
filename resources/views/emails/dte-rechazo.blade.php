<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTE Rechazado por SII</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
            margin: -30px -30px 30px -30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .alert-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .info-section {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #6b7280;
        }
        .info-value {
            color: #111827;
        }
        .glosa-box {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .glosa-box strong {
            color: #991b1b;
            display: block;
            margin-bottom: 8px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="alert-icon">⚠️</div>
            <h1>DTE Rechazado por SII</h1>
        </div>

        <p>Estimado/a,</p>

        <p>Le informamos que un Documento Tributario Electrónico (DTE) ha sido <strong>rechazado</strong> por el Servicio de Impuestos Internos (SII).</p>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Folio SII:</span>
                <span class="info-value"><strong>{{ $boleta->folio_sii }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Tipo DTE:</span>
                <span class="info-value">
                    @if($boleta->tipo_dte == 39)
                        Boleta Electrónica (39)
                    @elseif($boleta->tipo_dte == 61)
                        Nota de Crédito Electrónica (61)
                    @else
                        Tipo {{ $boleta->tipo_dte }}
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Boleta N°:</span>
                <span class="info-value">{{ $boleta->numero_boleta }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Socio:</span>
                <span class="info-value">{{ $boleta->socio->nombre_completo ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Monto:</span>
                <span class="info-value">${{ number_format($boleta->total, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha Emisión:</span>
                <span class="info-value">{{ $boleta->fecha_emision_dte ? $boleta->fecha_emision_dte->format('d/m/Y H:i') : 'N/A' }}</span>
            </div>
        </div>

        @if($glosa)
        <div class="glosa-box">
            <strong>Motivo del Rechazo:</strong>
            {{ $glosa }}
        </div>
        @endif

        <p><strong>¿Qué debe hacer?</strong></p>
        <ul>
            <li>Revisar el motivo del rechazo indicado arriba</li>
            <li>Verificar los datos del DTE emitido</li>
            <li>Contactar con LibreDTE o el SII si necesita más información</li>
            <li>Emitir un nuevo DTE corrigiendo el error (si corresponde)</li>
        </ul>

        <p style="text-align: center;">
            <a href="{{ config('app.url') }}/dte/dashboard" class="button">
                Ver Dashboard DTE
            </a>
        </p>

        <div class="footer">
            <p>Este es un mensaje automático del sistema de facturación electrónica.</p>
            <p><strong>{{ config('app.name') }}</strong></p>
            <p style="font-size: 12px; margin-top: 10px;">
                Si tiene alguna consulta, por favor contacte con su administrador.
            </p>
        </div>
    </div>
</body>
</html>
