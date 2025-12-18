# Reverb Credentials - Di mana menemukannya?

Setelah menjalankan `php artisan reverb:install`, credentials **otomatis ditambahkan** ke file `.env` Anda.

## ✅ Credentials sudah ada di file `.env`

Buka file `.env` di root project Anda dan cari baris berikut:

```env
REVERB_APP_ID=reverb-1467e0ea12d38e77
REVERB_APP_KEY=057766591543fe0ad798035457f7b77f
REVERB_APP_SECRET=099c4bea40e80a211e0574d7f24a2396e6890cd57e0de3b3254d2a2b30e35e74
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite Environment Variables
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

## 📝 Catatan Penting

1. **Credentials sudah di-generate otomatis** - Tidak perlu generate manual
2. **Jangan share credentials** - Ini adalah kunci rahasia untuk aplikasi Anda
3. **Pastikan VITE_REVERB_APP_KEY sama dengan REVERB_APP_KEY** - Penting untuk frontend

## 🔍 Cara melihat credentials

### Windows (PowerShell):
```powershell
cd myapps
Get-Content .env | Select-String -Pattern "REVERB"
```

### Linux/Mac:
```bash
cd myapps
grep REVERB .env
```

## ⚠️ Jika ada duplikasi

Jika Anda melihat credentials duplikat di file `.env`, gunakan yang pertama (yang lebih atas). Hapus duplikatnya.

## 🚀 Langkah selanjutnya

1. **Pastikan credentials ada di .env** ✅ (sudah ada)
2. **Clear cache:**
   ```bash
   php artisan config:clear
   ```
3. **Rebuild assets:**
   ```bash
   npm run build
   ```
4. **Start Reverb server:**
   ```bash
   php artisan reverb:start
   ```

## 🔑 Generate credentials baru (jika perlu)

Jika Anda ingin generate credentials baru, edit file `.env` dan ganti dengan nilai baru:

```env
REVERB_APP_ID=your-new-app-id
REVERB_APP_KEY=your-new-app-key
REVERB_APP_SECRET=your-new-app-secret
```

Atau gunakan random string generator untuk keamanan yang lebih baik.

