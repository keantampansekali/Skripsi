<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenyesuaianStok;
use App\Models\PenyesuaianItem;
use App\Models\BahanBaku;
use App\Models\Cabang;
use App\Models\StockMovement;
use Carbon\Carbon;

class PenyesuaianStokSeeder extends Seeder
{
    public function run(): void
    {
        $ambon = Cabang::where('nama_cabang', 'Ambon')->first();
        $lombok = Cabang::where('nama_cabang', 'Lombok')->first();

        if (!$ambon || !$lombok) {
            $this->command->error('Cabang Ambon atau Lombok tidak ditemukan. Pastikan CabangSeeder sudah dijalankan.');
            return;
        }

        // Data penyesuaian stok untuk setiap cabang
        $penyesuaianData = [
            [
                'tanggal' => Carbon::now()->subDays(4),
                'catatan' => 'Stock opname - Koreksi stok setelah audit gudang',
                'items' => [
                    ['nama_bahan' => 'Daging Sapi', 'stok_baru' => 4800, 'keterangan' => 'Stok berkurang karena ada yang rusak'],
                    ['nama_bahan' => 'Daging Ayam', 'stok_baru' => 5200, 'keterangan' => 'Stok bertambah dari stok tersembunyi'],
                    ['nama_bahan' => 'Roti Burger', 'stok_baru' => 95, 'keterangan' => 'Stok berkurang karena kadaluarsa'],
                ],
            ],
            [
                'tanggal' => Carbon::now()->subDays(2),
                'catatan' => 'Penyesuaian stok setelah inventarisasi bulanan',
                'items' => [
                    ['nama_bahan' => 'Keju Slice', 'stok_baru' => 180, 'keterangan' => 'Stok berkurang karena penggunaan tidak tercatat'],
                    ['nama_bahan' => 'Selada', 'stok_baru' => 2200, 'keterangan' => 'Stok bertambah dari pengiriman tambahan'],
                    ['nama_bahan' => 'Tomat', 'stok_baru' => 1900, 'keterangan' => 'Stok berkurang karena ada yang busuk'],
                ],
            ],
            [
                'tanggal' => Carbon::now()->subDays(1),
                'catatan' => 'Koreksi stok karena kesalahan input',
                'items' => [
                    ['nama_bahan' => 'Ayam Potong', 'stok_baru' => 120, 'keterangan' => 'Stok bertambah dari pengiriman yang terlambat dicatat'],
                    ['nama_bahan' => 'Saus Mayonaise', 'stok_baru' => 4800, 'keterangan' => 'Stok berkurang karena penggunaan untuk event'],
                    ['nama_bahan' => 'Saus Tomat', 'stok_baru' => 7500, 'keterangan' => 'Stok bertambah dari stok cadangan'],
                ],
            ],
            [
                'tanggal' => Carbon::now(),
                'catatan' => 'Penyesuaian stok harian - Koreksi minor',
                'items' => [
                    ['nama_bahan' => 'Kentang', 'stok_baru' => 10500, 'keterangan' => 'Stok bertambah dari pengiriman tambahan'],
                    ['nama_bahan' => 'Minyak Goreng', 'stok_baru' => 19500, 'keterangan' => 'Stok berkurang karena penggunaan tidak tercatat'],
                    ['nama_bahan' => 'Coca Cola', 'stok_baru' => 48000, 'keterangan' => 'Stok berkurang karena konsumsi harian'],
                ],
            ],
        ];

        // Insert untuk setiap cabang
        foreach ([$ambon, $lombok] as $cabang) {
            foreach ($penyesuaianData as $data) {
                // Buat penyesuaian stok
                $penyesuaian = PenyesuaianStok::create([
                    'id_cabang' => $cabang->id_cabang,
                    'tanggal' => $data['tanggal'],
                    'catatan' => $data['catatan'],
                ]);

                // Buat items penyesuaian
                foreach ($data['items'] as $itemData) {
                    $bahanBaku = BahanBaku::where('id_cabang', $cabang->id_cabang)
                        ->where('nama_bahan', $itemData['nama_bahan'])
                        ->first();

                    if ($bahanBaku) {
                        $stokLama = $bahanBaku->stok;
                        $stokBaru = $itemData['stok_baru'];
                        $selisih = $stokBaru - $stokLama;

                        // Buat penyesuaian item
                        PenyesuaianItem::create([
                            'penyesuaian_stok_id' => $penyesuaian->id,
                            'bahan_baku_id' => $bahanBaku->id,
                            'stok_lama' => $stokLama,
                            'stok_baru' => $stokBaru,
                            'selisih' => $selisih,
                            'keterangan' => $itemData['keterangan'] ?? null,
                        ]);

                        // Update stok bahan baku
                        $bahanBaku->update(['stok' => $stokBaru]);

                        // Buat stock movement
                        StockMovement::create([
                            'bahan_baku_id' => $bahanBaku->id,
                            'tipe' => 'adj',
                            'qty' => $selisih,
                            'ref_type' => 'penyesuaian',
                            'ref_id' => $penyesuaian->id,
                            'id_cabang' => $cabang->id_cabang,
                            'keterangan' => 'Penyesuaian stok',
                        ]);
                    }
                }
            }
        }

        $this->command->info('Penyesuaian stok berhasil di-seed untuk semua cabang!');
    }
}

