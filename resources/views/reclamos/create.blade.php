<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libro de Reclamos - Sistema APR</title>
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
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .header i {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .legal-info {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 30px 40px;
            border-radius: 8px;
        }

        .legal-info strong {
            color: #856404;
            display: block;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .legal-info p {
            color: #856404;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .form-container {
            padding: 40px;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section h3 {
            color: #333;
            font-size: 1.3rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #495057;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-group label .required {
            color: #dc3545;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        select.form-control {
            cursor: pointer;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 5px;
            display: block;
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
        }

        .btn-primary {
            background: #dc3545;
            color: white;
        }

        .btn-primary:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #e9ecef;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-container {
                padding: 30px 20px;
            }

            .legal-info {
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-book"></i>
            <h1>Libro de Reclamos</h1>
            <p>Sistema APR - Gestión Integral de Agua Potable Rural</p>
        </div>

        <div class="legal-info">
            <strong><i class="fas fa-info-circle"></i> Información Legal</strong>
            <p>
                De acuerdo con la <strong>Ley 19.496 sobre Protección de los Derechos de los Consumidores</strong>,
                tienes derecho a presentar un reclamo formal sobre nuestros servicios.
            </p>
            <p>
                Tu reclamo será registrado y recibirás una respuesta dentro de un plazo <strong>máximo de 5 días hábiles</strong>.
            </p>
            <p>
                Se te asignará un <strong>número de reclamo</strong> que podrás usar para hacer seguimiento.
            </p>
        </div>

        <div class="form-container">
            <form action="{{ route('reclamos.store') }}" method="POST">
                @csrf

                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Tus Datos</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre_completo">
                                Nombre Completo <span class="required">*</span>
                            </label>
                            <input type="text"
                                   id="nombre_completo"
                                   name="nombre_completo"
                                   class="form-control @error('nombre_completo') is-invalid @enderror"
                                   value="{{ old('nombre_completo') }}"
                                   placeholder="Juan Pérez González"
                                   required>
                            @error('nombre_completo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="rut">
                                RUT <span class="required">*</span>
                            </label>
                            <input type="text"
                                   id="rut"
                                   name="rut"
                                   class="form-control @error('rut') is-invalid @enderror"
                                   value="{{ old('rut') }}"
                                   placeholder="12.345.678-9"
                                   required>
                            @error('rut')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">
                                Email <span class="required">*</span>
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="correo@ejemplo.com"
                                   required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel"
                                   id="telefono"
                                   name="telefono"
                                   class="form-control @error('telefono') is-invalid @enderror"
                                   value="{{ old('telefono') }}"
                                   placeholder="+56 9 1234 5678">
                            @error('telefono')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <input type="text"
                               id="direccion"
                               name="direccion"
                               class="form-control @error('direccion') is-invalid @enderror"
                               value="{{ old('direccion') }}"
                               placeholder="Calle Principal #123, Comuna, Región">
                        @error('direccion')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-exclamation-triangle"></i> Detalles del Reclamo</h3>

                    <div class="form-group">
                        <label for="tipo_reclamo">
                            Tipo de Reclamo <span class="required">*</span>
                        </label>
                        <select id="tipo_reclamo"
                                name="tipo_reclamo"
                                class="form-control @error('tipo_reclamo') is-invalid @enderror"
                                required>
                            <option value="">Seleccione un tipo...</option>
                            <option value="servicio" {{ old('tipo_reclamo') == 'servicio' ? 'selected' : '' }}>
                                Servicio
                            </option>
                            <option value="facturacion" {{ old('tipo_reclamo') == 'facturacion' ? 'selected' : '' }}>
                                Facturación
                            </option>
                            <option value="soporte" {{ old('tipo_reclamo') == 'soporte' ? 'selected' : '' }}>
                                Soporte Técnico
                            </option>
                            <option value="funcionalidad" {{ old('tipo_reclamo') == 'funcionalidad' ? 'selected' : '' }}>
                                Funcionalidad del Sistema
                            </option>
                            <option value="otro" {{ old('tipo_reclamo') == 'otro' ? 'selected' : '' }}>
                                Otro
                            </option>
                        </select>
                        @error('tipo_reclamo')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="detalle_reclamo">
                            Describe tu Reclamo <span class="required">*</span>
                        </label>
                        <textarea id="detalle_reclamo"
                                  name="detalle_reclamo"
                                  class="form-control @error('detalle_reclamo') is-invalid @enderror"
                                  placeholder="Describe detalladamente tu reclamo..."
                                  required>{{ old('detalle_reclamo') }}</textarea>
                        @error('detalle_reclamo')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="solucion_solicitada">
                            Solución que Solicitas
                        </label>
                        <textarea id="solucion_solicitada"
                                  name="solucion_solicitada"
                                  class="form-control @error('solucion_solicitada') is-invalid @enderror"
                                  placeholder="¿Qué solución esperas? (Opcional)">{{ old('solucion_solicitada') }}</textarea>
                        @error('solucion_solicitada')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Enviar Reclamo
                    </button>
                    <a href="/" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
