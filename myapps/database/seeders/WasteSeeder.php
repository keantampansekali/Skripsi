<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Waste;
use App\Models\WasteItem;
use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\Cabang;
use App\Models\StockMovement;
use Carbon\Carbon;

class WasteSeeder extends Seeder
{
    public function run(): void
    {
        $ambon = Cabang::where('nama_cabang', 'Ambon')->first();
        $lombok = Cabang::where('nama_cabang', 'Lombok')->first();

        if (!$ambon || !$lombok) {
            $this->command->error('Cabang Ambon atau Lombok tidak ditemukan. Pastikan CabangSeeder sudah dijalankan.');
            return;
        }

        // Data waste management untuk setiap cabang
        $wasteData = [
            [
                'tanggal' => Carbon::now()->subDays(6),
                'catatan' => 'Pembuangan bahan baku yang sudah kadaluarsa',
                'items' => [
                    ['tipe' => 'bahan_baku', 'nama_bahan' => 'Selada', 'qty' => 200, 'alasan' => 'Kadaluarsa dan sudah layu'],
                    ['tipe' => 'bahan_baku', 'nama_bahan' => 'Tomat', 'qty' => 150, 'alasan' => 'Sudah busuk'],
                ],
            ],
            [
                'tanggal' => Carbon::now()->subDays(4),
                'catatan' => 'Bahan baku rusak karena kesalahan penyimpanan',
                'items' => [
                    ['tipe' => 'bahan_baku', 'nama_bahan' => 'Roti Burger', 'qty' => 10, 'alasan' => 'Terkena air dan berjamur'],
                    ['tipe' => 'bahan_baku', 'nama_bahan' => 'Keju Slice', 'qty' => 15, 'alasan' => 'Kemasan rusak'],
                ],
            ],
            [
                'tanggal' => Carbon::now()->subDays(3),
                'catatan' => 'Produk rusak dan tidak layak dijual',
                'items' => [
                    ['tipe' => 'produk', 'nama_produk' => 'Burger Sapi', 'qty' => 2, 'alasan' => 'Terjatuh dan kemasan rusak'],
                ],
            ],
            [
                'tanggal' => Carbon::now()->subDays(2),
                'catatan' => 'Bahan baku terbuang karena kesalahan produksi',
                'items' => [
                    ['tipe' => 'bahan_baku', 'nama_bahan' => 'Daging Ayam', 'qty' => 500, 'alasan' => 'Terlalu matang dan gosong'],
                    ['tipe' => 'bahan_baku', 'nama_bahan' => 'Minyak Goreng', 'qty' => 2000, 'alasan' => 'Sudah tidak layak pakai'],
                ],
            ],
            [
                'tanggal' => Carbon::now()->subDays(1),
                'catatan' => 'Pembuangan bahan baku yang tidak terpakai',
                'items' => [
                    ['tipe' => 'bahan_baku', 'nama_bahan' => 'Bawang Bombay', 'qty' => 100, 'alasan' => 'Sudah bertunas'],
                    ['tipe' => 'bahan_baku', 'nama_bahan' => 'Saus Mayonaise', 'qty' => 500, 'alasan' => 'Kemasan bocor'],
                ],
            ],
            [
                'tanggal' => Carbon::now(),
                'catatan' => 'Produk dan bahan baku rusak',
                'items' => [
                    ['tipe' => 'bahan_baku', 'nama_bahan' => 'Kentang', 'qty' => 300, 'alasan' => 'Sudah busuk'],
                    ['tipe' => 'produk', 'nama_produk' => 'Ayam Goreng', 'qty' => 1, 'alasan' => 'Terlalu lama di display'],
                ],
            ],
        ];

        // Insert untuk setiap cabang
        foreach ([$ambon, $lombok] as $cabang) {
            foreach ($wasteData as $data) {
                // Buat waste
                $waste = Waste::create([
                    'id_cabang' => $cabang->id_cabang,
                    'tanggal' => $data['tanggal'],
                    'catatan' => $data['catatan'],
                ]);

                // Buat waste items
                foreach ($data['items'] as $itemData) {
                    if ($itemData['tipe'] === 'bahan_baku') {
                        $bahanBaku = BahanBaku::where('id_cabang', $cabang->id_cabang)
                            ->where('nama_bahan', $itemData['nama_bahan'])
                            ->first();

                        if ($bahanBaku && $bahanBaku->stok >= $itemData['qty']) {
                            // Buat waste item
                            WasteItem::create([
                                'waste_id' => $waste->id,
                                'tipe' => 'bahan_baku',
                                'bahan_baku_id' => $bahanBaku->id,
                                'produk_id' => null,
                                'qty' => $itemData['qty'],
                                'alasan' => $itemData['alasan'] ?? null,
                            ]);

                            // Kurangi stok bahan baku
                            $bahanBaku->decrement('stok', $itemData['qty']);

                            // Buat stock movement
                            StockMovement::create([
                                'bahan_baku_id' => $bahanBaku->id,
                                'tipe' => 'out',
                                'qty' => $itemData['qty'],
                                'ref_type' => 'waste',
                                'ref_id' => $waste->id,
                                'id_cabang' => $cabang->id_cabang,
                                'keterangan' => 'Waste management: ' . ($itemData['alasan'] ?? 'Tanpa alasan'),
                            ]);
                        }
                    } elseif ($itemData['tipe'] === 'produk') {
                        $produk = Produk::where('id_cabang', $cabang->id_cabang)
                            ->where('nama_produk', $itemData['nama_produk'])
                            ->first();

                        if ($produk && $produk->stok >= $itemData['qty']) {
                            // Buat waste item
                            WasteItem::create([
                                'waste_id' => $waste->id,
                                'tipe' => 'produk',
                                'bahan_baku_id' => null,
                                'produk_id' => $produk->id,
                                'qty' => $itemData['qty'],
                                'alasan' => $itemData['alasan'] ?? null,
                            ]);

                            // Kurangi stok produk
                            $produk->decrement('stok', $itemData['qty']);
                        }
                    }
                }
            }
        }

        $this->command->info('Waste management berhasil di-seed untuk semua cabang!');
    }
}

