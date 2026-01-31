# Configuración de WhatsApp con Twilio

## 1. Instalación del SDK de Twilio

Ejecutar en la terminal:

```bash
composer require twilio/sdk
```

## 2. Crear cuenta en Twilio

1. Ir a [https://www.twilio.com/](https://www.twilio.com/)
2. Crear cuenta gratuita (incluye crédito de prueba)
3. Verificar número de teléfono

## 3. Obtener credenciales

1. Ir al Dashboard de Twilio
2. Copiar:
   - **Account SID**
   - **Auth Token**

## 4. Configurar WhatsApp Sandbox (Modo de prueba)

Para probar sin activar el servicio completo:

1. En el Dashboard de Twilio, ir a **Messaging** → **Try it out** → **Send a WhatsApp message**
2. Seguir las instrucciones para conectar tu WhatsApp personal
3. Enviar el código de activación al número de Twilio
4. Copiar el número de WhatsApp de Twilio (ejemplo: `whatsapp:+14155238886`)

## 5. Configurar variables de entorno

Editar el archivo `.env` y agregar:

```env
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=tu_auth_token_aqui
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
```

## 6. Probar el servicio

```bash
php artisan tinker
```

Dentro de tinker:

```php
$socio = App\Models\Socio::first();
$notificacion = App\Models\Notificacion::first();
$job = new App\Jobs\EnviarNotificacionWhatsApp($notificacion, $socio);
$job->handle();
```

## 7. Activar servicio en producción (Opcional)

Para enviar mensajes a cualquier número de WhatsApp sin sandbox:

1. En Twilio, ir a **Messaging** → **WhatsApp** → **Senders**
2. Solicitar un **WhatsApp Business Profile**
3. Completar el proceso de verificación de Facebook Business
4. Costos aproximados:
   - **Conversaciones iniciadas por usuario**: Gratis
   - **Conversaciones iniciadas por negocio**: ~$5-10 CLP por mensaje
   - **Mensajes de plantilla aprobados**: Requiere aprobación de Facebook

## 8. Configurar Queue (Recomendado para producción)

Para procesar los mensajes de forma asíncrona:

1. Cambiar en `.env`:
```env
QUEUE_CONNECTION=database
```

2. Crear tabla de trabajos:
```bash
php artisan queue:table
php artisan migrate
```

3. Ejecutar el worker:
```bash
php artisan queue:work
```

## 9. Formato de números de teléfono

El servicio acepta los siguientes formatos:

- `+56912345678` (formato internacional)
- `56912345678` (sin +)
- `912345678` (número chileno de 9 dígitos)

El servicio automáticamente formatea el número al formato internacional requerido por Twilio.

## 10. Troubleshooting

### Error: "The number +56XXXXXXXXX is unverified"
- Estás en modo Sandbox
- Necesitas verificar cada número destinatario en Twilio
- O activar el servicio en producción

### Error: "Unable to create record"
- Verificar credenciales en `.env`
- Verificar que el número FROM sea correcto
- Verificar saldo de cuenta Twilio

### Error: "Class 'Twilio\Rest\Client' not found"
- Ejecutar `composer require twilio/sdk`
- Ejecutar `composer dump-autoload`

## 11. Alternativas a Twilio

Si Twilio resulta muy costoso, existen alternativas:

- **Ultramsg**: ~$3-7 CLP/mensaje
- **ChatAPI**: ~$5-8 CLP/mensaje
- **Maytapi**: ~$4-9 CLP/mensaje

**Nota**: Estas alternativas usan WhatsApp Web API (no oficial) y pueden tener riesgos de bloqueo de cuenta.
