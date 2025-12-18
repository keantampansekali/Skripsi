# ⚠️ PENTING: Rebuild Assets Setelah Konfigurasi Reverb

## Masalah

Jika Anda melihat "WebSocket disconnected" di console, kemungkinan besar **assets belum di-rebuild** setelah mengubah konfigurasi Reverb.

## Solusi

### 1. Pastikan VITE_REVERB_APP_KEY di .env

Buka file `.env` dan pastikan ada:

```env
REVERB_APP_KEY=057766591543fe0ad798035457f7b77f
VITE_REVERB_APP_KEY=057766591543fe0ad798035457f7b77f
```

**PENTING:** `VITE_REVERB_APP_KEY` harus berisi **nilai langsung**, bukan `${REVERB_APP_KEY}`.

Jika Anda melihat:
```env
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
```

Ubah menjadi:
```env
VITE_REVERB_APP_KEY=057766591543fe0ad798035457f7b77f
```

(Gunakan nilai yang sama dengan `REVERB_APP_KEY`)

### 2. Rebuild Assets

**Untuk Production:**
```bash
cd myapps
npm run build
```

**Untuk Development:**
```bash
cd myapps
npm run dev
```

**PENTING:** Jika menggunakan `npm run dev`, terminal harus tetap berjalan!

### 3. Clear Browser Cache

Setelah rebuild, **hard refresh** browser:
- **Windows:** `Ctrl + Shift + R` atau `Ctrl + F5`
- **Mac:** `Cmd + Shift + R`

### 4. Verifikasi

Buka browser console (F12) dan cek:
- Harus ada log: "Reverb Configuration: ..."
- Harus ada: "✅ WebSocket connected successfully"
- **TIDAK** ada: "WebSocket disconnected"

## Checklist

- [ ] `VITE_REVERB_APP_KEY` di `.env` berisi nilai langsung (bukan variable reference)
- [ ] Assets sudah di-rebuild (`npm run build` atau `npm run dev`)
- [ ] Browser di-hard refresh
- [ ] Reverb server berjalan (`php artisan reverb:start`)
- [ ] Console browser menunjukkan koneksi berhasil

## Mengapa Perlu Rebuild?

Vite membaca environment variables saat **build time**, bukan runtime. Jadi setiap kali Anda mengubah `VITE_*` variables di `.env`, Anda **HARUS** rebuild assets agar perubahan ter-apply.

