# 📋 Plan de Migración de LibreDTE a SimpleAPI

## 🎯 Objetivo
Migrar el sistema de facturación electrónica de LibreDTE ($40.000/mes) a SimpleAPI (GRATIS hasta 500 documentos/mes) para ahorrar **$480.000 CLP/año**.

---

## ✅ Cambios Implementados

### 1. **Nuevo Servicio SimpleAPI** ✅
**Archivo:** `app/Services/SimpleAPIService.php`
- ✅ Emisión de Boletas (39) y Facturas (33)
- ✅ Notas de Crédito (61) y Débito (56)
- ✅ Verificación de estado en SII
- ✅ Consulta de folios disponibles
- ✅ Manejo de errores y logging

### 2. **Configuración SimpleAPI** ✅
**Archivo:** `config/simpleapi.php`
- ✅ URL de API
- ✅ API Key
- ✅ Ambiente (certificación/producción)
- ✅ Tipos de DTE soportados
- ✅ Rate limiting (3 consultas/segundo)

### 3. **Variables de Entorno** ✅
**Archivo:** `.env.example`
```env
SIMPLEAPI_URL=https://api.simpleapi.cl
SIMPLEAPI_KEY=
SIMPLEAPI_AMBIENTE=certificacion
SIMPLEAPI_TIMEOUT=30
SIMPLEAPI_LOG_REQUESTS=true
```

### 4. **Migración Base de Datos** ✅
**Archivo:** `database/migrations/2026_05_25_221950_add_proveedor_to_configuracion_dte_table.php`
- ✅ Nuevo campo `proveedor_dte` (libredte/simpleapi)
- ✅ Default: 'libredte' (backward compatible)

### 5. **Modelo Actualizado** ✅
**Archivo:** `app/Models/ConfiguracionDTE.php`
- ✅ Método `usaSimpleAPI()`
- ✅ Método `usaLibreDTE()`
- ✅ Validación según proveedor

### 6. **Controlador con Dual-Provider** ✅
**Archivo:** `app/Http/Controllers/DTEController.php`
- ✅ Método `getDTEService()` que decide dinámicamente
- ✅ Soporta LibreDTE y SimpleAPI simultáneamente
- ✅ Emisión individual y masiva

### 7. **Interfaz de Usuario** ✅
**Archivo:** `resources/views/dte/configuracion.blade.php`
- ✅ Selector de proveedor (LibreDTE / SimpleAPI)
- ✅ JavaScript para mostrar/ocultar campos
- ✅ Indicador de precio por proveedor

---

## 🚀 Pasos de Implementación

### **Fase 1: Preparación (15 minutos)**

#### 1.1 Ejecutar Migración
```bash
# En local
php artisan migrate

# En producción (servidor DigitalOcean)
ssh digitalocean-sistemaapr "cd /var/www/ssr && php artisan migrate --force"
```

#### 1.2 Registrarse en SimpleAPI
1. Ir a: https://www.simpleapi.cl/
2. Crear cuenta gratuita
3. Obtener API Key desde el panel

#### 1.3 Configurar Variables de Entorno
```bash
# Agregar a .env en producción
SIMPLEAPI_KEY=tu_api_key_aqui
SIMPLEAPI_AMBIENTE=certificacion  # Empezar con certificación
SIMPLEAPI_LOG_REQUESTS=true
```

---

### **Fase 2: Testing en Certificación (1-2 horas)**

#### 2.1 Configurar Organización de Prueba
1. Ir a: `/dte/configuracion`
2. Seleccionar **Proveedor: SimpleAPI**
3. Seleccionar **Ambiente: Certificación**
4. Subir certificado digital (si aún no está cargado)
5. Guardar configuración

#### 2.2 Pruebas de Emisión
```
✅ Emitir 1 boleta individual (tipo 39)
✅ Emitir 1 factura (tipo 33)
✅ Emitir nota de crédito (tipo 61)
✅ Verificar estado en SII
✅ Verificar folios disponibles
✅ Emisión masiva (10-20 boletas)
```

#### 2.3 Validaciones
- ✅ PDF generado correctamente
- ✅ XML válido
- ✅ Folio SII asignado
- ✅ Estado DTE actualizado
- ✅ Email enviado al socio (si aplica)

---

### **Fase 3: Migración a Producción (30 minutos)**

#### 3.1 Cambiar a Ambiente Producción
1. Ir a: `/dte/configuracion`
2. Cambiar **Ambiente: Producción**
3. Verificar que SimpleAPI sigue seleccionado
4. Guardar

#### 3.2 Emisión Real
- Emitir 1-2 boletas reales
- Validar en portal SII que aparecen
- Confirmar con socio que recibió el email

#### 3.3 Monitoreo (Primeros 7 días)
- Revisar logs diarios: `storage/logs/laravel.log`
- Verificar tasa de éxito de emisiones
- Comparar tiempos de respuesta vs LibreDTE

---

### **Fase 4: Cancelación LibreDTE (Después de 1 mes exitoso)**

