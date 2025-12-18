# WebSocket Implementation Files

Dokumen ini menjelaskan file-file penting untuk implementasi WebSocket menggunakan Laravel Reverb.

## 📁 File-file Utama

### 1. Frontend - JavaScript Files

#### `resources/js/bootstrap.js`
**Fungsi:** Initialize Laravel Echo dan konfigurasi WebSocket connection
- Import Laravel Echo dan Pusher JS
- Setup koneksi ke Reverb server
- Konfigurasi host, port, key, dan authentication
- **Lokasi:** `myapps/resources/js/bootstrap.js`

#### `resources/js/realtime.js`
**Fungsi:** Event listeners dan handler untuk real-time updates
- Listen ke berbagai event (stok.updated, transaksi.baru, dll)
- Function untuk update UI secara real-time
- Helper functions untuk format dan notification
- **Lokasi:** `myapps/resources/js/realtime.js`

#### `resources/js/app.js`
**Fungsi:** Entry point untuk JavaScript
- Import bootstrap.js dan realtime.js
- **Lokasi:** `myapps/resources/js/app.js`

### 2. Backend - Event Files

#### `app/Events/StokUpdated.php`
**Fungsi:** Event untuk broadcast update stok
- Broadcast ke channel `cabang.{idCabang}`
- Event name: `stok.updated`
- **Lokasi:** `myapps/app/Events/StokUpdated.php`

#### `app/Events/TransaksiBaru.php`
**Fungsi:** Event untuk broadcast transaksi baru
- **Lokasi:** `myapps/app/Events/TransaksiBaru.php`

#### `app/Events/ProdukUpdated.php`
**Fungsi:** Event untuk broadcast update produk
- **Lokasi:** `myapps/app/Events/ProdukUpdated.php`

#### `app/Events/BahanBakuUpdated.php`
**Fungsi:** Event untuk broadcast update bahan baku
- **Lokasi:** `myapps/app/Events/BahanBakuUpdated.php`

#### `app/Events/RestockCreated.php`
**Fungsi:** Event untuk broadcast restock baru
- **Lokasi:** `myapps/app/Events/RestockCreated.php`

#### `app/Events/PenyesuaianStokCreated.php`
**Fungsi:** Event untuk broadcast penyesuaian stok
- **Lokasi:** `myapps/app/Events/PenyesuaianStokCreated.php`

#### `app/Events/WasteCreated.php`
**Fungsi:** Event untuk broadcast waste management
- **Lokasi:** `myapps/app/Events/WasteCreated.php`

#### `app/Events/StokRendah.php`
**Fungsi:** Event untuk broadcast alert stok rendah
- **Lokasi:** `myapps/app/Events/StokRendah.php`

#### `app/Events/StokHabis.php`
**Fungsi:** Event untuk broadcast alert stok habis
- **Lokasi:** `myapps/app/Events/StokHabis.php`

### 3. Backend - Controller Files (Broadcast Events)

#### `app/Http/Controllers/ProdukController.php`
**Fungsi:** Broadcast event saat create/update produk
- Line ~93-101: Broadcast saat create produk
- Line ~188-204: Broadcast saat update produk
- **Lokasi:** `myapps/app/Http/Controllers/ProdukController.php`

#### `app/Http/Controllers/BahanBakuController.php`
**Fungsi:** Broadcast event saat update bahan baku
- **Lokasi:** `myapps/app/Http/Controllers/BahanBakuController.php`

#### `app/Http/Controllers/KasirController.php`
**Fungsi:** Broadcast event saat transaksi baru
- **Lokasi:** `myapps/app/Http/Controllers/KasirController.php`

#### `app/Http/Controllers/RestockController.php`
**Fungsi:** Broadcast event saat restock
- **Lokasi:** `myapps/app/Http/Controllers/RestockController.php`

#### `app/Http/Controllers/PenyesuaianStokController.php`
**Fungsi:** Broadcast event saat penyesuaian stok
- **Lokasi:** `myapps/app/Http/Controllers/PenyesuaianStokController.php`

#### `app/Http/Controllers/WasteManagementController.php`
**Fungsi:** Broadcast event saat waste management
- **Lokasi:** `myapps/app/Http/Controllers/WasteManagementController.php`

