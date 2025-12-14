<?php

namespace App\Console\Commands;

use App\Console\Commands\SendLaporan6Bulanan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schedule;
use Carbon\Carbon;

class TestScheduleLaporan extends Command
{
    protected $signature = 'laporan:test-schedule {--force : Paksa kirim email sekarang}';
    protected $description = 'Test schedule laporan 6 bulanan dan verifikasi email masuk';

    public function handle()
    {
        $this->info('🧪 Testing Schedule Laporan 6 Bulanan');
        $this->newLine();

        // Cek schedule yang terdaftar
        $this->info('📅 Schedule yang terdaftar:');
        $schedule = Schedule::command('laporan:send-6bulanan')
            ->cron('0 9 1 1,7 *')
            ->timezone('Asia/Jakarta');
        
        $this->line('   Command: laporan:send-6bulanan');
        $this->line('   Cron: 0 9 1 1,7 * (Setiap tanggal 1 bulan Januari dan Juli pukul 09:00)');
        $this->line('   Timezone: Asia/Jakarta');
        $this->newLine();

        // Hitung kapan schedule berikutnya
        $now = Carbon::now('Asia/Jakarta');
        $nextJanuary = Carbon::create($now->year + ($now->month > 1 ? 1 : 0), 1, 1, 9, 0, 0, 'Asia/Jakarta');
        $nextJuly = Carbon::create($now->year, 7, 1, 9, 0, 0, 'Asia/Jakarta');
        
        if ($now->month <= 1) {
            $nextRun = $nextJanuary;
        } elseif ($now->month <= 7) {
            $nextRun = $nextJuly;
        } else {
            $nextRun = $nextJanuary->addYear();
        }

        $this->info('⏰ Waktu Eksekusi Berikutnya:');
        $this->line('   ' . $nextRun->locale('id')->format('d F Y H:i:s T'));
        $this->line('   (' . $nextRun->diffForHumans() . ')');
        $this->newLine();

        // Test manual jika --force
        if ($this->option('force')) {
            $this->warn('⚠️  Mode FORCE: Mengirim email sekarang tanpa menunggu schedule');
            $this->newLine();
            
            if (!$this->confirm('Apakah Anda yakin ingin mengirim email sekarang?', true)) {
                $this->info('Dibatalkan.');
                return 0;
            }

            $this->info('📤 Menjalankan command laporan:send-6bulanan...');
            $this->newLine();
            
            $this->call('laporan:send-6bulanan');
            
            $this->newLine();
            $this->info('✅ Test selesai!');
            $this->newLine();
            $this->info('💡 Langkah verifikasi:');
            $this->line('   1. Cek inbox email owner');
            $this->line('   2. Cek folder Spam/Junk');
            $this->line('   3. Cek log: storage/logs/laravel.log');
            $this->line('   4. Verifikasi email masuk dengan subject: "Laporan 6 Bulanan - ..."');
            
            return 0;
        }

        // Tampilkan cara testing
        $this->info('📋 Cara Testing Schedule:');
        $this->newLine();
        $this->line('1️⃣  Test Manual (Tanpa menunggu schedule):');
        $this->line('   php artisan laporan:test-schedule --force');
        $this->newLine();
        $this->line('2️⃣  Test Email Langsung:');
        $this->line('   php artisan laporan:test-email your-email@gmail.com');
        $this->newLine();
        $this->line('3️⃣  Test Schedule List:');
        $this->line('   php artisan schedule:list');
        $this->newLine();
        $this->line('4️⃣  Test Schedule Run (Simulasi):');
        $this->line('   php artisan schedule:run');
        $this->newLine();
        $this->line('5️⃣  Verifikasi Email Masuk:');
        $this->line('   - Cek inbox email owner');
        $this->line('   - Cek folder Spam/Junk');
        $this->line('   - Cek log: storage/logs/laravel.log');
        $this->line('   - Subject email: "Laporan 6 Bulanan - [tanggal]"');
        $this->newLine();
        $this->line('6️⃣  Untuk Production (Setelah testing):');
        $this->line('   - Setup cron job di server');
        $this->line('   - Atau gunakan: php artisan schedule:work (untuk development)');
        $this->newLine();

        // Tampilkan info email owner
        $owners = \App\Models\Pegawai::where('role', 'owner')->whereNotNull('email')->get();
        if ($owners->count() > 0) {
            $this->info('👥 Owner yang akan menerima email:');
            foreach ($owners as $owner) {
                $this->line("   - {$owner->email} ({$owner->nama})");
            }
            $this->newLine();
        }

        return 0;
    }
}

