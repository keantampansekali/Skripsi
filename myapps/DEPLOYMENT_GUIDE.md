# 🚀 Deployment Guide - Git Server 72.60.78.65

## Overview
This guide explains how to deploy your Laravel application to the Git server at **72.60.78.65**.

---

## 📋 Prerequisites

Before deploying, ensure you have:
- [x] SSH access to the server (username and password/key)
- [x] Git installed on your local machine
- [x] Git installed on the server
- [x] Web server (Nginx/Apache) configured on server
- [x] PHP 8.1+ and required extensions on server
- [x] Composer installed on server
- [x] Node.js and npm installed on server (for frontend assets)

---

## 🔧 Step 1: Add Git Remote

### Current Remote
Your repository is currently connected to GitHub:
```
origin: https://github.com/keantampansekali/Skripsi
```

### Add Server Remote

#### Option A: SSH (Recommended)
```bash
# Add new remote for your server
git remote add production ssh://username@72.60.78.65/path/to/repo.git

# Example:
git remote add production ssh://deploy@72.60.78.65/var/www/html/skripsi.git
```

#### Option B: HTTP/HTTPS
```bash
# If server has HTTP Git access
git remote add production http://72.60.78.65/git/skripsi.git
```

### Verify Remotes
```bash
git remote -v
```

Should show:
```
origin      https://github.com/keantampansekali/Skripsi (fetch)
origin      https://github.com/keantampansekali/Skripsi (push)
production  ssh://username@72.60.78.65/path/to/repo.git (fetch)
production  ssh://username@72.60.78.65/path/to/repo.git (push)
```

---

## 📤 Step 2: Push Code to Server

### First Time Push
```bash
# Make sure all changes are committed
git add .
git commit -m "Prepare for production deployment"

# Push to GitHub (backup)
git push origin main

# Push to production server
git push production main
```

### Subsequent Pushes
```bash
# Commit your changes
git add .
git commit -m "Your commit message"

# Push to both remotes
git push origin main
git push production main
```

### Push to Both at Once
```bash
# Add this to push to both remotes with one command
git remote set-url --add --push origin https://github.com/keantampansekali/Skripsi
git remote set-url --add --push origin ssh://username@72.60.78.65/path/to/repo.git

# Now 'git push origin main' pushes to both
```

---

## 🖥️ Step 3: Server Setup

### SSH into Server
```bash
ssh username@72.60.78.65
```

### Set Up Application Directory
```bash
# Navigate to web root
cd /var/www/html

# Clone repository (first time only)
git clone ssh://username@72.60.78.65/path/to/repo.git skripsi
# OR
git clone /path/to/repo.git skripsi

cd skripsi
```

### Install Dependencies
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies
npm install

# Build frontend assets
npm run build
```

### Set Permissions
```bash
# Storage and cache permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# If using Nginx
chown -R nginx:nginx storage bootstrap/cache
```

### Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Edit environment variables
nano .env
```

**Important .env settings for production:**
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

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=72.60.78.65
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### Generate Application Key
```bash
php artisan key:generate
```

### Run Migrations
```bash
# Create database file if using SQLite
touch database/database.sqlite
chmod 664 database/database.sqlite

# Run migrations
php artisan migrate --force

# Seed database (optional)
php artisan db:seed --force
```

