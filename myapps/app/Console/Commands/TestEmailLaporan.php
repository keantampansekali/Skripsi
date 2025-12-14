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

class TestEmailLaporan extends Command
{
    protected $signature = 'laporan:test-email {email?}';
    protected $description = 'Test kirim email laporan ke email tertentu';

    public function handle()
    {
        $email = $this->argument('email');
        
        if (!$email) {
            // Ambil email owner pertama jika tidak ada argumen
            $owner = Pegawai::where('role', 'owner')->whereNotNull('email')->first();
            if ($owner) {
                $email = $owner->email;
                $this->info("📧 Menggunakan email owner: {$email} ({$owner->nama})");
            } else {
                $this->error("❌ Tidak ada email owner yang ditemukan!");
                $this->line("   Gunakan: php artisan laporan:test-email your-email@gmail.com");
                return 1;
            }
        }

        // Validasi email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error("❌ Email tidak valid: {$email}");
            return 1;
        }

        $this->info("🧪 Testing pengiriman email laporan...");
        $this->newLine();

        // Cek konfigurasi
        $mailer = config('mail.default');
        $host = config('mail.mailers.smtp.host');
        $username = config('mail.mailers.smtp.username');
        $password = config('mail.mailers.smtp.password');

        $this->info("📋 Konfigurasi Email:");
        $this->line("   Mailer: {$mailer}");
        $this->line("   Host: " . ($host ?: 'tidak dikonfigurasi'));
        $this->line("   Username: " . ($username ?: 'tidak dikonfigurasi'));
        $this->line("   Password: " . ($password ? '***' : 'tidak dikonfigurasi'));

        if ($mailer === 'log') {
            $this->newLine();
            $this->error("❌ Mailer saat ini adalah 'log' - email tidak akan dikirim!");
            $this->line("   Ubah MAIL_MAILER=smtp di file .env");
            $this->line("   Atau jalankan: php artisan email:setup your-email@gmail.com \"password\"");
            return 1;
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
            $this->line("   Jalankan: php artisan email:setup your-email@gmail.com \"app-password\"");
            return 1;
        }

        // Cek apakah masih menggunakan placeholder
        if ($username === 'your-email@gmail.com' || strpos($username, 'your-email') !== false) {
            $this->newLine();
            $this->error("❌ Konfigurasi email masih menggunakan placeholder!");
            $this->error("   Username: {$username}");
            $this->error("   Update file .env dengan email yang benar");
            $this->newLine();
            $this->info("📋 Cara memperbaiki:");
            $this->line("   1. Buka file .env di root project");
            $this->line("   2. Update MAIL_USERNAME dengan email Gmail Anda yang sebenarnya");
            $this->line("   3. Update MAIL_PASSWORD dengan App Password (bukan password biasa)");
            $this->line("   4. Clear cache: php artisan config:clear");
            $this->newLine();
            $this->warn("   💡 Untuk Gmail: Buat App Password di:");
            $this->line("      https://myaccount.google.com/apppasswords");
            return 1;
        }

        $this->newLine();

        // Ambil semua cabang
        $cabangs = Cabang::all();

        if ($cabangs->isEmpty()) {
            $this->error("❌ Tidak ada cabang yang ditemukan!");
            return 1;
        }

        // Hitung periode 6 bulan terakhir
        $endDate = Carbon::now()->endOfDay();
        $startDate = Carbon::now()->subMonths(6)->startOfDay();

        $this->info("📅 Periode laporan: {$startDate->locale('id')->format('d F Y')} - {$endDate->locale('id')->format('d F Y')}");
        $this->newLine();

        // Generate data laporan real
        $this->info("📊 Menggenerate data laporan real...");
        $laporanData = $this->generateLaporanData($cabangs, $startDate, $endDate);
        $this->info("✅ Data laporan berhasil digenerate");
        $this->newLine();

