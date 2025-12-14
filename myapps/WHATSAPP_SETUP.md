# Setup WhatsApp API untuk Notifikasi Stok Habis

Fitur ini akan mengirim notifikasi WhatsApp otomatis ketika stok bahan baku habis (stok <= 0).

## Konfigurasi

### 1. Tambahkan variabel ke file `.env`

Tambahkan konfigurasi berikut ke file `.env` Anda:

```env
# WhatsApp API Configuration
WHATSAPP_PROVIDER=fonnte
WHATSAPP_API_URL=https://api.fonnte.com/send
WHATSAPP_API_KEY=your_api_key_here
WHATSAPP_PHONE_NUMBER=6281234567890
WHATSAPP_ENABLED=true
WHATSAPP_STOK_RENDAH_THRESHOLD=10
```

### 2. Provider yang Didukung

#### Fonnte (Default)
- **URL**: `https://api.fonnte.com/send`
- **API Key**: Dapatkan dari https://fonnte.com
- **Format Header**: `Authorization: YOUR_API_KEY`

#### Wablas
- **URL**: `https://api.wablas.com/api/send-message`
- **API Key**: Dapatkan dari https://wablas.com
- **Format Header**: `Authorization: YOUR_API_KEY`

#### WhatsApp API (Generic)
- **URL**: Sesuai dengan provider Anda
- **API Key**: Token dari provider
- **Format Header**: `Authorization: Bearer YOUR_TOKEN`

### 3. Format Nomor Telepon

Nomor telepon harus dalam format internasional tanpa tanda `+`:
- ✅ Benar: `6281234567890`
- ❌ Salah: `+6281234567890` atau `081234567890`

Sistem akan otomatis mengkonversi nomor yang dimulai dengan `0` menjadi format `62`.

## Cara Kerja

1. **Observer**: `BahanBakuObserver` akan memantau setiap perubahan pada model `BahanBaku`
2. **Event**: Ketika stok berubah dari > 0 menjadi <= 0, event `StokHabis` akan dipicu
3. **Listener**: `SendWhatsAppNotification` akan menangani event dan mengirim pesan WhatsApp
4. **Service**: `WhatsAppService` akan mengirim pesan melalui API yang dikonfigurasi

## Testing

Untuk menguji fitur ini:

1. Pastikan konfigurasi WhatsApp sudah benar di `.env`
2. Update stok bahan baku menjadi 0 melalui:
   - Restock (mengurangi stok)
   - Waste Management
   - Penyesuaian Stok
   - Edit Bahan Baku langsung
3. Sistem akan otomatis mengirim notifikasi WhatsApp

## Menonaktifkan Notifikasi

Untuk menonaktifkan notifikasi sementara, ubah di `.env`:

```env
WHATSAPP_ENABLED=false
```

## Troubleshooting

### Notifikasi tidak terkirim

1. **Cek Log**: Lihat file `storage/logs/laravel.log` untuk error message
2. **Cek Konfigurasi**: Pastikan `WHATSAPP_API_KEY` dan `WHATSAPP_PHONE_NUMBER` sudah benar
3. **Cek API Key**: Pastikan API key masih aktif dan memiliki kredit
4. **Cek Format Nomor**: Pastikan nomor dalam format yang benar (62xxxxxxxxxx)

### Error: "Nomor WhatsApp tidak dikonfigurasi"

Pastikan `WHATSAPP_PHONE_NUMBER` sudah diisi di file `.env`

### Error: "Gagal mengirim pesan"

1. Cek koneksi internet
2. Cek apakah API key masih valid
3. Cek apakah provider API sedang down
4. Lihat detail error di log file

## Catatan

