# 🚀 Quick Deployment Guide

## First Time Setup

### 1. Add Production Remote
```bash
# Replace 'username' and adjust path as needed
git remote add production ssh://username@72.60.78.65/var/www/html/skripsi.git

# Verify
git remote -v
```

### 2. Initial Server Setup
SSH to server and run initial setup (only once):

```bash
ssh username@72.60.78.65

# Create application directory
sudo mkdir -p /var/www/html/skripsi
sudo chown $USER:$USER /var/www/html/skripsi

# Initialize bare repository (if using SSH)
mkdir -p /var/repos/skripsi.git
cd /var/repos/skripsi.git
git init --bare

# Or clone from GitHub
cd /var/www/html
git clone https://github.com/keantampansekali/Skripsi.git skripsi
cd skripsi

# Copy deployment script
# (copy contents from deploy.sh)
nano deploy.sh
chmod +x deploy.sh

# Install dependencies
composer install --no-dev
npm install
npm run build

# Setup environment
cp .env.example .env
nano .env  # Edit configuration
php artisan key:generate
php artisan migrate --force

# Set permissions
chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

---

## Daily Deployment Workflow

### Option 1: Using Batch Script (Windows)
```batch
REM Just double-click this file:
deploy-to-production.bat
```

The script will:
1. ✅ Commit your changes
2. ✅ Push to GitHub (backup)
3. ✅ Push to production server
4. ✅ Run deployment on server

### Option 2: Manual Commands
```bash
# 1. Commit changes
git add .
git commit -m "Your changes"

# 2. Push to GitHub
git push origin main

# 3. Push to production
git push production main

# 4. Deploy on server
ssh username@72.60.78.65 "bash /var/www/html/skripsi/deploy.sh"
```

---

## Server Deployment Script

The `deploy.sh` script automatically:
- ✅ Pulls latest code
- ✅ Installs dependencies
- ✅ Builds frontend assets
- ✅ Runs migrations
- ✅ Clears and rebuilds cache
- ✅ Restarts services
- ✅ Enables/disables maintenance mode

---

## Quick Commands Reference

### Deploy from Windows
```batch
deploy-to-production.bat
```

### Deploy from Linux/Mac
```bash
./deploy-to-production.sh
```

### Manual Deploy on Server
```bash
ssh username@72.60.78.65
cd /var/www/html/skripsi
./deploy.sh
```

### Check Application Status
```bash
# SSH to server
ssh username@72.60.78.65

# Check services
systemctl status reverb
systemctl status laravel-queue
systemctl status nginx

# View logs
tail -f /var/www/html/skripsi/storage/logs/laravel.log
```

---

## Emergency Rollback

If deployment causes issues:

```bash
# SSH to server
ssh username@72.60.78.65
cd /var/www/html/skripsi

# Enable maintenance mode
php artisan down

# Rollback to previous commit
git log --oneline  # Find previous commit hash
git reset --hard <previous-commit-hash>

# Reinstall dependencies
composer install --no-dev
npm install
npm run build

# Clear cache
php artisan cache:clear
php artisan config:clear

# Disable maintenance mode
php artisan up
```

---

## Troubleshooting

### Push to Production Fails
```bash
# Check if remote exists
git remote -v

# Add remote if missing
git remote add production ssh://username@72.60.78.65/var/repos/skripsi.git

# Test SSH connection
ssh username@72.60.78.65
```

### Deployment Script Fails
```bash
# Check script permissions
ls -l /var/www/html/skripsi/deploy.sh

# Make executable if needed
chmod +x /var/www/html/skripsi/deploy.sh

# Run manually with verbose output
bash -x /var/www/html/skripsi/deploy.sh
```

### Services Not Starting
```bash
# Check service status
systemctl status reverb
systemctl status laravel-queue

# View service logs
journalctl -u reverb -n 50
journalctl -u laravel-queue -n 50

# Restart services
sudo systemctl restart reverb
sudo systemctl restart laravel-queue
```

---

## Complete Documentation

For complete deployment instructions, see: **DEPLOYMENT_GUIDE.md**

---

**Quick Access URL**: http://72.60.78.65

**Last Updated**: December 18, 2025

