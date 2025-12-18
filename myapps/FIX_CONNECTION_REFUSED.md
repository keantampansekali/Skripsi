# Fix: ERR_CONNECTION_REFUSED Error

Error `ERR_CONNECTION_REFUSED` berarti **Reverb server tidak berjalan** atau tidak bisa diakses.

## ✅ Solusi: Start Reverb Server

### Cara 1: Menggunakan Batch File (Windows)

Double-click file: `start-reverb-server.bat`

Atau dari command prompt:
```bash
cd myapps
start-reverb-server.bat
```

### Cara 2: Manual Start

Buka **terminal baru** dan jalankan:

```bash
cd myapps
php artisan reverb:start
```

Anda harus melihat output seperti:
```
Reverb server started on 0.0.0.0:8080
```

**PENTING:** Terminal ini **HARUS tetap terbuka**! Jangan tutup terminal ini.

## ✅ Verifikasi Server Berjalan

Setelah start, cek di terminal lain:

```powershell
netstat -ano | findstr ":8080"
```

Harus ada output seperti:
```
TCP    0.0.0.0:8080           0.0.0.0:0              LISTENING       [PID]
```

## ✅ Test Koneksi

1. **Pastikan Reverb server berjalan** (lihat output di terminal)
2. **Refresh browser** (hard refresh: `Ctrl + Shift + R`)
3. **Buka console browser** (F12)
4. **Cek console** - harus ada:
   - "✅ WebSocket connected successfully"
   - **TIDAK** ada: "ERR_CONNECTION_REFUSED"

## 🔍 Troubleshooting

### Issue 1: Port 8080 sudah digunakan

Jika port 8080 sudah digunakan aplikasi lain:

**Solusi:** Ubah port di `.env`:
```env
REVERB_PORT=8081
VITE_REVERB_PORT=8081
```

Kemudian:
1. Rebuild assets: `npm run build`
2. Start Reverb dengan port baru: `php artisan reverb:start --port=8081`

### Issue 2: "Command not found: php artisan reverb:start"

**Solusi:** Pastikan Anda di folder `myapps`:
```bash
cd myapps
php artisan reverb:start
```

### Issue 3: Server start tapi masih error

**Solusi:**
1. Stop server (Ctrl+C)
2. Clear cache: `php artisan config:clear`
3. Start lagi: `php artisan reverb:start`
4. Rebuild assets: `npm run build`
5. Hard refresh browser

### Issue 4: Firewall memblokir

**Solusi:** 
- Windows Firewall mungkin memblokir port 8080
- Allow PHP melalui firewall jika diminta
- Atau disable firewall sementara untuk test

## 📋 Checklist

Sebelum test, pastikan:

- [ ] Reverb server **sedang berjalan** (lihat terminal)
- [ ] Terminal Reverb server **tidak ditutup**
- [ ] Port 8080 **tidak digunakan** aplikasi lain
- [ ] Assets sudah di-rebuild (`npm run build`)
- [ ] Browser di-hard refresh (`Ctrl + Shift + R`)
- [ ] Console browser menunjukkan koneksi berhasil

## 🚀 Development Workflow

Untuk development, jalankan **3 terminal**:

**Terminal 1 - Reverb Server (WAJIB!):**
```bash
cd myapps
php artisan reverb:start
```
**JANGAN TUTUP TERMINAL INI!**

**Terminal 2 - Laravel Application:**
```bash
cd myapps
php artisan serve
```

**Terminal 3 - Vite Dev Server:**
```bash
cd myapps
npm run dev
```

## 💡 Quick Test

Setelah start Reverb server, test dengan:

1. Buka browser
2. Hard refresh: `Ctrl + Shift + R`
3. Buka console (F12)
4. Cek harus ada: "✅ WebSocket connected successfully"

Jika masih error, cek terminal Reverb server untuk error messages.

