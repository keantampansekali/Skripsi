<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp API Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk WhatsApp API service.
    | Pilih provider yang akan digunakan: 'fonnte', 'wablas', atau 'whatsapp-api'
    |
    */

    'provider' => env('WHATSAPP_PROVIDER', 'fonnte'),

    /*
    |--------------------------------------------------------------------------
    | API URL
    |--------------------------------------------------------------------------
    |
    | URL endpoint untuk WhatsApp API
    |
    | Fonnte: https://api.fonnte.com/send
    | Wablas: https://api.wablas.com/api/send-message
    | WhatsApp API: Sesuai dengan provider Anda
    |
    */

    'api_url' => env('WHATSAPP_API_URL', 'https://api.fonnte.com/send'),

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | API Key atau Token untuk autentikasi
    |
    */

    'api_key' => env('WHATSAPP_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Phone Number
    |--------------------------------------------------------------------------
    |
    | Nomor WhatsApp yang akan menerima notifikasi
    | Format: 6281234567890 (tanpa +, dimulai dengan 62)
    |
    */

    'phone_number' => env('WHATSAPP_PHONE_NUMBER', ''),

    /*
    |--------------------------------------------------------------------------
    | Enable Notifications
    |--------------------------------------------------------------------------
    |
    | Aktifkan atau nonaktifkan notifikasi WhatsApp
    |
    */

    'enabled' => env('WHATSAPP_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Stok Rendah Threshold
    |--------------------------------------------------------------------------
    |
    | Threshold untuk menentukan stok rendah.
    | Notifikasi akan dikirim ketika stok turun di bawah nilai ini.
    | Default: 10
    |
    */

    'stok_rendah_threshold' => env('WHATSAPP_STOK_RENDAH_THRESHOLD', 10),
];

