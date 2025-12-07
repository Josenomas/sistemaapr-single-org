# Guía de Despliegue - Sistema APR en VPS Ubuntu 22.04

## 📋 Requisitos Previos
- VPS Ubuntu 22.04
- Acceso SSH al servidor
- Dominio apuntando al VPS (opcional pero recomendado)

---

## 🚀 Paso 1: Conectarse al VPS

```bash
ssh root@tu-ip-del-vps
```

---

## 📦 Paso 2: Actualizar el Sistema

```bash
apt update && apt upgrade -y
```

---

## 🔧 Paso 3: Instalar Dependencias Necesarias

### Instalar Nginx
```bash
apt install nginx -y
systemctl enable nginx
systemctl start nginx
```

### Instalar PHP 8.2 y extensiones necesarias
```bash
apt install software-properties-common -y
add-apt-repository ppa:ondrej/php -y
apt update

apt install php8.2 php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring \
php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd \
php8.2-intl php8.2-soap php8.2-tokenizer -y
```

### Instalar MySQL
```bash
apt install mysql-server -y
systemctl enable mysql
systemctl start mysql
```

### Instalar Composer
```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
```

### Instalar Git
```bash
apt install git -y
```

---

## 🗄️ Paso 4: Configurar MySQL

```bash
# Configuración segura de MySQL
mysql_secure_installation
```

Crear base de datos y usuario:

```bash
mysql -u root -p
```

Dentro de MySQL:
```sql
CREATE DATABASE ssr_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ssr_user'@'localhost' IDENTIFIED BY 'tu_contraseña_segura';
GRANT ALL PRIVILEGES ON ssr_database.* TO 'ssr_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 📁 Paso 5: Subir la Aplicación al VPS

### Opción A: Usando Git (Recomendado)

```bash
# Crear directorio para la app
mkdir -p /var/www/ssr
cd /var/www/ssr

# Clonar repositorio (si tienes el código en Git)
git clone tu-repositorio-git .

# O subir archivos manualmente con SCP/FTP
```

### Opción B: Subir archivos con SCP desde tu PC local

Desde tu PC Windows (PowerShell o CMD):
```bash
scp -r C:\xampp\htdocs\ssr root@tu-ip-del-vps:/var/www/ssr
```

---

## ⚙️ Paso 6: Configurar la Aplicación Laravel

```bash
cd /var/www/ssr

# Instalar dependencias de Composer
composer install --optimize-autoloader --no-dev

# Copiar archivo de configuración
cp .env.example .env

# Editar archivo .env
nano .env
```

### Configurar variables en `.env`:

```env
APP_NAME="Sistema APR"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://tudominio.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ssr_database
DB_USERNAME=ssr_user
DB_PASSWORD=tu_contraseña_segura

# Configuración de email (usa tu servicio de email)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-contraseña-de-aplicación
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
MAIL_CONTACT=aravenanacho890@gmail.com

# Configuración Flow (si usas pagos)
FLOW_API_URL=https://www.flow.cl/api
FLOW_API_KEY=tu-api-key
FLOW_SECRET_KEY=tu-secret-key
```

### Generar clave de aplicación y ejecutar migraciones:

```bash
# Generar APP_KEY
php artisan key:generate

# Ejecutar migraciones
php artisan migrate --force

# Crear enlace simbólico para storage
php artisan storage:link

# Cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimizar Composer
composer dump-autoload --optimize
```

---

## 🔐 Paso 7: Configurar Permisos

```bash
# Cambiar propietario de archivos
chown -R www-data:www-data /var/www/ssr

# Configurar permisos correctos
chmod -R 755 /var/www/ssr
chmod -R 775 /var/www/ssr/storage
chmod -R 775 /var/www/ssr/bootstrap/cache
```

---

## 🌐 Paso 8: Configurar Nginx

```bash
# Crear archivo de configuración
nano /etc/nginx/sites-available/ssr
```

Contenido del archivo:

```nginx
server {
    listen 80;
    server_name tudominio.com www.tudominio.com;
    root /var/www/ssr/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 20M;
}
```

### Activar sitio y reiniciar Nginx:

```bash
# Crear enlace simbólico
ln -s /etc/nginx/sites-available/ssr /etc/nginx/sites-enabled/

