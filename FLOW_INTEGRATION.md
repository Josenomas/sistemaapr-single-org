# Integración con Flow - Pasarela de Pagos

## 📋 Descripción

Sistema completo de pagos electrónicos integrado con **Flow**, permitiendo que los socios realicen pagos con tarjetas de débito y crédito de forma segura.

## ✨ Características Implementadas

### 1. **Generación de Links de Pago**
- Al seleccionar "Débito" o "Crédito" como método de pago, se activa la sección de Flow
- Solicita el email del socio para enviar el link de pago
- Genera un link único asociado a la boleta seleccionada
- El link se puede copiar o abrir directamente

### 2. **Procesamiento Automático de Pagos**
- Flow notifica automáticamente cuando un pago es exitoso
- El sistema crea automáticamente el registro de pago
- Actualiza el estado de la boleta a "pagada"
- Registra toda la actividad en el historial

### 3. **Seguimiento de Transacciones**
- Tabla dedicada para transacciones Flow (`transacciones_flow`)
- Estados: pendiente, pagado, rechazado, anulado, expirado
- Almacena toda la información de la transacción

### 4. **Callbacks Seguros**
- Validación de firma HMAC SHA256
- URL de confirmación (servidor a servidor)
- URL de retorno (redirección del usuario)

## 🗂️ Archivos Creados

### Modelos
- `app/Models/TransaccionFlow.php` - Modelo de transacciones Flow

### Controladores
- `app/Http/Controllers/FlowController.php` - Manejo de callbacks
- Actualizado: `app/Http/Controllers/PagosController.php` - Método `generarLinkFlow()`

### Servicios
- `app/Services/FlowPaymentService.php` - Lógica de integración con Flow API

### Configuración
- `config/flow.php` - Configuración de Flow
- `.env.flow.example` - Variables de entorno

### Vistas
- Actualizado: `resources/views/pagos/create.blade.php` - Interfaz de pago con Flow

### Base de Datos
- Tabla `transacciones_flow` - Almacena transacciones

### Rutas
Rutas agregadas en `routes/web.php`:
```php
// Autenticadas
Route::post('/pagos/generar-link-flow', [PagosController::class, 'generarLinkFlow']);
Route::get('/flow/transaccion/{id}', [FlowController::class, 'verTransaccion']);

// Públicas (callbacks)
Route::post('/flow/confirmar', [FlowController::class, 'confirmar']);
Route::get('/flow/retorno', [FlowController::class, 'retorno']);
```

## 🔧 Configuración

### 1. Variables de Entorno

Agrega estas variables a tu archivo `.env`:

```env
# Modo: sandbox (pruebas) o production (producción)
FLOW_MODE=sandbox

# Credenciales de Flow
FLOW_API_KEY=tu_api_key_aqui
FLOW_SECRET_KEY=tu_secret_key_aqui

# URL de tu aplicación
APP_URL=http://localhost
```

### 2. Obtener Credenciales de Flow

