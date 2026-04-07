# ⚡ Quick Fix: Real-Time Not Working on Production

## The Problem
Real-time updates work on `localhost` but NOT on `72.60.78.65` ❌

---

## 🎯 The Root Cause
Your production server `.env` has:
```env
REVERB_HOST=localhost              # ❌ WRONG!
VITE_REVERB_HOST="localhost"       # ❌ WRONG!
```

Should be:
```env
REVERB_HOST=72.60.78.65            # ✅ CORRECT
VITE_REVERB_HOST="72.60.78.65"    # ✅ CORRECT
```

---

## ⚡ Super Quick Fix (1 Command)

From your local PC, run:
```batch
fix-server-realtime.bat
```

This will:
1. ✅ SSH to server
2. ✅ Fix .env configuration
3. ✅ Rebuild assets
4. ✅ Restart Reverb
5. ✅ Done!

---

## 📝 Manual Fix (If needed)

### Step 1: SSH to Server
```bash
ssh username@72.60.78.65
cd /var/www/html/skripsi
```

### Step 2: Edit .env
```bash
nano .env
```

Change these lines:
```env
# FROM:
REVERB_HOST=localhost
VITE_REVERB_HOST="localhost"

# TO:
REVERB_HOST=72.60.78.65
VITE_REVERB_HOST="72.60.78.65"
```

Save: `Ctrl+X`, `Y`, `Enter`

### Step 3: Rebuild & Restart
```bash
# Clear cache
php artisan config:clear

# Rebuild assets (IMPORTANT!)
npm run build

# Restart Reverb
sudo systemctl restart reverb

# Check status
systemctl status reverb
```

---

## ✅ How to Verify It Works

### 1. Check Browser Console
Visit: `http://72.60.78.65/master/produk`

Press `F12` → Console, should see:

```javascript
// ✅ GOOD - Shows production IP
Reverb Configuration: {
  host: "72.60.78.65",    // ← Should NOT be "localhost"
  port: 8080,
  ...
}

// ✅ GOOD - Connected
✅ Connected to Reverb, subscribing to channel: cabang.1
```

```javascript
// ❌ BAD - Shows localhost
Failed to connect to ws://localhost:8080
// This means you need to rebuild: npm run build
```

### 2. Test Real-Time
1. Open Window 1: `http://72.60.78.65/master/produk`
2. Open Window 2: `http://72.60.78.65/master/produk`
3. Edit product in Window 1
4. Watch Window 2 update automatically ✨

---

## 🔍 Common Issues

### Issue: "Still connecting to localhost"
**Fix**: Rebuild assets on server
```bash
npm run build
```

### Issue: "Connection refused"
**Fix**: Check Reverb is running
```bash
sudo systemctl status reverb
sudo systemctl start reverb
```

### Issue: "Port 8080 blocked"
**Fix**: Open firewall
```bash
sudo ufw allow 8080/tcp
```

---

## 📋 Checklist

Production real-time works when:
- [ ] `.env` has `REVERB_HOST=72.60.78.65`
- [ ] `.env` has `VITE_REVERB_HOST="72.60.78.65"`
- [ ] Ran `npm run build` on server
- [ ] Reverb service is running
- [ ] Port 8080 is open
- [ ] Browser console shows production IP
- [ ] Two windows update simultaneously

---

## 🚀 Files Created

| File | Purpose |
|------|---------|
| **fix-server-realtime.bat** | Run from local PC - fixes everything |
| **fix-realtime-production.sh** | Server script (auto-uploaded) |
| **FIX_PRODUCTION_REALTIME.md** | Complete documentation |

---

## 💡 Pro Tip

After every deployment, always check:
```bash
# On server
grep "REVERB_HOST" /var/www/html/skripsi/.env

# Should show:
# REVERB_HOST=72.60.78.65
# VITE_REVERB_HOST="72.60.78.65"
```

---

## 🆘 Need Help?

Run diagnostics on server:
```bash
ssh username@72.60.78.65
cd /var/www/html/skripsi

# Check Reverb
systemctl status reverb

# Check config
grep "REVERB_HOST" .env

# Check logs
tail -50 storage/logs/laravel.log
journalctl -u reverb -n 20
```

---

**Quick Start**: Just run `fix-server-realtime.bat` from your PC! 🚀

---

**Last Updated**: December 18, 2025

