# 🚀 Simple Deployment Guide (Using Git Pull)

## Your Current Workflow

Since you're already using `git pull` on the server, deployment is straightforward:

```
Local Machine → Push to GitHub → Server pulls from GitHub → Deploy
```

---

## 🎯 Quick Deployment (3 Steps)

### Step 1: Push to GitHub
```bash
git add .
git commit -m "Your changes"
git push origin main
```

### Step 2: Pull on Server & Deploy
```bash
ssh username@72.60.78.65
cd /var/www/html/skripsi
git pull origin main
./deploy.sh
```

### Step 3: Done! ✅
Visit: `http://72.60.78.65`

---

## ⚡ Even Simpler: Use the Batch Script

Just double-click: **`deploy-simple.bat`**

This script will:
1. ✅ Commit your changes
2. ✅ Push to GitHub
3. ✅ SSH to server
4. ✅ Pull latest code
5. ✅ Run deployment script

---

## 📝 What the Deployment Script Does

The `deploy.sh` script on the server automatically:
- ✅ Puts site in maintenance mode
- ✅ Updates dependencies (composer, npm)
- ✅ Builds frontend assets
- ✅ Runs database migrations
- ✅ Clears and rebuilds cache
- ✅ Restarts services (Reverb, Queue)
- ✅ Takes site out of maintenance mode

---

## 🔄 Daily Workflow

### Option 1: Automated (Recommended)
```batch
REM Windows: Just double-click
deploy-simple.bat
```

### Option 2: Manual Commands
```bash
# Local machine
git add .
git commit -m "Update features"
git push origin main

# Then SSH to server
ssh username@72.60.78.65 "cd /var/www/html/skripsi && git pull origin main && ./deploy.sh"
```

### Option 3: Step by Step
```bash
# 1. Local: Push changes
git push origin main

# 2. SSH to server
ssh username@72.60.78.65

# 3. On server: Pull and deploy
cd /var/www/html/skripsi
git pull origin main
./deploy.sh
exit
```

---

## 🛠️ Server Setup (One-Time)

If not already set up, run this on the server once:

```bash
# SSH to server
ssh username@72.60.78.65

# Navigate to web directory
cd /var/www/html

# Clone from GitHub (if not already cloned)
git clone https://github.com/keantampansekali/Skripsi.git skripsi
cd skripsi

# Configure Git
git config pull.rebase false  # Use merge strategy

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Setup environment
cp .env.example .env
nano .env  # Edit configuration for production

# Set APP_URL, DB settings, Reverb settings, etc.
# Important settings:
#   APP_ENV=production
#   APP_DEBUG=false
#   APP_URL=http://72.60.78.65
#   REVERB_HOST=72.60.78.65

# Generate key
php artisan key:generate

# Setup database
touch database/database.sqlite
chmod 664 database/database.sqlite
php artisan migrate --force
php artisan db:seed --force

# Set permissions
chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache public/uploads

# Create deployment script
nano deploy.sh
# Paste contents from deploy.sh file
chmod +x deploy.sh

# Test deployment script
./deploy.sh
```

---

## 📋 Production .env Settings

Important settings for server `.env`:

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
VITE_REVERB_HOST="72.60.78.65"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

**Important**: 
- Set `APP_DEBUG=false` in production
- Use server IP `72.60.78.65` for URLs
- Keep `.env` secure (never commit to Git)

---

## ✅ Deployment Checklist

### Before Each Deployment
- [ ] Test locally: `php artisan serve`
- [ ] Check real-time features work locally
- [ ] Commit all changes: `git status`
- [ ] Push to GitHub: `git push origin main`

### During Deployment
- [ ] Run `deploy-simple.bat` OR manual SSH deploy
- [ ] Watch for errors in deployment output
- [ ] Wait for "Deployment Complete" message

### After Deployment
- [ ] Visit: `http://72.60.78.65`
- [ ] Test login
- [ ] Test real-time updates (open 2 windows)
- [ ] Check WebSocket: F12 → Console → Look for "✅ Connected to Reverb"
- [ ] Test key features (products, stock, transactions)

---

## 🔍 Verify Services are Running

```bash
# SSH to server
ssh username@72.60.78.65

# Check all services
systemctl status nginx
systemctl status php8.2-fpm
systemctl status reverb
systemctl status laravel-queue

# View application logs
tail -f /var/www/html/skripsi/storage/logs/laravel.log

# View Reverb logs
journalctl -u reverb -f

# View Queue logs
journalctl -u laravel-queue -f
```

