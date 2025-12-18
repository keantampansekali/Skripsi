#!/bin/bash

# Deploy script for production server
# Place this file at: /var/www/html/skripsi/deploy.sh
# Make executable: chmod +x deploy.sh

set -e  # Exit on any error

echo "=========================================="
echo "🚀 Starting Deployment"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
APP_DIR="/var/www/html/skripsi"
PHP_FPM_SERVICE="php8.2-fpm"  # Adjust to your PHP version
WEB_SERVER="nginx"  # Change to 'apache2' if using Apache

# Change to application directory
cd $APP_DIR || exit 1

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: artisan file not found. Are we in the Laravel directory?${NC}"
    exit 1
fi

# Enable maintenance mode
echo -e "${BLUE}🔒 Enabling maintenance mode...${NC}"
php artisan down || true

# Pull latest changes from Git
echo -e "${BLUE}📥 Pulling latest code from repository...${NC}"
git fetch --all
git reset --hard origin/main
echo -e "${GREEN}✅ Code updated${NC}"

# Install/Update Composer dependencies
echo -e "${BLUE}📦 Installing PHP dependencies...${NC}"
composer install --optimize-autoloader --no-dev --no-interaction
echo -e "${GREEN}✅ Composer dependencies installed${NC}"

# Install/Update NPM dependencies
echo -e "${BLUE}📦 Installing Node dependencies...${NC}"
npm ci --production=false
echo -e "${GREEN}✅ Node dependencies installed${NC}"

# Build frontend assets
echo -e "${BLUE}🏗️ Building frontend assets...${NC}"
npm run build
echo -e "${GREEN}✅ Assets built${NC}"

# Run database migrations
echo -e "${BLUE}🗃️ Running database migrations...${NC}"
php artisan migrate --force
echo -e "${GREEN}✅ Migrations completed${NC}"

# Clear all caches
echo -e "${BLUE}🧹 Clearing caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo -e "${GREEN}✅ Caches cleared${NC}"

# Optimize for production
echo -e "${BLUE}⚡ Optimizing application...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
composer dump-autoload --optimize
echo -e "${GREEN}✅ Application optimized${NC}"

# Set correct permissions
echo -e "${BLUE}🔐 Setting file permissions...${NC}"
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache public/uploads
echo -e "${GREEN}✅ Permissions set${NC}"

# Restart services
echo -e "${BLUE}♻️ Restarting services...${NC}"

# Restart Reverb (WebSocket)
if systemctl is-active --quiet reverb; then
    echo "  - Restarting Reverb..."
    systemctl restart reverb
    echo -e "${GREEN}    ✅ Reverb restarted${NC}"
else
    echo -e "${YELLOW}    ⚠️ Reverb service not running${NC}"
fi

# Restart Queue Worker
if systemctl is-active --quiet laravel-queue; then
    echo "  - Restarting Queue Worker..."
    systemctl restart laravel-queue
    echo -e "${GREEN}    ✅ Queue worker restarted${NC}"
else
    echo -e "${YELLOW}    ⚠️ Queue worker service not running${NC}"
fi

# Reload PHP-FPM
if systemctl is-active --quiet $PHP_FPM_SERVICE; then
    echo "  - Reloading PHP-FPM..."
    systemctl reload $PHP_FPM_SERVICE
    echo -e "${GREEN}    ✅ PHP-FPM reloaded${NC}"
else
    echo -e "${YELLOW}    ⚠️ PHP-FPM service not running${NC}"
fi

# Reload Web Server
if systemctl is-active --quiet $WEB_SERVER; then
    echo "  - Reloading $WEB_SERVER..."
    systemctl reload $WEB_SERVER
    echo -e "${GREEN}    ✅ $WEB_SERVER reloaded${NC}"
else
    echo -e "${YELLOW}    ⚠️ $WEB_SERVER service not running${NC}"
fi

# Disable maintenance mode
echo -e "${BLUE}🔓 Disabling maintenance mode...${NC}"
php artisan up
echo -e "${GREEN}✅ Application is now live${NC}"

# Show application status
echo ""
echo "=========================================="
echo -e "${GREEN}✅ Deployment Complete!${NC}"
echo "=========================================="
echo ""
echo "Application Status:"
echo "  - Web: http://72.60.78.65"
echo "  - Reverb: $(systemctl is-active reverb 2>/dev/null || echo 'not configured')"
echo "  - Queue: $(systemctl is-active laravel-queue 2>/dev/null || echo 'not configured')"
echo ""
echo "Deployed at: $(date)"
echo "Git commit: $(git rev-parse --short HEAD)"
echo "Branch: $(git rev-parse --abbrev-ref HEAD)"
echo ""
echo "Monitor logs:"
echo "  tail -f $APP_DIR/storage/logs/laravel.log"
echo ""

