# 📚 Dokumentasi Lengkap Fitur Sistem Manajemen Restoran

## 🎯 Overview
Sistem manajemen restoran/kafe dengan fitur kasir, inventori, laporan, dan notifikasi WhatsApp. Mendukung multi-cabang dengan manajemen stok produk yang terintegrasi dengan bahan baku.

---

## 🔐 1. AUTHENTICATION & AUTHORIZATION

### Fitur Login/Logout
**Lokasi:**
- Controller: `app/Http/Controllers/AuthController.php`
- View: `resources/views/auth/login.blade.php`
- Route: `/login` (GET/POST), `/logout` (POST)

**Fitur:**
- Login dengan username/email dan password
- Session management
- Multi-role: Owner, Admin, Kasir
- Auto-redirect berdasarkan role

---

## 📊 2. DASHBOARD

### Dashboard Utama
**Lokasi:**
- Controller: `app/Http/Controllers/DashboardController.php`
- View: `resources/views/dashboard/index.blade.php`
- Route: `/dashboard`, `/dashboard/stats` (API)

**Fitur:**
- Statistik real-time (penjualan hari ini, bulan ini)
- Jumlah transaksi
- Stok produk rendah (alert)
- Stok bahan baku rendah (alert)
- Real-time updates via WebSocket/Broadcasting

---

## 💳 3. SISTEM KASIR

### Fitur Utama Kasir
**Lokasi:**
- Controller: `app/Http/Controllers/KasirController.php`
- View: `resources/views/kasir/index.blade.php`
- Route: `/kasir/*`

**Fitur:**
- ✅ **Tampilan Produk Grid** dengan foto
- ✅ **Search produk** real-time
- ✅ **Keranjang belanja** dengan quantity control
- ✅ **Diskon** (Rupiah atau Persentase)
- ✅ **Tax 10%** otomatis
- ✅ **Perhitungan kembalian** otomatis
- ✅ **Struk transaksi** (print-friendly)
- ✅ **Validasi stok real-time** berdasarkan bahan baku
- ✅ **Produk disabled** jika bahan baku tidak cukup
- ✅ **Badge stok** dengan warna:
  - 🟢 Hijau: Stok >= 10
  - 🟡 Kuning: Stok < 10 (stok rendah)
  - 🔴 Merah: Tidak tersedia (bahan baku habis)

**Sub-fitur:**
- **Daftar Transaksi**: `/kasir/transaksi`
- **Print Transaksi**: `/kasir/transaksi/print`
- **Check Availability**: `/kasir/produk/{id}/availability` (API)

**File Terkait:**
- Layout: `resources/views/layouts/kasir.blade.php`

---

## 📦 4. DATA MASTER

### 4.1. Data Produk
**Lokasi:**
- Controller: `app/Http/Controllers/ProdukController.php`
- Views: `resources/views/master/produk/*`
- Route: `/master/produk/*`

**Fitur:**
- ✅ CRUD Produk (Create, Read, Update, Delete)
- ✅ Upload foto produk
- ✅ Validasi stok tidak melebihi kapasitas bahan baku
- ✅ Auto-sinkronisasi stok dengan bahan baku
- ✅ Filter berdasarkan cabang
- ✅ Search produk

**Validasi Khusus:**
- Stok tidak boleh melebihi `max_producible_quantity` (dari bahan baku)
- Real-time warning di form edit

---

### 4.2. Data Bahan Baku
**Lokasi:**
- Controller: `app/Http/Controllers/BahanBakuController.php`
- Views: `resources/views/master/bahan-baku/*`
- Route: `/master/bahan-baku/*`

**Fitur:**
- ✅ CRUD Bahan Baku
- ✅ Satuan dengan format "nilai satuan" (contoh: "500 gram")
- ✅ Harga satuan
- ✅ Auto-update stok produk terkait saat bahan baku berubah
- ✅ Notifikasi WhatsApp saat stok rendah/habis

**Observer:**
- `app/Observers/BahanBakuObserver.php` - Auto-adjust stok produk

---

