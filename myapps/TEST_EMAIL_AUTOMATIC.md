# Cara Test Email Otomatis

Panduan untuk menguji apakah sistem pengiriman email otomatis berfungsi dengan baik.

## 🚀 Cara Cepat Test (Recommended)

### 1. Test Kirim Email Sekarang (Tanpa Menunggu Schedule)

```bash
php artisan laporan:test-schedule --force
```

Command ini akan:
- ✅ Mengirim email laporan ke semua owner sekarang
- ✅ Menampilkan status pengiriman
- ✅ Tidak perlu menunggu tanggal 1 Januari atau 1 Juli

**Verifikasi:**
- Cek inbox email owner
- Cek folder Spam/Junk
- Cek log: `storage/logs/laravel.log`

---

### 2. Test Kirim Email Langsung

```bash
php artisan laporan:send-6bulanan
```

Command ini akan mengirim email laporan 6 bulanan ke semua owner.

---

### 3. Test Email ke Email Tertentu

```bash
php artisan laporan:test-email your-email@gmail.com
```

Mengirim email test ke email yang Anda tentukan.

---

## 📋 Test Schedule Configuration

### 1. Lihat Schedule yang Terdaftar

```bash
php artisan schedule:list
```

Menampilkan semua schedule yang terdaftar, termasuk:
- Command yang dijalankan
- Waktu eksekusi (cron expression)
- Waktu eksekusi berikutnya
- Status (aktif/tidak)

### 2. Test Schedule Run (Simulasi)

```bash
php artisan schedule:run
```

Menjalankan semua schedule yang seharusnya berjalan saat ini. Jika tidak ada schedule yang harus berjalan, tidak akan terjadi apa-apa.

### 3. Lihat Info Schedule

```bash
php artisan laporan:test-schedule
```

Menampilkan:
- Schedule yang terdaftar
- Waktu eksekusi berikutnya
- Owner yang akan menerima email
- Cara testing

---

## 🔄 Test Schedule Otomatis (Development)

### Untuk Development/Testing

Jalankan scheduler yang berjalan setiap menit (untuk testing):

```bash
php artisan schedule:work
```

Command ini akan:
- ✅ Menjalankan scheduler setiap menit
- ✅ Otomatis menjalankan schedule yang waktunya sudah tiba
- ✅ Cocok untuk development/testing
- ⚠️ Tekan `Ctrl+C` untuk stop

**Catatan:** Command ini hanya untuk development. Untuk production, gunakan cron job.

---

## 🖥️ Setup Cron Job (Production)

Untuk production, setup cron job di server:

### Linux/Unix/Mac

1. Edit crontab:
```bash
crontab -e
```

2. Tambahkan baris berikut:
```bash
* * * * * cd /path/to/your/project/myapps && php artisan schedule:run >> /dev/null 2>&1
```

**Ganti `/path/to/your/project/myapps`** dengan path absolut ke folder `myapps` Anda.

Contoh:
```bash
* * * * * cd /var/www/myapps && php artisan schedule:run >> /dev/null 2>&1
```

3. Verifikasi cron job:
```bash
crontab -l
```

### Windows

1. Buat file batch: `run-scheduler.bat`
2. Isi dengan:
```batch
cd C:\Users\Keannn\Skripsi\myapps
php artisan schedule:run
```

3. Setup Task Scheduler:
   - Buka Task Scheduler
   - Create Basic Task
   - Trigger: Every minute
   - Action: Start a program
   - Program: `C:\path\to\run-scheduler.bat`

---

## ✅ Verifikasi Email Terkirim

### 1. Cek Log Laravel

```bash
# Windows PowerShell
Get-Content storage\logs\laravel.log -Tail 50

# Linux/Mac
tail -f storage/logs/laravel.log
```

Cari log dengan kata kunci:
- `Email laporan berhasil dikirim`
- `Mengirim email laporan ke`

### 2. Cek Email Owner

- ✅ Cek inbox email owner
- ✅ Cek folder Spam/Junk (sangat penting!)
- ✅ Cek folder All Mail (untuk Gmail)
- ✅ Subject email: `Laporan 6 Bulanan - [tanggal] - [tanggal]`

### 3. Verifikasi dengan Command

```bash
php artisan laporan:send-6bulanan --verify
```

Menampilkan email yang sudah dikirim berdasarkan log.

---

## 🧪 Testing Checklist

- [ ] Konfigurasi email sudah benar (cek dengan `php artisan email:check-status`)
- [ ] Test kirim email manual berhasil (`php artisan laporan:test-schedule --force`)
- [ ] Email masuk ke inbox owner
- [ ] Schedule terdaftar dengan benar (`php artisan schedule:list`)
- [ ] Cron job sudah setup (untuk production)
- [ ] Log menunjukkan email berhasil dikirim

---

## ⚠️ Troubleshooting

### Email Tidak Terkirim

1. **Cek konfigurasi email:**
   ```bash
   php artisan email:check-status
   ```

2. **Setup email jika belum:**
   ```bash
   php artisan email:setup your-email@gmail.com "app-password" --test
   ```

3. **Cek log untuk error:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Schedule Tidak Berjalan

1. **Cek schedule terdaftar:**
   ```bash
   php artisan schedule:list
   ```

2. **Test schedule run:**
   ```bash
   php artisan schedule:run
   ```

3. **Cek cron job (production):**
   ```bash
   crontab -l
   ```

### Email Masuk Spam

- ✅ Pastikan `MAIL_FROM_ADDRESS` sama dengan `MAIL_USERNAME`
- ✅ Gunakan email yang valid dan terverifikasi
- ✅ Hindari kata-kata spam di subject/body
- ✅ Cek folder Spam/Junk secara berkala

---

## 📅 Waktu Pengiriman Otomatis

Email akan otomatis terkirim:
- **Tanggal 1 Januari** pukul 09:00 WIB → Laporan periode Juli-Desember tahun sebelumnya
- **Tanggal 1 Juli** pukul 09:00 WIB → Laporan periode Januari-Juni tahun berjalan

---

## 💡 Tips

1. **Test dulu sebelum production:**
   - Gunakan `php artisan laporan:test-schedule --force` untuk test
   - Pastikan email masuk sebelum setup cron job

2. **Development:**
   - Gunakan `php artisan schedule:work` untuk testing
   - Lebih mudah daripada setup cron job

3. **Production:**
   - Setup cron job di server
   - Monitor log secara berkala
   - Pastikan email owner sudah benar

