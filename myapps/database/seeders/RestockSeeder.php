<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restock;
use App\Models\RestockItem;
use App\Models\BahanBaku;
use App\Models\Supplier;
use App\Models\Cabang;
use Carbon\Carbon;

class RestockSeeder extends Seeder
{
    public function run(): void
    {
        $ambon = Cabang::where('nama_cabang', 'Ambon')->first();
        $lombok = Cabang::where('nama_cabang', 'Lombok')->first();

        if (!$ambon || !$lombok) {
            $this->command->error('Cabang Ambon atau Lombok tidak ditemukan. Pastikan CabangSeeder sudah dijalankan.');
            return;
        }

        // Ambil supplier untuk setiap cabang
        $suppliersAmbon = Supplier::where('id_cabang', $ambon->id_cabang)->get();
        $suppliersLombok = Supplier::where('id_cabang', $lombok->id_cabang)->get();

        if ($suppliersAmbon->isEmpty() || $suppliersLombok->isEmpty()) {
            $this->command->error('Supplier tidak ditemukan. Pastikan SupplierSeeder sudah dijalankan.');
            return;
        }

        // Data nota pembelian untuk setiap cabang
        $notaData = [
            [
                'tanggal' => Carbon::now()->subDays(5),
                'no_nota' => 'AB' . str_pad(rand(1, 99999), 6, '0', STR_PAD_LEFT),
                'supplier_index' => 0, // Index supplier
                'items' => [
                    ['nama_bahan' => 'Daging Sapi', 'qty' => 5000, 'harga_satuan' => 100],
                    ['nama_bahan' => 'Daging Ayam', 'qty' => 10000, 'harga_satuan' => 40],
                    ['nama_bahan' => 'Ayam Potong', 'qty' => 50, 'harga_satuan' => 15000],
                ],
                'diskon' => 50000,
                'ppn' => 0,
                'catatan' => 'Pembelian bahan daging untuk stok bulan ini',
            ],
            [
                'tanggal' => Carbon::now()->subDays(3),
                'no_nota' => 'AB' . str_pad(rand(1, 99999), 6, '0', STR_PAD_LEFT),
                'supplier_index' => 1,
                'items' => [
                    ['nama_bahan' => 'Roti Burger', 'qty' => 200, 'harga_satuan' => 2000],
                    ['nama_bahan' => 'Roti Hotdog', 'qty' => 150, 'harga_satuan' => 2500],
                    ['nama_bahan' => 'Keju Slice', 'qty' => 300, 'harga_satuan' => 3000],
                ],
                'diskon' => 0,
                'ppn' => 0,
                'catatan' => 'Pembelian roti dan keju',
            ],
            [
                'tanggal' => Carbon::now()->subDays(2),
                'no_nota' => 'AB' . str_pad(rand(1, 99999), 6, '0', STR_PAD_LEFT),
                'supplier_index' => 2,
                'items' => [
                    ['nama_bahan' => 'Selada', 'qty' => 3000, 'harga_satuan' => 15],
                    ['nama_bahan' => 'Tomat', 'qty' => 4000, 'harga_satuan' => 20],
                    ['nama_bahan' => 'Bawang Bombay', 'qty' => 2000, 'harga_satuan' => 30],
                ],
                'diskon' => 25000,
                'ppn' => 0,
                'catatan' => 'Pembelian sayuran segar',
            ],
            [
                'tanggal' => Carbon::now()->subDays(1),
                'no_nota' => 'AB' . str_pad(rand(1, 99999), 6, '0', STR_PAD_LEFT),
                'supplier_index' => 3,
                'items' => [
                    ['nama_bahan' => 'Saus Mayonaise', 'qty' => 10000, 'harga_satuan' => 40],
                    ['nama_bahan' => 'Saus Tomat', 'qty' => 8000, 'harga_satuan' => 30],
                    ['nama_bahan' => 'Saus Sambal', 'qty' => 5000, 'harga_satuan' => 35],
                ],
                'diskon' => 0,
                'ppn' => 110000,
                'catatan' => 'Pembelian saus dan bumbu',
            ],
            [
                'tanggal' => Carbon::now(),
                'no_nota' => 'AB' . str_pad(rand(1, 99999), 6, '0', STR_PAD_LEFT),
                'supplier_index' => 4,
                'items' => [
                    ['nama_bahan' => 'Coca Cola', 'qty' => 50000, 'harga_satuan' => 15],
                    ['nama_bahan' => 'Sprite', 'qty' => 50000, 'harga_satuan' => 15],
                    ['nama_bahan' => 'Es Batu', 'qty' => 20000, 'harga_satuan' => 2],
                ],
                'diskon' => 100000,
                'ppn' => 0,
                'catatan' => 'Pembelian minuman dan es',
            ],
        ];

        // Insert untuk setiap cabang
        foreach ([$ambon, $lombok] as $cabang) {
            $suppliers = $cabang->id_cabang == $ambon->id_cabang ? $suppliersAmbon : $suppliersLombok;
            $prefix = $cabang->id_cabang == $ambon->id_cabang ? 'AB' : 'LB';

            foreach ($notaData as $nota) {
                // Pastikan supplier_index tidak melebihi jumlah supplier
                $supplierIndex = min($nota['supplier_index'], $suppliers->count() - 1);
                $supplier = $suppliers[$supplierIndex];

                // Hitung subtotal dari items
                $subtotal = 0;
                $itemsData = [];

                foreach ($nota['items'] as $itemData) {
                    $bahanBaku = BahanBaku::where('id_cabang', $cabang->id_cabang)
                        ->where('nama_bahan', $itemData['nama_bahan'])
                        ->first();

                    if ($bahanBaku) {
                        $itemSubtotal = $itemData['qty'] * $itemData['harga_satuan'];
                        $subtotal += $itemSubtotal;

                        $itemsData[] = [
                            'bahan_baku' => $bahanBaku,
                            'qty' => $itemData['qty'],
                            'harga_satuan' => $itemData['harga_satuan'],
                            'subtotal' => $itemSubtotal,
                        ];
                    }
                }

                if (empty($itemsData)) {
                    continue; // Skip jika tidak ada bahan baku yang ditemukan
                }

                // Hitung total
                $diskon = $nota['diskon'] ?? 0;
                $subtotalSetelahDiskon = $subtotal - $diskon;
                $ppn = $nota['ppn'] ?? 0;
                $total = $subtotalSetelahDiskon + $ppn;

                // Generate no_nota dengan prefix cabang
                $noNota = $prefix . str_pad(rand(1, 99999), 6, '0', STR_PAD_LEFT);

                // Buat restock
                $restock = Restock::create([
                    'id_cabang' => $cabang->id_cabang,
                    'supplier_id' => $supplier->id,
                    'no_nota' => $noNota,
                    'tanggal' => $nota['tanggal'],
                    'catatan' => $nota['catatan'] ?? null,
                    'subtotal' => $subtotal,
                    'diskon' => $diskon,
                    'ppn' => $ppn,
                    'total' => $total,
                ]);

                // Buat restock items
                foreach ($itemsData as $itemData) {
                    RestockItem::create([
                        'restock_id' => $restock->id,
                        'bahan_baku_id' => $itemData['bahan_baku']->id,
                        'qty' => $itemData['qty'],
                        'harga_satuan' => $itemData['harga_satuan'],
                        'subtotal' => $itemData['subtotal'],
                    ]);

                    // Update stok bahan baku (tambah stok)
                    $itemData['bahan_baku']->increment('stok', $itemData['qty']);
                }
            }
        }

        $this->command->info('Nota pembelian berhasil di-seed untuk semua cabang!');
    }
}

