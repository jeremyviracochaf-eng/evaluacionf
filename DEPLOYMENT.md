# 🚀 Guía de Deployment

## Para Hosting/Servidor

### Opción 1: Hostinger, Bluehost, SiteGround

```bash
# 1. Subir archivos (excepto .env y vendor/)
# Usar SFTP/FTP

# 2. SSH al servidor
ssh usuario@tudominio.com

# 3. Instalar dependencias
cd public_html/atracciones
composer install --no-dev --optimize-autoloader

# 4. Configurar .env
nano .env
# Cambiar:
# APP_ENV=production
# APP_DEBUG=false
# DB_HOST, DB_USERNAME, DB_PASSWORD (credenciales del hosting)

# 5. Generar clave
php artisan key:generate

# 6. Ejecutar migraciones
php artisan migrate --force

# 7. Optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Opción 2: Heroku

```bash
# Instalar Heroku CLI
# https://devcenter.heroku.com/articles/heroku-cli

# 1. Login
heroku login

# 2. Crear app
heroku create mi-atracciones-app

# 3. Configurar BD
heroku addons:create cleardb:ignite

# 4. Variables de entorno
heroku config:set APP_KEY=$(php artisan key:generate --show)
heroku config:set APP_ENV=production
heroku config:set APP_DEBUG=false

# 5. Deploy
git push heroku main
```

### Opción 3: AWS (EC2 + RDS)

```bash
# 1. Conectar a instancia EC2
ssh -i tu-key.pem ubuntu@tu-ip

# 2. Instalar dependencias
sudo apt update
sudo apt install php-cli php-mysql composer nginx

# 3. Clonar repositorio
git clone tu-repo
cd atracciones_turisticasp

# 4. Configurar
composer install --no-dev
cp .env.example .env
# Editar .env con credenciales RDS

# 5. Migraciones
php artisan migrate --force

# 6. Configurar Nginx
sudo nano /etc/nginx/sites-available/default
# Ver documentación Nginx + Laravel

# 7. Reiniciar
sudo systemctl restart nginx
```

---

## Requisitos Mínimos

| Requisito | Especificación |
|-----------|---|
| PHP | 8.4+ |
| MySQL | 8.0+ |
| Almacenamiento | 500MB mínimo |
| Memoria RAM | 256MB mínimo |
| Banda ancha | 10GB/mes |

---

## Pre-Deploy Checklist

- [ ] `.env` configurado sin credenciales sensibles
- [ ] `.env.example` actualizado
- [ ] Database credentials en `.env`
- [ ] Google Places API Key configurada
- [ ] Firebase Storage configurada (si aplica)
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] CORS configurado correctamente
- [ ] Migraciones ejecutadas
- [ ] Assets compilados (si aplica)
- [ ] Logs configurados a archivo

---

## Post-Deploy

```bash
# Verificar salud
php artisan health

# Ver logs
tail -f storage/logs/laravel.log

# Backup de BD
mysqldump -u user -p db > backup.sql

# Monitoreo
# Configurar alertas en hosting panel
```

---

## Solución de Problemas

### "SQLSTATE[HY000]: General error"
- Verificar credenciales de BD
- Ejecutar: `php artisan migrate:refresh`

### "500 Internal Server Error"
- Revisar: `storage/logs/laravel.log`
- Verificar permisos: `chmod -R 775 storage bootstrap/cache`

### "TokenMismatchException"
- Limpiar cookies del navegador
- Ejecutar: `php artisan session:clear`

### APIs lentas
- Habilitar Redis/Memcached
- Configurar caching de rutas
- Optimizar índices BD

