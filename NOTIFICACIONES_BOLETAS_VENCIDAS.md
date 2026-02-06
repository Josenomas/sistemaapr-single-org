# Sistema de Notificaciones Automáticas de Boletas Vencidas

## 📧 Descripción

Sistema automático que envía notificaciones por correo electrónico a socios con boletas vencidas.

---

## 🎯 Características

✅ Envío automático diario de notificaciones
✅ Detección de todas las boletas vencidas por socio
✅ Email profesional con diseño responsive
✅ Información detallada de deuda
✅ Alertas escalonadas según días de vencimiento
✅ Modo de prueba para testing
✅ Logs detallados de envíos
✅ Validación de emails

---

## 📋 Archivos Creados

1. **app/Mail/BoletaVencidaMail.php**
   - Clase Mailable para el correo
   - Calcula total adeudado y días de vencimiento

2. **resources/views/emails/boleta-vencida.blade.php**
   - Plantilla HTML del correo
   - Diseño profesional y responsive
   - Alertas diferenciadas por gravedad

3. **app/Console/Commands/EnviarNotificacionesBoletasVencidas.php**
   - Comando artisan para envío masivo
   - Modo de prueba incluido
   - Barra de progreso y estadísticas

4. **app/Console/Kernel.php** (modificado)
   - Tarea programada diaria a las 9:00 AM

---

## 🚀 Uso Manual

### Modo de Prueba (Recomendado primero)

```bash
php artisan notificaciones:boletas-vencidas --test
```

**Resultado:**
- ✅ Muestra cuántos correos se enviarían
- ✅ Lista los destinatarios
- ❌ NO envía correos reales

### Envío Real

```bash
php artisan notificaciones:boletas-vencidas
```

**Resultado:**
- ✅ Envía correos reales a todos los socios con boletas vencidas
- ✅ Muestra progreso en tiempo real
- ✅ Genera estadísticas de envío

---

## ⏰ Ejecución Automática

### Configuración Actual

El comando se ejecuta **automáticamente** todos los días a las **9:00 AM**.

### En Servidor Local (XAMPP)

1. **Abrir terminal en la carpeta del proyecto**

2. **Ejecutar el scheduler de Laravel:**
   ```bash
   php artisan schedule:work
   ```

   **Nota:** Este comando debe estar ejecutándose siempre. Se recomienda:
   - Usar `screen` o `tmux` en Linux
   - Crear un servicio de Windows
   - Usar Task Scheduler de Windows

### En VPS (Servidor de Producción)

1. **Conectar por SSH:**
   ```bash
   ssh ubuntu@18.117.172.118
   ```

2. **Editar el crontab:**
   ```bash
   crontab -e
   ```

3. **Agregar esta línea:**
   ```cron
   * * * * * cd /var/www/html/ssr && php artisan schedule:run >> /dev/null 2>&1
   ```

4. **Guardar y salir** (Ctrl+O, Enter, Ctrl+X)

5. **Verificar que se agregó:**
   ```bash
   crontab -l
   ```

---

## 🔧 Personalización

### Cambiar Frecuencia de Envío

Editar `app/Console/Kernel.php`:

#### Opción 1: Enviar cada 3 días
```php
$schedule->command('notificaciones:boletas-vencidas')
         ->days([1, 4, 7, 10, 13, 16, 19, 22, 25, 28])
         ->at('09:00');
```

#### Opción 2: Enviar solo los lunes
```php
$schedule->command('notificaciones:boletas-vencidas')
         ->weekly()
         ->mondays()
         ->at('09:00');
```

#### Opción 3: Enviar dos veces por semana
```php
$schedule->command('notificaciones:boletas-vencidas')
         ->twiceDaily(9, 15); // 9:00 AM y 3:00 PM
```

### Cambiar Hora de Envío

```php
->dailyAt('14:00')  // 2:00 PM
->dailyAt('08:30')  // 8:30 AM
```