        try {
            $this->info("📤 Mengirim email test ke: {$email}...");
            $this->line("   Subject: Laporan 6 Bulanan - {$startDate->locale('id')->format('d F Y')} - {$endDate->locale('id')->format('d F Y')}");
            $this->line("   From: " . config('mail.from.address') . " (" . config('mail.from.name') . ")");
            $this->newLine();
            
            // Log sebelum kirim
            \Log::info("Mengirim email laporan ke: {$email}", [
                'from' => config('mail.from.address'),
                'mailer' => $mailer,
                'host' => $host,
            ]);
            
            Mail::to($email)->send(new Laporan6BulananMail($laporanData, $startDate, $endDate));

            // Log setelah kirim
            \Log::info("Email laporan berhasil dikirim ke: {$email}");

            $this->newLine();
            $this->info("✅ Email test berhasil dikirim!");
            $this->newLine();
            $this->info("📋 Informasi Email:");
            $this->line("   To: {$email}");
            $this->line("   From: " . config('mail.from.address'));
            $this->line("   Subject: Laporan 6 Bulanan - {$startDate->locale('id')->format('d F Y')} - {$endDate->locale('id')->format('d F Y')}");
            $this->newLine();
            $this->info("💡 Langkah selanjutnya:");
            $this->line("   1. ✅ Cek inbox email: {$email}");
            $this->line("   2. ✅ Cek folder Spam/Junk (sangat penting!)");
            $this->line("   3. ✅ Cek folder All Mail di Gmail");
            $this->line("   4. ⏰ Email mungkin membutuhkan 1-5 menit untuk sampai");
            $this->line("   5. 🔍 Cek log: storage/logs/laravel.log");
            $this->newLine();
            $this->warn("⚠️  Jika email tidak masuk:");
            $this->line("   - Pastikan MAIL_MAILER=smtp di .env (bukan 'log')");
            $this->line("   - Untuk Gmail: gunakan App Password (bukan password biasa)");
            $this->line("   - Cek apakah email masuk ke Spam/Junk");
            $this->line("   - Cek log untuk error: tail -f storage/logs/laravel.log");

            return 0;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error("❌ Gagal mengirim email!");
            $this->error("   Error: {$e->getMessage()}");
            $this->newLine();
            
            // Cek apakah error terkait autentikasi
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, '535') !== false || strpos($errorMessage, 'BadCredentials') !== false || strpos($errorMessage, 'Username and Password not accepted') !== false) {
                $this->warn("🔐 MASALAH AUTENTIKASI GMAIL:");
                $this->newLine();
                $this->info("📋 Langkah-langkah untuk memperbaiki:");
                $this->newLine();
                $this->line("1️⃣  Aktifkan 2-Step Verification di Gmail:");
                $this->line("   - Buka: https://myaccount.google.com/security");
                $this->line("   - Aktifkan '2-Step Verification'");
                $this->newLine();
                $this->line("2️⃣  Buat App Password:");
                $this->line("   - Buka: https://myaccount.google.com/apppasswords");
                $this->line("   - Pilih 'Mail' dan 'Other (Custom name)'");
                $this->line("   - Masukkan nama: 'Laravel App'");
                $this->line("   - Copy password yang dihasilkan (16 karakter)");
                $this->newLine();
                $this->line("3️⃣  Update file .env:");
                $this->line("   MAIL_USERNAME=your-email@gmail.com");
                $this->line("   MAIL_PASSWORD=\"xxxx xxxx xxxx xxxx\"  (App Password, bukan password biasa)");
                $this->newLine();
                $this->line("4️⃣  Clear cache dan test lagi:");
                $this->line("   php artisan config:clear");
                $this->line("   php artisan laporan:test-email {$email}");
                $this->newLine();
            } else {
                $this->warn("💡 Kemungkinan masalah:");
                $this->line("   1. Email atau password salah");
                $this->line("   2. Untuk Gmail: Perlu App Password jika 2-Step Verification aktif");
                $this->line("   3. Port 587 diblokir firewall");
                $this->line("   4. Konfigurasi SMTP tidak sesuai");
                $this->newLine();
                $this->line("   Cek log: storage/logs/laravel.log");
            }
            
            return 1;
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

