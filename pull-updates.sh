#!/bin/bash

# Daar Al Quran - Pull Updates Script for Namecheap
# This script pulls the latest code from GitHub and updates your Namecheap hosting

echo "🔄 Pulling latest updates from GitHub..."

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

# Pull latest changes from GitHub
print_status "Pulling latest changes from GitHub..."
git pull origin main

if [ $? -ne 0 ]; then
    print_error "Failed to pull from GitHub. Please check your connection and try again."
    exit 1
fi

print_success "Successfully pulled latest changes"

# Check if we need to update the deployment
print_status "Preparing updated deployment files..."

# Remove old deployment if exists
if [ -d "namecheap-deployment" ]; then
    rm -rf namecheap-deployment
    print_status "Removed old deployment files"
fi

# Run the deployment preparation script
./deploy-namecheap.sh

if [ $? -ne 0 ]; then
    print_error "Deployment preparation failed. Please check the deploy-namecheap.sh script."
    exit 1
fi

print_success "Deployment files prepared successfully"

echo ""
echo "📋 Next Steps:"
echo "1. Upload the contents of 'namecheap-deployment/app/' to your hosting 'app/' directory"
echo "2. Upload the contents of 'namecheap-deployment/public_html/' to your hosting 'public_html/' directory"
echo "3. If you have SSH access, run the following commands on your server:"
echo "   cd app/"
echo "   composer install --optimize-autoloader --no-dev"
echo "   php artisan migrate --force"
echo "   php artisan config:cache"
echo "   php artisan route:cache"
echo "   php artisan view:cache"
echo ""
echo "🎉 Update preparation completed!" 