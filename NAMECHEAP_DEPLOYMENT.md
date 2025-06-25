# Namecheap Shared Hosting Deployment Guide

This guide explains how to deploy the Daar Al Quran Laravel application on Namecheap shared hosting.

## Overview

Namecheap shared hosting requires a specific file structure:
- `public_html/` - Web accessible folder (contains Laravel's public folder contents)
- `app/` - Private folder above web root (contains Laravel application files)

## Prerequisites

- Namecheap shared hosting account with PHP 8.0+ support
- SSH access enabled (if available)
- Database created in cPanel
- Domain pointed to your hosting account

## Step 1: Prepare Local Files

### Create Deployment Structure

The repository is already structured for easy deployment:

```
/
├── daar-al-quran/          # Main Laravel application
├── public_html/            # Will be created for web root
├── deploy-namecheap.sh     # Will be created for deployment
└── README.md
```

## Step 2: Database Setup

1. Login to your Namecheap cPanel
2. Go to "MySQL Databases"
3. Create a new database (e.g., `username_daar_al_quran`)
4. Create a database user and assign it to the database
5. Note down the database credentials

## Step 3: Upload Files

### Option A: Using File Manager (Recommended for beginners)

1. Login to cPanel → File Manager
2. Navigate to your domain's root directory
3. Create folder structure:
   ```
   /public_html/          # Your domain's web root
   /app/                  # Above web root (private)
   ```

4. Upload the Laravel application:
   - Upload entire `daar-al-quran/` folder contents to `/app/`
   - Upload `daar-al-quran/public/` folder contents to `/public_html/`

### Option B: Using Git (If SSH is available)

```bash
# Clone repository
git clone https://github.com/homam49/daar-al-quran.git
cd daar-al-quran

# Run the deployment script
./deploy-namecheap.sh
```

## Step 4: Configure Environment

1. Copy `daar-al-quran/.env.example` to `/app/.env`
2. Edit `/app/.env` with your production settings:

```env
APP_NAME="Daar Al Quran"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomainname.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomainname.com
MAIL_PORT=587
MAIL_USERNAME=your_email@yourdomainname.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@yourdomainname.com
MAIL_FROM_NAME="Daar Al Quran"
```

## Step 5: Modify public_html/index.php

Update `/public_html/index.php` to point to the correct application path:

```php
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Autoloader
require_once '../app/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once '../app/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
```

## Step 6: Set File Permissions

Using File Manager or SSH:

```bash
chmod -R 755 /app/storage
chmod -R 755 /app/bootstrap/cache
chmod 644 /app/.env
```

## Step 7: Install Dependencies & Run Migrations

### If SSH is available:

```bash
cd /app
composer install --optimize-autoloader --no-dev
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### If SSH is not available:

1. Upload a pre-built `vendor/` folder from your local environment
2. Use a web-based migration tool or contact support

## Step 8: Configure Web Server

### Create .htaccess in public_html/

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## Step 9: SSL Configuration

1. Go to cPanel → SSL/TLS
2. Enable "Force HTTPS Redirect"
3. Install SSL certificate (Let's Encrypt is usually available)

## Step 10: Testing

1. Visit your website: `https://yourdomainname.com`
2. Test user registration and login
3. Verify all functionality works correctly

## Troubleshooting

### Common Issues:

1. **500 Internal Server Error**
   - Check file permissions
   - Verify .env configuration
   - Check error logs in cPanel

2. **Database Connection Issues**
   - Verify database credentials in .env
   - Ensure database user has proper permissions
   - Check database host (sometimes it's not 'localhost')

3. **Missing CSS/JS Files**
   - Ensure all public folder contents are in public_html
   - Check file permissions
   - Clear browser cache

4. **CSRF Token Mismatch**
   - Check APP_URL in .env matches your domain
   - Clear application cache

## Maintenance

### For Future Updates:

1. Pull latest changes from Git
2. Upload changed files
3. Run migrations if needed:
   ```bash
   php artisan migrate --force
   ```
4. Clear caches:
   ```bash
   php artisan config:clear
   php artisan view:clear
   php artisan route:clear
   ```

### Backup Strategy:

1. Regular database backups via cPanel
2. File backups of the `/app/` directory
3. Keep local copy of the codebase

## Support

- Check Namecheap documentation for PHP version and extension support
- Contact Namecheap support for server-specific issues
- Refer to Laravel documentation for framework-related questions 