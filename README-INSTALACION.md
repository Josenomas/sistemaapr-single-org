# 📘 Guía de Instalación - Sistema APR (Versión Cliente)

Esta es la versión **mono-organización** del Sistema APR, diseñada para instalarse en el servidor de un cliente específico.

---

## 🎯 Requisitos del Servidor

- **PHP**: 8.0 o superior
- **MySQL**: 5.7 o superior / MariaDB 10.3+
- **Composer**: 2.x
- **Node.js**: 16.x o superior (opcional, para assets)
- **Nginx** o **Apache**
- **SSL/TLS**: Certificado válido (Let's Encrypt recomendado)

---

## 🚀 Instalación Paso a Paso

### 1. Clonar Repositorio en el Servidor

```bash
cd /var/www
git clone https://github.com/Josenomas/sistemaapr-single-org.git nombre-cliente
cd nombre-cliente
```

### 2. Instalar Dependencias

```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build  # Opcional
```

### 3. Configurar Permisos

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 4. Configurar Base de Datos

```bash
# Crear base de datos
mysql -u root -p
CREATE DATABASE apr_cliente CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'apr_user'@'localhost' IDENTIFIED BY 'contraseña_segura';
GRANT ALL PRIVILEGES ON apr_cliente.* TO 'apr_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 5. Configurar Archivo .env

```bash
cp .env.example .env
nano .env
```

Editar las siguientes variables:

```env
APP_NAME="APR NombreCliente"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://cliente.dominio.cl

DB_DATABASE=apr_cliente
DB_USERNAME=apr_user
DB_PASSWORD=contraseña_segura

# Configurar email
MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=contacto@cliente.cl
MAIL_PASSWORD=tu_app_password

# Configurar Flow (obtener en flow.cl)
FLOW_API_KEY=tu_api_key
FLOW_SECRET_KEY=tu_secret_key
FLOW_SANDBOX=false
```

### 6. Generar Key de Aplicación

```bash
php artisan key:generate
```

### 7. Ejecutar Migraciones

```bash
php artisan migrate --force
```

### 8. Instalar Organización del Cliente

```bash
php artisan apr:install \
  --nombre="APR Los Pinos" \
  --rut="12345678-9" \
  --email="admin@aprpinos.cl" \
  --password="ContraseñaSegura123" \
  --telefono="+56912345678" \
  --direccion="Calle Principal 123" \
  --ciudad="Puerto Montt" \
  --region="Los Lagos"
```

Este comando creará:
- ✅ La organización con los datos proporcionados
- ✅ Usuario administrador con permisos completos
- ✅ Configuración inicial lista para usar

### 9. Crear Symlink de Storage

```bash
php artisan storage:link
```

### 10. Optimizar para Producción

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🌐 Configurar Nginx

Crear archivo de configuración:

```bash
sudo nano /etc/nginx/sites-available/apr-cliente
```

Contenido:

```nginx
server {
    listen 80;
    server_name cliente.dominio.cl;

    # Redirigir a HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name cliente.dominio.cl;

    root /var/www/nombre-cliente/public;
    index index.php index.html;

    # SSL
    ssl_certificate /etc/letsencrypt/live/cliente.dominio.cl/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/cliente.dominio.cl/privkey.pem;

    # Logs
    access_log /var/log/nginx/apr-cliente-access.log;
    error_log /var/log/nginx/apr-cliente-error.log;

    # PHP
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Activar sitio:

```bash
sudo ln -s /etc/nginx/sites-available/apr-cliente /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 🎨 Personalizar Landing Page

Editar el archivo `resources/views/landing.blade.php`:

```php
// Línea 389: Cambiar título
<h1>💧 APR NombreCliente</h1>

// Línea 390: Cambiar descripción
<p>Descripción personalizada del APR...</p>

// Sección de Contacto (línea ~520)
<p><i class="fas fa-map-marker-alt"></i> Dirección del Cliente</p>
<p><i class="fas fa-phone"></i> +56 9 XXXX XXXX</p>
<p><i class="fas fa-envelope"></i> contacto@cliente.cl</p>
```

Después de modificar:

```bash
php artisan view:clear
```

---

## 🔐 Configurar Flow (Pagos)

1. Crear cuenta en [Flow.cl](https://www.flow.cl)
2. Obtener **API Key** y **Secret Key**
3. Configurar URLs de retorno en panel Flow:
   - **URL Confirmación**: `https://cliente.dominio.cl/flow/confirmar`
   - **URL Retorno**: `https://cliente.dominio.cl/flow/retorno`
4. Actualizar `.env` con las credenciales

---

## 📧 Configurar Email

### Gmail:
1. Habilitar **Verificación en 2 pasos**
2. Generar **Contraseña de aplicación**
3. Configurar en `.env`:

```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=contraseña_app_16_digitos
MAIL_ENCRYPTION=tls
```

---

## 👨‍💼 Acceso SuperAdmin (Soporte)

Como desarrollador, puedes crear tu usuario SuperAdmin para soporte:

```bash
php artisan tinker
```

```php
$superadmin = new App\Models\User;
$superadmin->nombre = 'Jose Norambuena';
$superadmin->email = 'aravenanacho@gmail.com';
$superadmin->password = Hash::make('tu_contraseña_segura');
$superadmin->rol = 'superadmin';
$superadmin->activo = 1;
$superadmin->save();
```

Esto te permite:
- ✅ Acceder al panel `/superadmin`
- ✅ Configurar precio mensual de suscripción
- ✅ Monitorear facturación DTE
- ✅ Ver métricas y auditoría

---

## 🔧 Mantenimiento

### Limpiar caché:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Actualizar sistema:
```bash
git pull origin main
composer install --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Backup de base de datos:
```bash
mysqldump -u apr_user -p apr_cliente > backup_$(date +%Y%m%d).sql
```

---

## 📋 Checklist Post-Instalación

- [ ] Sistema instalado y funcionando
- [ ] Organización creada con `php artisan apr:install`
- [ ] Landing personalizado con datos del cliente
- [ ] Flow configurado y probado
- [ ] Email configurado y probado
- [ ] SSL/HTTPS funcionando
- [ ] Permisos de archivos correctos
- [ ] Backup automático configurado
- [ ] Usuario SuperAdmin creado (para soporte)
- [ ] Precio mensual configurado desde panel SuperAdmin

---

## 🆘 Soporte

Para soporte técnico contactar a:
- **Email**: aravenanacho@gmail.com
- **WhatsApp**: +56 9 XXXX XXXX

---

## 📝 Notas Importantes

1. **NO exponer** credenciales de `.env` en Git
2. **Siempre usar HTTPS** en producción
3. **Configurar backups** automáticos de BD
4. **Monitorear logs**: `storage/logs/laravel.log`
5. **Actualizar** sistema regularmente

---

✅ **Sistema listo para producción** 🎉
