<?php

namespace App\Listeners;

use App\Events\StokHabis;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotification
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
    public function handle(StokHabis $event): void
    {
        // Cek apakah notifikasi WhatsApp diaktifkan
        if (!config('whatsapp.enabled', true)) {
            Log::info('WhatsApp notifications are disabled');
            return;
        }

        try {
            $bahanBaku = $event->bahanBaku;
            
            // Deduplication: cek apakah notifikasi untuk bahan baku ini sudah dikirim dalam 5 menit terakhir
            $cacheKey = "whatsapp_stok_habis_bahan_baku_{$bahanBaku->id}_cabang_{$bahanBaku->id_cabang}";
            if (Cache::has($cacheKey)) {
                Log::info("WhatsApp notification skipped (deduplication): bahan_baku ID {$bahanBaku->id} at cabang {$bahanBaku->id_cabang}");
                return;
            }
            
            // Ambil nama cabang jika ada
            $cabangName = null;
            if ($bahanBaku->id_cabang) {
                $cabang = \App\Models\Cabang::find($bahanBaku->id_cabang);
                $cabangName = $cabang ? $cabang->nama_cabang : null;
            }

            // Kirim notifikasi WhatsApp
            $result = $this->whatsappService->sendStockEmptyNotification(
                $bahanBaku->nama_bahan,
                $bahanBaku->satuan,
                $cabangName
            );

            if ($result['success']) {
                // Simpan flag di cache selama 5 menit untuk mencegah duplikasi
                Cache::put($cacheKey, true, now()->addMinutes(5));
                Log::info('WhatsApp notification sent successfully for: ' . $bahanBaku->nama_bahan);
            } else {
                Log::warning('Failed to send WhatsApp notification: ' . ($result['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp notification: ' . $e->getMessage());
        }
    }
}

