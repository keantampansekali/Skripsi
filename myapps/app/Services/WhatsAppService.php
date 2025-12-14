<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $apiKey;
    protected $phoneNumber;

    public function __construct()
    {
        $this->apiUrl = config('whatsapp.api_url');
        $this->apiKey = config('whatsapp.api_key');
        $this->phoneNumber = config('whatsapp.phone_number');
    }

    /**
     * Mengirim pesan WhatsApp
     *
     * @param string $to Nomor tujuan (format: 6281234567890)
     * @param string $message Pesan yang akan dikirim
     * @return array
     */
    public function sendMessage(string $to, string $message): array
    {
        try {
            // Format nomor: hapus karakter non-digit dan pastikan dimulai dengan 62
            $to = $this->formatPhoneNumber($to);

            // Pilih provider berdasarkan config
            $provider = config('whatsapp.provider', 'fonnte');

            switch ($provider) {
                case 'fonnte':
                    return $this->sendViaFonnte($to, $message);
                case 'wablas':
                    return $this->sendViaWablas($to, $message);
                case 'whatsapp-api':
                    return $this->sendViaWhatsAppAPI($to, $message);
                default:
                    return $this->sendViaFonnte($to, $message);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Service Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal mengirim pesan WhatsApp: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Mengirim via Fonnte API
     */
    protected function sendViaFonnte(string $to, string $message): array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->post($this->apiUrl, [
            'target' => $to,
            'message' => $message,
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'Pesan berhasil dikirim',
                'data' => $response->json()
            ];
        }

        return [
            'success' => false,
            'message' => 'Gagal mengirim pesan: ' . $response->body(),
            'data' => $response->json()
        ];
    }

    /**
     * Mengirim via Wablas API
     */
    protected function sendViaWablas(string $to, string $message): array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->post($this->apiUrl, [
            'phone' => $to,
            'message' => $message,
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'Pesan berhasil dikirim',
                'data' => $response->json()
            ];
        }

        return [
            'success' => false,
            'message' => 'Gagal mengirim pesan: ' . $response->body(),
            'data' => $response->json()
        ];
    }

    /**
     * Mengirim via WhatsApp API (generic)
     */
    protected function sendViaWhatsAppAPI(string $to, string $message): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, [
            'phone' => $to,
            'message' => $message,
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'Pesan berhasil dikirim',
                'data' => $response->json()
            ];
        }

        return [
            'success' => false,
            'message' => 'Gagal mengirim pesan: ' . $response->body(),
            'data' => $response->json()
        ];
    }

    /**
     * Format nomor telepon ke format internasional (62)
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Hapus semua karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika dimulai dengan 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Jika tidak dimulai dengan 62, tambahkan 62
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Mengirim notifikasi stok habis
     */
    public function sendStockEmptyNotification(string $bahanBakuName, string $satuan, string $cabangName = null): array
    {
        $message = "⚠️ *PERINGATAN: STOK BAHAN BAKU HABIS*\n\n";
        $message .= "Bahan baku berikut sudah habis:\n";
        $message .= "📦 *Nama:* {$bahanBakuName}\n";
        $message .= "📏 *Satuan:* {$satuan}\n";
        
        if ($cabangName) {
            $message .= "🏢 *Cabang:* {$cabangName}\n";
        }
        
        $message .= "\n";
        $message .= "Mohon segera lakukan restock untuk bahan baku ini.\n";
        $message .= "\n";
        $message .= "Waktu: " . now()->format('d/m/Y H:i:s');

        $to = $this->phoneNumber;
        
        if (empty($to)) {
            Log::warning('WhatsApp phone number not configured');
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp tidak dikonfigurasi'
            ];
        }

        return $this->sendMessage($to, $message);
    }

    /**
     * Mengirim notifikasi stok rendah
     */
    public function sendStockLowNotification(string $itemName, string $tipe, int $stok, string $satuan = null, string $cabangName = null): array
    {
        $threshold = config('whatsapp.stok_rendah_threshold', 10);
        $tipeLabel = $tipe === 'produk' ? 'Produk' : 'Bahan Baku';
        
        $message = "⚠️ *PERINGATAN: STOK {$tipeLabel} RENDAH*\n\n";
        $message .= "{$tipeLabel} berikut memiliki stok rendah:\n";
        $message .= "📦 *Nama:* {$itemName}\n";
        
        if ($satuan) {
            $message .= "📏 *Satuan:* {$satuan}\n";
        }
        
        $message .= "📊 *Stok Saat Ini:* {$stok} " . ($satuan ?? 'unit') . "\n";
        $message .= "⚠️ *Threshold:* < {$threshold} " . ($satuan ?? 'unit') . "\n";
        
        if ($cabangName) {
            $message .= "🏢 *Cabang:* {$cabangName}\n";
        }
        
        $message .= "\n";
        $message .= "Mohon segera lakukan restock untuk {$tipeLabel} ini sebelum stok habis.\n";
        $message .= "\n";
        $message .= "Waktu: " . now()->format('d/m/Y H:i:s');

        $to = $this->phoneNumber;
        
        if (empty($to)) {
            Log::warning('WhatsApp phone number not configured');
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp tidak dikonfigurasi'
            ];
        }

        return $this->sendMessage($to, $message);
    }
}

