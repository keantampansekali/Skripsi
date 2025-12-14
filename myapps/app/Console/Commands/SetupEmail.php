<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class SetupEmail extends Command
{
    protected $signature = 'email:setup {email} {password} {--test : Test koneksi email setelah setup}';
    protected $description = 'Setup konfigurasi email untuk laporan 6 bulanan';

    public function handle()
    {
        $email = trim($this->argument('email'));
        $password = trim($this->argument('password'));
        
        // Validasi email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('❌ Email tidak valid!');
            return 1;
        }
        
        // Validasi password tidak kosong
        if (empty($password)) {
            $this->error('❌ Password tidak boleh kosong!');
            return 1;
        }
        
        $envFile = base_path('.env');
        
        if (!File::exists($envFile)) {
            $this->error('❌ File .env tidak ditemukan!');
            return 1;
        }
        
        $envContent = File::get($envFile);
        $lines = explode("\n", $envContent);
        $newLines = [];
        $updated = [];
        
        // Konfigurasi yang akan di-set
        $configs = [
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => 'smtp.gmail.com',
            'MAIL_PORT' => '587',
            'MAIL_USERNAME' => $email,
            'MAIL_PASSWORD' => $password,
            'MAIL_ENCRYPTION' => 'tls',
            'MAIL_FROM_ADDRESS' => $email,
            'MAIL_FROM_NAME' => '${APP_NAME}',
        ];
        
        // Update atau tambahkan konfigurasi
        foreach ($lines as $line) {
            $trimmed = trim($line);
            $found = false;
            
            foreach ($configs as $key => $value) {
                // Cek jika line mengandung key (termasuk yang ada placeholder)
                if (preg_match('/^' . preg_quote($key, '/') . '\s*=/i', $trimmed)) {
                    // Handle password dengan quote jika mengandung spasi atau karakter khusus
                    if ($key === 'MAIL_PASSWORD' && (strpos($value, ' ') !== false || strpos($value, '#') !== false || strpos($value, '$') !== false)) {
                        $newLines[] = $key . '="' . $value . '"';
                    } else {
                        $newLines[] = $key . '=' . $value;
                    }
                    $updated[] = $key;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $newLines[] = $line;
            }
        }
        
        // Tambahkan konfigurasi yang belum ada
        foreach ($configs as $key => $value) {
            if (!in_array($key, $updated)) {
                // Cari posisi yang tepat untuk menambahkan konfigurasi MAIL
                $inserted = false;
                for ($i = 0; $i < count($newLines); $i++) {
                    if (strpos(trim($newLines[$i]), 'MAIL_') === 0) {
                        // Insert sebelum konfigurasi MAIL pertama
                        array_splice($newLines, $i, 0, '');
                        array_splice($newLines, $i + 1, 0, '# Email Configuration');
                        $inserted = true;
                        break;
                    }
                }
                
                if (!$inserted) {
                    // Jika tidak ada konfigurasi MAIL, tambahkan di akhir
                    $newLines[] = '';
                    $newLines[] = '# Email Configuration';
                }
                
                // Tambahkan semua konfigurasi yang belum ada
                foreach ($configs as $k => $v) {
                    if (!in_array($k, $updated)) {
                        if ($k === 'MAIL_PASSWORD' && (strpos($v, ' ') !== false || strpos($v, '#') !== false)) {
                            $newLines[] = $k . '="' . $v . '"';
                        } else {
                            $newLines[] = $k . '=' . $v;
                        }
                        $updated[] = $k;
                    }
                }
                break;
            }
        }
        
        // Tulis kembali ke file .env
        File::put($envFile, implode("\n", $newLines));
        
        // Verifikasi bahwa konfigurasi sudah tersimpan
        $savedContent = File::get($envFile);
        $usernameFound = false;
        $passwordFound = false;
        
        foreach (explode("\n", $savedContent) as $line) {
            if (preg_match('/^MAIL_USERNAME\s*=\s*(.+)$/i', $line, $matches)) {
                $savedUsername = trim($matches[1], ' "\'');
                if ($savedUsername === $email) {
                    $usernameFound = true;
                }
            }
            if (preg_match('/^MAIL_PASSWORD\s*=\s*(.+)$/i', $line, $matches)) {
                $savedPassword = trim($matches[1], ' "\'');
                if ($savedPassword === $password) {
                    $passwordFound = true;
                }
            }
        }
        
        $this->info('✅ Konfigurasi email berhasil disimpan!');
        $this->info("📧 Email: {$email}");
        
        if (!$usernameFound || !$passwordFound) {
            $this->warn('⚠️  Peringatan: Mungkin ada masalah dengan penyimpanan konfigurasi.');
            $this->warn('   Silakan cek file .env secara manual.');
        }
        
        $this->newLine();
        
        // Clear config cache
        $this->call('config:clear');
        $this->call('cache:clear');
        
        // Verifikasi config setelah clear cache
        $this->info('🔍 Memverifikasi konfigurasi...');
        $configUsername = config('mail.mailers.smtp.username');
        $configPassword = config('mail.mailers.smtp.password');
        
        if ($configUsername === $email && $configPassword === $password) {
            $this->info('✅ Konfigurasi sudah benar dan siap digunakan!');
        } else {
            $this->warn('⚠️  Konfigurasi yang terbaca:');
            $this->warn('   MAIL_USERNAME: ' . ($configUsername ?: 'tidak ditemukan'));
            $this->warn('   MAIL_PASSWORD: ' . ($configPassword ? '***' : 'tidak ditemukan'));
            $this->warn('   Pastikan file .env sudah benar dan tidak ada placeholder.');
        }
        $this->newLine();
        
        // Test koneksi jika diminta
        if ($this->option('test')) {
            $this->newLine();
            $this->info('🧪 Testing koneksi email...');
            
            try {
                Mail::raw('Test email dari Laravel', function($message) use ($email) {
                    $message->to($email)
                            ->subject('Test Email - Laravel');
                });
                
                $this->info('✅ Test email berhasil dikirim!');
                $this->info("   Cek inbox: {$email}");
            } catch (\Exception $e) {
                $this->error('❌ Gagal mengirim test email!');
                $errorMessage = $e->getMessage();
                $this->error('   Error: ' . $errorMessage);
                $this->newLine();
                
                // Cek apakah error terkait autentikasi Gmail
                if (strpos($errorMessage, '535') !== false || strpos($errorMessage, 'BadCredentials') !== false || strpos($errorMessage, 'Username and Password not accepted') !== false) {
                    $this->warn('🔐 MASALAH AUTENTIKASI GMAIL:');
                    $this->newLine();
                    $this->info('📋 Untuk Gmail, Anda HARUS menggunakan App Password:');
                    $this->newLine();
                    $this->line('1️⃣  Aktifkan 2-Step Verification:');
                    $this->line('   https://myaccount.google.com/security');
                    $this->newLine();
                    $this->line('2️⃣  Buat App Password:');
                    $this->line('   https://myaccount.google.com/apppasswords');
                    $this->line('   - Pilih "Mail" dan "Other (Custom name)"');
                    $this->line('   - Masukkan nama: "Laravel App"');
                    $this->line('   - Copy password 16 karakter yang dihasilkan');
                    $this->newLine();
                    $this->line('3️⃣  Jalankan command lagi dengan App Password:');
                    $this->line('   php artisan email:setup ' . $email . ' "xxxx xxxx xxxx xxxx" --test');
                    $this->line('   (Ganti xxxx dengan App Password Anda)');
                    $this->newLine();
                } else {
                    $this->warn('⚠️  Kemungkinan masalah:');
                    $this->warn('   1. Email atau password salah');
                    $this->warn('   2. Untuk Gmail: Perlu App Password jika 2-Step Verification aktif');
                    $this->warn('   3. Port 587 diblokir firewall');
                    $this->warn('   4. Konfigurasi SMTP tidak sesuai dengan provider email');
                }
                return 1;
            }
        } else {
            $this->info('Selanjutnya, jalankan:');
            $this->line('  php artisan config:clear');
            $this->line('  php artisan cache:clear');
            $this->line('  php artisan laporan:send-6bulanan');
            $this->newLine();
            $this->comment('💡 Tips: Tambahkan --test untuk test koneksi email:');
            $this->line('  php artisan email:setup ' . $email . ' "password" --test');
        }
        
        return 0;
    }
}
