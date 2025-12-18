<?php

namespace App\Listeners;

use App\Events\StokRendah;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendWhatsAppStockLowNotification
{
    protected $whatsappService;

    /**
     * Create the event listener.
     */
    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Handle the event.
     */
    public function handle(StokRendah $event): void
    {
        // Cek apakah notifikasi WhatsApp diaktifkan
        if (!config('whatsapp.enabled', true)) {
            Log::info('WhatsApp notifications are disabled');
            return;
        }

        try {
            $item = $event->item;
            $tipe = $event->tipe;
            $stokBaru = $event->stokBaru;
            $idCabang = $event->idCabang;
            
            // Deduplication: cek apakah notifikasi untuk item ini sudah dikirim dalam 5 menit terakhir
            $cacheKey = "whatsapp_stok_rendah_{$tipe}_{$item->id}_cabang_{$idCabang}";
            if (Cache::has($cacheKey)) {
                Log::info("WhatsApp notification skipped (deduplication): {$tipe} ID {$item->id} at cabang {$idCabang}");
                return;
            }
            
            // Ambil nama cabang jika ada
            $cabangName = null;
            if (isset($item->id_cabang) && $item->id_cabang) {
                $cabang = \App\Models\Cabang::find($item->id_cabang);
                $cabangName = $cabang ? $cabang->nama_cabang : null;
            }

            // Tentukan nama item dan satuan berdasarkan tipe
            if ($tipe === 'bahan_baku') {
                $itemName = $item->nama_bahan;
                $satuan = $item->satuan;
            } else {
                $itemName = $item->nama_produk;
                $satuan = null; // Produk tidak punya satuan
            }

            // Kirim notifikasi WhatsApp
            $result = $this->whatsappService->sendStockLowNotification(
                $itemName,
                $tipe,
                $stokBaru,
                $satuan,
                $cabangName
            );

            if ($result['success']) {
                // Simpan flag di cache selama 5 menit untuk mencegah duplikasi
                Cache::put($cacheKey, true, now()->addMinutes(5));
                Log::info('WhatsApp low stock notification sent successfully for: ' . $itemName);
            } else {
                Log::warning('Failed to send WhatsApp low stock notification: ' . ($result['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp low stock notification: ' . $e->getMessage());
        }
    }
}

