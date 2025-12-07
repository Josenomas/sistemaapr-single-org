# 🚀 Integración Flow - Guía Rápida

## ✅ Configuración (Ya tienes credenciales)

### 1. Agrega tus credenciales al archivo `.env`:

```env
# Modo: sandbox (pruebas) o production (producción)
FLOW_MODE=sandbox

# Tus credenciales de Flow
FLOW_API_KEY=tu_api_key_de_flow
FLOW_SECRET_KEY=tu_secret_key_de_flow

# URL de tu aplicación
APP_URL=http://tu-dominio.com
```

### 2. Configura las URLs de callback en Flow:

Accede a tu panel de Flow y configura:
- **URL Confirmación:** `http://tu-dominio.com/flow/confirmar`
- **URL Retorno:** `http://tu-dominio.com/flow/retorno`

## 💳 Cómo Usar

### Generar Link de Pago:

1. Ve a **Pagos → Nuevo Pago**
2. Selecciona una boleta pendiente
3. Elige **"Débito"** o **"Crédito"** como método de pago
4. Ingresa el email del socio
5. Click en **"Generar Link de Pago"**
6. Comparte el link generado con el socio

### El Socio Paga:

1. El socio abre el link
2. Ingresa sus datos de tarjeta en Flow
3. Flow procesa el pago
4. **El sistema registra automáticamente el pago**
5. La boleta se actualiza a "pagada"

## 🧪 Modo Sandbox (Pruebas)

Para probar con tarjetas de prueba:

**Tarjeta Aprobada:**
- Número: `4513 1200 7678 9283`
- CVV: `123`
- Fecha: Cualquier fecha futura

**Tarjeta Rechazada:**
- Número: `5475 4420 7069 5441`
- CVV: `123`
- Fecha: Cualquier fecha futura

## 🌐 Desarrollo Local (Localhost)

Si estás en desarrollo local (localhost), necesitas exponer tu servidor:

### Con ngrok:
```bash
ngrok http 80
```

Luego actualiza tu `.env`:
```env
APP_URL=https://tu-url-ngrok.ngrok.io
```

Y en Flow, configura las URLs con tu URL de ngrok.

## 🔐 Pasar a Producción

1. Actualiza `.env`:
   ```env
   FLOW_MODE=production
   FLOW_API_KEY=tu_api_key_produccion
   FLOW_SECRET_KEY=tu_secret_key_produccion
   APP_URL=https://tu-dominio.com
   ```

2. Verifica que tu servidor tenga HTTPS (certificado SSL)

3. Prueba con una transacción real de bajo monto

## 📊 Ver Transacciones

Todas las transacciones Flow se guardan en la tabla `transacciones_flow`:

```sql
SELECT * FROM transacciones_flow ORDER BY id DESC;
```

Puedes ver:
- Estado de cada transacción
- Monto
- Email del socio
- Token de Flow
- Datos de confirmación

## 🆘 Problemas Comunes

### No se genera el link
- ✅ Verifica credenciales en `.env`
- ✅ Revisa `storage/logs/laravel.log`

### No llegan los callbacks
- ✅ Verifica que APP_URL sea accesible públicamente
- ✅ En local, usa ngrok
- ✅ Verifica URLs en panel de Flow

### El pago no se registra
- ✅ Revisa tabla `transacciones_flow`
- ✅ Revisa logs de Laravel
- ✅ Verifica que el callback esté llegando

## 📚 Documentación

- Flow API: https://www.flow.cl/docs/api.html
- Panel Flow: https://www.flow.cl/app/web/
- Soporte: soporte@flow.cl

---

**¡Listo!** Con tus credenciales configuradas, el sistema está funcionando con Flow real. 🎉
