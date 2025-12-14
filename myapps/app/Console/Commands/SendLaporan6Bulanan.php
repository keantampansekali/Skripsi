<?php

namespace App\Console\Commands;

use App\Mail\Laporan6BulananMail;
use App\Models\Pegawai;
use App\Models\Cabang;
use App\Models\TransaksiKasir;
use App\Models\Restock;
use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\StockMovement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendLaporan6Bulanan extends Command
{
    protected $signature = 'laporan:send-6bulanan {--test : Test kirim email tanpa data laporan} {--verify : Verifikasi email yang sudah dikirim}';
    protected $description = 'Kirim laporan penjualan, stok, dan pembelian ke email owner setiap 6 bulan sekali';

    public function handle()
    {
        $this->info('📊 Memulai proses pengiriman laporan 6 bulanan...');
        $this->newLine();

        // Cek konfigurasi email
        $this->checkEmailConfig();

        // Ambil semua owner
        $owners = Pegawai::where('role', 'owner')->get();

        if ($owners->isEmpty()) {
            $this->warn('⚠️  Tidak ada owner yang ditemukan!');
            return 1;
        }

        // Hitung periode 6 bulan terakhir
        $endDate = Carbon::now()->endOfDay();
        $startDate = Carbon::now()->subMonths(6)->startOfDay();

        $this->info("📅 Periode laporan: {$startDate->locale('id')->format('d F Y')} - {$endDate->locale('id')->format('d F Y')}");
        $this->newLine();

        // Ambil semua cabang
        $cabangs = Cabang::all();

        if ($cabangs->isEmpty()) {
            $this->warn('⚠️  Tidak ada cabang yang ditemukan!');
            return 1;
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($owners as $owner) {
            if (empty($owner->email)) {
                $this->warn("⚠️  Owner {$owner->nama} tidak memiliki email, dilewati.");
                $errorCount++;
                continue;
            }

            $this->info("📧 Mengirim laporan ke: {$owner->email} ({$owner->nama})");

            try {
                // Generate data laporan untuk semua cabang
                $this->info("   📊 Menggenerate data laporan...");
                $laporanData = $this->generateLaporanData($cabangs, $startDate, $endDate);
                $this->info("   ✅ Data laporan berhasil digenerate");

                // Cek konfigurasi email sekali lagi sebelum kirim
                $mailer = config('mail.default');
                $username = config('mail.mailers.smtp.username');
                
                $this->info("   📧 Mailer yang digunakan: {$mailer}");
                
                if ($mailer === 'log') {
                    $this->warn("   ⚠️  Peringatan: Mailer saat ini adalah 'log', email tidak akan benar-benar dikirim!");
                    $this->warn("   💡 Gunakan 'smtp' untuk mengirim email sebenarnya.");
                }
                
                // Validasi username bukan placeholder
                if ($username === 'your-email@gmail.com' || strpos($username, 'your-email') !== false) {
                    $this->error("   ❌ Username masih menggunakan placeholder: {$username}");
                    $this->error("   Update file .env dengan email yang benar!");
                    throw new \Exception("Konfigurasi email masih menggunakan placeholder. Update file .env terlebih dahulu.");
                }

                // Kirim email
                $this->info("   📤 Mengirim email...");
                $this->line("      Subject: Laporan 6 Bulanan - {$startDate->locale('id')->format('d F Y')} - {$endDate->locale('id')->format('d F Y')}");
                $this->line("      From: " . config('mail.from.address'));
                
                // Log sebelum kirim
                \Log::info("Mengirim email laporan ke: {$owner->email}", [
                    'owner' => $owner->nama,
                    'from' => config('mail.from.address'),
                ]);
                
                Mail::to($owner->email)->send(new Laporan6BulananMail($laporanData, $startDate, $endDate));

                // Log setelah kirim
                \Log::info("Email laporan berhasil dikirim ke: {$owner->email}");

                $this->info("✅ Laporan berhasil dikirim ke {$owner->email}");
                $this->info("   💡 Cek inbox, folder Spam/Junk, dan All Mail");
                $this->info("   📝 Log tersimpan di: storage/logs/laravel.log");
                $successCount++;
            } catch (\Exception $e) {
                $this->error("❌ Gagal mengirim laporan ke {$owner->email}");
                $errorMessage = $e->getMessage();
                $this->error("   Error: {$errorMessage}");
                
                // Cek apakah error terkait autentikasi Gmail
                if (strpos($errorMessage, '535') !== false || strpos($errorMessage, 'BadCredentials') !== false || strpos($errorMessage, 'Username and Password not accepted') !== false) {
                    $this->newLine();
                    $this->warn("   🔐 Masalah autentikasi Gmail!");
                    $this->warn("   💡 Gunakan App Password, bukan password biasa");
                    $this->warn("   📋 Lihat: https://myaccount.google.com/apppasswords");
                    $this->newLine();
                }
                
                $errorCount++;
            }

            $this->newLine();
        }

        $this->info("📊 Ringkasan:");
        $this->info("   ✅ Berhasil: {$successCount}");
        $this->info("   ❌ Gagal: {$errorCount}");

        if ($successCount > 0) {
            $this->newLine();
            $this->info("💡 Tips Verifikasi Email:");
            $this->line("   1. ✅ Cek inbox email Anda");
            $this->line("   2. ✅ Cek folder Spam/Junk (sangat penting!)");
            $this->line("   3. ✅ Cek folder All Mail di Gmail");
            $this->line("   4. ⏰ Email mungkin membutuhkan 1-5 menit untuk sampai");
            $this->line("   5. 📝 Cek log: storage/logs/laravel.log");
            $this->line("   6. 🔍 Subject email: 'Laporan 6 Bulanan - [tanggal]'");
            $this->newLine();
            $this->info("📋 Untuk verifikasi lebih lanjut:");
            $this->line("   php artisan email:check-status");
        }

        // Mode verify
        if ($this->option('verify')) {
            $this->newLine();
            $this->info("🔍 Verifikasi Email yang Dikirim:");
            $this->verifyEmailSent();
        }

        return 0;
    }

    private function verifyEmailSent()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!file_exists($logFile)) {
            $this->warn("   ⚠️  File log tidak ditemukan: {$logFile}");
            return;
        }

        $logContent = file_get_contents($logFile);
        $lines = explode("\n", $logContent);
        
        // Cari log email terakhir
        $emailLogs = [];
        foreach ($lines as $line) {
            if (strpos($line, 'Email laporan berhasil dikirim') !== false || 
                strpos($line, 'Mengirim email laporan ke') !== false) {
                $emailLogs[] = $line;
            }
        }

        if (empty($emailLogs)) {
            $this->warn("   ⚠️  Tidak ada log email ditemukan");
            $this->line("   Pastikan email sudah dikirim sebelumnya");
            return;
        }

        $this->info("   📧 Email yang terkirim (dari log):");
        $recentLogs = array_slice($emailLogs, -10);
        foreach ($recentLogs as $log) {
            // Extract email dari log
            if (preg_match('/ke: ([^\s]+)/', $log, $matches)) {
                $email = $matches[1];
                $this->line("      ✅ {$email}");
            }
        }
        
        $this->newLine();
        $this->info("   💡 Untuk melihat detail log:");
        $this->line("      tail -n 100 storage/logs/laravel.log | grep -i email");
    }

    private function checkEmailConfig()
    {
        $mailer = config('mail.default');
        $host = config('mail.mailers.smtp.host');
        $username = config('mail.mailers.smtp.username');
        $password = config('mail.mailers.smtp.password');
        $fromAddress = config('mail.from.address');

        $this->info("🔍 Konfigurasi Email:");
        $this->line("   Mailer: {$mailer}");
        $this->line("   Host: " . ($host ?: 'tidak dikonfigurasi'));
        $this->line("   Username: " . ($username ?: 'tidak dikonfigurasi'));
        $this->line("   Password: " . ($password ? '***' : 'tidak dikonfigurasi'));
        $this->line("   From Address: " . ($fromAddress ?: 'tidak dikonfigurasi'));

        $hasError = false;

        if ($mailer === 'log') {
            $this->newLine();
            $this->error("❌ PERINGATAN: Mailer saat ini adalah 'log'!");
            $this->error("   Email tidak akan benar-benar dikirim, hanya di-log ke file.");
            $this->error("   Untuk mengirim email sebenarnya, ubah MAIL_MAILER=smtp di file .env");
            $hasError = true;
        }

        if (empty($username) || empty($host) || empty($password)) {
            $this->newLine();
            $this->error("❌ Konfigurasi email belum lengkap!");
            if (empty($username)) {
                $this->error("   - MAIL_USERNAME tidak dikonfigurasi");
            }
            if (empty($host)) {
                $this->error("   - MAIL_HOST tidak dikonfigurasi");
            }
            if (empty($password)) {
                $this->error("   - MAIL_PASSWORD tidak dikonfigurasi");
            }
            $hasError = true;
        }

        // Cek apakah masih menggunakan placeholder
        if ($username === 'your-email@gmail.com' || strpos($username, 'your-email') !== false) {
            $this->newLine();
            $this->error("❌ Konfigurasi email masih menggunakan placeholder!");
            $this->error("   Username: {$username}");
            $this->error("   Update file .env dengan email dan password yang benar");
            $hasError = true;
        }

        if ($hasError) {
            $this->newLine();
            $this->info("📋 Cara memperbaiki:");
            $this->line("   1. Buka file .env di root project");
            $this->line("   2. Update konfigurasi email:");
            $this->line("      MAIL_MAILER=smtp");
            $this->line("      MAIL_HOST=smtp.gmail.com");
            $this->line("      MAIL_PORT=587");
            $this->line("      MAIL_USERNAME=your-actual-email@gmail.com");
            $this->line("      MAIL_PASSWORD=\"your-app-password\"");
            $this->line("      MAIL_ENCRYPTION=tls");
            $this->line("      MAIL_FROM_ADDRESS=your-actual-email@gmail.com");
            $this->newLine();
            $this->line("   3. Atau jalankan: php artisan email:setup your-email@gmail.com \"app-password\"");
            $this->newLine();
            $this->line("   4. Clear cache: php artisan config:clear");
            $this->newLine();
            $this->warn("   💡 Untuk Gmail: Gunakan App Password, bukan password biasa!");
            $this->warn("      Lihat: https://myaccount.google.com/apppasswords");
            $this->newLine();
            
            if (!$this->confirm('Lanjutkan pengiriman email? (Mungkin akan gagal)', false)) {
                $this->info('Dibatalkan. Silakan perbaiki konfigurasi email terlebih dahulu.');
                exit(1);
            }
        }
    }

    private function generateLaporanData($cabangs, $startDate, $endDate)
    {
        $data = [];

        foreach ($cabangs as $cabang) {
            $idCabang = $cabang->id_cabang;
            $namaCabang = $cabang->nama_cabang;

            // Laporan Penjualan
            $penjualanData = $this->getPenjualanData($idCabang, $startDate, $endDate);
            
            // Laporan Stok
            $stokData = $this->getStokData($idCabang, $endDate);
            
            // Laporan Pembelian
            $pembelianData = $this->getPembelianData($idCabang, $startDate, $endDate);

            $data[] = [
                'cabang' => [
                    'id' => $idCabang,
                    'nama' => $namaCabang,
                ],
                'penjualan' => $penjualanData,
                'stok' => $stokData,
                'pembelian' => $pembelianData,
            ];
        }

        return $data;
    }

    private function getPenjualanData($idCabang, $startDate, $endDate)
    {
        $transaksi = TransaksiKasir::where('id_cabang', $idCabang)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $summary = [
            'total_transaksi' => $transaksi->count(),
            'total_subtotal' => $transaksi->sum('subtotal'),
            'total_diskon' => $transaksi->sum('diskon'),
            'total_tax' => $transaksi->sum('tax'),
            'total_penjualan' => $transaksi->sum('total'),
        ];

        // Group by month
        $groupedByMonth = [];
        foreach ($transaksi as $t) {
            $monthKey = Carbon::parse($t->created_at)->format('Y-m');
            $monthName = Carbon::parse($t->created_at)->locale('id')->format('F Y');

            if (!isset($groupedByMonth[$monthKey])) {
                $groupedByMonth[$monthKey] = [
                    'bulan' => $monthName,
                    'jumlah_transaksi' => 0,
                    'subtotal' => 0,
                    'diskon' => 0,
                    'tax' => 0,
                    'total' => 0,
                ];
            }

            $groupedByMonth[$monthKey]['jumlah_transaksi']++;
            $groupedByMonth[$monthKey]['subtotal'] += $t->subtotal;
            $groupedByMonth[$monthKey]['diskon'] += $t->diskon;
            $groupedByMonth[$monthKey]['tax'] += $t->tax;
            $groupedByMonth[$monthKey]['total'] += $t->total;
        }

        ksort($groupedByMonth);

        return [
            'summary' => $summary,
            'detail' => array_values($groupedByMonth),
        ];
    }

    private function getStokData($idCabang, $endDate)
    {
        $bahanBaku = BahanBaku::where('id_cabang', $idCabang)->get();
        $produk = Produk::where('id_cabang', $idCabang)->get();

        $totalBahanBaku = 0;
        $totalNilaiBahanBaku = 0;

        foreach ($bahanBaku as $b) {
            $stokSaatIni = $b->stok;

            // Hitung perubahan setelah endDate
            $movementsAfter = StockMovement::where('bahan_baku_id', $b->id)
                ->where('id_cabang', $idCabang)
                ->where('created_at', '>', $endDate)
                ->get();

            $perubahanSetelah = 0;
            foreach ($movementsAfter as $mov) {
                if ($mov->tipe === 'in') {
                    $perubahanSetelah += $mov->qty;
                } elseif ($mov->tipe === 'out') {
                    $perubahanSetelah -= $mov->qty;
                } elseif ($mov->tipe === 'adj') {
                    $perubahanSetelah += $mov->qty;
                }
            }

            $stokTanggal = $stokSaatIni - $perubahanSetelah;
            $totalBahanBaku += $stokTanggal;
            $totalNilaiBahanBaku += $stokTanggal * $b->harga_satuan;
        }

        $totalProduk = $produk->sum('stok');
        $totalNilaiProduk = $produk->sum(function($p) {
            return $p->stok * $p->harga;
        });

        return [
            'total_bahan_baku' => $totalBahanBaku,
            'total_produk' => $totalProduk,
            'total_nilai_bahan_baku' => $totalNilaiBahanBaku,
            'total_nilai_produk' => $totalNilaiProduk,
            'total_nilai' => $totalNilaiBahanBaku + $totalNilaiProduk,
        ];
    }

    private function getPembelianData($idCabang, $startDate, $endDate)
    {
        $restocks = Restock::where('id_cabang', $idCabang)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $summary = [
            'total_pembelian' => $restocks->count(),
            'total_subtotal' => $restocks->sum('subtotal') ?? 0,
            'total_diskon' => $restocks->sum('diskon') ?? 0,
            'total_ppn' => $restocks->sum('ppn') ?? 0,
            'total_pembayaran' => $restocks->sum('total') ?? 0,
        ];

        // Group by month
        $groupedByMonth = [];
        foreach ($restocks as $r) {
            $monthKey = Carbon::parse($r->tanggal)->format('Y-m');
            $monthName = Carbon::parse($r->tanggal)->locale('id')->format('F Y');

            if (!isset($groupedByMonth[$monthKey])) {
                $groupedByMonth[$monthKey] = [
                    'bulan' => $monthName,
                    'jumlah_pembelian' => 0,
                    'subtotal' => 0,
                    'diskon' => 0,
                    'ppn' => 0,
                    'total' => 0,
                ];
            }

            $groupedByMonth[$monthKey]['jumlah_pembelian']++;
            $groupedByMonth[$monthKey]['subtotal'] += $r->subtotal ?? 0;
            $groupedByMonth[$monthKey]['diskon'] += $r->diskon ?? 0;
            $groupedByMonth[$monthKey]['ppn'] += $r->ppn ?? 0;
            $groupedByMonth[$monthKey]['total'] += $r->total ?? 0;
        }

        ksort($groupedByMonth);

        return [
            'summary' => $summary,
            'detail' => array_values($groupedByMonth),
        ];
    }
}

