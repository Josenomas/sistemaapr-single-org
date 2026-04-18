<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad - Sistema APR</title>
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
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            margin-bottom: 2rem;
        }

        .section {
            margin-bottom: 2.5rem;
        }

        .section h2 {
            color: #1e40af;
            font-size: 1.75rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid #2563eb;
        }

        .section h3 {
            color: #1e40af;
            font-size: 1.3rem;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .section p {
            margin-bottom: 1rem;
            text-align: justify;
        }

        .section ul, .section ol {
            margin-left: 2rem;
            margin-bottom: 1rem;
        }

        .section li {
            margin-bottom: 0.5rem;
        }

        .highlight {
            background-color: #dbeafe;
            border-left: 4px solid #2563eb;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0.5rem;
        }

        .info-box {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-radius: 0.75rem;
        }

        .info-box strong {
            color: #0c4a6e;
            display: block;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .warning-box {
            background-color: #fef3c7;
            border: 1px solid #fbbf24;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-radius: 0.75rem;
        }

        .warning-box strong {
            color: #92400e;
            display: block;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
        }

        table th {
            background-color: #2563eb;
            color: white;
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
        }

        table td {
            padding: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
        }

        table tr:hover {
            background-color: #f9fafb;
        }

        .footer {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            text-align: center;
            color: #6b7280;
        }

        .footer a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .back-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: background 0.2s;
            margin-top: 1rem;
        }

        .back-button:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Política de Privacidad</h1>
            <p>Última actualización: {{ date('d/m/Y') }}</p>
            <p>Protección de Datos Personales - Ley 19.628</p>
        </div>

        <div class="content">
            <div class="section">
                <h2>1. Introducción</h2>
                <p>
                    En <strong>Sistema APR</strong> nos comprometemos con la protección de sus datos personales.
                    Esta Política de Privacidad explica cómo recopilamos, usamos, almacenamos y protegemos su
                    información personal de acuerdo con la <strong>Ley 19.628 sobre Protección de la Vida Privada</strong>
                    de Chile.
                </p>
                <p>
                    Al utilizar nuestros servicios, usted acepta las prácticas descritas en esta política.
                    Le recomendamos leerla cuidadosamente.
                </p>
            </div>

            <div class="section">
                <h2>2. Responsable del Tratamiento de Datos</h2>

                <div class="info-box">
                    <strong>Identificación del Responsable:</strong>
                    <p><strong>Razón Social:</strong> Sistema APR</p>
                    <p><strong>RUT:</strong> 19.762.564-3</p>
                    <p><strong>Representante Legal:</strong> José Aravena</p>
                    <p><strong>Correo electrónico:</strong> soportesistemaapr@gmail.com</p>
                    <p><strong>Dirección:</strong> Chile</p>
                </div>

                <p>
                    Esta base de datos está inscrita en el Registro de Bancos de Datos Personales del
                    Servicio de Registro Civil e Identificación de Chile, en cumplimiento del Art. 22
                    de la Ley 19.628.
                </p>
            </div>

            <div class="section">
                <h2>3. Datos Personales que Recopilamos</h2>

                <p>Recopilamos diferentes tipos de información personal según su relación con nuestro sistema:</p>

                <h3>3.1. Datos de Organizaciones (APR)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Tipo de Dato</th>
                            <th>Ejemplos</th>
                            <th>Finalidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Identificación</strong></td>
                            <td>Nombre APR, RUT, razón social</td>
                            <td>Identificación y facturación</td>
                        </tr>
                        <tr>
                            <td><strong>Contacto</strong></td>
                            <td>Email, teléfono, dirección</td>
                            <td>Comunicaciones y soporte</td>
                        </tr>
                        <tr>
                            <td><strong>Representante Legal</strong></td>
                            <td>Nombre, RUT, cargo</td>
                            <td>Validación contractual</td>
                        </tr>
                        <tr>
                            <td><strong>Datos Bancarios</strong></td>
                            <td>Banco, tipo cuenta, número cuenta</td>
                            <td>Procesamiento de pagos</td>
                        </tr>
                    </tbody>
                </table>

                <h3>3.2. Datos de Usuarios del Sistema</h3>
                <ul>
                    <li><strong>Identificación:</strong> Nombre completo, RUT, cargo</li>
                    <li><strong>Contacto:</strong> Correo electrónico, teléfono</li>
                    <li><strong>Acceso:</strong> Contraseña encriptada, permisos de usuario</li>
                    <li><strong>Actividad:</strong> Registro de acciones, IP, navegador, fecha/hora</li>
                </ul>

                <h3>3.3. Datos de Socios/Clientes de las APR</h3>

                <div class="info-box">
                    <strong>Importante:</strong> Los socios/clientes de las APR <strong>NO tienen acceso directo</strong>
                    a Sistema APR. Sus datos son gestionados únicamente por los administradores de cada APR.
                </div>

                <p>
                    <strong>Base Legal del Tratamiento:</strong> Los datos de socios son recopilados por cada APR
                    mediante <strong>contratos de adhesión al servicio de agua potable</strong>, donde el socio
                    autoriza expresamente el tratamiento de sus datos personales para la gestión del servicio.
                    Sistema APR actúa como <strong>encargado del tratamiento</strong> (procesador de datos),
                    siendo cada APR el <strong>responsable del tratamiento</strong> de los datos de sus socios.
                </p>

                <p>Datos almacenados de socios:</p>
                <ul>
                    <li><strong>Identificación:</strong> Nombre completo, RUT</li>
                    <li><strong>Contacto:</strong> Dirección, teléfono, correo electrónico</li>
                    <li><strong>Servicios:</strong> Medidor asignado, tarifa, consumo de agua</li>
                    <li><strong>Financieros:</strong> Boletas emitidas, pagos realizados, morosidad</li>
                </ul>

                <p>
                    <strong>Derechos ARCO de los socios:</strong> Los socios deben ejercer sus derechos de acceso,
                    rectificación, cancelación y oposición directamente ante su APR, quien es el responsable de
                    sus datos personales. La APR utilizará Sistema APR para gestionar dichas solicitudes.
                </p>

                <h3>3.4. Datos Técnicos</h3>
                <ul>
                    <li>Dirección IP</li>
                    <li>Tipo de navegador y sistema operativo</li>
                    <li>Fecha y hora de acceso</li>
                    <li>Páginas visitadas dentro del sistema</li>
                </ul>
            </div>

            <div class="section">
                <h2>4. Finalidad del Tratamiento de Datos</h2>

                <p>Utilizamos sus datos personales para las siguientes finalidades legítimas:</p>

                <ol>
                    <li><strong>Prestación del Servicio:</strong> Proporcionar acceso y funcionalidades del sistema de gestión APR</li>
                    <li><strong>Facturación y Cobro:</strong> Emitir boletas electrónicas y gestionar pagos</li>
                    <li><strong>Soporte Técnico:</strong> Responder consultas y resolver problemas técnicos</li>
                    <li><strong>Cumplimiento Contractual:</strong> Ejecutar los términos del servicio contratado</li>
                    <li><strong>Seguridad:</strong> Prevenir fraudes y proteger la integridad del sistema</li>
                    <li><strong>Auditoría:</strong> Mantener registro de actividades para trazabilidad</li>
                    <li><strong>Cumplimiento Legal:</strong> Cumplir obligaciones tributarias (SII) y regulatorias</li>
                    <li><strong>Comunicaciones:</strong> Enviar notificaciones sobre el servicio, actualizaciones y cambios importantes</li>
                    <li><strong>Mejora del Servicio:</strong> Análisis estadísticos agregados para optimizar funcionalidades</li>
                </ol>

                <div class="highlight">
                    <strong>Base Legal del Tratamiento:</strong> El tratamiento de sus datos se fundamenta en:
                    <ul style="margin-top: 0.5rem;">
                        <li>Ejecución del contrato de servicios (Art. 4 letra d, Ley 19.628)</li>
                        <li>Consentimiento informado del titular</li>
                        <li>Cumplimiento de obligaciones legales (tributarias, sanitarias)</li>
                    </ul>
                </div>
            </div>

            <div class="section">
                <h2>5. Compartir Datos con Terceros</h2>

                <p>
                    <strong>No vendemos ni cedemos</strong> sus datos personales a terceros para fines comerciales.
                    Sin embargo, compartimos datos limitados con terceros en los siguientes casos:
                </p>

                <h3>5.1. Procesadores de Pago</h3>
                <ul>
                    <li><strong>Flow / Khipu:</strong> Compartimos email y monto de transacción para procesar pagos de manera segura</li>
                    <li>Estos proveedores están certificados y cumplen estándares PCI-DSS</li>
                </ul>

                <h3>5.2. Servicio de Impuestos Internos (SII)</h3>
                <ul>
                    <li>Compartimos RUT, nombre y monto para emisión de boletas electrónicas</li>
                    <li>Obligación legal según normativa tributaria chilena</li>
                </ul>

                <h3>5.3. Proveedores de Servicios Técnicos</h3>
                <ul>
                    <li>Hosting de servidores y almacenamiento en la nube</li>
                    <li>Servicios de envío de correos electrónicos</li>
                    <li>Todos los proveedores están contractualmente obligados a proteger sus datos</li>
                </ul>

                <h3>5.4. Autoridades Competentes</h3>
                <ul>
                    <li>Cuando sea requerido por ley o por orden judicial</li>
                    <li>Para proteger derechos legales o seguridad</li>
                </ul>

                <div class="warning-box">
                    <strong>Importante:</strong> Ningún tercero tiene permiso para usar sus datos personales
                    para fines distintos a los específicamente autorizados por Sistema APR.
                </div>
            </div>

            <div class="section">
                <h2>6. Seguridad de los Datos Personales</h2>

                <p>Implementamos medidas técnicas y organizativas para proteger sus datos personales:</p>

                <h3>6.1. Medidas Técnicas</h3>
                <ul>
                    <li><strong>Encriptación:</strong> Todas las contraseñas se almacenan encriptadas con bcrypt</li>
                    <li><strong>SSL/TLS:</strong> Comunicaciones cifradas mediante protocolo HTTPS</li>
                    <li><strong>Control de Acceso:</strong> Sistema de permisos basado en roles</li>
                    <li><strong>Firewall:</strong> Protección perimetral de servidores</li>
                    <li><strong>Copias de Seguridad:</strong> Respaldos diarios automatizados</li>
                    <li><strong>Auditoría:</strong> Registro de todos los accesos y modificaciones de datos</li>
                </ul>

                <h3>6.2. Medidas Organizativas</h3>
                <ul>
                    <li>Acceso restringido solo a personal autorizado</li>
                    <li>Acuerdos de confidencialidad con empleados</li>
                    <li>Capacitación en protección de datos</li>
                    <li>Procedimientos de respuesta ante incidentes de seguridad</li>
                </ul>

                <h3>6.3. Aislamiento de Datos (Multi-tenancy)</h3>
                <p>
                    Cada organización APR tiene sus datos completamente aislados. Los usuarios de una
                    organización <strong>no pueden acceder</strong> a los datos de otras organizaciones.
                </p>
            </div>

            <div class="section">
                <h2>7. Sus Derechos sobre los Datos Personales (Derechos ARCO)</h2>

                <p>
                    De acuerdo con la Ley 19.628, usted tiene los siguientes derechos sobre sus datos personales:
                </p>

                <h3>7.1. Derecho de Acceso</h3>
                <p>
                    Tiene derecho a conocer qué datos personales tenemos sobre usted y cómo los utilizamos.
                    Puede solicitar una copia de sus datos en formato estructurado.
                </p>

                <h3>7.2. Derecho de Rectificación</h3>
                <p>
                    Puede solicitar la corrección de datos inexactos o incompletos. Nos comprometemos a
                    actualizar o corregir sus datos dentro de 2 días hábiles.
                </p>

                <h3>7.3. Derecho de Cancelación (Eliminación)</h3>
                <p>
                    Puede solicitar la eliminación de sus datos personales cuando:
                </p>
                <ul>
                    <li>Ya no sean necesarios para las finalidades para las cuales fueron recopilados</li>
                    <li>Haya retirado su consentimiento y no exista otra base legal</li>
                    <li>Los datos hayan sido tratados ilícitamente</li>
                </ul>
                <p>
                    <strong>Nota:</strong> Algunos datos deben conservarse por obligaciones legales (ej: boletas
                    tributarias por 6 años según normativa SII).
                </p>

                <h3>7.4. Derecho de Oposición</h3>
                <p>
                    Puede oponerse al tratamiento de sus datos personales en situaciones particulares,
                    especialmente para fines de marketing directo.
                </p>

                <h3>7.5. Derecho de Bloqueo</h3>
                <p>
                    Puede solicitar el bloqueo temporal de sus datos cuando cuestione su exactitud o
                    legalidad del tratamiento, mientras se verifica su solicitud.
                </p>

                <div class="info-box">
                    <strong>¿Cómo Ejercer sus Derechos?</strong>
                    <p>Para ejercer cualquiera de estos derechos, puede:</p>
                    <ol style="margin-left: 1.5rem; margin-top: 0.5rem;">
                        <li>Enviar un correo electrónico a: <strong>soportesistemaapr@gmail.com</strong></li>
                        <li>Utilizar el formulario de solicitud ARCO disponible en su panel de usuario</li>
                        <li>Contactarnos a través del sistema de tickets de soporte</li>
                    </ol>
                    <p style="margin-top: 0.5rem;">
                        <strong>Plazo de respuesta:</strong> Responderemos su solicitud dentro de
                        <strong>5 días hábiles</strong> desde su recepción.
                    </p>
                </div>

                <h3>7.6. Derecho a Presentar Reclamo</h3>
                <p>
                    Si considera que sus derechos han sido vulnerados, puede presentar un reclamo ante el
                    <strong>Consejo para la Transparencia</strong> de Chile:
                </p>
                <ul>
                    <li>Web: <a href="https://www.consejotransparencia.cl" target="_blank">www.consejotransparencia.cl</a></li>
                    <li>Teléfono: +56 2 2754 8100</li>
                    <li>Dirección: Morandé 360, Santiago</li>
                </ul>
            </div>

            <div class="section">
                <h2>8. Retención y Eliminación de Datos</h2>

                <h3>8.1. Plazos de Retención</h3>
                <p>Conservamos sus datos personales durante los siguientes períodos:</p>

                <table>
                    <thead>
                        <tr>
                            <th>Tipo de Dato</th>
                            <th>Plazo de Retención</th>
                            <th>Fundamento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Datos de cuenta activa</td>
                            <td>Mientras dure la relación contractual</td>
                            <td>Prestación del servicio</td>
                        </tr>
                        <tr>
                            <td>Boletas y documentos tributarios</td>
                            <td>Mínimo 6 años</td>
                            <td>Obligación legal SII</td>
                        </tr>
                        <tr>
                            <td>Registros de auditoría</td>
                            <td>3 años</td>
                            <td>Seguridad y trazabilidad</td>
                        </tr>
                        <tr>
                            <td>Datos de cuenta eliminada</td>
                            <td>30 días (período de gracia)</td>
                            <td>Posibilidad de recuperación</td>
                        </tr>
                        <tr>
                            <td>Copias de respaldo</td>
                            <td>90 días</td>
                            <td>Continuidad operacional</td>
                        </tr>
                    </tbody>
                </table>

                <h3>8.2. Eliminación Definitiva</h3>
                <p>
                    Transcurridos los plazos establecidos, procedemos a la eliminación definitiva e irreversible
                    de los datos personales, salvo aquellos que debamos conservar por obligación legal.
                </p>

                <h3>8.3. Eliminación de Cuenta</h3>
                <p>
                    Si solicita la eliminación de su cuenta, sus datos personales serán:
                </p>
                <ul>
                    <li>Marcados como "eliminados" inmediatamente (soft delete)</li>
                    <li>Inaccesibles para todos los usuarios del sistema</li>
                    <li>Purgados definitivamente después de 30 días</li>
                    <li>Conservados solo en backups por 90 días adicionales (luego eliminados automáticamente)</li>
                </ul>
            </div>

            <div class="section">
                <h2>9. Cookies y Tecnologías Similares</h2>

                <p>Utilizamos cookies y tecnologías similares para mejorar la experiencia del usuario:</p>

                <h3>9.1. Cookies Estrictamente Necesarias</h3>
                <ul>
                    <li><strong>Sesión de usuario:</strong> Mantienen su sesión activa mientras navega</li>
                    <li><strong>CSRF Token:</strong> Protegen contra ataques de falsificación de peticiones</li>
                    <li>Estas cookies son esenciales y no requieren consentimiento</li>
                </ul>

                <h3>9.2. Cookies de Funcionalidad</h3>
                <ul>
                    <li>Recordar preferencias de usuario (idioma, configuración de pantalla)</li>
                    <li>Puede deshabilitarlas, pero algunas funcionalidades pueden verse limitadas</li>
                </ul>

                <h3>9.3. Control de Cookies</h3>
                <p>
                    Puede configurar su navegador para rechazar cookies o recibir una notificación cuando
                    se envíen. Sin embargo, esto puede afectar la funcionalidad del sistema.
                </p>
            </div>

            <div class="section">
                <h2>10. Transferencia Internacional de Datos</h2>

                <p>
                    Actualmente, todos sus datos personales se almacenan en servidores ubicados en
                    <strong>Chile</strong> y no se transfieren fuera del país.
                </p>

                <p>
                    En caso de que en el futuro sea necesario transferir datos a otros países, le
                    notificaremos previamente y solicitaremos su consentimiento explícito, asegurando
                    que el país receptor tenga niveles adecuados de protección de datos.
                </p>
            </div>

            <div class="section">
                <h2>11. Menores de Edad</h2>

                <p>
                    Nuestro servicio no está dirigido a menores de 18 años. No recopilamos
                    intencionalmente datos personales de menores.
                </p>

                <p>
                    En caso de gestión de servicios de agua para hogares con menores, los datos
                    recopilados corresponden al titular adulto del servicio (padre, madre o tutor legal).
                </p>

                <p>
                    Si descubrimos que hemos recopilado datos de un menor sin consentimiento del tutor,
                    eliminaremos esa información inmediatamente.
                </p>
            </div>

            <div class="section">
                <h2>12. Actualizaciones de esta Política</h2>

                <p>
                    Podemos actualizar esta Política de Privacidad periódicamente para reflejar cambios en:
                </p>
                <ul>
                    <li>Nuestras prácticas de tratamiento de datos</li>
                    <li>Requisitos legales o regulatorios</li>
                    <li>Nuevas funcionalidades del sistema</li>
                </ul>

                <p>
                    <strong>Notificación de cambios:</strong> Le notificaremos cambios importantes mediante:
                </p>
                <ul>
                    <li>Correo electrónico a la dirección registrada</li>
                    <li>Aviso destacado en el sistema al iniciar sesión</li>
                    <li>Actualización de la fecha en la parte superior de este documento</li>
                </ul>

                <p>
                    Le recomendamos revisar esta política periódicamente. El uso continuado del servicio
                    después de las modificaciones constituye su aceptación de los cambios.
                </p>

                <div class="info-box">
                    <strong>Versión Actual:</strong> 1.0<br>
                    <strong>Fecha de Vigencia:</strong> {{ date('d/m/Y') }}<br>
                    <strong>Última Modificación:</strong> {{ date('d/m/Y') }}
                </div>
            </div>

            <div class="section">
                <h2>13. Contacto y Consultas</h2>

                <p>
                    Para cualquier consulta, solicitud o reclamo relacionado con el tratamiento de sus
                    datos personales, puede contactarnos:
                </p>

                <div class="info-box">
                    <strong>Datos de Contacto del Responsable de Protección de Datos:</strong>
                    <p><strong>Correo electrónico:</strong> soportesistemaapr@gmail.com</p>
                    <p><strong>Asunto:</strong> "Protección de Datos Personales"</p>
                    <p><strong>Horario de atención:</strong> Lunes a Viernes, 9:00 - 18:00 hrs (hora Chile)</p>
                    <p><strong>Plazo de respuesta:</strong> Máximo 5 días hábiles</p>
                </div>

                <p>
                    Nos comprometemos a responder todas sus consultas de manera oportuna y transparente.
                </p>
            </div>

            <div class="section">
                <h2>14. Consentimiento</h2>

                <div class="highlight">
                    <p>
                        Al registrarse y utilizar Sistema APR, usted confirma que:
                    </p>
                    <ol style="margin-left: 1.5rem; margin-top: 0.5rem;">
                        <li>Ha leído y comprendido esta Política de Privacidad</li>
                        <li>Consiente el tratamiento de sus datos personales según lo descrito</li>
                        <li>Conoce sus derechos ARCO y cómo ejercerlos</li>
                        <li>Acepta que compartamos datos mínimos necesarios con terceros mencionados</li>
                        <li>Comprende los plazos de retención y eliminación de datos</li>
                    </ol>
                    <p style="margin-top: 1rem;">
                        <strong>Puede retirar su consentimiento en cualquier momento</strong> solicitando
                        la eliminación de su cuenta, sujeto a las obligaciones legales de retención de
                        ciertos datos.
                    </p>
                </div>
            </div>

            <div class="section">
                <h2>15. Legislación Aplicable</h2>

                <p>Esta Política de Privacidad se rige por:</p>
                <ul>
                    <li><strong>Ley 19.628</strong> sobre Protección de la Vida Privada (Chile)</li>
                    <li><strong>Ley 19.496</strong> sobre Protección de los Derechos de los Consumidores</li>
                    <li><strong>Ley 20.575</strong> sobre Principio de Finalidad en el Tratamiento de Datos</li>
                    <li>Normativa del Servicio de Impuestos Internos (SII)</li>
                    <li>Instrucciones del Consejo para la Transparencia</li>
                </ul>

                <p>
                    Cualquier controversia relacionada con el tratamiento de datos personales será
                    resuelta conforme a la legislación chilena y bajo la jurisdicción de los tribunales
                    competentes de Chile.
                </p>
            </div>
        </div>

        <div class="footer">
            <p><strong>Sistema APR</strong> - Gestión Integral de Agua Potable Rural</p>
            <p>RUT: 19.762.564-3 | Email: soportesistemaapr@gmail.com</p>
            <p style="margin-top: 1rem;">
                <a href="{{ route('terminos.condiciones') }}">Términos y Condiciones</a> |
                <a href="{{ route('politicas.privacidad') }}">Política de Privacidad</a>
            </p>
            <a href="javascript:history.back()" class="back-button">Volver</a>
        </div>
    </div>
</body>
</html>
