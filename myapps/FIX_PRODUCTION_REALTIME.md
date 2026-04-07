# 🔧 Fix Real-Time Updates in Production (72.60.78.65)

## Problem
Real-time stock updates work locally but NOT on production server (72.60.78.65).

---

## 🎯 Root Causes & Solutions

### Issue 1: Wrong Host Configuration (Most Common!)

**Problem**: Your production `.env` still has `localhost` instead of the server IP.

**Fix on Server**:

```bash
# SSH to server
ssh username@72.60.78.65

# Edit .env
cd /var/www/html/skripsi
nano .env

# Change these lines:
REVERB_HOST=localhost          # ❌ WRONG
REVERB_HOST=72.60.78.65        # ✅ CORRECT

VITE_REVERB_HOST="localhost"   # ❌ WRONG
VITE_REVERB_HOST="72.60.78.65" # ✅ CORRECT

# Save and exit (Ctrl+X, Y, Enter)

# Clear cache and rebuild
php artisan config:clear
npm run build

# Restart services
sudo systemctl restart reverb
```

---

### Issue 2: Reverb Not Running on Production

**Check if Reverb is running**:

```bash
# SSH to server
ssh username@72.60.78.65

# Check Reverb service
systemctl status reverb

# If not running, start it
sudo systemctl start reverb

# Check port
netstat -tulpn | grep 8080
```

**If Reverb service doesn't exist**, create it:

```bash
# Create systemd service file
sudo nano /etc/systemd/system/reverb.service
```

Paste this:

```ini
[Unit]
Description=Laravel Reverb WebSocket Server
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/html/skripsi
ExecStart=/usr/bin/php /var/www/html/skripsi/artisan reverb:start
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

Then:

```bash
# Reload systemd
sudo systemctl daemon-reload

# Enable and start
sudo systemctl enable reverb
sudo systemctl start reverb

# Check status
systemctl status reverb
```

---

### Issue 3: Firewall Blocking Port 8080

**Check firewall**:

```bash
# Check if port 8080 is open
sudo ufw status

# If port 8080 is not allowed, add it
sudo ufw allow 8080/tcp

# Or if using firewalld
sudo firewall-cmd --permanent --add-port=8080/tcp
sudo firewall-cmd --reload
```

---

### Issue 4: Assets Not Rebuilt on Production

**Problem**: Frontend assets still have `localhost` hardcoded from local build.

**Fix**:

```bash
# SSH to server
ssh username@72.60.78.65
cd /var/www/html/skripsi

# Rebuild assets on production
npm run build

# Clear cache
php artisan config:cache

# Restart web server
sudo systemctl reload nginx
# OR
sudo systemctl reload apache2
```

---

### Issue 5: Wrong WebSocket URL in Nginx

**If using Nginx**, check WebSocket proxy configuration:

```bash
# Edit Nginx config
sudo nano /etc/nginx/sites-available/skripsi
```

Make sure you have WebSocket support:

```nginx
server {
    listen 80;
    server_name 72.60.78.65;
    root /var/www/html/skripsi/public;

    # ... other configuration ...

    # WebSocket support for Reverb (IMPORTANT!)
    location /app {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_cache_bypass $http_upgrade;
    }
}
```

Then:

```bash
# Test Nginx config
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

---

## ✅ Complete Production .env Configuration

On the server, your `.env` should look like this:

```env
APP_NAME="Sistem Inventori"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://72.60.78.65

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/skripsi/database/database.sqlite

BROADCAST_DRIVER=reverb
CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file

# Reverb Configuration (PRODUCTION VALUES!)
REVERB_APP_ID=123456
REVERB_APP_KEY=your-production-key
REVERB_APP_SECRET=your-production-secret
REVERB_HOST=72.60.78.65           # ← Production IP
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite Variables (MUST match server IP!)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="72.60.78.65"    # ← Production IP (in quotes!)
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

**⚠️ CRITICAL**: `VITE_REVERB_HOST` MUST be `"72.60.78.65"`, NOT `"localhost"`!

---

## 🔧 Quick Fix Script for Server

Create this script on the server: `/var/www/html/skripsi/fix-realtime-production.sh`

```bash
#!/bin/bash

echo "=========================================="
echo "Fixing Real-Time Updates on Production"
echo "=========================================="

cd /var/www/html/skripsi

# Backup current .env
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Update .env with production values
echo "Updating .env configuration..."
sed -i 's/REVERB_HOST=localhost/REVERB_HOST=72.60.78.65/g' .env
sed -i 's/VITE_REVERB_HOST="localhost"/VITE_REVERB_HOST="72.60.78.65"/g' .env
sed -i 's/VITE_REVERB_HOST=localhost/VITE_REVERB_HOST="72.60.78.65"/g' .env

echo "Configuration updated:"
grep "REVERB_HOST" .env

# Clear cache
echo "Clearing cache..."
php artisan config:clear
php artisan cache:clear

# Rebuild assets
echo "Rebuilding assets..."
npm run build

# Restart services
echo "Restarting services..."
sudo systemctl restart reverb
sudo systemctl reload nginx

echo "=========================================="
echo "✅ Fix Complete!"
echo "=========================================="
echo ""
echo "Check Reverb status:"
systemctl status reverb
echo ""
echo "Test the application:"
echo "1. Visit: http://72.60.78.65"
echo "2. Open browser console (F12)"
echo "3. Look for: 'Connected to Reverb'"
echo ""
```

Make it executable and run:

```bash
chmod +x fix-realtime-production.sh
./fix-realtime-production.sh
```

---

## 🧪 Test Real-Time on Production

### Step 1: Check Browser Console

1. Visit: `http://72.60.78.65/master/produk`
2. Press F12 → Console
3. Look for these messages:

```javascript
// ✅ GOOD - Should see production IP
Reverb Configuration: {
  key: "***...",
  host: "72.60.78.65",    // ← Should be production IP, NOT localhost
  port: 8080,
  scheme: "http"
}

// ✅ GOOD - Connection successful
✅ Connected to Reverb, subscribing to channel: cabang.1
✅ WebSocket connected successfully

// ❌ BAD - Connection failed
Failed to connect to ws://localhost:8080
// This means assets still have localhost!
```

### Step 2: Test WebSocket Connection

In browser console (F12), run:

```javascript
// Check Echo connection
window.Echo.connector.pusher.connection.state
// Should return: "connected"

// Check configured host
window.Echo.connector.pusher.config.wsHost
// Should return: "72.60.78.65" (NOT "localhost")
```

### Step 3: Test Real-Time Update

1. Open browser Window 1: `http://72.60.78.65/master/produk`
2. Open browser Window 2: `http://72.60.78.65/master/produk`
3. Edit product in Window 1
4. Watch Window 2 - should update automatically

---

## 🔍 Diagnostic Commands

### Check if Reverb is Running
```bash
ssh username@72.60.78.65 "systemctl status reverb"
```

### Check if Port 8080 is Open
```bash
ssh username@72.60.78.65 "netstat -tulpn | grep 8080"
```

### Test WebSocket from Outside
```bash
# From your local PC
telnet 72.60.78.65 8080
```

Should connect (press Ctrl+] then type `quit` to exit)

### Check Reverb Logs
```bash
ssh username@72.60.78.65 "journalctl -u reverb -n 50"
```

### Check Application Logs
```bash
ssh username@72.60.78.65 "tail -50 /var/www/html/skripsi/storage/logs/laravel.log"
```

---

## 📋 Complete Deployment Checklist

When deploying, ensure:

- [ ] `.env` on server has `REVERB_HOST=72.60.78.65`
- [ ] `.env` on server has `VITE_REVERB_HOST="72.60.78.65"`
- [ ] Run `npm run build` on server after .env changes
- [ ] Reverb service is running: `systemctl status reverb`
- [ ] Port 8080 is open: `ufw allow 8080/tcp`
- [ ] Nginx has WebSocket proxy config
- [ ] Browser console shows production IP, not localhost

---

## 🚀 Updated Deployment Script

Update your `deploy.sh` on the server to include these checks:

```bash
#!/bin/bash

echo "🚀 Starting Deployment"

cd /var/www/html/skripsi

# Enable maintenance mode
php artisan down

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install

# ⚠️ IMPORTANT: Rebuild assets on production!
npm run build

# Migrate database
php artisan migrate --force

# Clear and cache config
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart reverb
sudo systemctl restart laravel-queue
sudo systemctl reload nginx

# Disable maintenance mode
php artisan up

# Check Reverb status
echo ""
echo "Reverb Status:"
systemctl status reverb --no-pager

echo ""
echo "✅ Deployment Complete!"
echo "Test: http://72.60.78.65"
```

---

## 🆘 Common Errors & Solutions

### Error: "Failed to connect to ws://localhost:8080"

**Cause**: Assets were built with localhost configuration

**Fix**:
```bash
# On server
cd /var/www/html/skripsi
nano .env  # Make sure VITE_REVERB_HOST="72.60.78.65"
npm run build  # Rebuild!
```

### Error: "WebSocket connection failed"

**Cause**: Reverb not running or port blocked

**Fix**:
```bash
# Check Reverb
systemctl status reverb
sudo systemctl start reverb

# Check firewall
sudo ufw allow 8080/tcp
```

### Error: "Connection refused"

**Cause**: Nginx not proxying WebSocket

**Fix**: Add WebSocket location block to Nginx config (see above)

### Browser Console: "NOT SET" for Reverb key

**Cause**: VITE variables not in .env or assets not rebuilt

**Fix**:
```bash
# Make sure .env has VITE_REVERB_APP_KEY
# Then rebuild
npm run build
```

---

## 💡 Pro Tips

### 1. Always Build on Production
Never copy `public/build` from local to production. Always run `npm run build` on the production server.

### 2. Check .env After Every Deployment
```bash
grep "REVERB_HOST" .env
# Should show: 72.60.78.65, not localhost
```

### 3. Monitor Reverb Logs
```bash
# Watch live logs
journalctl -u reverb -f
```

### 4. Use Different Reverb Keys
Use different `REVERB_APP_KEY` for local and production for security.

---

## ✅ Verification Steps

After fixing, verify:

1. **Server-side**: 
   ```bash
   systemctl status reverb
   # Should show: active (running)
   ```

2. **Browser console**:
   ```javascript
   // Should show production IP
   Reverb Configuration: {host: "72.60.78.65", ...}
   ```

3. **Real-time test**:
   - Open 2 windows
   - Edit product
   - Both windows update ✨

---

## 🎉 Success Criteria

Real-time is working when:
- ✅ Browser console shows: `✅ Connected to Reverb`
- ✅ Host shows `72.60.78.65` not `localhost`
- ✅ Two windows update simultaneously
- ✅ No WebSocket errors in console

---

**Need help?** Run the diagnostic commands above and check the logs!

**Last Updated**: December 18, 2025