### 4.3. Data Resep
**Lokasi:**
- Controller: `app/Http/Controllers/ResepController.php`
- Views: `resources/views/master/resep/*`
- Route: `/master/resep/*`

**Fitur:**
- ✅ CRUD Resep
- ✅ Multiple bahan baku per resep
- ✅ Quantity bahan baku per produk
- ✅ Link ke produk
- ✅ Validasi bahan baku tersedia

**Model:**
- `app/Models/Resep.php`
- `app/Models/ResepItem.php`

---

### 4.4. Data Supplier
**Lokasi:**
- Controller: `app/Http/Controllers/SupplierController.php`
- Views: `resources/views/master/supplier/*`
- Route: `/master/supplier/*`

**Fitur:**
- ✅ CRUD Supplier
- ✅ Multiple contact person per supplier
- ✅ Informasi lengkap (alamat, telepon, email)

**Model:**
- `app/Models/Supplier.php`
- `app/Models/SupplierContact.php`

---

### 4.5. Data Pegawai/User
**Lokasi:**
- Controller: `app/Http/Controllers/PegawaiController.php`
- Views: `resources/views/master/pegawai/*`
- Route: `/master/pegawai/*`

**Fitur:**
- ✅ CRUD Pegawai
- ✅ Role management (Owner, Admin, Kasir)
- ✅ Multi-cabang assignment
- ✅ Username, email, no telp
- ✅ Highlight user yang sedang login

**Model:**
- `app/Models/Pegawai.php`

---

## 📥 5. STOK & INVENTORI

### 5.1. Restock (Pembelian Bahan Baku)
**Lokasi:**
- Controller: `app/Http/Controllers/RestockController.php`
- Views: `resources/views/inventori/restock/*`
- Route: `/inventori/restock/*`

**Fitur:**
- ✅ Input nota pembelian
- ✅ Multiple items per restock
- ✅ Auto-increment stok bahan baku
- ✅ Stock movement tracking
- ✅ Link ke supplier
- ✅ Diskon & PPN
- ✅ Broadcast event: `RestockCreated`

**Model:**
- `app/Models/Restock.php`
- `app/Models/RestockItem.php`
- `app/Models/StockMovement.php`

---

### 5.2. Penyesuaian Stok
**Lokasi:**
- Controller: `app/Http/Controllers/PenyesuaianStokController.php`
- Views: `resources/views/inventori/penyesuaian/*`
- Route: `/inventori/penyesuaian/*`

**Fitur:**
- ✅ Adjust stok bahan baku (naik/turun)
- ✅ Multiple items per penyesuaian
- ✅ Keterangan penyesuaian
- ✅ Stock movement tracking
- ✅ Broadcast event: `PenyesuaianStokCreated`
- ✅ Auto-trigger notifikasi stok habis/rendah

---

### 5.3. Waste Management
**Lokasi:**
- Controller: `app/Http/Controllers/WasteManagementController.php`
- Views: `resources/views/inventori/waste-management/*`
- Route: `/inventori/waste-management/*`

**Fitur:**
- ✅ Input waste (produk/bahan baku yang rusak/kadaluarsa)
- ✅ Multiple items per waste
- ✅ Alasan waste
- ✅ Auto-decrement stok
- ✅ Stock movement tracking
- ✅ Bisa delete waste (restore stok)
- ✅ Broadcast event: `WasteCreated`

**Model:**
- `app/Models/Waste.php`
- `app/Models/WasteItem.php`

---

### 5.4. Laporan Stok
**Lokasi:**
- Controller: `app/Http/Controllers/LaporanStokController.php`
- View: `resources/views/inventori/laporan-stok/index.blade.php`
- Route: `/inventori/laporan-stok`

**Fitur:**
- ✅ Laporan stok bahan baku & produk
- ✅ Filter berdasarkan tanggal
- ✅ Export Excel
- ✅ Stock movement history

---

## 📈 6. LAPORAN (REPORTS)

### 6.1. Laporan Penjualan
**Lokasi:**
- Controller: `app/Http/Controllers/LaporanPenjualanController.php`
- View: `resources/views/reports/penjualan/index.blade.php`
- Route: `/reports/penjualan`

