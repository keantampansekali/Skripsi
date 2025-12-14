<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckEmailStatus extends Command
{
    protected $signature = 'email:check-status';
    protected $description = 'Cek status konfigurasi email dan log pengiriman';

    public function handle()
    {
        $this->info('🔍 Memeriksa Status Email...');
        $this->newLine();

        // Cek konfigurasi
        $mailer = config('mail.default');
        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');
        $username = config('mail.mailers.smtp.username');
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');

        $this->info('📋 Konfigurasi Email:');
        $this->line("   Mailer: {$mailer}");
        $this->line("   Host: " . ($host ?: 'tidak dikonfigurasi'));
        $this->line("   Port: " . ($port ?: 'tidak dikonfigurasi'));
        $this->line("   Username: " . ($username ?: 'tidak dikonfigurasi'));
        $this->line("   From Address: " . ($fromAddress ?: 'tidak dikonfigurasi'));
        $this->line("   From Name: " . ($fromName ?: 'tidak dikonfigurasi'));
        $this->newLine();

        // Validasi
        $errors = [];
        $warnings = [];

        if ($mailer === 'log') {
            $errors[] = "❌ Mailer adalah 'log' - email tidak akan dikirim!";
            $errors[] = "   Ubah MAIL_MAILER=smtp di file .env";
        }

        if (empty($host)) {
            $errors[] = "❌ MAIL_HOST tidak dikonfigurasi";
        }

        if (empty($username)) {
            $errors[] = "❌ MAIL_USERNAME tidak dikonfigurasi";
        }

        if (empty($fromAddress)) {
            $warnings[] = "⚠️  MAIL_FROM_ADDRESS tidak dikonfigurasi";
        }

        if (!empty($errors)) {
            $this->error('Masalah ditemukan:');
            foreach ($errors as $error) {
                $this->line("   {$error}");
            }
            $this->newLine();
        }

        if (!empty($warnings)) {
            foreach ($warnings as $warning) {
                $this->warn("   {$warning}");
            }
            $this->newLine();
        }

        if (empty($errors)) {
            $this->info('✅ Konfigurasi email terlihat baik!');
            $this->newLine();
        }

        // Cek log terakhir
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            $this->info('📝 Log Email Terakhir:');
            $logContent = File::get($logFile);
            $lines = explode("\n", $logContent);
            $emailLogs = array_filter($lines, function($line) {
                return strpos($line, 'email') !== false || 
                       strpos($line, 'Email') !== false ||
                       strpos($line, 'laporan') !== false ||
                       strpos($line, 'Laporan') !== false;
            });
            
            $recentLogs = array_slice($emailLogs, -10);
            if (!empty($recentLogs)) {
                foreach ($recentLogs as $log) {
                    $this->line("   " . substr($log, 0, 150));
                }
            } else {
                $this->line("   Tidak ada log email ditemukan");
            }
            $this->newLine();
        }

        // Tips
        $this->info('💡 Tips:');
        $this->line('   1. Test email: php artisan laporan:test-email');
        $this->line('   2. Cek log: tail -f storage/logs/laravel.log');
        $this->line('   3. Untuk Gmail: pastikan menggunakan App Password');
        $this->line('   4. Cek folder Spam/Junk jika email tidak masuk');

        return 0;
    }
}

