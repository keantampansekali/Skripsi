<?php

namespace App\Services;

use App\Models\Produk;
use App\Models\Resep;
use App\Models\BahanBaku;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResepService
{
    /**
     * Hitung maksimal produk yang bisa dibuat berdasarkan stok bahan baku
     * 
     * @param Produk $produk
     * @param int|null $idCabang
     * @return int Maksimal quantity produk yang bisa dibuat (0 jika tidak ada resep atau bahan baku habis)
     */
    public function calculateMaxProducibleQuantity(Produk $produk, ?int $idCabang = null): int
    {
        $idCabang = $idCabang ?? session('id_cabang');
        
        // Cari resep yang terkait dengan produk di cabang yang sama
        $resep = Resep::where('produk_id', $produk->id)
            ->where('id_cabang', $idCabang)
            ->first();
        
        // Jika produk tidak punya resep, kembalikan stok produk saat ini (tidak dibatasi oleh bahan baku)
        if (!$resep) {
            return $produk->stok;
        }

        $resep->load('items.bahan');
        $maxQuantities = [];

        foreach ($resep->items as $item) {
            $bahan = $item->bahan;
            if (!$bahan || $item->qty <= 0) {
                continue;
            }

            // Cek stok bahan baku di cabang yang sama
            $stokTersedia = BahanBaku::where('id', $bahan->id)
                ->where('id_cabang', $idCabang)
                ->value('stok') ?? 0;

            // Hitung maksimal produk yang bisa dibuat dari bahan baku ini
            $maxFromThisBahan = floor($stokTersedia / $item->qty);
            $maxQuantities[] = $maxFromThisBahan;
        }

        // Jika tidak ada item resep, return stok produk saat ini
        if (empty($maxQuantities)) {
            return $produk->stok;
        }

        // Return minimum dari semua maksimal (bottleneck)
        return min($maxQuantities);
    }

    /**
     * Cek ketersediaan bahan baku untuk membuat produk berdasarkan resep
     * 
     * @param Produk $produk
     * @param int $quantity Jumlah produk yang akan dibuat/dijual
     * @return array ['available' => bool, 'missing' => array, 'message' => string, 'max_quantity' => int]
     */
    public function checkBahanBakuAvailability(Produk $produk, int $quantity): array
    {
        $idCabang = session('id_cabang');
        
        // Cari resep yang terkait dengan produk di cabang yang sama
        $resep = Resep::where('produk_id', $produk->id)
            ->where('id_cabang', $idCabang)
            ->first();
        
        // Hitung maksimal quantity yang bisa dibuat
        $maxQuantity = $this->calculateMaxProducibleQuantity($produk, $idCabang);
        
        // Jika produk tidak punya resep, return available
        if (!$resep) {
            return [
                'available' => true,
                'missing' => [],
                'message' => 'Produk tidak memiliki resep',
                'max_quantity' => $maxQuantity
            ];
        }

        $resep->load('items.bahan');
        $missing = [];

        foreach ($resep->items as $item) {
            $bahan = $item->bahan;
            if (!$bahan) {
                continue;
            }

            // Hitung kebutuhan bahan baku untuk jumlah produk
            $kebutuhan = $item->qty * $quantity;
            
            // Cek stok bahan baku di cabang yang sama
            $stokTersedia = BahanBaku::where('id', $bahan->id)
                ->where('id_cabang', $idCabang)
                ->value('stok') ?? 0;

            if ($stokTersedia < $kebutuhan) {
                $missing[] = [
                    'bahan_baku_id' => $bahan->id,
                    'nama_bahan' => $bahan->nama_bahan,
                    'satuan' => $bahan->satuan,
                    'kebutuhan' => $kebutuhan,
                    'stok_tersedia' => $stokTersedia,
                    'kurang' => $kebutuhan - $stokTersedia,
                ];
            }
        }

        return [
            'available' => empty($missing),
            'missing' => $missing,
            'message' => empty($missing) 
                ? 'Semua bahan baku tersedia' 
                : 'Beberapa bahan baku tidak mencukupi',
            'max_quantity' => $maxQuantity
        ];
    }

    /**
     * Kurangi stok bahan baku berdasarkan resep produk yang dijual
     * 
     * @param Produk $produk
     * @param int $quantity Jumlah produk yang dijual
     * @param int $transaksiId ID transaksi kasir (optional)
     * @return array ['success' => bool, 'message' => string, 'details' => array]
     */
    public function reduceBahanBakuFromResep(Produk $produk, int $quantity, ?int $transaksiId = null): array
    {
        $idCabang = session('id_cabang');
        
        // Cari resep yang terkait dengan produk di cabang yang sama
        $resep = Resep::where('produk_id', $produk->id)
            ->where('id_cabang', $idCabang)
            ->first();
        
        // Jika produk tidak punya resep, tidak perlu kurangi bahan baku
        if (!$resep) {
            return [
                'success' => true,
                'message' => 'Produk tidak memiliki resep, tidak ada bahan baku yang dikurangi',
                'details' => []
            ];
        }

        $resep->load('items.bahan');
        $details = [];

        try {
            DB::transaction(function () use ($resep, $quantity, $idCabang, $transaksiId, &$details) {
                foreach ($resep->items as $item) {
                    $bahan = $item->bahan;
                    if (!$bahan) {
                        continue;
                    }

                    // Hitung kebutuhan bahan baku
                    $kebutuhan = $item->qty * $quantity;
                    
                    // Cari bahan baku di cabang yang sama
                    $bahanBaku = BahanBaku::where('id', $bahan->id)
                        ->where('id_cabang', $idCabang)
                        ->first();

                    if (!$bahanBaku) {
                        Log::warning("Bahan baku {$bahan->nama_bahan} tidak ditemukan di cabang {$idCabang}");
                        continue;
                    }

                    // Kurangi stok
                    $stokLama = $bahanBaku->stok;
                    $bahanBaku->decrement('stok', $kebutuhan);
                    $bahanBaku->refresh();

                    // Catat stock movement
                    $produkNama = $resep->produk ? $resep->produk->nama_produk : $resep->nama_resep;
                    StockMovement::create([
                        'bahan_baku_id' => $bahanBaku->id,
                        'tipe' => 'out',
                        'qty' => $kebutuhan,
                        'ref_type' => 'penjualan',
                        'ref_id' => $transaksiId,
                        'id_cabang' => $idCabang,
                        'keterangan' => "Penggunaan bahan baku untuk produk: {$produkNama} (qty: {$quantity})",
                    ]);

                    $details[] = [
                        'bahan_baku' => $bahanBaku->nama_bahan,
                        'qty_digunakan' => $kebutuhan,
                        'stok_sebelum' => $stokLama,
                        'stok_sesudah' => $bahanBaku->stok,
                    ];
                }
            });

            return [
                'success' => true,
                'message' => 'Stok bahan baku berhasil dikurangi',
                'details' => $details
            ];
        } catch (\Exception $e) {
            Log::error('Error reducing bahan baku from resep: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal mengurangi stok bahan baku: ' . $e->getMessage(),
                'details' => []
            ];
        }
    }

    /**
     * Cek dan kurangi bahan baku sekaligus (untuk transaksi kasir)
     * 
     * @param Produk $produk
     * @param int $quantity
     * @param int|null $transaksiId
     * @return array
     */
    public function checkAndReduceBahanBaku(Produk $produk, int $quantity, ?int $transaksiId = null): array
    {
        // Cek ketersediaan dulu
        $check = $this->checkBahanBakuAvailability($produk, $quantity);
        
        if (!$check['available']) {
            return [
                'success' => false,
                'message' => 'Bahan baku tidak mencukupi',
                'missing' => $check['missing']
            ];
        }

        // Jika tersedia, kurangi stok
        return $this->reduceBahanBakuFromResep($produk, $quantity, $transaksiId);
    }

    /**
     * Hitung Cost of Goods Sold (COGS) untuk produk berdasarkan resep
     * 
     * @param Produk $produk
     * @return array ['cogs' => float, 'details' => array, 'message' => string]
     */
    public function calculateProductCost(Produk $produk): array
    {
        $idCabang = session('id_cabang');
        
        // Cari resep yang terkait dengan produk di cabang yang sama
        $resep = Resep::where('produk_id', $produk->id)
            ->where('id_cabang', $idCabang)
            ->first();
        
        // Jika produk tidak punya resep, return 0
        if (!$resep) {
            return [
                'cogs' => 0,
                'details' => [],
                'message' => 'Produk tidak memiliki resep'
            ];
        }

        $resep->load('items.bahan');
        $totalCost = 0;
        $details = [];

        foreach ($resep->items as $item) {
            $bahan = $item->bahan;
            if (!$bahan) {
                continue;
            }

            // Parse satuan untuk mendapatkan nilai dan jenis
            $satuanParts = explode(' ', $bahan->satuan, 2);
            $nilaiSatuan = count($satuanParts) > 1 && is_numeric($satuanParts[0]) ? (float)$satuanParts[0] : 1;
            $jenisSatuan = count($satuanParts) > 1 ? $satuanParts[1] : $bahan->satuan;

            // Hitung harga per unit terkecil (gram/ml/pcs)
            // harga_satuan adalah harga untuk satuan yang ditulis (misalnya "500 gram")
            // Jadi harga per gram = harga_satuan / nilai_satuan
            $hargaPerUnit = $bahan->harga_satuan / $nilaiSatuan;
            
            // Hitung biaya bahan baku untuk 1 produk
            $biayaBahan = $item->qty * $hargaPerUnit;
            $totalCost += $biayaBahan;

            $details[] = [
                'bahan_baku' => $bahan->nama_bahan,
                'qty' => $item->qty,
                'satuan' => $bahan->satuan,
                'harga_satuan' => $bahan->harga_satuan,
                'harga_per_unit' => $hargaPerUnit,
                'biaya' => $biayaBahan,
            ];
        }

        return [
            'cogs' => round($totalCost, 2),
            'details' => $details,
            'message' => 'COGS berhasil dihitung'
        ];
    }

    /**
     * Hitung harga jual yang disarankan berdasarkan COGS dan margin
     * 
     * @param Produk $produk
     * @param float $marginPercent Margin profit dalam persen (default: 50%)
     * @return array ['suggested_price' => float, 'cogs' => float, 'margin' => float, 'details' => array]
     */
    public function calculateSuggestedPrice(Produk $produk, float $marginPercent = 50): array
    {
        $costData = $this->calculateProductCost($produk);
        $cogs = $costData['cogs'];
        
        // Hitung harga jual dengan margin
        // Harga jual = COGS / (1 - margin/100)
        // Contoh: COGS = 10.000, margin = 50% → Harga jual = 10.000 / (1 - 0.5) = 20.000
        $suggestedPrice = $cogs > 0 ? round($cogs / (1 - ($marginPercent / 100)), 0) : 0;
        $marginAmount = $suggestedPrice - $cogs;

        return [
            'suggested_price' => $suggestedPrice,
            'cogs' => $cogs,
            'margin_percent' => $marginPercent,
            'margin_amount' => round($marginAmount, 2),
            'details' => $costData['details'],
            'message' => $cogs > 0 
                ? "Harga jual disarankan: Rp " . number_format($suggestedPrice, 0, ',', '.') . " (COGS: Rp " . number_format($cogs, 0, ',', '.') . ", Margin: {$marginPercent}%)"
                : 'Produk tidak memiliki resep, tidak dapat menghitung harga jual'
        ];
    }
}

