<?php

namespace App\Observers;

use App\Models\BahanBaku;
use App\Events\StokHabis;
use App\Events\StokRendah;
use Illuminate\Support\Facades\Log;

class BahanBakuObserver
{
    protected $stokRendahThreshold;

    public function __construct()
    {
        $this->stokRendahThreshold = config('whatsapp.stok_rendah_threshold', 10);
    }

    /**
     * Handle the BahanBaku "updated" event.
     */
    public function updated(BahanBaku $bahanBaku): void
    {
        if ($bahanBaku->isDirty('stok')) {
            $stokLama = $bahanBaku->getOriginal('stok');
            $stokBaru = $bahanBaku->stok;

            // Cek jika stok berubah menjadi 0 atau kurang
            if ($stokBaru <= 0 && $stokLama > 0) {
                Log::info('Stok habis detected for: ' . $bahanBaku->nama_bahan . ' (Stok: ' . $stokBaru . ')');
                event(new StokHabis($bahanBaku));
            }
            
            // Cek jika stok berubah menjadi rendah (< threshold)
            // Hanya kirim notifikasi jika:
            // 1. Stok baru < threshold
            // 2. Stok lama >= threshold (baru saja masuk ke zona stok rendah)
            // 3. Stok baru > 0 (bukan stok habis, karena sudah ditangani di atas)
            if ($stokBaru > 0 && $stokBaru < $this->stokRendahThreshold && $stokLama >= $this->stokRendahThreshold) {
                Log::info('Stok rendah detected for: ' . $bahanBaku->nama_bahan . ' (Stok: ' . $stokBaru . ')');
                event(new StokRendah($bahanBaku, 'bahan_baku', $stokLama, $stokBaru, $bahanBaku->id_cabang));
            }
            
            // Broadcast stok updated
            try {
                \App\Events\StokUpdated::dispatch($bahanBaku->id_cabang, [
                    'tipe' => 'bahan_baku',
                    'id' => $bahanBaku->id,
                    'nama' => $bahanBaku->nama_bahan,
                    'stok' => $bahanBaku->stok,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to broadcast stock update: ' . $e->getMessage());
            }
        }
    }


    /**
     * Handle the BahanBaku "created" event.
     */
    public function created(BahanBaku $bahanBaku): void
    {
        // Jika bahan baku baru dibuat dengan stok 0, kirim notifikasi
        if ($bahanBaku->stok <= 0) {
            Log::info('Bahan baku baru dengan stok 0: ' . $bahanBaku->nama_bahan);
            // Tidak perlu notifikasi untuk bahan baku baru yang stoknya 0
        }
    }
}

