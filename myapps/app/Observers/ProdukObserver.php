<?php

namespace App\Observers;

use App\Models\Produk;
use App\Events\StokHabis;
use App\Events\StokRendah;
use Illuminate\Support\Facades\Log;

class ProdukObserver
{
    protected $stokRendahThreshold;

    public function __construct()
    {
        $this->stokRendahThreshold = config('whatsapp.stok_rendah_threshold', 10);
    }

    /**
     * Handle the Produk "updated" event.
     */
    public function updated(Produk $produk): void
    {
        if ($produk->isDirty('stok')) {
            $stokLama = $produk->getOriginal('stok');
            $stokBaru = $produk->stok;

            // Cek jika stok berubah menjadi 0 atau kurang
            if ($stokBaru <= 0 && $stokLama > 0) {
                Log::info('Stok habis detected for produk: ' . $produk->nama_produk . ' (Stok: ' . $stokBaru . ')');
                // Bisa ditambahkan event StokHabis untuk produk jika diperlukan
                // event(new StokHabis($produk));
            }
            
            // Cek jika stok berubah menjadi rendah (< threshold)
            // Hanya kirim notifikasi jika:
            // 1. Stok baru < threshold
            // 2. Stok lama >= threshold (baru saja masuk ke zona stok rendah)
            if ($stokBaru < $this->stokRendahThreshold && $stokLama >= $this->stokRendahThreshold) {
                Log::info('Stok rendah detected for produk: ' . $produk->nama_produk . ' (Stok: ' . $stokBaru . ')');
                event(new StokRendah($produk, 'produk', $stokLama, $stokBaru, $produk->id_cabang));
            }
            
            // Broadcast stok updated
            try {
                \App\Events\StokUpdated::dispatch($produk->id_cabang, [
                    'tipe' => 'produk',
                    'id' => $produk->id,
                    'nama' => $produk->nama_produk,
                    'stok' => $produk->stok,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to broadcast stock update: ' . $e->getMessage());
            }
        }
    }

    /**
     * Handle the Produk "created" event.
     */
    public function created(Produk $produk): void
    {
        // Broadcast stok created untuk produk baru
        try {
            \App\Events\StokUpdated::dispatch($produk->id_cabang, [
                'tipe' => 'produk',
                'id' => $produk->id,
                'nama' => $produk->nama_produk,
                'stok' => $produk->stok,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to broadcast stock creation: ' . $e->getMessage());
        }
    }
}

