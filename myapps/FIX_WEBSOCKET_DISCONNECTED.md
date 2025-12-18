# Fix: WebSocket Disconnected Error

Jika Anda melihat "WebSocket disconnected" di console browser, ikuti langkah-langkah berikut:

## ✅ Langkah 1: Pastikan Reverb Server Berjalan

Buka terminal **BARU** dan jalankan:

```bash
cd myapps
php artisan reverb:start
```

Anda harus melihat output:
```
Reverb server started on 0.0.0.0:8080
```

**PENTING:** Biarkan terminal ini tetap terbuka dan berjalan!

## ✅ Langkah 2: Clear Cache

Di terminal lain (atau hentikan Reverb server sementara dengan Ctrl+C):

```bash
cd myapps
php artisan config:clear
php artisan cache:clear
```

## ✅ Langkah 3: Rebuild Assets

**PENTING:** Assets HARUS di-rebuild agar environment variables ter-load!

```bash
cd myapps
npm run build
```

Atau untuk development mode:
```bash
npm run dev
```

**Catatan:** Jika menggunakan `npm run dev`, pastikan terminal Vite tetap berjalan!

## ✅ Langkah 4: Verifikasi Konfigurasi

Jalankan script check:

```powershell
cd myapps
.\check-reverb.ps1
```

Pastikan semua menunjukkan ✅ (hijau).

## ✅ Langkah 5: Test di Browser

1. **Hard refresh browser:**
   - Windows: `Ctrl + Shift + R` atau `Ctrl + F5`
   - Mac: `Cmd + Shift + R`

2. **Buka Developer Console (F12)**

3. **Cek console output:**
   - Harus ada: "Reverb Configuration: ..."
   - Harus ada: "✅ WebSocket connected successfully"
   - **JANGAN** ada: "WebSocket disconnected" atau error

## 🔍 Debugging

### Jika masih error, cek di console:

1. **Cek konfigurasi yang ter-load:**
   ```javascript
   console.log('Reverb Config:', {
       key: import.meta.env.VITE_REVERB_APP_KEY,
       host: import.meta.env.VITE_REVERB_HOST,
       port: import.meta.env.VITE_REVERB_PORT
   });
   ```

2. **Cek Echo object:**
   ```javascript
   console.log('Echo:', window.Echo);
   console.log('Connection state:', window.Echo?.connector?.pusher?.connection?.state);
   ```

### Common Issues:

#### Issue 1: "Reverb key not configured"
**Solution:**
- Pastikan `VITE_REVERB_APP_KEY` ada di `.env`
- Rebuild assets: `npm run build`
- Hard refresh browser

#### Issue 2: "Connection refused" atau "Failed to connect"
**Solution:**
- Pastikan Reverb server berjalan: `php artisan reverb:start`
- Cek port 8080 tidak diblokir firewall
- Cek `REVERB_HOST` sesuai dengan URL aplikasi

#### Issue 3: "WebSocket disconnected" setelah connect
**Solution:**
- Cek Reverb server masih berjalan
- Cek tidak ada error di terminal Reverb server
- Cek network tab di browser untuk melihat request WebSocket

#### Issue 4: Assets tidak ter-update
**Solution:**
- Hapus folder `public/build` (jika ada)
- Rebuild: `npm run build`
- Clear browser cache
- Hard refresh

## 📋 Checklist

Sebelum test, pastikan:

- [ ] Reverb server berjalan (`php artisan reverb:start`)
- [ ] Config cache cleared (`php artisan config:clear`)
- [ ] Assets di-rebuild (`npm run build` atau `npm run dev`)
- [ ] Browser di-hard refresh (`Ctrl + Shift + R`)
- [ ] Console browser tidak ada error
- [ ] Network tab menunjukkan WebSocket connection

## 🚀 Development Workflow

Untuk development, jalankan **3 terminal**:

**Terminal 1 - Reverb Server:**
```bash
php artisan reverb:start
```

**Terminal 2 - Laravel Application:**
```bash
php artisan serve
```

**Terminal 3 - Vite Dev Server:**
```bash
npm run dev
```

**PENTING:** Semua 3 terminal harus tetap berjalan!

## 💡 Quick Test

Buka file `test-websocket.html` di browser untuk test koneksi WebSocket langsung tanpa Laravel.

## ❓ Masih Error?

1. Cek log Laravel: `storage/logs/laravel.log`
2. Cek console browser untuk error details
3. Cek terminal Reverb server untuk error messages
4. Pastikan semua dependencies terinstall: `composer install && npm install`

