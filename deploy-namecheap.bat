@echo off
setlocal enabledelayedexpansion

echo 🚀 Starting Namecheap deployment preparation...

REM Check if we're in the right directory
if not exist "daar-al-quran" (
    echo [ERROR] daar-al-quran directory not found. Please run this script from the project root.
    pause
    exit /b 1
)

echo [INFO] Creating Namecheap deployment structure...

REM Create directories
if not exist "namecheap-deployment" mkdir namecheap-deployment
if not exist "namecheap-deployment\app" mkdir namecheap-deployment\app
if not exist "namecheap-deployment\public_html" mkdir namecheap-deployment\public_html

echo [SUCCESS] Created deployment directories

echo [INFO] Copying Laravel application files...

REM Copy all files except public directory
xcopy "daar-al-quran\*" "namecheap-deployment\app\" /E /H /C /I /Y /EXCLUDE:deploy-exclude.txt

echo [SUCCESS] Copied application files to app directory

echo [INFO] Copying public files to public_html...

REM Copy public directory contents to public_html
xcopy "daar-al-quran\public\*" "namecheap-deployment\public_html\" /E /H /C /I /Y

echo [SUCCESS] Copied public files to public_html

echo [INFO] Creating modified index.php for shared hosting...

REM Create modified index.php
(
echo ^<?php
echo.
echo use Illuminate\Contracts\Http\Kernel;
echo use Illuminate\Http\Request;
echo.
echo define^('LARAVEL_START', microtime^(true^)^);
echo.
echo if ^(file_exists^($maintenance = __DIR__.'/../app/storage/framework/maintenance.php'^)^) {
echo     require $maintenance;
echo }
echo.
echo require __DIR__.'/../app/vendor/autoload.php';
echo.
echo $app = require_once __DIR__.'/../app/bootstrap/app.php';
echo.
echo $kernel = $app-^>make^(Kernel::class^);
echo.
echo $response = $kernel-^>handle^(
echo     $request = Request::capture^(^)
echo ^)-^>send^(^);
echo.
echo $kernel-^>terminate^($request, $response^);
) > "namecheap-deployment\public_html\index.php"

echo [SUCCESS] Created modified index.php

echo [INFO] Creating .htaccess file...

REM Create .htaccess
(
echo ^<IfModule mod_rewrite.c^>
echo     ^<IfModule mod_negotiation.c^>
echo         Options -MultiViews -Indexes
echo     ^</IfModule^>
echo.
echo     RewriteEngine On
echo.
echo     # Handle Authorization Header
echo     RewriteCond %%{HTTP:Authorization} .
echo     RewriteRule .* - [E=HTTP_AUTHORIZATION:%%{HTTP:Authorization}]
echo.
echo     # Redirect Trailing Slashes If Not A Folder...
echo     RewriteCond %%{REQUEST_FILENAME} !-d
echo     RewriteCond %%{REQUEST_URI} ^(.+^)/$
echo     RewriteRule ^ %%1 [L,R=301]
echo.
echo     # Send Requests To Front Controller...
echo     RewriteCond %%{REQUEST_FILENAME} !-d
echo     RewriteCond %%{REQUEST_FILENAME} !-f
echo     RewriteRule ^ index.php [L]
echo ^</IfModule^>
) > "namecheap-deployment\public_html\.htaccess"

echo [SUCCESS] Created .htaccess file

echo [INFO] Preparing environment configuration...

REM Copy environment example
copy "namecheap-deployment\app\.env.example" "namecheap-deployment\app\.env.production" > nul

echo [SUCCESS] Created .env.production template

echo [INFO] Creating deployment instructions...

REM Create deployment instructions
(
echo NAMECHEAP DEPLOYMENT INSTRUCTIONS
echo =================================
echo.
echo 1. UPLOAD FILES:
echo    - Upload the contents of 'app/' folder to your hosting account's 'app/' directory
echo    - Upload the contents of 'public_html/' folder to your hosting account's 'public_html/' directory
echo.
echo 2. DATABASE SETUP:
echo    - Create a MySQL database in cPanel
echo    - Note the database name, username, and password
echo.
echo 3. CONFIGURE ENVIRONMENT:
echo    - Rename 'app/.env.production' to 'app/.env'
echo    - Edit 'app/.env' with your production settings
echo.
echo 4. INSTALL DEPENDENCIES ^(if SSH available^):
echo    cd app
echo    composer install --optimize-autoloader --no-dev
echo    php artisan key:generate
echo    php artisan migrate --force
echo    php artisan config:cache
echo    php artisan route:cache
echo    php artisan view:cache
echo.
echo 5. SET FILE PERMISSIONS:
echo    chmod -R 755 app/storage
echo    chmod -R 755 app/bootstrap/cache
echo.
echo 6. TEST YOUR WEBSITE:
echo    Visit your domain to verify everything works
echo.
echo For detailed instructions, see NAMECHEAP_DEPLOYMENT.md
) > "namecheap-deployment\DEPLOYMENT_INSTRUCTIONS.txt"

echo [SUCCESS] Created deployment instructions

echo [INFO] Creating ZIP file for easy upload...

REM Create exclude file for xcopy
(
echo public
echo node_modules
echo .git
) > deploy-exclude.txt

REM Create ZIP file using PowerShell
powershell -command "Compress-Archive -Path 'namecheap-deployment\*' -DestinationPath 'daar-al-quran-namecheap.zip' -Force"

REM Clean up exclude file
del deploy-exclude.txt

echo [SUCCESS] Created daar-al-quran-namecheap.zip

echo.
echo 🎉 Deployment preparation complete!
echo.
echo 📦 Files ready for Namecheap deployment:
echo    📁 namecheap-deployment\app\        → Upload to /app/ on your hosting
echo    📁 namecheap-deployment\public_html\ → Upload to /public_html/ on your hosting
echo    📦 daar-al-quran-namecheap.zip      → ZIP file for easy upload
echo.
echo 📋 Next steps:
echo    1. Upload the files to your Namecheap hosting account
echo    2. Create a MySQL database in cPanel
echo    3. Configure the .env file with your production settings
echo    4. Run composer install and Laravel commands ^(if SSH available^)
echo.
echo 📖 For detailed instructions, see NAMECHEAP_DEPLOYMENT.md
echo.
echo [SUCCESS] Ready for deployment! 🚀

pause 