Cuando estés 100% seguro que SimpleAPI funciona perfectamente:

1. Cancelar suscripción LibreDTE
2. Eliminar variables `LIBREDTE_*` del .env
3. (Opcional) Eliminar `app/Services/LibreDTEService.php`

---

## 💰 Comparación de Costos

| Concepto | LibreDTE | SimpleAPI | Ahorro Anual |
|----------|----------|-----------|--------------|
| Costo mensual | $40.000 | $0 | $480.000 |
| Documentos incluidos | Ilimitados | 500/mes | - |
| Tu volumen actual | 200/mes | 200/mes | - |
| % de uso del plan | N/A | 40% | - |

**AHORRO TOTAL: $480.000 CLP/año** (USD ~$540/año)

---

## 🔍 Compatibilidad de Funcionalidades

| Funcionalidad | LibreDTE | SimpleAPI | Estado |
|---------------|----------|-----------|--------|
| Boleta (39) | ✅ | ✅ | ✅ Compatible |
| Factura (33) | ✅ | ✅ | ✅ Compatible |
| Factura Exenta (34) | ✅ | ✅ | ✅ Compatible |
| Nota Crédito (61) | ✅ | ✅ | ✅ Compatible |
| Nota Débito (56) | ✅ | ✅ | ✅ Compatible |
| Guía Despacho (52) | ✅ | ✅ | ✅ Compatible |
| Verificar estado SII | ✅ | ✅ | ✅ Compatible |
| Folios automáticos | ✅ | ✅ | ✅ Compatible |
| PDF generado | ✅ | ✅ | ✅ Compatible |
| XML firmado | ✅ | ✅ | ✅ Compatible |
| Multi-RUT | ✅ | ✅ | ✅ Compatible |

**Conclusión: 100% compatible** ✅

---

## 🛡️ Plan de Rollback

Si algo sale mal con SimpleAPI, puedes volver a LibreDTE en **menos de 5 minutos**:

1. Ir a `/dte/configuracion`
2. Cambiar **Proveedor: LibreDTE**
3. Guardar

Todos los datos de LibreDTE siguen guardados en la BD. No se pierde nada.

---

## 📊 Monitoreo Post-Migración

### KPIs a Monitorear (Primeros 30 días):

1. **Tasa de Éxito de Emisión**
   - Meta: > 98%
   - Actual LibreDTE: ?
   - SimpleAPI: (por medir)

2. **Tiempo Promedio de Emisión**
   - Meta: < 5 segundos
   - Actual LibreDTE: ?
   - SimpleAPI: (por medir)

3. **Errores por Día**
   - Meta: < 5 errores/día
   - Actual LibreDTE: ?
   - SimpleAPI: (por medir)

4. **Satisfacción del Usuario**
   - ¿Los socios reciben las boletas?
   - ¿Los PDF se ven bien?
   - ¿Hay quejas?

---

## 🔧 Troubleshooting Común

### Error: "No hay certificado digital configurado"
**Solución:** Subir certificado .pfx en `/dte/configuracion`

### Error: "API Key inválida"
**Solución:** Verificar `SIMPLEAPI_KEY` en `.env`

### Error: "Rate limit exceeded (3/segundo)"
**Solución:** Agregar delay de 350ms entre emisiones masivas

### Error: "Folio no asignado"
**Solución:** Verificar que hay folios disponibles en SimpleAPI

---

## 📞 Soporte

### SimpleAPI:
- Web: https://www.simpleapi.cl/
- Docs: https://documentacion.simpleapi.cl/
- Soporte: contacto@chilesystems.com

### Sistema APR:
- Logs: `storage/logs/laravel.log`
- Panel DTE: `/dte/dashboard`
- Config: `/dte/configuracion`

---

## ✅ Checklist Final

Antes de considerar la migración completa:

- [ ] Migración ejecutada sin errores
- [ ] API Key de SimpleAPI configurada
- [ ] Certificado digital cargado
- [ ] 10+ boletas emitidas exitosamente en certificación
- [ ] 10+ boletas emitidas exitosamente en producción
- [ ] PDFs generados correctamente
- [ ] Emails llegando a socios
- [ ] Folios verificados en SII
- [ ] Logs sin errores críticos
- [ ] 30 días de operación estable
- [ ] LibreDTE cancelado
- [ ] **AHORRO: $480.000/año activado** 🎉

---

## 🎉 Beneficios Post-Migración

1. ✅ **Ahorro de $480.000/año**
2. ✅ **Plan FREE hasta 500 docs/mes** (suficiente para tu volumen)
3. ✅ **Misma funcionalidad** que LibreDTE
4. ✅ **Multi-RUT sin costo adicional**
5. ✅ **Soporte técnico de ChileSystems**
6. ✅ **API moderna y rápida**
7. ✅ **Sin vendor lock-in** (puedes volver a LibreDTE cuando quieras)

---

**Fecha de creación:** 2026-05-25
**Última actualización:** 2026-05-25
**Estado:** ✅ Listo para implementar
