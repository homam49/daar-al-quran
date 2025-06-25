@echo off
:: Daar Al Quran - Pull Updates Script for Namecheap (Windows)
:: This script pulls the latest code from GitHub and updates your Namecheap hosting

echo 🔄 Pulling latest updates from GitHub...

:: Check if we're in the right directory
if not exist "daar-al-quran" (
    echo [ERROR] daar-al-quran directory not found. Please run this script from the project root.
    pause
    exit /b 1
)

:: Pull latest changes from GitHub
echo [INFO] Pulling latest changes from GitHub...
git pull origin main

if %ERRORLEVEL% neq 0 (
    echo [ERROR] Failed to pull from GitHub. Please check your connection and try again.
    pause
    exit /b 1
)

echo [SUCCESS] Successfully pulled latest changes

:: Check if we need to update the deployment
echo [INFO] Preparing updated deployment files...

:: Remove old deployment if exists
if exist "namecheap-deployment" (
    rmdir /s /q namecheap-deployment
    echo [INFO] Removed old deployment files
)

:: Run the deployment preparation script
call deploy-namecheap.bat

if %ERRORLEVEL% neq 0 (
    echo [ERROR] Deployment preparation failed. Please check the deploy-namecheap.bat script.
    pause
    exit /b 1
)

echo [SUCCESS] Deployment files prepared successfully
echo.
echo 📋 Next Steps:
echo 1. Upload the contents of 'namecheap-deployment/app/' to your hosting 'app/' directory
echo 2. Upload the contents of 'namecheap-deployment/public_html/' to your hosting 'public_html/' directory
echo 3. If you have SSH access, run the following commands on your server:
echo    cd app/
echo    composer install --optimize-autoloader --no-dev
echo    php artisan migrate --force
echo    php artisan config:cache
echo    php artisan route:cache
echo    php artisan view:cache
echo.
echo 🎉 Update preparation completed!
pause 