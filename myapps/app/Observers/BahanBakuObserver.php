<?php

namespace App\Observers;

use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\ResepItem;
use App\Events\StokHabis;
use App\Events\StokRendah;
use App\Services\ResepService;
use Illuminate\Support\Facades\Log;

class BahanBakuObserver
{
    protected $stokRendahThreshold;
    protected $resepService;

    public function __construct(ResepService $resepService)
    {
        $this->stokRendahThreshold = config('whatsapp.stok_rendah_threshold', 10);
        $this->resepService = $resepService;
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
            
            // Update stok produk yang menggunakan bahan baku ini
            $this->updateRelatedProductStock($bahanBaku);
            
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
     * Update stok produk yang terkait dengan bahan baku ini
     */
    protected function updateRelatedProductStock(BahanBaku $bahanBaku): void
    {
        try {
            // Cari semua resep yang menggunakan bahan baku ini
            $resepItems = ResepItem::where('bahan_baku_id', $bahanBaku->id)
                ->with(['resep.produk'])
                ->get();

            foreach ($resepItems as $resepItem) {
                if ($resepItem->resep && $resepItem->resep->produk) {
                    $produk = $resepItem->resep->produk;
                    
                    // Skip jika produk bukan dari cabang yang sama
                    if ($produk->id_cabang != $bahanBaku->id_cabang) {
                        continue;
                    }

                    // Hitung maksimal produk yang bisa dibuat
                    $maxProducible = $this->resepService->calculateMaxProducibleQuantity($produk, $bahanBaku->id_cabang);
                    
                    // Update stok produk jika melebihi kapasitas bahan baku
                    if ($produk->stok > $maxProducible) {
                        $stokLama = $produk->stok;
                        $produk->stok = $maxProducible;
                        $produk->saveQuietly(); // Save tanpa trigger observer lagi
                        
                        Log::info("Auto-adjusted product stock: {$produk->nama_produk} from {$stokLama} to {$maxProducible} (ingredient: {$bahanBaku->nama_bahan})");
                        
                        // Broadcast update stok produk
                        try {
                            \App\Events\StokUpdated::dispatch($produk->id_cabang, [
                                'tipe' => 'produk',
                                'id' => $produk->id,
                                'nama' => $produk->nama_produk,
                                'stok' => $produk->stok,
                            ]);
                        } catch (\Exception $e) {
                            Log::warning('Failed to broadcast product stock update: ' . $e->getMessage());
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error updating related product stock: ' . $e->getMessage());
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