# Eliminar sitio por defecto
rm /etc/nginx/sites-enabled/default

# Verificar configuración
nginx -t

# Reiniciar Nginx
systemctl restart nginx
```

---

## 🔒 Paso 9: Configurar SSL con Let's Encrypt (HTTPS)

```bash
# Instalar Certbot
apt install certbot python3-certbot-nginx -y

# Obtener certificado SSL
certbot --nginx -d tudominio.com -d www.tudominio.com

# Verificar renovación automática
certbot renew --dry-run
```

---

## 🔄 Paso 10: Configurar Tareas Programadas (Cron)

```bash
# Editar crontab
crontab -e
```

Agregar esta línea:
```
* * * * * cd /var/www/ssr && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔥 Paso 11: Configurar Firewall (Opcional pero Recomendado)

```bash
# Instalar UFW
apt install ufw -y

# Configurar reglas
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw enable

# Verificar estado
ufw status
```

---

## ✅ Paso 12: Verificar Instalación

Abre tu navegador y visita:
- `http://tudominio.com` o `http://tu-ip-del-vps`
- Deberías ver la landing page del Sistema APR

---

## 📊 Paso 13: Crear Usuario Administrador

```bash
cd /var/www/ssr
php artisan tinker
```

Dentro de tinker:
```php
$user = new App\Models\User();
$user->nombre = 'Administrador';
$user->email = 'admin@ejemplo.com';
$user->password = bcrypt('tu-contraseña-segura');
$user->rol = 'admin';
$user->save();
exit
```

---

## 🔧 Comandos Útiles de Mantenimiento

### Ver logs de errores:
```bash
tail -f /var/www/ssr/storage/logs/laravel.log
```

### Limpiar caché:
```bash
cd /var/www/ssr
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Actualizar aplicación:
```bash
cd /var/www/ssr
git pull origin main
composer install --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
systemctl restart nginx
```

### Reiniciar servicios:
```bash
systemctl restart nginx
systemctl restart php8.2-fpm
systemctl restart mysql
```

---

## 🚨 Solución de Problemas Comunes

### Error 500 - Internal Server Error
```bash
# Verificar logs
tail -f /var/www/ssr/storage/logs/laravel.log
tail -f /var/log/nginx/error.log

# Verificar permisos
chmod -R 775 /var/www/ssr/storage
chmod -R 775 /var/www/ssr/bootstrap/cache
chown -R www-data:www-data /var/www/ssr
```

### Error de conexión a base de datos
```bash
# Verificar servicio MySQL
systemctl status mysql

# Verificar credenciales en .env
nano /var/www/ssr/.env

# Limpiar caché de configuración
php artisan config:clear
```

### Error 403 - Forbidden
```bash
# Verificar que el root apunta a /public
# Verificar permisos del directorio
ls -la /var/www/ssr/public
```

---

## 📝 Notas Importantes

1. **Seguridad**: Cambia todas las contraseñas por defecto
2. **Backups**: Configura backups automáticos de la base de datos
3. **Monitoreo**: Considera usar herramientas como `htop`, `glances` para monitorear recursos
4. **Logs**: Revisa logs regularmente en `/var/www/ssr/storage/logs/`
5. **Updates**: Mantén el sistema y dependencias actualizadas

---

## 🎯 Checklist de Despliegue

- [ ] VPS actualizado
- [ ] Nginx instalado y configurado
- [ ] PHP 8.2 instalado con todas las extensiones
- [ ] MySQL instalado y base de datos creada
- [ ] Composer instalado
- [ ] Código de la aplicación subido
- [ ] .env configurado correctamente
- [ ] Migraciones ejecutadas
- [ ] Permisos configurados
- [ ] Nginx configurado con el sitio
- [ ] SSL/HTTPS configurado
- [ ] Cron configurado
- [ ] Firewall configurado
- [ ] Usuario administrador creado
- [ ] Aplicación funcionando correctamente

---

## 📞 Recursos Adicionales

- [Documentación Laravel Deployment](https://laravel.com/docs/deployment)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [Let's Encrypt](https://letsencrypt.org/)
- [Ubuntu Server Guide](https://ubuntu.com/server/docs)
