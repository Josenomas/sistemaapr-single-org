# Configuración de Subdominios y Dominios Personalizados

## Sistema APR - Multitenancy por Dominio

Este documento explica cómo configurar el sistema para soportar subdominios y dominios personalizados para cada organización.

---

## 📋 Tabla de Contenidos

1. [Arquitectura del Sistema](#arquitectura-del-sistema)
2. [Configuración del Servidor](#configuración-del-servidor)
3. [Configuración DNS](#configuración-dns)
4. [Configuración para Organizaciones](#configuración-para-organizaciones)
5. [Troubleshooting](#troubleshooting)

---

## 🏗️ Arquitectura del Sistema

### Tipos de Acceso

El sistema soporta 3 tipos de acceso:

1. **Dominio principal**: `sistemaapr.cl`
2. **Subdominios**: `[slug].sistemaapr.cl` (todos los planes)
3. **Dominios personalizados**: `www.aprnombre.cl` (solo Enterprise)

### Identificación de Organizaciones

El middleware `IdentifyTenant` identifica la organización en este orden:

1. Por **dominio personalizado** (si existe en BD)
2. Por **subdominio** (extrae el slug de `*.sistemaapr.cl`)
3. Por **usuario autenticado** (fallback para localhost/desarrollo)

---

## ⚙️ Configuración del Servidor

### 1. Apache (XAMPP/Producción)

#### a) Habilitar módulos necesarios

Asegúrate de que estos módulos estén habilitados en `httpd.conf`:

```apache
LoadModule rewrite_module modules/mod_rewrite.so
LoadModule vhost_alias_module modules/mod_vhost_alias.so
```

#### b) Virtual Host para Wildcard Subdominios

Edita `httpd-vhosts.conf` o tu archivo de configuración de virtual hosts:

```apache
# Virtual Host Principal
<VirtualHost *:80>
    ServerName sistemaapr.cl
    ServerAlias www.sistemaapr.cl
    DocumentRoot "C:/xampp/htdocs/ssr-v2/public"

    <Directory "C:/xampp/htdocs/ssr-v2/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/sistemaapr-error.log"
    CustomLog "logs/sistemaapr-access.log" combined
</VirtualHost>

# Virtual Host para Subdominios (Wildcard)
<VirtualHost *:80>
    ServerName sistemaapr.cl
    ServerAlias *.sistemaapr.cl
    DocumentRoot "C:/xampp/htdocs/ssr-v2/public"

    <Directory "C:/xampp/htdocs/ssr-v2/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/subdominios-error.log"
    CustomLog "logs/subdominios-access.log" combined
</VirtualHost>

# Virtual Host para Dominios Personalizados (ejemplo)
<VirtualHost *:80>
    ServerName www.aprenterprise.cl
    ServerAlias aprenterprise.cl
    DocumentRoot "C:/xampp/htdocs/ssr-v2/public"

    <Directory "C:/xampp/htdocs/ssr-v2/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/aprenterprise-error.log"
    CustomLog "logs/aprenterprise-access.log" combined
</VirtualHost>
```

#### c) Configuración para HTTPS (Producción)

```apache
<VirtualHost *:443>
    ServerName sistemaapr.cl
    ServerAlias *.sistemaapr.cl
    DocumentRoot "/var/www/html/ssr-v2/public"

    SSLEngine on
    SSLCertificateFile /path/to/sistemaapr.cl.crt
    SSLCertificateKeyFile /path/to/sistemaapr.cl.key
    SSLCertificateChainFile /path/to/bundle.crt

    <Directory "/var/www/html/ssr-v2/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 2. Nginx (Alternativa)

```nginx
# Servidor principal y subdominios
server {
    listen 80;
    server_name sistemaapr.cl *.sistemaapr.cl;
    root /var/www/html/ssr-v2/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

# Dominios personalizados (agregar uno por cada dominio)
server {
    listen 80;
    server_name www.aprnombre.cl aprnombre.cl;
    root /var/www/html/ssr-v2/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 3. Configuración Local (Desarrollo)

Edita el archivo `hosts`:

**Windows**: `C:\Windows\System32\drivers\etc\hosts`
**Linux/Mac**: `/etc/hosts`

```
127.0.0.1  sistemaapr.cl
127.0.0.1  www.sistemaapr.cl
127.0.0.1  santarosa.sistemaapr.cl
127.0.0.1  lapaz.sistemaapr.cl
127.0.0.1  www.aprenterprise.cl
```

---

## 🌐 Configuración DNS

### Para Subdominios (*.sistemaapr.cl)

Configura un **registro Wildcard A** en tu proveedor DNS:

```
Tipo:  A
Host:  *
Valor: [IP del servidor]
TTL:   3600
```

O un **registro Wildcard CNAME**:

```
Tipo:  CNAME
Host:  *
Valor: sistemaapr.cl
TTL:   3600
```

### Para Dominios Personalizados

Cada cliente debe configurar en su proveedor DNS:

```
Tipo:  CNAME
Host:  www (o @)
Valor: sistemaapr.cl
TTL:   3600
```

**Ejemplo en NIC Chile:**

1. Ingresar a tu cuenta en NIC Chile
2. Ir a "Administrar DNS"
3. Agregar registro:
   - Tipo: CNAME
   - Nombre: www
   - Destino: sistemaapr.cl.
   - TTL: 3600

**Verificar propagación DNS:**

```bash
# Linux/Mac
dig www.aprnombre.cl
nslookup www.aprnombre.cl

# Online
https://dnschecker.org
```

---

## 👥 Configuración para Organizaciones

### 1. Asignar Slug (Subdominio)

Cada organización debe tener un `slug` único al momento de crearse:

```php
$organizacion = Organizacion::create([
    'nombre_apr' => 'APR Santa Rosa',
    'slug' => 'santarosa', // URL: santarosa.sistemaapr.cl
    'rut' => '12345678-9',
    // ... otros campos
]);
```

### 2. Configurar Dominio Personalizado (Solo Enterprise)

El administrador de la organización puede:

1. Ir a **Organización > Editar Organización**
2. Ingresar el dominio en "Dominio Personalizado"
3. Guardar cambios

**Requisitos:**
- Plan Enterprise activo
- Dominio registrado a nombre del cliente
- DNS configurado correctamente (CNAME a sistemaapr.cl)

### 3. Validación del Sistema

El sistema valida:

✅ Formato correcto del dominio (regex)
✅ Dominio único (no usado por otra organización)
✅ Plan permite dominio personalizado

---

## 🔧 Troubleshooting

### Problema 1: Subdominio no funciona

**Síntomas**: Al acceder a `slug.sistemaapr.cl` sale error 404 o no carga.

**Solución**:
1. Verificar que el registro DNS Wildcard esté configurado
2. Comprobar Virtual Host en Apache/Nginx
3. Reiniciar servidor web: `sudo systemctl restart apache2`
4. Limpiar caché DNS: `ipconfig /flushdns` (Windows) o `sudo systemd-resolve --flush-caches` (Linux)

### Problema 2: Dominio personalizado no funciona

**Síntomas**: Al acceder al dominio personalizado no carga o muestra otra página.

**Solución**:
1. Verificar configuración DNS del cliente (debe tener CNAME)
2. Esperar propagación DNS (hasta 48 horas)
3. Verificar que el dominio esté guardado correctamente en BD
4. Comprobar que la organización tenga plan Enterprise

### Problema 3: Middleware no identifica organización

**Síntomas**: Usuario autenticado pero no puede acceder a datos de su organización.

**Solución**:
1. Verificar que `IdentifyTenant` esté registrado en `Kernel.php`
2. Comprobar que esté en el array `$middleware` global
3. Verificar logs: `storage/logs/laravel.log`
4. Debug: `dd(session('tenant_id'))` en un controller

### Problema 4: Certificado SSL inválido para subdominios

**Síntomas**: Navegador muestra error de certificado SSL en subdominios.

**Solución**:
1. Usar certificado **Wildcard SSL** (*.sistemaapr.cl)
2. Obtener con Let's Encrypt:
   ```bash
   sudo certbot certonly --manual --preferred-challenges dns -d sistemaapr.cl -d *.sistemaapr.cl
   ```
3. Configurar en Apache/Nginx el certificado wildcard

---

## 📞 Soporte

Para asistencia técnica, contacta a:
- **Email**: soporte@sistemaapr.cl
- **Documentación**: https://docs.sistemaapr.cl

---

## 📝 Notas Importantes

⚠️ **Seguridad**:
- Siempre usar HTTPS en producción
- Validar que el usuario pertenezca a la organización del dominio
- Super-admins pueden acceder a cualquier organización

⚠️ **Performance**:
- El middleware se ejecuta en cada request
- Usa caché de sesión para evitar queries repetidas a BD
- Considera usar Redis para sesiones en producción

⚠️ **Mantenimiento**:
- Documentar cada dominio personalizado agregado
- Monitorear logs de acceso por dominio
- Revisar periódicamente dominios expirados