1. Regístrate en [Flow.cl](https://www.flow.cl/)
2. Accede a tu panel de comercio
3. Obtén tu **API Key** y **Secret Key**
4. Configura las URLs de callback en Flow:
   - URL Confirmación: `http://tu-dominio.com/flow/confirmar`
   - URL Retorno: `http://tu-dominio.com/flow/retorno`

### 3. Configuración para Desarrollo Local

Si estás en desarrollo local (localhost), necesitas exponer tu servidor para recibir los callbacks de Flow:

#### Opción 1: Usar ngrok
```bash
ngrok http 80
```

Luego actualiza tu `.env`:
```env
APP_URL=https://tu-url-ngrok.ngrok.io
```

#### Opción 2: Usar LocalTunnel
```bash
npm install -g localtunnel
lt --port 80
```

## 🧪 Modo Sandbox (Pruebas)

### Tarjetas de Prueba

**✅ Tarjeta Aprobada (Visa):**
- Número: `4513 1200 7678 9283`
- CVV: `123`
- Fecha: Cualquier fecha futura
- Nombre: Cualquier nombre

**❌ Tarjeta Rechazada (Mastercard):**
- Número: `5475 4420 7069 5441`
- CVV: `123`
- Fecha: Cualquier fecha futura

### Flujo de Prueba

1. Ve a **Pagos → Nuevo Pago**
2. Selecciona una boleta pendiente
3. Selecciona método de pago: **Débito** o **Crédito**
4. Ingresa un email válido
5. Click en **Generar Link de Pago**
6. Copia y abre el link generado
7. Usa una tarjeta de prueba
8. El sistema procesará automáticamente el pago

## 📊 Estructura de Base de Datos

### Tabla: `transacciones_flow`

```sql
- id (INT) - ID único
- flow_order (INT) - Número de orden Flow
- token (VARCHAR) - Token único de la transacción
- id_socio (INT) - ID del socio
- id_boleta (INT) - ID de la boleta
- monto (DECIMAL) - Monto de la transacción
- email (VARCHAR) - Email del socio
- subject (VARCHAR) - Asunto del pago
- url_confirmacion (VARCHAR) - URL de confirmación
- url_retorno (VARCHAR) - URL de retorno
- estado (ENUM) - Estado: pendiente, pagado, rechazado, anulado, expirado
- flow_status (INT) - Estado de Flow (1=Pagado, 2=Rechazado, etc)
- payment_data (TEXT) - JSON con datos de confirmación
- fecha_creacion (TIMESTAMP)
- fecha_pago (TIMESTAMP)
- fecha_actualizacion (TIMESTAMP)
```

## 🔐 Seguridad

### Validación de Firma

Todas las peticiones de Flow son validadas mediante firma HMAC SHA256:

```php
// En FlowPaymentService.php
private function firmarParametros($params) {
    ksort($params);
    $string = '';
    foreach ($params as $key => $value) {
        $string .= $key . $value;
    }
    $string .= $this->secretKey;
    return hash_hmac('sha256', $string, $this->secretKey);
}
```

### Verificación en Callbacks

El sistema verifica automáticamente que las notificaciones provengan de Flow.

## 📝 Registro de Actividades

Todas las operaciones se registran automáticamente:

- ✅ Link de pago generado
- ✅ Pago confirmado por Flow
- ✅ Pago registrado en el sistema
- ✅ Boleta actualizada a "pagada"

## 🚀 Pasar a Producción

1. Actualiza `.env`:
   ```env
   FLOW_MODE=production
   FLOW_API_KEY=tu_api_key_produccion
   FLOW_SECRET_KEY=tu_secret_key_produccion
   APP_URL=https://tu-dominio.com
   ```

2. Configura las URLs en Flow:
   - URL Confirmación: `https://tu-dominio.com/flow/confirmar`
   - URL Retorno: `https://tu-dominio.com/flow/retorno`

3. Verifica que tu servidor tenga certificado SSL (HTTPS)

4. Prueba con una transacción real de bajo monto

## 🐛 Troubleshooting

### El link de pago no se genera

- ✅ Verifica que las credenciales en `.env` sean correctas
- ✅ Revisa los logs de Laravel: `storage/logs/laravel.log`
- ✅ Verifica conexión a internet

### No llegan los callbacks

- ✅ Verifica que APP_URL sea accesible públicamente
- ✅ Si estás en local, usa ngrok o similar
- ✅ Revisa los logs: `storage/logs/laravel.log`
- ✅ Verifica que las URLs en Flow estén configuradas correctamente

### El pago no se registra automáticamente

- ✅ Revisa la tabla `transacciones_flow`
- ✅ Verifica que el callback de confirmación esté llegando
- ✅ Revisa los logs de Flow en el panel de Flow

## 📚 API de Flow

Documentación oficial: [https://www.flow.cl/docs/api.html](https://www.flow.cl/docs/api.html)

### Endpoints Utilizados

- **POST /payment/create** - Crear pago
- **GET /payment/getStatus** - Obtener estado del pago

## 💡 Mejoras Futuras

- [ ] Envío automático de emails con el link de pago
- [ ] Panel de administración de transacciones Flow
- [ ] Reportes de pagos por método (Flow vs otros)
- [ ] Reembolsos automáticos
- [ ] Suscripciones y pagos recurrentes

## 🤝 Soporte

Para problemas con Flow:
- Soporte Flow: soporte@flow.cl
- Documentación: https://www.flow.cl/docs/

Para problemas del sistema:
- Revisa `storage/logs/laravel.log`
- Contacta al equipo de desarrollo

---

**Última actualización:** Noviembre 2025
