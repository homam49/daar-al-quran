#!/bin/bash

# Daar Al Quran - Namecheap Deployment Script
# This script prepares the application for Namecheap shared hosting deployment

echo "🚀 Starting Namecheap deployment preparation..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the right directory
if [ ! -d "daar-al-quran" ]; then
    print_error "daar-al-quran directory not found. Please run this script from the project root."
    exit 1
fi

# Create deployment structure
print_status "Creating Namecheap deployment structure..."

# Create directories
mkdir -p namecheap-deployment/app
mkdir -p namecheap-deployment/public_html

print_success "Created deployment directories"

# Copy Laravel application to app directory (excluding public folder)
print_status "Copying Laravel application files..."

# Copy all files except public directory
rsync -av --exclude='public' --exclude='node_modules' --exclude='.git' daar-al-quran/ namecheap-deployment/app/

print_success "Copied application files to app directory"

# Copy public directory contents to public_html
print_status "Copying public files to public_html..."

cp -r daar-al-quran/public/* namecheap-deployment/public_html/

print_success "Copied public files to public_html"

# Create modified index.php for Namecheap structure
print_status "Creating modified index.php for shared hosting..."

cat > namecheap-deployment/public_html/index.php << 'EOF'
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is maintenance / demo mode via the "down" command we
| will require this file so that any prerendered template can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../app/storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../app/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../app/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
EOF

print_success "Created modified index.php"

# Create .htaccess for public_html
print_status "Creating .htaccess file..."

cat > namecheap-deployment/public_html/.htaccess << 'EOF'
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

# Prevent access to sensitive files
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.json">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.lock">
    Order allow,deny
    Deny from all
</Files>

<Files "package.json">
    Order allow,deny
    Deny from all
</Files>
EOF

print_success "Created .htaccess file"

# Copy environment example
print_status "Preparing environment configuration..."

cp namecheap-deployment/app/.env.example namecheap-deployment/app/.env.production

print_success "Created .env.production template"

# Create deployment instructions
print_status "Creating deployment instructions..."

cat > namecheap-deployment/DEPLOYMENT_INSTRUCTIONS.txt << 'EOF'
NAMECHEAP DEPLOYMENT INSTRUCTIONS
=================================

1. UPLOAD FILES:
   - Upload the contents of 'app/' folder to your hosting account's 'app/' directory (create this directory above public_html)
   - Upload the contents of 'public_html/' folder to your hosting account's 'public_html/' directory

2. DATABASE SETUP:
   - Create a MySQL database in cPanel
   - Note the database name, username, and password

3. CONFIGURE ENVIRONMENT:
   - Rename 'app/.env.production' to 'app/.env'
   - Edit 'app/.env' with your production settings:
     * Set APP_URL to your domain
     * Configure database credentials
     * Set APP_ENV=production
     * Set APP_DEBUG=false
     * Configure mail settings

4. INSTALL DEPENDENCIES (if SSH available):
   cd app
   composer install --optimize-autoloader --no-dev
   php artisan key:generate
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache

5. SET FILE PERMISSIONS:
   chmod -R 755 app/storage
   chmod -R 755 app/bootstrap/cache

6. TEST YOUR WEBSITE:
   Visit your domain to verify everything works

For detailed instructions, see NAMECHEAP_DEPLOYMENT.md
EOF

print_success "Created deployment instructions"

# Create ZIP file for easy upload
print_status "Creating ZIP file for easy upload..."

cd namecheap-deployment
zip -r ../daar-al-quran-namecheap.zip .
cd ..

print_success "Created daar-al-quran-namecheap.zip"

# Final instructions
echo ""
echo "🎉 Deployment preparation complete!"
echo ""
echo "📦 Files ready for Namecheap deployment:"
echo "   📁 namecheap-deployment/app/        → Upload to /app/ on your hosting"
echo "   📁 namecheap-deployment/public_html/ → Upload to /public_html/ on your hosting"
echo "   📦 daar-al-quran-namecheap.zip      → ZIP file for easy upload"
echo ""
echo "📋 Next steps:"
echo "   1. Upload the files to your Namecheap hosting account"
echo "   2. Create a MySQL database in cPanel"
echo "   3. Configure the .env file with your production settings"
echo "   4. Run composer install and Laravel commands (if SSH available)"
echo ""
echo "📖 For detailed instructions, see NAMECHEAP_DEPLOYMENT.md"
echo ""
print_success "Ready for deployment! 🚀" 