---

## 🆘 Troubleshooting

### Problem: Git Pull Shows Conflicts

```bash
# On server
cd /var/www/html/skripsi

# Stash local changes
git stash

# Pull latest
git pull origin main

# If needed, restore local changes
git stash pop
```

### Problem: Deployment Script Fails

```bash
# Run deployment steps manually on server
cd /var/www/html/skripsi

php artisan down
git pull origin main
composer install --no-dev
npm install
npm run build
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl restart reverb
sudo systemctl restart laravel-queue
php artisan up
```

### Problem: Permission Denied

```bash
# Fix permissions on server
cd /var/www/html/skripsi
sudo chown -R www-data:www-data .
chmod -R 775 storage bootstrap/cache
chmod +x deploy.sh
```

### Problem: Services Not Running

```bash
# Check and restart services
sudo systemctl status reverb
sudo systemctl restart reverb

sudo systemctl status laravel-queue
sudo systemctl restart laravel-queue

sudo systemctl status nginx
sudo systemctl reload nginx
```

### Problem: Can't SSH to Server

```bash
# Test connection
ping 72.60.78.65

# Test SSH with verbose
ssh -v username@72.60.78.65

# Try with password explicitly
ssh -o PreferredAuthentications=password username@72.60.78.65
```

---

## 📊 Monitor Application

### Check Application Status
```bash
# Quick health check
curl -I http://72.60.78.65

# Should return: HTTP/1.1 200 OK
```

### Watch Real-Time Logs
```bash
# SSH to server
ssh username@72.60.78.65

# Open logs in separate terminals
# Terminal 1: Application logs
tail -f /var/www/html/skripsi/storage/logs/laravel.log

# Terminal 2: Nginx access logs
tail -f /var/log/nginx/access.log

# Terminal 3: Reverb logs
journalctl -u reverb -f
```

---

## 🔄 Rollback if Needed

If deployment causes issues:

```bash
# SSH to server
ssh username@72.60.78.65
cd /var/www/html/skripsi

# Enable maintenance mode
php artisan down

# Find previous working commit
git log --oneline -10

# Rollback to previous commit
git reset --hard abc1234  # Replace with commit hash

# Redeploy
./deploy.sh

# Or manually:
composer install --no-dev
npm install
npm run build
php artisan migrate --force
php artisan cache:clear
php artisan config:cache

# Disable maintenance mode
php artisan up
```

---

## 💡 Pro Tips

### 1. Create SSH Alias
Add to `~/.ssh/config` on your local machine:
```
Host prod
    HostName 72.60.78.65
    User username
    Port 22
```

Then just use: `ssh prod`

### 2. Create Deploy Alias
Add to `.bashrc` or `.zshrc`:
```bash
alias deploy='git push origin main && ssh prod "cd /var/www/html/skripsi && git pull origin main && ./deploy.sh"'
```

Then just run: `deploy`

### 3. Set Up Git Hooks
Create `.git/hooks/post-commit`:
```bash
#!/bin/bash
echo "Don't forget to deploy: ./deploy-simple.bat"
```

---

## 📚 Additional Resources

- **Complete Guide**: `DEPLOYMENT_GUIDE.md`
- **Real-Time Setup**: `README_REALTIME.md`
- **Feature Docs**: `DOKUMENTASI_FITUR.md`

---

## ✅ Quick Command Reference

```bash
# Local: Deploy everything
deploy-simple.bat

# Local: Push only
git push origin main

# Server: Pull and deploy
ssh username@72.60.78.65 "cd /var/www/html/skripsi && git pull origin main && ./deploy.sh"

# Server: Check status
ssh username@72.60.78.65 "systemctl status reverb && systemctl status laravel-queue"

# Server: View logs
ssh username@72.60.78.65 "tail -50 /var/www/html/skripsi/storage/logs/laravel.log"

# Server: Manual deploy
ssh username@72.60.78.65
cd /var/www/html/skripsi
git pull origin main
./deploy.sh
```

---

**🎉 That's it! Your simple deployment workflow is ready!**

Just run `deploy-simple.bat` and you're done! 🚀

---

**Application URL**: http://72.60.78.65

**Last Updated**: December 18, 2025

