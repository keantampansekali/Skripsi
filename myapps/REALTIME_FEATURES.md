# Fitur Real-time WebSocket

Aplikasi ini sekarang memiliki fitur real-time menggunakan Laravel Reverb dan WebSocket. Semua perubahan akan ter-update secara otomatis di semua browser yang terhubung.

## Fitur yang Mendukung Real-time

### 1. Update Stok
- **Bahan Baku**: Stok bahan baku ter-update secara real-time
- **Produk**: Stok produk ter-update secara real-time
- **Notifikasi**: Muncul notifikasi ketika stok berubah

### 2. Transaksi Kasir
- **Transaksi Baru**: Notifikasi ketika ada transaksi baru
- **Update Dashboard**: Statistik dashboard ter-update otomatis
- **Update List**: Daftar transaksi ter-refresh otomatis

### 3. Restock
- **Notifikasi**: Muncul notifikasi ketika ada restock baru
- **Auto Refresh**: Halaman restock ter-refresh otomatis

### 4. Penyesuaian Stok
- **Notifikasi**: Muncul notifikasi ketika ada penyesuaian stok
- **Auto Refresh**: Halaman penyesuaian ter-refresh otomatis

### 5. Waste Management
- **Notifikasi**: Muncul notifikasi ketika ada waste management baru
- **Auto Refresh**: Halaman waste management ter-refresh otomatis

### 6. Alert Stok Rendah
- **Peringatan**: Muncul alert ketika stok < 10
- **Real-time**: Alert muncul segera setelah stok berubah

### 7. Alert Stok Habis
- **Peringatan**: Muncul alert ketika stok habis (0)
- **Real-time**: Alert muncul segera setelah stok habis

## Cara Kerja

1. **Backend**: Controller dan Observer mengirim event melalui Laravel Broadcasting
2. **WebSocket Server**: Laravel Reverb menerima event dan mengirim ke semua client yang terhubung
3. **Frontend**: Laravel Echo mendengarkan channel dan update UI secara otomatis

## Channel yang Digunakan

- `cabang.{idCabang}` - Channel untuk setiap cabang
- Hanya user yang memiliki akses ke cabang tersebut yang bisa mendengarkan

## Event yang Tersedia

- `stok.updated` - Stok diperbarui
- `transaksi.baru` - Transaksi baru
- `restock.created` - Restock baru
- `penyesuaian.created` - Penyesuaian stok baru
- `waste.created` - Waste management baru
- `produk.updated` - Produk diperbarui
- `bahan-baku.updated` - Bahan baku diperbarui
- `stok.rendah` - Stok rendah alert
- `stok.habis` - Stok habis alert

## Testing Real-time

1. Buka aplikasi di 2 browser berbeda (atau tab berbeda)
2. Di browser 1: Lakukan aksi (misalnya: buat transaksi, update stok)
3. Di browser 2: Lihat apakah perubahan muncul secara otomatis tanpa refresh

## Troubleshooting

Jika real-time tidak bekerja:

1. Pastikan Reverb server berjalan: `php artisan reverb:start`
2. Cek browser console untuk error
3. Pastikan konfigurasi `.env` sudah benar
4. Pastikan user sudah login dan memiliki akses ke cabang