**Fitur:**
- ✅ Laporan penjualan per periode
- ✅ Filter berdasarkan tanggal
- ✅ Export Excel
- ✅ Detail per transaksi
- ✅ Total penjualan, diskon, tax

---

### 6.2. Laporan Pembelian
**Lokasi:**
- Controller: `app/Http/Controllers/LaporanPembelianController.php`
- View: `resources/views/reports/pembelian/index.blade.php`
- Route: `/reports/pembelian`

**Fitur:**
- ✅ Laporan pembelian (restock) per periode
- ✅ Filter berdasarkan tanggal
- ✅ Detail per supplier
- ✅ Total pembelian, diskon, PPN

---

### 6.3. Laporan Stok Report
**Lokasi:**
- Controller: `app/Http/Controllers/LaporanStokReportController.php`
- View: `resources/views/reports/stok/index.blade.php`
- Route: `/reports/stok`

**Fitur:**
- ✅ Laporan stok lengkap
- ✅ Export Excel
- ✅ Stok bahan baku & produk

---

## 🔔 7. NOTIFIKASI

### 7.1. WhatsApp Notifications
**Lokasi:**
- Service: `app/Services/WhatsAppService.php`
- Listeners: 
  - `app/Listeners/SendWhatsAppNotification.php` (Stok Habis)
  - `app/Listeners/SendWhatsAppStockLowNotification.php` (Stok Rendah)
- Config: `config/whatsapp.php`

**Fitur:**
- ✅ Notifikasi stok habis (bahan baku)
- ✅ Notifikasi stok rendah (< 10 unit)
- ✅ Deduplication guard (5 menit) - mencegah spam
- ✅ Support multiple provider (Fonnte, Wablas, WhatsApp API)
- ✅ Format pesan dengan emoji & informasi lengkap

**Events:**
- `app/Events/StokHabis.php`
- `app/Events/StokRendah.php`

---

### 7.2. Real-time Broadcasting
**Lokasi:**
- Events: `app/Events/*`
- Config: `config/broadcasting.php`

**Fitur:**
- ✅ Real-time update stok (WebSocket)
- ✅ Notifikasi transaksi baru
- ✅ Update dashboard real-time
- ✅ Channel per cabang

**Events:**
- `StokUpdated.php` - Update stok real-time
- `TransaksiBaru.php` - Notifikasi transaksi baru
- `ProdukUpdated.php` - Update produk
- `RestockCreated.php` - Notifikasi restock
- `PenyesuaianStokCreated.php` - Notifikasi penyesuaian
- `WasteCreated.php` - Notifikasi waste

---

## ⚙️ 8. SISTEM OTOMATISASI

### 8.1. Auto-Sinkronisasi Stok Produk dengan Bahan Baku
**Lokasi:**
- Observer: `app/Observers/BahanBakuObserver.php`
- Service: `app/Services/ResepService.php`
- Command: `app/Console/Commands/SinkronStokProdukDenganBahanBaku.php`

**Fitur:**
- ✅ Auto-adjust stok produk saat bahan baku berubah
- ✅ Hitung maksimal produk yang bisa dibuat
- ✅ Validasi stok tidak melebihi kapasitas
- ✅ Command manual: `php artisan produk:sinkron-stok`

**Method:**
- `ResepService::calculateMaxProducibleQuantity()` - Hitung max produksi
- `ResepService::checkBahanBakuAvailability()` - Cek ketersediaan

---

### 8.2. Observers
**Lokasi:**
- `app/Observers/ProdukObserver.php`
- `app/Observers/BahanBakuObserver.php`

**Fitur:**
- ✅ Auto-trigger event saat stok berubah
- ✅ Auto-adjust stok produk terkait
- ✅ Auto-broadcast update

---

## 🛠️ 9. ARTISAN COMMANDS

### Commands yang Tersedia
**Lokasi:** `app/Console/Commands/`

1. **SinkronStokProdukDenganBahanBaku.php**
   - Command: `php artisan produk:sinkron-stok`
   - Fungsi: Sinkronisasi stok produk dengan bahan baku
   - Options: `--dry-run`, `--cabang=ID`