---

## 📊 Niveles de Alertas

El correo muestra diferentes niveles de urgencia según los días de vencimiento:

| Días Vencidos | Tipo de Alerta | Color | Mensaje |
|---------------|----------------|-------|---------|
| 1-15 días | ⚠️ Aviso | Amarillo | "Aviso de pago pendiente" |
| 16-30 días | ⚠️ Recordatorio | Naranja | "Recordatorio de pago" |
| 31+ días | 🔴 Urgente | Rojo | "Boleta con más de X días de vencimiento" |

---

## 📧 Contenido del Correo

El correo incluye:

✅ Saludo personalizado con nombre del socio
✅ Tabla con todas las boletas vencidas
✅ Total adeudado destacado
✅ Información del socio (N° Socio, RUT, Dirección)
✅ Formas de pago disponibles
✅ Datos de contacto
✅ Avisos según gravedad de la deuda

---

## 📝 Logs

Los envíos se registran automáticamente en:

```
storage/logs/laravel.log
```

**Ejemplo de log:**
```
[2026-02-06 09:00:15] local.INFO: Notificación enviada a SOC-0001 - Juan Pérez (juan@example.com) - 2 boleta(s)
[2026-02-06 09:00:16] local.WARNING: Socio SOC-0002 sin email registrado
[2026-02-06 09:00:17] local.ERROR: Error al enviar notificación a SOC-0003: Connection timeout
```

---

## 🔍 Verificación

### 1. Verificar que el comando existe
```bash
php artisan list | grep notificaciones
```

Debe aparecer:
```
notificaciones:boletas-vencidas  Envía notificaciones por correo electrónico a socios con boletas vencidas
```

### 2. Ver tareas programadas
```bash
php artisan schedule:list
```

Debe aparecer:
```
0 9 * * *  php artisan notificaciones:boletas-vencidas  Next Due: Tomorrow at 9:00 AM
```

### 3. Ejecutar prueba
```bash
php artisan notificaciones:boletas-vencidas --test
```

---

## ⚙️ Requisitos

### Configuración de Correo (.env)

Asegúrate de tener configurado el correo en `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-contraseña-de-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@aprpitrelahue.cl
MAIL_FROM_NAME="APR Pitrelahue"
```

**Importante para Gmail:**
- Activar "Verificación en 2 pasos"
- Generar "Contraseña de aplicación" en: https://myaccount.google.com/apppasswords

---

## 🐛 Troubleshooting

### Error: "Class 'App\Console\Commands\EnviarNotificacionesBoletasVencidas' not found"

**Solución:**
```bash
composer dump-autoload
```

### Error: "Connection could not be established with host"

**Solución:**
- Verificar configuración de correo en `.env`
- Verificar que el puerto 587 no esté bloqueado
- Probar con otro servicio SMTP

### No se envían correos automáticamente

**Solución en Local:**
```bash
php artisan schedule:work
```

**Solución en VPS:**
```bash
crontab -e
# Agregar: * * * * * cd /var/www/html/ssr && php artisan schedule:run >> /dev/null 2>&1
```

### Socio no recibe correo

**Verificar:**
1. Que el socio tenga email registrado
2. Que el email sea válido
3. Revisar carpeta de SPAM
4. Revisar logs: `tail -f storage/logs/laravel.log`

---

## 📞 Contacto de Soporte

Si tienes problemas con la implementación, verifica:

1. **Logs de Laravel:** `storage/logs/laravel.log`
2. **Logs del cron:** `grep CRON /var/log/syslog` (en VPS)
3. **Configuración de correo:** `.env`

---

## 🎉 ¡Listo!

El sistema está completamente configurado y funcionando.

Para activarlo:
1. **Local:** `php artisan schedule:work`
2. **VPS:** Agregar cron job (ver sección "En VPS")

**Prueba inicial recomendada:**
```bash
php artisan notificaciones:boletas-vencidas --test
```
