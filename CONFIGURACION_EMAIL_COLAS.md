# Configuración de Correo Electrónico y Colas

## 📧 Configuración de Correo SMTP

Para que el sistema pueda enviar notificaciones por correo electrónico, necesitas configurar las credenciales SMTP en el archivo `.env`:

### Opción 1: Gmail (Recomendado para desarrollo)

1. **Habilita la verificación en 2 pasos** en tu cuenta de Gmail
2. **Genera una contraseña de aplicación**: https://myaccount.google.com/apppasswords
3. **Agrega estas líneas a tu archivo `.env`**:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tucorreo@gmail.com
MAIL_PASSWORD=tu_contraseña_de_aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tucorreo@gmail.com
MAIL_FROM_NAME="Sistema APR"
```

### Opción 2: Otro proveedor SMTP

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.tuproveedor.com
MAIL_PORT=587
MAIL_USERNAME=tu_usuario
MAIL_PASSWORD=tu_contraseña
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="Sistema APR"
```

## 🔄 Configuración de Colas (Queue)

El sistema utiliza colas para enviar correos de forma asíncrona, evitando que la aplicación se bloquee durante el envío.

### 1. Configurar el driver de colas en `.env`:

```env
QUEUE_CONNECTION=database
```

### 2. Las tablas de colas ya están creadas (`jobs`, `failed_jobs`)

### 3. Iniciar el worker de colas

Abre una **nueva terminal/cmd** y ejecuta:

```bash
cd c:\xampp\htdocs\ssr
php artisan queue:work
```

**Este comando debe estar ejecutándose constantemente** para procesar los correos.

### Alternativa: Usar supervisor o crear un script

Para producción, puedes usar:
- **Windows**: Crear un archivo .bat y ejecutarlo al inicio
- **Linux**: Usar supervisor

#### Script para Windows (queue-worker.bat):

```bat
@echo off
:inicio
cd c:\xampp\htdocs\ssr
php artisan queue:work --sleep=3 --tries=3
goto inicio
```

## 🚀 Cómo usar el sistema de notificaciones

### Desde la interfaz web:

1. Ve a **Notificaciones** → **Nueva Notificación**
2. Completa el formulario:
   - **Título**: Asunto del correo
   - **Mensaje**: Contenido del correo
   - **Tipo**: Informativa, Importante, Urgente, etc.
   - **Destinatario**: Todos, Morosos, Activos, Sector, Individual
   - **Canal**: Selecciona "Email" o "Múltiple"
   - **Estado**: Borrador o Programada
3. Haz clic en **Guardar**
4. En la vista de detalle, haz clic en **Enviar Notificación**

### Los correos se procesarán automáticamente en segundo plano

## 📊 Monitoreo

- **Ver cola de trabajos**: Revisa la tabla `jobs` en la base de datos
- **Ver trabajos fallidos**: Revisa la tabla `failed_jobs`
- **Logs**: Revisa `storage/logs/laravel.log`

### Comandos útiles:

```bash
# Ver trabajos en cola
php artisan queue:monitor

# Limpiar trabajos fallidos
php artisan queue:flush

# Reintentar trabajos fallidos
php artisan queue:retry all

# Ver estadísticas
php artisan queue:failed
```

## ⚙️ Configuración avanzada

### Reintentos automáticos

El sistema está configurado para reintentar 3 veces si falla el envío:
- Intento 1: Inmediato
- Intento 2: Después de 30 segundos
- Intento 3: Después de 1 minuto

### Timeout

Cada trabajo tiene un timeout de 120 segundos.

## 🔍 Solución de problemas

### Los correos no se envían

1. ✅ Verifica que el worker esté ejecutándose: `php artisan queue:work`
2. ✅ Revisa las credenciales SMTP en `.env`
3. ✅ Verifica la tabla `jobs` para ver si hay trabajos pendientes
4. ✅ Revisa la tabla `failed_jobs` para ver si hay errores
5. ✅ Revisa `storage/logs/laravel.log`

### Error de autenticación

- Verifica que la contraseña sea correcta
- Si usas Gmail, asegúrate de usar una **contraseña de aplicación**, no tu contraseña normal

### Los correos van a spam

- Configura SPF, DKIM y DMARC en tu dominio
- Usa un servicio profesional como SendGrid, Mailgun o Amazon SES en producción

## 📝 Notas importantes

1. **Los socios deben tener email configurado** en su perfil para recibir notificaciones
2. **El sistema registra estadísticas**: total_enviados, total_errores, total_leidos
3. **Todas las operaciones se registran** en el log de actividad reciente
4. **Los correos se envían de forma asíncrona** usando colas de Laravel

## 🎯 Próximos pasos

- [ ] Implementar WhatsApp (requiere integración con Twilio o API similar)
- [ ] Implementar SMS (requiere integración con proveedor)
- [ ] Dashboard de estadísticas de notificaciones
- [ ] Programación automática de notificaciones
- [ ] Templates personalizables

---

**Sistema APR** - Agua Potable Rural