2. **SendLaporan6Bulanan.php**
   - Command: `php artisan laporan:send-6bulanan`
   - Fungsi: Kirim laporan 6 bulanan ke email owner
   - Options: `--test`, `--verify`

3. **SetupEmail.php**
   - Command: `php artisan email:setup`
   - Fungsi: Setup konfigurasi email

4. **TestEmailLaporan.php**
   - Command: `php artisan email:test-laporan`
   - Fungsi: Test kirim email laporan

5. **CheckEmailStatus.php**
   - Command: `php artisan email:check-status`
   - Fungsi: Cek status email yang sudah dikirim

6. **GenerateSeederFromDatabase.php**
   - Command: `php artisan seeder:generate`
   - Fungsi: Generate seeder dari database existing

---

## 📧 10. EMAIL SYSTEM

### Laporan Email
**Lokasi:**
- Mail: `app/Mail/Laporan6BulananMail.php`
- View: `resources/views/emails/laporan-6bulanan.blade.php`

**Fitur:**
- ✅ Kirim laporan 6 bulanan ke owner
- ✅ Include: Penjualan, Pembelian, Stok
- ✅ Format HTML email
- ✅ Scheduled (bisa di-setup di Kernel.php)

---

## 🔧 11. SERVICES & HELPERS

### Services
**Lokasi:** `app/Services/`

1. **ResepService.php**
   - `checkBahanBakuAvailability()` - Cek ketersediaan bahan baku
   - `reduceBahanBakuFromResep()` - Kurangi bahan baku saat penjualan
   - `calculateMaxProducibleQuantity()` - Hitung max produk yang bisa dibuat
   - `calculateProductCost()` - Hitung COGS

2. **WhatsAppService.php**
   - `sendMessage()` - Kirim pesan WhatsApp
   - `sendStockEmptyNotification()` - Notifikasi stok habis
   - `sendStockLowNotification()` - Notifikasi stok rendah
   - Support multiple provider

### Helpers
**Lokasi:** `app/Helpers/`

1. **BranchHelper.php**
   - Helper untuk manajemen cabang
   - Session management cabang aktif

---

## 📱 12. REAL-TIME FEATURES

### WebSocket/Broadcasting
**Lokasi:**
- Config: `config/broadcasting.php`
- Events: `app/Events/*`
- JavaScript: `resources/js/realtime.js`

**Fitur:**
- ✅ Real-time update stok di dashboard
- ✅ Real-time update kasir
- ✅ Notifikasi transaksi baru
- ✅ Channel per cabang: `cabang.{id}`

---

## 🗄️ 13. DATABASE STRUCTURE

### Models
**Lokasi:** `app/Models/`

**Master Data:**
- `Produk.php` - Produk yang dijual
- `BahanBaku.php` - Bahan baku untuk produksi
- `Resep.php` - Resep produk
- `ResepItem.php` - Item bahan baku dalam resep
- `Supplier.php` - Supplier bahan baku
- `SupplierContact.php` - Kontak supplier
- `Pegawai.php` - User/Pegawai
- `Cabang.php` - Cabang/Outlet

**Transaksi:**
- `TransaksiKasir.php` - Transaksi penjualan
- `TransaksiKasirItem.php` - Item dalam transaksi

**Inventori:**
- `Restock.php` - Nota pembelian
- `RestockItem.php` - Item pembelian
- `PenyesuaianStok.php` - Penyesuaian stok
- `PenyesuaianItem.php` - Item penyesuaian
- `Waste.php` - Waste management
- `WasteItem.php` - Item waste
- `StockMovement.php` - History pergerakan stok

---

## 🔄 14. WORKFLOW SISTEM

### Alur Penjualan (Kasir)
```
1. User pilih produk di kasir
   ↓
2. Sistem cek max_producible_quantity (dari bahan baku)
   ↓
3. Jika stok cukup → Add to cart
   ↓
4. User input pembayaran
   ↓
5. Save transaksi → Kurangi stok produk
   ↓
6. Kurangi bahan baku sesuai resep (ResepService)
   ↓
7. Broadcast update stok
   ↓
8. Cek stok rendah → Trigger WhatsApp (jika perlu)
```