### Optimize for Production
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoload
composer dump-autoload --optimize
```

---

## 🌐 Step 4: Web Server Configuration

### Nginx Configuration
Create/edit: `/etc/nginx/sites-available/skripsi`

```nginx
server {
    listen 80;
    server_name 72.60.78.65;
    root /var/www/html/skripsi/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # WebSocket support for Reverb
    location /app {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/skripsi /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Apache Configuration
Create/edit: `/etc/apache2/sites-available/skripsi.conf`

```apache
<VirtualHost *:80>
    ServerName 72.60.78.65
    DocumentRoot /var/www/html/skripsi/public

    <Directory /var/www/html/skripsi/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/skripsi-error.log
    CustomLog ${APACHE_LOG_DIR}/skripsi-access.log combined
</VirtualHost>
```

Enable site:
```bash
sudo a2ensite skripsi
sudo a2enmod rewrite
sudo systemctl reload apache2
```

---

## 🔄 Step 5: Start Services

### Start Laravel Reverb (WebSocket)
```bash
# Using systemd service (recommended)
sudo nano /etc/systemd/system/reverb.service
```

**reverb.service:**
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

Start service:
```bash
sudo systemctl daemon-reload
sudo systemctl enable reverb
sudo systemctl start reverb
sudo systemctl status reverb
```

### Start Queue Worker
```bash
sudo nano /etc/systemd/system/laravel-queue.service
```

**laravel-queue.service:**
```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/html/skripsi
ExecStart=/usr/bin/php /var/www/html/skripsi/artisan queue:work --sleep=3 --tries=3
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

Start service:
```bash
sudo systemctl daemon-reload
sudo systemctl enable laravel-queue
sudo systemctl start laravel-queue
sudo systemctl status laravel-queue
```

---

## 🔄 Step 6: Deployment Script (Automated Updates)

Create a deployment script on the server:

```bash
sudo nano /var/www/html/skripsi/deploy.sh
```

**deploy.sh:**
```bash
#!/bin/bash

echo "🚀 Starting deployment..."

# Navigate to project directory
cd /var/www/html/skripsi

# Enable maintenance mode
php artisan down

# Pull latest changes
echo "📥 Pulling latest code..."
git pull origin main

# Install/Update dependencies
echo "📦 Installing dependencies..."
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Run migrations
echo "🗃️ Running migrations..."
php artisan migrate --force

# Clear and cache
echo "🔄 Optimizing..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
echo "♻️ Restarting services..."
sudo systemctl restart reverb
sudo systemctl restart laravel-queue
sudo systemctl reload nginx

# Disable maintenance mode
php artisan up

echo "✅ Deployment complete!"
```

Make executable:
```bash
chmod +x /var/www/html/skripsi/deploy.sh
```

### Use Deployment Script
From your local machine after pushing:
```bash
ssh username@72.60.78.65 'bash /var/www/html/skripsi/deploy.sh'
```

Or directly on server:
```bash
ssh username@72.60.78.65
cd /var/www/html/skripsi
./deploy.sh
```

---

## 📝 Step 7: Quick Deployment Commands

### From Local Machine
Create this script on your local machine: `myapps/deploy-to-server.bat`

```batch
@echo off
echo ========================================
echo Deploying to Production Server
echo ========================================

REM Commit changes
echo.
echo Committing changes...
git add .
set /p commit_msg="Enter commit message: "
git commit -m "%commit_msg%"

REM Push to GitHub (backup)
echo.
echo Pushing to GitHub...
git push origin main

REM Push to production
echo.
echo Pushing to production server...
git push production main

REM Run deployment script on server
echo.
echo Running deployment on server...
ssh username@72.60.78.65 "bash /var/www/html/skripsi/deploy.sh"

echo.
echo ========================================
echo ✅ Deployment Complete!
echo ========================================
echo.
echo Application URL: http://72.60.78.65
echo.
pause
```

---

## 🔒 Security Checklist

### Before Deploying
- [ ] Set `APP_DEBUG=false` in production .env
- [ ] Set `APP_ENV=production` in .env
- [ ] Generate strong `APP_KEY`
- [ ] Use strong database credentials
- [ ] Configure firewall (allow only ports 80, 443, 22)
- [ ] Set up SSL certificate (Let's Encrypt)
- [ ] Disable directory listing
- [ ] Set correct file permissions (775 for storage, 644 for files)
- [ ] Keep `.env` out of version control (.gitignore)

### Server Security
```bash
# Firewall
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 8080/tcp  # For Reverb
sudo ufw enable

# Disable root login
sudo nano /etc/ssh/sshd_config
# Set: PermitRootLogin no
sudo systemctl restart sshd
```

---

## 🧪 Testing After Deployment

### Test Checklist
1. **Application Access**
   ```bash
   curl http://72.60.78.65
   ```
   Should return HTML content.

2. **Database Connection**
   - Login to application
   - Check if data loads correctly

3. **WebSocket Connection**
   - Open browser console (F12)
   - Should see: `✅ Connected to Reverb`

4. **Real-Time Updates**
   - Open 2 browser windows
   - Test stock updates
   - Verify real-time synchronization

5. **File Uploads**
   - Test product image upload
   - Check storage permissions

---

## 🔄 Regular Maintenance

### Update Application
```bash
# On local machine
git push production main

# On server
cd /var/www/html/skripsi
./deploy.sh
```

### Monitor Logs
```bash
# Laravel logs
tail -f /var/www/html/skripsi/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/error.log

# Reverb service
journalctl -u reverb -f

# Queue worker
journalctl -u laravel-queue -f
```

### Backup Database
```bash
# Backup SQLite database
cp /var/www/html/skripsi/database/database.sqlite \
   /var/www/html/skripsi/database/backups/database-$(date +%Y%m%d).sqlite
```

---

## 🆘 Troubleshooting

### Issue: Can't SSH to Server
```bash
# Test SSH connection
ssh username@72.60.78.65

# Use verbose mode
ssh -v username@72.60.78.65
```

### Issue: Permission Denied
```bash
# On server
cd /var/www/html/skripsi
sudo chown -R www-data:www-data .
chmod -R 775 storage bootstrap/cache
```

### Issue: 500 Internal Server Error
```bash
# Check logs
tail -n 50 storage/logs/laravel.log

# Check permissions
ls -la storage/
ls -la bootstrap/cache/
```

### Issue: WebSocket Not Connecting
```bash
# Check if Reverb is running
sudo systemctl status reverb

# Restart Reverb
sudo systemctl restart reverb

# Check port
netstat -tulpn | grep 8080
```

---

## 📚 Quick Reference

### Important Paths
- **Application**: `/var/www/html/skripsi`
- **Public**: `/var/www/html/skripsi/public`
- **Storage**: `/var/www/html/skripsi/storage`
- **Logs**: `/var/www/html/skripsi/storage/logs`
- **Database**: `/var/www/html/skripsi/database/database.sqlite`

### Important Commands
```bash
# Deploy
./deploy.sh

# View logs
tail -f storage/logs/laravel.log

# Restart services
sudo systemctl restart reverb
sudo systemctl restart laravel-queue
sudo systemctl reload nginx

# Clear cache
php artisan cache:clear
php artisan config:clear

# Optimize
php artisan optimize
```

---

## ✅ Deployment Checklist

- [ ] Add production remote: `git remote add production ...`
- [ ] Push code: `git push production main`
- [ ] SSH to server: `ssh username@72.60.78.65`
- [ ] Clone repository on server
- [ ] Run `composer install --no-dev`
- [ ] Run `npm install && npm run build`
- [ ] Copy and configure `.env` file
- [ ] Run `php artisan key:generate`
- [ ] Set file permissions (775 storage)
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Configure web server (Nginx/Apache)
- [ ] Set up Reverb systemd service
- [ ] Set up Queue systemd service
- [ ] Test application access
- [ ] Test WebSocket connection
- [ ] Test real-time features
- [ ] Set up SSL certificate (optional but recommended)

---

**🎉 Your application is now deployed!**

Access it at: **http://72.60.78.65**

For SSL setup, consider using Let's Encrypt: `sudo certbot --nginx`

---

**Last Updated**: December 18, 2025

