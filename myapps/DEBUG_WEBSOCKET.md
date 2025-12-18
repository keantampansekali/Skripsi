# Debug: WebSocket Connected tapi Tidak Auto Refresh

Jika WebSocket sudah connected tapi tidak auto refresh setelah edit stok, ikuti langkah debugging berikut:

## ✅ Step 1: Cek Console Browser

Buka Developer Console (F12) dan cek:

1. **Setelah edit stok, cek console:**
   - Harus ada: "📦 Stock updated event received: ..."
   - Jika tidak ada, berarti event tidak ter-broadcast atau tidak sampai ke client

2. **Cek log Laravel:**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   Atau buka file: `storage/logs/laravel.log`
   
   Harus ada log:
   - "Broadcasting StokUpdated for cabang: ..."
   - "Broadcast completed successfully"

## ✅ Step 2: Test Event Broadcasting

Edit stok produk, lalu cek:

1. **Console browser** - harus ada event received
2. **Laravel log** - harus ada broadcast log
3. **Reverb server terminal** - harus ada activity

## ✅ Step 3: Verifikasi Channel

Pastikan channel name sesuai:

1. **Backend broadcast ke:** `cabang.{idCabang}`
2. **Frontend listen ke:** `cabang.{idCabang}`
3. **Pastikan idCabang sama** di backend dan frontend

Cek di console browser:
```javascript
console.log('Current idCabang:', window.idCabang);
console.log('Echo channel:', window.Echo?.channel(`cabang.${window.idCabang}`));
```

## ✅ Step 4: Cek Broadcasting Connection

Pastikan di `.env`:
```env
BROADCAST_CONNECTION=reverb
```

Verifikasi:
```bash
php artisan tinker
>>> config('broadcasting.default')
# Harus return: "reverb"
```

## ✅ Step 5: Test Manual Broadcast

Test broadcast event secara manual:

```bash
php artisan tinker
```

```php
use App\Events\StokUpdated;
$idCabang = 1; // Ganti dengan id_cabang Anda
broadcast(new StokUpdated($idCabang, [
    'tipe' => 'produk',
    'id' => 999,
    'nama' => 'Test Product',
    'stok' => 100,
]));
```

Cek console browser - harus ada event received.

## 🔍 Common Issues

### Issue 1: Event tidak ter-broadcast

**Gejala:** Tidak ada log di Laravel log

**Solusi:**
- Cek `BROADCAST_CONNECTION=reverb` di `.env`
- Clear cache: `php artisan config:clear`
- Cek Reverb server berjalan

### Issue 2: Event ter-broadcast tapi tidak sampai client

**Gejala:** Ada log di Laravel tapi tidak ada di console browser

**Solusi:**
- Cek channel authorization di `routes/channels.php`
- Cek user sudah login dan memiliki akses ke cabang
- Cek Reverb server terminal untuk error

### Issue 3: Event sampai tapi tidak update UI

**Gejala:** Ada log "Stock updated event received" di console tapi UI tidak update

**Solusi:**
- Cek function `updateStockDisplay()` bekerja
- Cek selector DOM element benar
- Cek tidak ada JavaScript error

### Issue 4: Channel name mismatch

**Gejala:** Event tidak pernah received

**Solusi:**
- Pastikan `idCabang` sama di backend dan frontend
- Cek `session('id_cabang')` di backend
- Cek `window.idCabang` di frontend

## 📋 Checklist

- [ ] WebSocket connected (console: "✅ WebSocket connected successfully")
- [ ] Event listeners setup (console: "✅ Setting up event listeners")
- [ ] Edit stok produk
- [ ] Cek Laravel log - ada "Broadcasting StokUpdated"
- [ ] Cek console browser - ada "📦 Stock updated event received"
- [ ] UI update otomatis

## 🚀 Quick Test

1. Buka 2 browser window dengan user yang sama
2. Edit stok di window 1
3. Window 2 harus auto update tanpa refresh

Jika window 2 tidak update, berarti event tidak ter-broadcast atau tidak sampai.