### Alur Restock
```
1. Input nota pembelian
   ↓
2. Input items (bahan baku + qty)
   ↓
3. Save → Increment stok bahan baku
   ↓
4. BahanBakuObserver → Auto-adjust stok produk terkait
   ↓
5. Broadcast update
```

### Alur Sinkronisasi Stok
```
1. Bahan baku berubah (restock/waste/penyesuaian)
   ↓
2. BahanBakuObserver triggered
   ↓
3. Cari produk yang pakai bahan baku ini
   ↓
4. Hitung max_producible_quantity
   ↓
5. Jika stok produk > max → Auto-adjust ke max
   ↓
6. Broadcast update
```

---

## 🎨 15. UI/UX FEATURES

### Design System
- **Dark Mode** support
- **Responsive** design
- **Modern gradient** backgrounds
- **Glass morphism** effects
- **Real-time search** dengan debounce
- **Smooth animations**

### Color Coding
- 🟢 **Hijau**: Stok normal (>= 10)
- 🟡 **Kuning**: Stok rendah (< 10)
- 🔴 **Merah**: Tidak tersedia / Habis
- 🟣 **Ungu**: Owner role
- 🔴 **Merah**: Admin role
- 🔵 **Biru**: Kasir role

---

## 📋 16. VALIDASI & KEAMANAN

### Validasi
- ✅ Stok produk tidak melebihi kapasitas bahan baku
- ✅ Produk tanpa resep tidak muncul di kasir
- ✅ Validasi quantity berdasarkan stok tersedia
- ✅ Validasi pembayaran (minimal = total)

### Keamanan
- ✅ Authentication required
- ✅ CSRF protection
- ✅ Role-based access
- ✅ Session management
- ✅ Input sanitization

---

## 🚀 17. DEPLOYMENT & CONFIGURATION

### Environment Variables
- Database configuration
- WhatsApp API keys
- Email configuration
- Broadcasting driver

### Config Files
- `config/whatsapp.php` - WhatsApp settings
- `config/broadcasting.php` - Real-time settings
- `config/mail.php` - Email settings

---

## 📝 18. SEEDERS

**Lokasi:** `database/seeders/`

- `DatabaseSeeder.php` - Main seeder
- `CabangSeeder.php` - Seed cabang
- `UserSeeder.php` - Seed users
- `ProdukSeeder.php` - Seed produk
- `BahanBakuSeeder.php` - Seed bahan baku
- `ResepSeeder.php` - Seed resep + items
- `SupplierSeeder.php` - Seed supplier
- `RestockSeeder.php` - Seed restock
- `PenyesuaianStokSeeder.php` - Seed penyesuaian
- `WasteSeeder.php` - Seed waste

**Note:** Setelah seed, jalankan `php artisan produk:sinkron-stok` untuk sinkronisasi stok.

---

## 🎯 SUMMARY FITUR UTAMA

### ✅ Fitur Core
1. **Sistem Kasir** - Penjualan dengan validasi real-time
2. **Manajemen Stok** - Auto-sinkronisasi produk dengan bahan baku
3. **Resep Management** - Link produk dengan bahan baku
4. **Multi-cabang** - Support multiple outlet
5. **Real-time Updates** - WebSocket broadcasting
6. **Notifikasi WhatsApp** - Auto-notifikasi stok rendah/habis
7. **Laporan Lengkap** - Penjualan, pembelian, stok
8. **Role Management** - Owner, Admin, Kasir

### ✅ Fitur Advanced
1. **Auto-sinkronisasi** - Stok produk selalu sesuai bahan baku
2. **Deduplication** - Mencegah spam notifikasi
3. **Stock Movement Tracking** - History lengkap pergerakan stok
4. **Email Reports** - Laporan 6 bulanan otomatis
5. **Export Excel** - Semua laporan bisa di-export

---

**Total Routes:** ~40+ routes  
**Total Controllers:** 16 controllers  
**Total Models:** 17+ models  
**Total Views:** 30+ views  
**Total Commands:** 6 artisan commands

