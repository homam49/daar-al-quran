# Daar Al Quran Production Deployment Guide

This guide outlines the steps required to deploy the Daar Al Quran application on a production server.

## Server Requirements

- Ubuntu 20.04 LTS or higher
- Nginx web server
- PHP 8.0+ with the following extensions:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Git

## Step 1: Server Setup

### Install required packages

```bash
sudo apt update
sudo apt install nginx mysql-server php8.1-fpm php8.1-cli php8.1-common php8.1-mysql php8.1-zip php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml php8.1-bcmath php8.1-intl php8.1-readline php8.1-ldap php8.1-msgpack php8.1-igbinary php8.1-redis composer git
```

### Configure MySQL

```bash
sudo mysql_secure_installation
```

Create a database and user for the application:

```bash
sudo mysql
```

```sql
CREATE DATABASE daar_al_quran;
CREATE USER 'daar_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON daar_al_quran.* TO 'daar_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Step 2: Configure Nginx

Copy the provided Nginx configuration file:

```bash
sudo cp /path/to/daaralquran.conf /etc/nginx/sites-available/
sudo ln -s /etc/nginx/sites-available/daaralquran.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Step 3: Setup SSL with Let's Encrypt

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d daaralquran.com -d www.daaralquran.com
```

## Step 4: Application Setup

### Clone the Repository

```bash
mkdir -p /var/www/
cd /var/www/
git clone https://github.com/homam49/daar-al-quran.git daaralquran
cd daaralquran
```

### Configure Environment

Copy the .env.example file:

```bash
cp .env.example .env
nano .env
```

Update the following values:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://daaralquran.com`
- Database credentials
- Mail settings

### Set File Permissions

```bash
sudo chown -R www-data:www-data /var/www/daaralquran
sudo chmod -R 775 storage bootstrap/cache
```

### Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### Generate App Key (if needed)

```bash
php artisan key:generate
```

### Run Migrations

```bash
php artisan migrate --force
```

### Optimize the Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## Step 5: Set Up Cron Jobs for Laravel Scheduler

```bash
sudo crontab -e
```

Add the following line:

```
* * * * * cd /var/www/daaralquran && php artisan schedule:run >> /dev/null 2>&1
```

## Step 6: Configure PHP Settings

Edit PHP configuration for better performance:

```bash
sudo nano /etc/php/8.1/fpm/php.ini
```

Make these changes:
- `memory_limit = 256M`
- `upload_max_filesize = 32M`
- `post_max_size = 32M`
- `max_execution_time = 60`
- `opcache.enable=1`
- `opcache.memory_consumption=128`
- `opcache.interned_strings_buffer=8`
- `opcache.max_accelerated_files=4000`
- `opcache.revalidate_freq=60`
- `opcache.fast_shutdown=1`

Restart PHP-FPM:

```bash
sudo systemctl restart php8.1-fpm
```

## Step 7: Future Deployments

Use the provided deployment script for future updates:

```bash
chmod +x deploy.sh
./deploy.sh
```

## Maintenance Tasks

### Database Backups

Set up regular database backups:

```bash
sudo crontab -e
```

Add:

```
0 2 * * * mysqldump -u daar_user -p'secure_password_here' daar_al_quran | gzip > /var/backups/daar_al_quran_$(date +\%Y-\%m-\%d).sql.gz
```

### Log Rotation

Ensure log rotation is configured:

```bash
sudo nano /etc/logrotate.d/daar-al-quran
```

Add:

```
/var/www/daaralquran/storage/logs/*.log {
    daily
    missingok
    rotate 7
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
```

## Troubleshooting

### Check Application Logs

```bash
tail -f /var/www/daaralquran/storage/logs/laravel.log
```

### Check Nginx Logs

```bash
tail -f /var/log/nginx/daaralquran.error.log
```

### Check PHP-FPM Logs

```bash
tail -f /var/log/php8.1-fpm.log
```

### Common Issues

1. **Permission Issues**: Ensure proper ownership and permissions:
   ```bash
   sudo chown -R www-data:www-data /var/www/daaralquran
   sudo chmod -R 775 storage bootstrap/cache
   ```

2. **URL Issues**: Make sure APP_URL in .env is correctly set to match your domain.

3. **Database Connection Issues**: Verify database credentials in .env file. 