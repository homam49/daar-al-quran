# Deployment Checklist for Daar Al Quran

## Pre-Deployment Tasks

- [ ] Review and remove any debug statements
- [ ] Set APP_ENV to "production" in .env
- [ ] Set APP_DEBUG to "false" in .env
- [ ] Generate and set a proper APP_KEY
- [ ] Update APP_URL to match your production domain
- [ ] Configure proper database credentials
- [ ] Configure mail settings for production
- [ ] Optimize autoloader: `composer install --optimize-autoloader --no-dev`
- [ ] Cache configuration: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Cache views: `php artisan view:cache`

## Server Requirements

- PHP 7.4+ with required extensions (BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML)
- Composer
- MySQL 5.7+ or MariaDB 10.2+
- Web server (Apache/Nginx)

## Deployment Steps

1. Clone the repository on your production server
2. Run `composer install --optimize-autoloader --no-dev`
3. Copy `.env.example` to `.env` and update with production settings
4. Run `php artisan key:generate`
5. Run `php artisan migrate`
6. Set proper file permissions:
   ```
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```
7. Configure your web server to point to the `/public` directory
8. Set up SSL certificate for secure HTTPS connections

## Post-Deployment Verification

- [ ] Verify user registration works
- [ ] Verify user login works
- [ ] Test student, teacher, and admin dashboards
- [ ] Verify messaging functionality works
- [ ] Check classroom management features
- [ ] Test all critical application paths

## Monitoring & Maintenance

- Set up regular database backups
- Configure error logging and monitoring
- Implement a deployment strategy for future updates 