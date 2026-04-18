<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones - Sistema APR</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8fafc;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            margin-bottom: 2rem;
            border-radius: 1rem;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .content {
            background: white;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .section {
            margin-bottom: 2.5rem;
        }

        .section h2 {
            color: #2563eb;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .section h3 {
            color: #334155;
            font-size: 1.2rem;
            margin: 1.5rem 0 0.75rem;
        }

        .section p {
            margin-bottom: 1rem;
            color: #475569;
        }

        .section ul {
            margin-left: 2rem;
            margin-bottom: 1rem;
        }

        .section li {
            margin-bottom: 0.5rem;
            color: #475569;
        }

        .highlight {
            background: #dbeafe;
            padding: 1.5rem;
            border-left: 4px solid #2563eb;
            border-radius: 0.5rem;
            margin: 1.5rem 0;
        }

        .footer {
            text-align: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
        }

        .btn-back {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 0.5rem;
            margin-top: 2rem;
            transition: background 0.3s;
        }

        .btn-back:hover {
            background: #1d4ed8;
        }

        .last-updated {
            background: #f1f5f9;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            text-align: center;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💧 Términos y Condiciones</h1>
            <p>Sistema APR - Gestión Integral de Agua Potable Rural</p>
        </div>

        <div class="content">
            <div class="last-updated">
                <strong>Última actualización:</strong> {{ date('d/m/Y') }}
            </div>

            <div class="section">
                <h2>1. Aceptación de los Términos</h2>
                <p>
                    Al registrarse y utilizar el Sistema APR, usted acepta estar vinculado por estos Términos y Condiciones.
                    Si no está de acuerdo con alguna parte de estos términos, no debe utilizar nuestro servicio.
                </p>
                <p>
                    Estos términos se aplican a todas las organizaciones APR (Agua Potable Rural) que utilicen nuestra plataforma
                    de gestión integral.
                </p>
            </div>

            <div class="section">
                <h2>2. Descripción del Servicio</h2>
                <p>
                    Sistema APR es una plataforma SaaS (Software as a Service) diseñada específicamente para la gestión
                    integral de organizaciones de Agua Potable Rural en Chile, que incluye:
                </p>
                <ul>
                    <li>Gestión de socios y usuarios del sistema</li>
                    <li>Registro y control de lecturas de medidores</li>
                    <li>Generación automática de boletas y facturación</li>
                    <li>Gestión de pagos y cobranza</li>
                    <li>Control de inventario y activos fijos</li>
                    <li>Gestión de personal y nómina</li>
                    <li>Reportes y estadísticas</li>
                    <li>Integración con pasarela de pagos Flow</li>
                </ul>
            </div>

            <div class="section">
                <h2>3. Registro y Cuenta de Usuario</h2>

                <h3>3.1 Período de Prueba</h3>
                <p>
                    Todas las organizaciones nuevas reciben <strong>30 días de prueba gratuita</strong> con acceso completo
                    a todas las funcionalidades del plan seleccionado.
                </p>

                <h3>3.2 Información de Registro</h3>
                <p>
                    Al crear una cuenta, usted se compromete a proporcionar información precisa, actualizada y completa.
                    Es su responsabilidad mantener la confidencialidad de su contraseña y cuenta.
                </p>

                <h3>3.3 Responsabilidad de la Cuenta</h3>
                <p>
                    Usted es responsable de todas las actividades que ocurran bajo su cuenta. Debe notificarnos
                    inmediatamente sobre cualquier uso no autorizado de su cuenta.
                </p>
            </div>

            <div class="section">
                <h2>4. Planes y Pagos</h2>

                <h3>4.1 Planes de Suscripción</h3>
                <p>Ofrecemos tres planes de suscripción mensual:</p>
                <ul>
                    <li><strong>Plan Básico:</strong> Funcionalidades esenciales para APR pequeñas</li>
                    <li><strong>Plan Profesional:</strong> Funcionalidades avanzadas y módulos adicionales</li>
                    <li><strong>Plan Enterprise:</strong> Funcionalidades completas con soporte prioritario</li>
                </ul>

                <h3>4.2 Facturación</h3>
                <p>
                    La facturación se realiza mensualmente por adelantado. Los pagos se procesan automáticamente
                    el día correspondiente de cada mes.
                </p>

                <h3>4.3 Cambios de Plan</h3>
                <ul>
                    <li><strong>Upgrade:</strong> Se cobra la diferencia prorrateada del período restante</li>
                    <li><strong>Downgrade:</strong> Se aplica al final del período de facturación actual</li>
                </ul>

                <h3>4.4 Cancelación</h3>
                <p>
                    Puede cancelar su suscripción en cualquier momento. La cancelación será efectiva al final del
                    período de facturación actual. No se realizan reembolsos por períodos parciales.
                </p>
            </div>

            <div class="section">
                <h2>5. Uso Aceptable</h2>

                <div class="highlight">
                    <strong>Usted se compromete a NO:</strong>
                    <ul style="margin-top: 0.5rem;">
                        <li>Usar el servicio para actividades ilegales</li>
                        <li>Intentar acceder a datos de otras organizaciones</li>
                        <li>Realizar ingeniería inversa del software</li>
                        <li>Transmitir virus, malware o código malicioso</li>
                        <li>Sobrecargar o interferir con la infraestructura del servicio</li>
                        <li>Revender o redistribuir el servicio sin autorización</li>
                    </ul>
                </div>
            </div>

            <div class="section">
                <h2>6. Datos y Privacidad</h2>

                <h3>6.1 Propiedad de los Datos</h3>
                <p>
                    Todos los datos ingresados en el sistema son propiedad de su organización. Usted mantiene
                    todos los derechos sobre sus datos.
                </p>

                <h3>6.2 Protección de Datos</h3>
                <p>
                    Implementamos medidas de seguridad técnicas y organizativas para proteger sus datos contra
                    acceso no autorizado, pérdida o alteración.
                </p>

                <h3>6.3 Uso de Datos</h3>
                <p>
                    Solo utilizamos sus datos para proporcionar y mejorar el servicio. No vendemos ni compartimos
                    sus datos con terceros sin su consentimiento, excepto cuando sea requerido por ley.
                </p>

                <h3>6.4 Aislamiento de Datos (Multitenancy)</h3>
                <p>
                    Cada organización tiene sus datos completamente aislados. Ninguna organización puede acceder
                    a los datos de otra.
                </p>
            </div>

            <div class="section">
                <h2>7. Disponibilidad del Servicio</h2>

                <h3>7.1 Tiempo de Actividad</h3>
                <p>
                    Nos esforzamos por mantener una disponibilidad del servicio del 99.9%, aunque no podemos
                    garantizar un tiempo de actividad ininterrumpido.
                </p>

                <h3>7.2 Mantenimiento</h3>
                <p>
                    Nos reservamos el derecho de realizar mantenimiento programado con previo aviso. Intentaremos
                    realizar el mantenimiento en horarios de baja actividad.
                </p>

                <h3>7.3 Respaldos</h3>
                <p>
                    Realizamos respaldos automáticos diarios de todos los datos. Sin embargo, recomendamos que
                    también mantenga copias de seguridad de sus datos críticos.
                </p>
            </div>

            <div class="section">
                <h2>8. Límites del Servicio</h2>
                <p>
                    Cada plan tiene límites específicos en cuanto a:
                </p>
                <ul>
                    <li>Número de socios que puede gestionar</li>
                    <li>Número de usuarios del sistema</li>
                    <li>Módulos y funcionalidades disponibles</li>
                    <li>Almacenamiento de archivos</li>
                </ul>
                <p>
                    Al alcanzar los límites de su plan, deberá actualizar a un plan superior para continuar
                    agregando más registros.
                </p>
            </div>

            <div class="section">
                <h2>9. Propiedad Intelectual</h2>
                <p>
                    El Sistema APR, incluyendo su diseño, código, lógica y funcionalidades, es propiedad exclusiva
                    de nuestros desarrolladores y está protegido por leyes de propiedad intelectual.
                </p>
                <p>
                    La licencia otorgada es únicamente para el uso del servicio según estos términos, no para
                    copiar, modificar o redistribuir el software.
                </p>
            </div>

            <div class="section">
                <h2>10. Limitación de Responsabilidad</h2>
                <p>
                    En la máxima medida permitida por la ley, no seremos responsables por:
                </p>
                <ul>
                    <li>Pérdida de datos o información</li>
                    <li>Pérdidas financieras indirectas</li>
                    <li>Interrupción del servicio</li>
                    <li>Errores en cálculos o reportes generados</li>
                    <li>Uso indebido del sistema por parte de sus usuarios</li>
                </ul>
                <p>
                    Nuestra responsabilidad total no excederá el monto pagado por usted en los últimos 12 meses.
                </p>
            </div>

            <div class="section">
                <h2>11. Modificaciones de los Términos</h2>
                <p>
                    Nos reservamos el derecho de modificar estos términos en cualquier momento. Las modificaciones
                    entrarán en vigor inmediatamente después de su publicación en nuestra plataforma.
                </p>
                <p>
                    Le notificaremos sobre cambios significativos por correo electrónico. El uso continuado del
                    servicio después de los cambios constituye su aceptación de los nuevos términos.
                </p>
            </div>

            <div class="section">
                <h2>12. Terminación del Servicio</h2>

                <h3>12.1 Por su Parte</h3>
                <p>
                    Puede cancelar su suscripción en cualquier momento desde la configuración de su organización.
                </p>

                <h3>12.2 Por Nuestra Parte</h3>
                <p>
                    Podemos suspender o terminar su acceso si:
                </p>
                <ul>
                    <li>Viola estos términos y condiciones</li>
                    <li>No realiza los pagos correspondientes</li>
                    <li>Usa el servicio de manera que perjudique a otros usuarios</li>
                    <li>Proporciona información falsa o engañosa</li>
                </ul>
            </div>

            <div class="section">
                <h2>13. Soporte Técnico</h2>
                <p>
                    El nivel de soporte técnico depende de su plan de suscripción:
                </p>
                <ul>
                    <li><strong>Plan Básico:</strong> Soporte por email (respuesta en 48 horas)</li>
                    <li><strong>Plan Profesional:</strong> Soporte por email (respuesta en 24 horas)</li>
                    <li><strong>Plan Enterprise:</strong> Soporte prioritario (respuesta en 8 horas)</li>
                </ul>
            </div>

            <div class="section">
                <h2>14. Funcionalidades Beta</h2>
                <p>
                    Algunos módulos de la aplicación pueden aparecer marcados con la etiqueta <strong>"BETA"</strong>
                    en el menú lateral. Esto significa que:
                </p>
                <ul>
                    <li><strong>Son completamente funcionales:</strong> Puede usar estas funcionalidades sin restricciones</li>
                    <li><strong>En fase de prueba:</strong> Estamos recopilando retroalimentación y realizando mejoras continuas</li>
                    <li><strong>Pueden cambiar:</strong> Las funcionalidades beta pueden ser modificadas o mejoradas basándose en el feedback de los usuarios</li>
                    <li><strong>Reporte de problemas:</strong> Agradecemos que nos informe cualquier error o sugerencia de mejora</li>
                </ul>
                <div class="highlight">
                    <strong>Importante:</strong> Las funcionalidades beta son seguras de usar y sus datos están protegidos.
                    La etiqueta beta solo indica que seguimos perfeccionando la experiencia de usuario.
                </div>
            </div>

            <div class="section">
                <h2>15. Ley Aplicable</h2>
                <p>
                    Estos términos se rigen por las leyes de la República de Chile. Cualquier disputa será
                    resuelta en los tribunales competentes de Chile.
                </p>
            </div>

            <div class="section">
                <h2>16. Contacto</h2>
                <p>
                    Para preguntas sobre estos términos y condiciones, puede contactarnos a través de:
                </p>
                <ul>
                    <li><strong>Email:</strong> soportesistemaapr@gmail.com</li>
                    <li><strong>Formulario de contacto:</strong> Disponible en nuestra página principal</li>
                </ul>
            </div>

            <div class="footer">
                <p>
                    Al hacer clic en "Acepto los términos y condiciones" en el formulario de registro,
                    usted confirma que ha leído, entendido y aceptado estos términos.
                </p>
                <a href="{{ route('registro.formulario') }}" class="btn-back">← Volver al Registro</a>
            </div>
        </div>
    </div>
</body>
</html>
