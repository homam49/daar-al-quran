#!/bin/bash
# Deployment script for Daar Al Quran application

echo "Starting deployment process for Daar Al Quran..."
echo "------------------------------------------------"

# Step 1: Update code from repository
echo "Pulling latest code from repository..."
git pull origin main

# Step 2: Install PHP dependencies
echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Step 3: Set appropriate permissions
echo "Setting file permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data .

# Step 4: Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Step 5: Clear and rebuild caches
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Step 6: Restart queue workers if using them
# echo "Restarting queue workers..."
# php artisan queue:restart

# Step 7: Check if we need to reload PHP-FPM
echo "Checking if PHP-FPM needs reloading..."
if command -v systemctl &> /dev/null; then
    echo "Reloading PHP-FPM..."
    sudo systemctl reload php8.1-fpm
fi

echo "------------------------------------------------"
echo "Deployment completed successfully!"
echo "Don't forget to check the application after deployment." 