### Notifikasi Stok Habis
- Notifikasi hanya akan dikirim sekali ketika stok berubah dari > 0 menjadi <= 0
- Jika stok sudah 0 dan diupdate menjadi 0 lagi, notifikasi tidak akan dikirim
- Notifikasi akan mencakup informasi: nama bahan baku, satuan, nama cabang, dan waktu
- Notifikasi akan otomatis terkirim ketika stok habis melalui:
  - Waste Management (pengurangan stok)
  - Penyesuaian Stok (jika diubah menjadi 0)
  - Edit Bahan Baku langsung (jika stok diubah menjadi 0)

### Notifikasi Stok Rendah
- Notifikasi akan dikirim ketika stok turun di bawah threshold (default: 10)
- Notifikasi hanya dikirim sekali ketika stok berubah dari >= threshold menjadi < threshold
- Jika stok sudah < threshold dan diupdate lagi, notifikasi tidak akan dikirim lagi
- Berlaku untuk **Produk** dan **Bahan Baku**
- Notifikasi akan otomatis terkirim ketika stok rendah melalui:
  - Transaksi Kasir (penjualan produk)
  - Waste Management (pengurangan stok)
  - Penyesuaian Stok (jika diubah menjadi < threshold)
  - Edit Produk/Bahan Baku langsung (jika stok diubah menjadi < threshold)

## Struktur File yang Dibuat

### File Utama
1. **app/Services/WhatsAppService.php** - Service untuk mengirim pesan WhatsApp
2. **config/whatsapp.php** - File konfigurasi WhatsApp
3. **app/Providers/AppServiceProvider.php** - Provider yang mendaftarkan observer dan listener

### Event & Listener
4. **app/Events/StokHabis.php** - Event yang dipicu ketika stok habis
5. **app/Events/StokRendah.php** - Event yang dipicu ketika stok rendah (< threshold)
6. **app/Listeners/SendWhatsAppNotification.php** - Listener untuk notifikasi stok habis
7. **app/Listeners/SendWhatsAppStockLowNotification.php** - Listener untuk notifikasi stok rendah

### Observer
8. **app/Observers/BahanBakuObserver.php** - Observer yang memantau perubahan stok bahan baku
9. **app/Observers/ProdukObserver.php** - Observer yang memantau perubahan stok produk

## Testing Manual

Untuk menguji fitur ini secara manual:

1. Pastikan konfigurasi WhatsApp sudah benar
2. Buka aplikasi dan masuk ke menu Waste Management atau Penyesuaian Stok
3. Kurangi stok bahan baku yang memiliki stok > 0 menjadi 0
4. Cek WhatsApp Anda, seharusnya menerima notifikasi

## Contoh Pesan Notifikasi

### Notifikasi Stok Habis
```
⚠️ *PERINGATAN: STOK BAHAN BAKU HABIS*

Bahan baku berikut sudah habis:
📦 *Nama:* Tepung Terigu
📏 *Satuan:* gram
🏢 *Cabang:* Cabang Utama

Mohon segera lakukan restock untuk bahan baku ini.

Waktu: 15/01/2025 14:30:45
```

### Notifikasi Stok Rendah
```
⚠️ *PERINGATAN: STOK PRODUK RENDAH*

Produk berikut memiliki stok rendah:
📦 *Nama:* Burger Sapi 1
📊 *Stok Saat Ini:* 7 unit
⚠️ *Threshold:* < 10 unit
🏢 *Cabang:* Cabang Utama

Mohon segera lakukan restock untuk Produk ini sebelum stok habis.

Waktu: 15/01/2025 14:30:45
```

atau untuk Bahan Baku:
```
⚠️ *PERINGATAN: STOK BAHAN BAKU RENDAH*

Bahan Baku berikut memiliki stok rendah:
📦 *Nama:* Tepung Terigu
📏 *Satuan:* gram
📊 *Stok Saat Ini:* 8 gram
⚠️ *Threshold:* < 10 gram
🏢 *Cabang:* Cabang Utama

Mohon segera lakukan restock untuk Bahan Baku ini sebelum stok habis.

Waktu: 15/01/2025 14:30:45
```