### 4. Configuration Files

#### `config/reverb.php`
**Fungsi:** Konfigurasi Reverb server
- Server host, port, path
- App credentials (key, secret, app_id)
- Scaling configuration
- **Lokasi:** `myapps/config/reverb.php`

#### `config/broadcasting.php`
**Fungsi:** Konfigurasi broadcasting driver
- Default broadcaster: `reverb`
- Reverb connection configuration
- **Lokasi:** `myapps/config/broadcasting.php`

#### `.env`
**Fungsi:** Environment variables untuk Reverb
- `BROADCAST_CONNECTION=reverb`
- `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`
- `REVERB_HOST`, `REVERB_PORT`, `REVERB_SCHEME`
- `VITE_REVERB_APP_KEY`, `VITE_REVERB_HOST`, dll
- **Lokasi:** `myapps/.env`

### 5. Channel Authorization

#### `routes/channels.php`
**Fungsi:** Authorization untuk broadcasting channels
- Channel `cabang.{idCabang}` authorization
- Cek user access ke cabang
- **Lokasi:** `myapps/routes/channels.php`

### 6. Layout Files (Include JavaScript)

#### `resources/views/layouts/dashboard.blade.php`
**Fungsi:** Include JavaScript files di layout
- Line ~833: `@vite(['resources/js/app.js'])`
- Set `window.idCabang` untuk real-time updates
- **Lokasi:** `myapps/resources/views/layouts/dashboard.blade.php`

#### `resources/views/layouts/kasir.blade.php`
**Fungsi:** Include JavaScript files di layout kasir
- Line ~32: `@vite(['resources/js/app.js'])`
- **Lokasi:** `myapps/resources/views/layouts/kasir.blade.php`

### 7. Build Configuration

#### `vite.config.js`
**Fungsi:** Vite configuration untuk build assets
- Input files: `resources/js/app.js`
- **Lokasi:** `myapps/vite.config.js`

#### `package.json`
**Fungsi:** Dependencies untuk WebSocket
- `laravel-echo`: Laravel Echo client
- `pusher-js`: Pusher JavaScript client
- **Lokasi:** `myapps/package.json`

### 8. Server Scripts

#### `start-reverb-server.bat`
**Fungsi:** Script untuk start Reverb server (Windows)
- **Lokasi:** `myapps/start-reverb-server.bat`

## 📊 Flow WebSocket Implementation

### 1. **Initialization** (Frontend)
```
resources/js/bootstrap.js
  ↓
Initialize Echo with Reverb config
  ↓
resources/js/realtime.js
  ↓
Setup event listeners
```

### 2. **Event Broadcasting** (Backend)
```
Controller (e.g., ProdukController.php)
  ↓
broadcast(new StokUpdated(...))
  ↓
app/Events/StokUpdated.php
  ↓
Reverb Server
  ↓
Broadcast to channel: cabang.{idCabang}
```

### 3. **Event Reception** (Frontend)
```
Reverb Server
  ↓
WebSocket connection
  ↓
resources/js/realtime.js
  ↓
Event listener: channel.listen('.stok.updated')
  ↓
Update UI: updateStockDisplay()
```

## 🎯 Key Points untuk Presentasi

1. **Frontend:** `resources/js/bootstrap.js` dan `resources/js/realtime.js`
2. **Backend Events:** `app/Events/StokUpdated.php` dan event lainnya
3. **Broadcasting:** Controller files yang memanggil `broadcast()`
4. **Configuration:** `config/reverb.php` dan `config/broadcasting.php`
5. **Authorization:** `routes/channels.php`

## 📝 Summary

**File paling penting untuk ditunjukkan:**
1. `resources/js/bootstrap.js` - Setup WebSocket connection
2. `resources/js/realtime.js` - Event handlers dan UI updates
3. `app/Events/StokUpdated.php` - Contoh event broadcasting
4. `app/Http/Controllers/ProdukController.php` - Contoh broadcast dari controller
5. `config/reverb.php` - Konfigurasi Reverb server
6. `routes/channels.php` - Channel authorization

File-file ini menunjukkan alur lengkap implementasi WebSocket dari frontend ke backend.

