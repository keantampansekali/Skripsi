# Test Real-time Update

## Cara Test

1. **Buka 2 browser window/tab** dengan user yang sama
2. **Window 1:** Buka halaman `/master/produk`
3. **Window 2:** Buka halaman `/master/produk` (atau halaman lain)
4. **Window 1:** Edit stok produk
5. **Window 2:** Harus auto update tanpa refresh

## Debug Steps

### 1. Cek Console Browser

Setelah edit stok di Window 1, cek console di Window 2:

**Harus ada:**
```
📦 Stock updated event received: {...}
📦 Event data to process: {...}
🔄 updateStockDisplay called with: {...}
🔍 Looking for produk row with id: X
🔍 Found row: <tr>...</tr>
🔍 Found stok cell: <td>...</td>
✅ Updated stok from X to Y
```

### 2. Jika Event Tidak Ter-receive

**Cek:**
- Reverb server masih berjalan
- WebSocket masih connected (console: "✅ WebSocket connected")
- Channel authorization berhasil
- idCabang sama di kedua window

### 3. Jika Event Ter-receive Tapi UI Tidak Update

**Cek:**
- Console log menunjukkan "Found row" dan "Found stok cell"
- Jika "row not found", cek selector di view
- Cek tidak ada JavaScript error

### 4. Test Manual di Console

Buka console browser dan test:

```javascript
// Test update function
updateStockDisplay({
    tipe: 'produk',
    id: 1, // Ganti dengan ID produk yang ada
    nama: 'Test',
    stok: 50
});

// Test selector
const row = document.querySelector('tr[data-produk-id="1"]');
console.log('Row found:', row);
const cell = row?.querySelector('[data-field="stok"]');
console.log('Cell found:', cell);
```

## Expected Behavior

1. Edit stok di Window 1
2. Window 2 console shows: "📦 Stock updated event received"
3. Window 2 console shows: "✅ Updated stok from X to Y"
4. Window 2 UI updates automatically (stok number changes)
5. Window 2 shows notification: "Stok diperbarui: [nama produk]"

## Common Issues

### Issue: Event received tapi row not found
**Solution:** Cek `data-produk-id` attribute di view sesuai dengan ID yang di-broadcast

### Issue: Event received tapi cell not found
**Solution:** Cek `data-field="stok"` attribute ada di cell

### Issue: UI update tapi tidak terlihat
**Solution:** Cek CSS/styling tidak override perubahan

