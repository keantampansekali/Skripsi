<?php

namespace App\Console\Commands;

use App\Models\Produk;
use App\Models\Cabang;
use App\Services\ResepService;
use Illuminate\Console\Command;

class SinkronStokProdukDenganBahanBaku extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'produk:sinkron-stok {--cabang= : ID cabang (kosongkan untuk semua cabang)} {--dry-run : Simulasi tanpa mengubah database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi stok produk dengan ketersediaan bahan baku. Stok produk akan disesuaikan dengan maksimal produk yang bisa dibuat dari bahan baku.';

    protected $resepService;

    public function __construct(ResepService $resepService)
    {
        parent::__construct();
        $this->resepService = $resepService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Memulai sinkronisasi stok produk dengan bahan baku...');
        $this->newLine();

        $isDryRun = $this->option('dry-run');
        $cabangId = $this->option('cabang');

        if ($isDryRun) {
            $this->warn('⚠️  Mode DRY-RUN: Tidak ada perubahan yang akan disimpan ke database');
            $this->newLine();
        }

        // Ambil semua cabang atau cabang tertentu
        $cabangs = $cabangId 
            ? Cabang::where('id_cabang', $cabangId)->get()
            : Cabang::all();

        if ($cabangs->isEmpty()) {
            $this->error('❌ Tidak ada cabang yang ditemukan!');
            return 1;
        }

        $totalUpdated = 0;
        $totalChecked = 0;

        foreach ($cabangs as $cabang) {
            $this->info("📍 Cabang: {$cabang->nama_cabang} (ID: {$cabang->id_cabang})");
            $this->newLine();

            // Ambil semua produk di cabang ini
            $produks = Produk::where('id_cabang', $cabang->id_cabang)->get();

            if ($produks->isEmpty()) {
                $this->line('   Tidak ada produk di cabang ini.');
                $this->newLine();
                continue;
            }

            $this->withProgressBar($produks, function ($produk) use ($cabang, &$totalUpdated, &$totalChecked, $isDryRun) {
                $totalChecked++;
                $stokSekarang = $produk->stok;
                
                // Hitung maksimal produk yang bisa dibuat
                $maxProducible = $this->resepService->calculateMaxProducibleQuantity($produk, $cabang->id_cabang);

                // Jika stok produk lebih besar dari yang bisa dibuat, perlu sinkronisasi
                if ($stokSekarang > $maxProducible) {
                    $this->newLine();
                    $this->warn("   ⚠️  {$produk->nama_produk}: Stok {$stokSekarang} > Max {$maxProducible}");
                    
                    if (!$isDryRun) {
                        $produk->stok = $maxProducible;
                        $produk->save();
                        $this->info("   ✅ Diperbarui menjadi: {$maxProducible}");
                    } else {
                        $this->line("   🔍 Akan diperbarui menjadi: {$maxProducible}");
                    }
                    
                    $totalUpdated++;
                } elseif ($stokSekarang < $maxProducible) {
                    // Informasi: produk bisa ditambah stoknya
                    $this->newLine();
                    $this->comment("   ℹ️  {$produk->nama_produk}: Stok {$stokSekarang} < Max {$maxProducible} (bisa produksi tambahan " . ($maxProducible - $stokSekarang) . " unit)");
                }
            });

            $this->newLine(2);
        }

        $this->newLine();
        $this->info('✅ Sinkronisasi selesai!');
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Produk diperiksa', $totalChecked],
                ['Produk diperbarui', $totalUpdated],
                ['Produk tidak berubah', $totalChecked - $totalUpdated],
            ]
        );

        if ($isDryRun) {
            $this->newLine();
            $this->info('💡 Jalankan tanpa --dry-run untuk menyimpan perubahan ke database:');
            $this->line('   php artisan produk:sinkron-stok');
        }

        return 0;
    }
}
