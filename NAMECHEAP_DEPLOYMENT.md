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

## Pulling Updates from GitHub Repository

### Option 1: Git via SSH (Recommended)

If your Namecheap plan includes SSH access:

```bash
# Connect to your hosting account via SSH
ssh username@yourdomainname.com

# Navigate to your application directory
cd app/

# Pull latest changes from GitHub
git pull origin main

# Update dependencies and cache
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache

# If there are new migrations
php artisan migrate --force

# Update public files if needed
cp -r public/* ../public_html/
```

### Option 2: Automated Deployment Script (via SSH)

Create a deployment script on your server:

```bash
# Create deployment script
nano deploy.sh

# Add the following content:
#!/bin/bash
echo "Starting deployment..."

# Backup current version
cp -r app app_backup_$(date +%Y%m%d_%H%M%S)

# Pull latest changes
cd app
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear and cache config
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Update public files
cp -r public/* ../public_html/

echo "Deployment completed successfully!"

# Make it executable
chmod +x deploy.sh

# Run deployment
./deploy.sh
```

### Option 3: GitHub Webhooks (Advanced)

Set up automatic deployments triggered by GitHub pushes:

1. Create a webhook endpoint in your Laravel app:

```php
// In routes/web.php
Route::post('/deploy-webhook', function (Request $request) {
    // Verify GitHub signature for security
    $signature = $request->header('X-Hub-Signature-256');
    $payload = $request->getContent();
    $secret = env('GITHUB_WEBHOOK_SECRET');
    
    $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    
    if (!hash_equals($signature, $expectedSignature)) {
        return response('Unauthorized', 401);
    }
    
    // Run deployment commands
    exec('cd /path/to/your/app && git pull origin main 2>&1', $output);
    exec('cd /path/to/your/app && composer install --optimize-autoloader --no-dev 2>&1', $output);
    exec('cd /path/to/your/app && php artisan migrate --force 2>&1', $output);
    exec('cd /path/to/your/app && php artisan config:cache 2>&1', $output);
    
    return response('Deployment triggered', 200);
});
```

2. Add webhook URL in GitHub repository settings.

### Option 4: Manual File Manager Method

If SSH is not available:

1. **Download latest code locally:**
   ```bash
   git pull origin main
   ./deploy-namecheap.sh  # Prepare files for upload
   ```

2. **Upload via cPanel File Manager:**
   - Login to cPanel → File Manager
   - Upload and extract the prepared files from `namecheap-deployment/`
   - Overwrite existing files

3. **Clear caches via web interface:**
   Create a simple web-accessible script:
   
   ```php
   // Create public_html/deploy.php (remove after use!)
   <?php
   if (isset($_GET['secret']) && $_GET['secret'] === 'your-secret-key') {
       exec('cd ../app && php artisan config:cache 2>&1', $output1);
       exec('cd ../app && php artisan route:cache 2>&1', $output2);
       exec('cd ../app && php artisan view:cache 2>&1', $output3);
       
       echo "Caches cleared successfully!<br>";
       echo "Config: " . implode('<br>', $output1) . "<br>";
       echo "Routes: " . implode('<br>', $output2) . "<br>";
       echo "Views: " . implode('<br>', $output3) . "<br>";
   } else {
       echo "Access denied!";
   }
   ?>
   ```
   
   Visit: `https://yourdomainname.com/deploy.php?secret=your-secret-key`

### Option 5: FTP/SFTP Automation

Use FTP clients with synchronization features:

```bash
# Using lftp for automated sync
lftp -c "
open sftp://username:password@yourdomainname.com
mirror --reverse --delete --verbose /local/path/to/project/namecheap-deployment/app /app
mirror --reverse --delete --verbose /local/path/to/project/namecheap-deployment/public_html /public_html
quit
"
```

## Continuous Integration Setup

For automated deployments, create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Namecheap

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        
    - name: Install dependencies
      run: composer install --optimize-autoloader --no-dev
      
    - name: Prepare deployment files
      run: ./deploy-namecheap.sh
      
    - name: Deploy via SFTP
      uses: SamKirkland/FTP-Deploy-Action@4.0.0
      with:
        server: ${{ secrets.FTP_SERVER }}
        username: ${{ secrets.FTP_USERNAME }}
        password: ${{ secrets.FTP_PASSWORD }}
        local-dir: ./namecheap-deployment/
        server-dir: /
```

## Recommended Workflow

1. **Initial Setup:** Use the deployment script to set up your hosting environment
2. **Regular Updates:** Use SSH + Git for quick updates
3. **Emergency Updates:** Use File Manager method as backup
4. **Automated Deployments:** Set up CI/CD for hands-off deployments

## Security Considerations

- Never commit `.env` files to Git
- Use environment variables for sensitive data
- Regularly update dependencies
- Monitor server logs for security issues
- Use HTTPS for all communications

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