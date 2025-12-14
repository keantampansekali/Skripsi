# Setup WebSocket dengan Laravel Reverb

Aplikasi ini menggunakan Laravel Reverb untuk real-time updates melalui WebSocket.

## Instalasi

1. Package sudah terinstall:
   - `laravel/reverb` (backend)
   - `laravel-echo` dan `pusher-js` (frontend)

## Konfigurasi

### 1. Environment Variables

Tambahkan konfigurasi berikut ke file `.env`:

```env
# Broadcasting
BROADCAST_CONNECTION=reverb

# Reverb Configuration
REVERB_APP_ID=my-app-id
REVERB_APP_KEY=my-app-key
REVERB_APP_SECRET=my-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Untuk production, gunakan:
# REVERB_SCHEME=https
# REVERB_PORT=443
```

### 2. Generate Reverb Keys

Jalankan perintah berikut untuk generate keys:

```bash
php artisan reverb:install
```

Atau generate keys secara manual:

```bash
php artisan key:generate --force
```

Kemudian copy key yang dihasilkan ke `REVERB_APP_KEY` di `.env`.

### 3. Vite Configuration

Pastikan file `vite.config.js` sudah mengimport `resources/js/app.js` dan `resources/js/realtime.js`.

## Menjalankan Server

### Development

1. **Jalankan Laravel Reverb Server** (terminal terpisah):
   ```bash
   php artisan reverb:start
   ```

2. **Jalankan Laravel Application**:
   ```bash
   php artisan serve
   ```

3. **Jalankan Vite Dev Server** (jika menggunakan Vite):
   ```bash
   npm run dev
   ```

### Production

Untuk production, gunakan process manager seperti Supervisor atau PM2 untuk menjalankan Reverb server.

**Contoh Supervisor Configuration** (`/etc/supervisor/conf.d/reverb.conf`):

```ini
[program:reverb]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/app/artisan reverb:start
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/reverb.log
stopwaitsecs=3600
```

## Fitur Real-time

Aplikasi ini memiliki real-time updates untuk:

1. **Stok Updates** - Update stok bahan baku dan produk secara real-time
2. **Transaksi Baru** - Notifikasi transaksi baru dari kasir
3. **Restock** - Notifikasi ketika ada restock baru
4. **Penyesuaian Stok** - Notifikasi penyesuaian stok
5. **Waste Management** - Notifikasi waste management baru
6. **Stok Rendah** - Alert ketika stok rendah (< 10)
7. **Stok Habis** - Alert ketika stok habis

## Testing

Untuk test WebSocket connection:

1. Buka aplikasi di browser
2. Buka Developer Console (F12)
3. Cek apakah ada pesan "Echo or idCabang not available" atau error lainnya
4. Lakukan aksi (misalnya: buat transaksi, update stok)
5. Cek apakah notifikasi muncul dan data ter-update secara real-time

## Troubleshooting

### WebSocket tidak terhubung

1. Pastikan Reverb server berjalan: `php artisan reverb:start`
2. Cek konfigurasi di `.env` sudah benar
3. Cek browser console untuk error
4. Pastikan port 8080 tidak digunakan aplikasi lain

### Notifikasi tidak muncul

1. Cek apakah event di-broadcast dengan benar (lihat logs)
2. Pastikan channel authorization berhasil (cek `routes/channels.php`)
3. Cek browser console untuk error JavaScript

### CORS Error

Jika ada CORS error, pastikan `REVERB_HOST` di `.env` sesuai dengan domain aplikasi.

## Catatan

- Reverb server harus berjalan terpisah dari Laravel application
- Untuk production, gunakan HTTPS dan WSS (WebSocket Secure)
- Pastikan firewall mengizinkan koneksi ke port Reverb

