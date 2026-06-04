# Guía para Activar Facturación Electrónica en Sistema APR

## ¿Qué es la Facturación Electrónica?

La facturación electrónica es un sistema que permite emitir boletas válidas ante el SII de forma digital. Con esto, tu APR podrá:

✅ Emitir boletas electrónicas automáticamente cada mes
✅ Cumplir con las normativas del SII
✅ Enviar boletas por email a los socios
✅ Eliminar boletas en papel
✅ Tener respaldo digital de todas las transacciones

---

## Requisitos para Activar

### 1. Certificado Digital del SII 🔐

**¿Qué es?**
Es un archivo digital que permite firmar electrónicamente las boletas, otorgándoles validez legal.

**¿Cómo obtenerlo?**

1. Ingresar al sitio web del SII: [www.sii.cl](https://www.sii.cl)
2. Acceder con la Clave Tributaria del representante legal de la organización
3. Ir a **Facturación Electrónica** → **Certificado Digital**
4. Solicitar certificado digital
5. Descargar el archivo (formato `.pfx` o `.p12`)
6. Guardar la contraseña que asignaste

**Costo:** Gratis
**Tiempo:** 10-15 minutos
**Validez:** 3 años

---

### 2. Autorización de Folios en el SII 📝

**¿Qué son los folios?**
Los folios son los números consecutivos que el SII asigna a cada boleta electrónica. Debes solicitar autorización de un rango de folios antes de emitir.

**¿Cómo obtenerlos?**

1. Ingresar al sitio web del SII: [www.sii.cl](https://www.sii.cl)
2. Acceder con la Clave Tributaria del representante legal
3. Ir a **Facturación Electrónica** → **Administrar Folios**
4. Seleccionar tipo de documento: **Boleta Electrónica (39)**
5. Solicitar rango de folios (recomendado: 1000 folios)
6. Descargar archivo CAF (Código de Autorización de Folios)

**Costo:** Gratis
**Tiempo:** 5-10 minutos
**Nota:** Cuando se agoten los folios, deberás solicitar un nuevo rango

---

### 3. Datos de la Organización 📋

Debes proporcionar los siguientes datos:

- **RUT de la organización** (ejemplo: 12.345.678-9)
- **Razón social** (ejemplo: "APR Agua Limpia")
- **Giro** (ejemplo: "Distribución de agua potable")
- **Dirección completa** (calle, número, comuna)
- **Comuna**
- **Teléfono de contacto**
- **Email de contacto**

**Datos del representante legal:**
- Nombre completo
- RUT
- Email

---

### 4. Contratación de SimpleFactura 💰

**¿Qué es SimpleFactura?**
Es el servicio que conecta nuestro sistema con el SII para la emisión de boletas electrónicas.

**Plan recomendado:** Plan Independiente

**Costo mensual:** $17.850 (IVA incluido)

**Incluye:**
- Hasta 500 boletas electrónicas al mes
- 1 usuario activo
- Portal web y API
- Soporte técnico

**¿Cómo contratarlo?**

1. Ingresar a: [www.simplefactura.cl](https://simplefactura.cl)
2. Clic en "Contrata"
3. Seleccionar **Plan Independiente**
4. En "Certificación ante el SII" seleccionar: **"Ya estoy certificado / lo haré por mi cuenta"**
5. Completar datos de la organización
6. Realizar el pago

---

## Proceso de Activación

### Paso 1: Obtener Certificado Digital
- El representante legal debe ingresar al SII y descargar el certificado
- Guardar el archivo `.pfx` o `.p12` de forma segura
- Anotar la contraseña del certificado

### Paso 2: Solicitar Folios en el SII
- Ingresar al SII y solicitar rango de folios para Boleta Electrónica (39)
- Descargar archivo CAF (Código de Autorización de Folios)
- Guardar el archivo `.xml` del CAF

### Paso 3: Contratar SimpleFactura
- Crear cuenta en SimpleFactura
- Contratar Plan Independiente
- Configurar método de pago

### Paso 4: Enviar Información al Administrador del Sistema
Enviar por email la siguiente información:

```
Asunto: Activación Facturación Electrónica - [Nombre APR]

Datos de la organización:
- RUT:
- Razón Social:
- Giro:
- Dirección:
- Comuna:
- Teléfono:
- Email:

Representante Legal:
- Nombre:
- RUT:
- Email:

Archivos adjuntos:
- Certificado Digital (.pfx o .p12)
- Documento con la contraseña del certificado
- Archivo CAF de folios (.xml)

Credenciales SimpleFactura:
- Usuario:
- Contraseña:
- RUT registrado:
```

### Paso 5: Configuración en el Sistema
El administrador del sistema realizará la configuración técnica (demora 1-2 días hábiles):

- Subir certificado a SimpleFactura
- Subir archivo CAF de folios
- Configurar credenciales API
- Realizar pruebas de emisión
- Activar módulo de facturación electrónica

### Paso 6: ¡Listo para Emitir!
Una vez configurado, el sistema automáticamente:
- Generará boletas electrónicas cada mes
- Enviará las boletas por email a los socios
- Almacenará respaldo digital
- Permitirá descargar PDF de las boletas

---

## Costos Mensuales

| Concepto | Monto |
|----------|-------|
| Plan SimpleFactura | $17.850/mes |
| Certificado Digital SII | $0 (gratis, renovar cada 3 años) |
| **TOTAL MENSUAL** | **$17.850** |

**Nota:** Si se emiten más de 500 boletas al mes, se cobra aproximadamente $200 por boleta adicional.

---

## Preguntas Frecuentes

### ¿Es obligatorio usar facturación electrónica?
Sí, desde el año 2022 la emisión de boletas electrónicas es obligatoria para todas las organizaciones en Chile.

### ¿Qué pasa con las boletas ya emitidas en papel?
Pueden coexistir durante el período de transición. Una vez activada la facturación electrónica, se recomienda emitir solo documentos digitales.

### ¿Los socios recibirán las boletas por email?
Sí, el sistema enviará automáticamente cada boleta al email registrado del socio.

### ¿Puedo seguir usando boletas en papel?
No se recomienda. La ley exige emisión electrónica y puede haber multas por no cumplir.

### ¿Qué pasa si se cae el sistema?
SimpleFactura tiene respaldo de todos los documentos emitidos. Además, nuestro sistema guarda copia local de cada boleta.

### ¿Cuánto demora la activación?
Una vez recibida toda la información, la activación toma entre 1-2 días hábiles.

### ¿Necesito conocimientos técnicos?
No. Solo debes obtener el certificado y folios del SII, y contratar SimpleFactura. La configuración técnica la realiza el administrador del sistema.

### ¿Qué pasa cuando se agoten los folios?
Cuando estés por agotar el rango de folios asignado, debes solicitar un nuevo rango en el SII y enviarlo al administrador del sistema para actualizarlo.

### ¿Puedo cancelar el servicio?
Sí, pero ten en cuenta que sin facturación electrónica no podrás emitir boletas válidas ante el SII.

---

## Contacto y Soporte

**Para activar facturación electrónica o consultas:**

📧 Email: sistemaapr@gmail.com
🌐 Web: sistemaapr.cl

**Soporte SimpleFactura:**

📧 Email: soporte@simplefactura.cl
🌐 Web: www.simplefactura.cl
📞 Teléfono: (consultar en su sitio web)

---

## Documentos de Referencia

- [Guía SII para Certificado Digital](https://www.sii.cl/destacados/certificado_digital/index.html)
- [Normativa Facturación Electrónica SII](https://www.sii.cl/factura_electronica/)
- [SimpleFactura - Documentación](https://docs.simplefactura.cl/)

---

**Última actualización:** Junio 2026
**Versión:** 1.0
