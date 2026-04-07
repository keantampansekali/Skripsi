#!/bin/bash

# Fix Real-Time Updates on Production Server
# Place at: /var/www/html/skripsi/fix-realtime-production.sh
# Run: ./fix-realtime-production.sh

set -e

echo "=========================================="
echo "Fixing Real-Time Updates on Production"
echo "=========================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

APP_DIR="/var/www/html/skripsi"
SERVER_IP="72.60.78.65"

# Check if we're in the right directory
cd $APP_DIR || exit 1

echo -e "${BLUE}Step 1: Backing up current .env...${NC}"
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
echo -e "${GREEN}✅ Backup created${NC}"
echo ""

echo -e "${BLUE}Step 2: Checking current configuration...${NC}"
echo "Current REVERB_HOST:"
grep "REVERB_HOST" .env || echo "Not found"
echo ""

echo -e "${BLUE}Step 3: Updating .env with production values...${NC}"

# Update REVERB_HOST
if grep -q "^REVERB_HOST=" .env; then
    sed -i "s/^REVERB_HOST=.*/REVERB_HOST=$SERVER_IP/" .env
    echo "Updated REVERB_HOST"
else
    echo "REVERB_HOST not found in .env"
fi

# Update VITE_REVERB_HOST
if grep -q "^VITE_REVERB_HOST=" .env; then
    sed -i "s/^VITE_REVERB_HOST=.*/VITE_REVERB_HOST=\"$SERVER_IP\"/" .env
    echo "Updated VITE_REVERB_HOST"
else
    # Add if missing
    echo "VITE_REVERB_HOST=\"$SERVER_IP\"" >> .env
    echo "Added VITE_REVERB_HOST"
fi

echo ""
echo "New configuration:"
grep "REVERB_HOST" .env
echo -e "${GREEN}✅ Configuration updated${NC}"
echo ""

echo -e "${BLUE}Step 4: Clearing cache...${NC}"
php artisan config:clear
php artisan cache:clear
echo -e "${GREEN}✅ Cache cleared${NC}"
echo ""

echo -e "${BLUE}Step 5: Rebuilding frontend assets...${NC}"
echo "(This may take 30-60 seconds...)"
npm run build
echo -e "${GREEN}✅ Assets rebuilt${NC}"
echo ""

echo -e "${BLUE}Step 6: Restarting services...${NC}"

# Restart Reverb
if systemctl is-active --quiet reverb; then
    sudo systemctl restart reverb
    echo -e "${GREEN}✅ Reverb restarted${NC}"
else
    echo -e "${YELLOW}⚠️  Reverb service not found${NC}"
    echo "You may need to set up Reverb as a systemd service"
fi

# Reload web server
if systemctl is-active --quiet nginx; then
    sudo systemctl reload nginx
    echo -e "${GREEN}✅ Nginx reloaded${NC}"
elif systemctl is-active --quiet apache2; then
    sudo systemctl reload apache2
    echo -e "${GREEN}✅ Apache reloaded${NC}"
fi

echo ""
echo "=========================================="
echo -e "${GREEN}✅ Fix Complete!${NC}"
echo "=========================================="
echo ""

echo -e "${BLUE}Service Status:${NC}"
echo ""

if systemctl is-active --quiet reverb; then
    echo -e "${GREEN}✅ Reverb: Running${NC}"
    systemctl status reverb --no-pager | head -3
else
    echo -e "${RED}❌ Reverb: Not Running${NC}"
fi

echo ""
echo -e "${BLUE}Testing Configuration:${NC}"
echo ""

# Check if port 8080 is listening
if netstat -tulpn 2>/dev/null | grep -q ":8080"; then
    echo -e "${GREEN}✅ Port 8080: Open${NC}"
else
    echo -e "${YELLOW}⚠️  Port 8080: Not listening${NC}"
fi

echo ""
echo -e "${BLUE}Next Steps:${NC}"
echo "1. Visit: http://$SERVER_IP"
echo "2. Open browser console (F12)"
echo "3. Look for: '✅ Connected to Reverb'"
echo "4. Check host shows: $SERVER_IP (not localhost)"
echo ""
echo "To test real-time:"
echo "- Open 2 browser windows"
echo "- Edit a product in window 1"
echo "- Watch it update in window 2"
echo ""

echo -e "${BLUE}View Logs:${NC}"
echo "Application: tail -f storage/logs/laravel.log"
echo "Reverb: journalctl -u reverb -f"
echo ""

