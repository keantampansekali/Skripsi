<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BahanBaku;
use App\Models\Cabang;

class BahanBakuSeeder extends Seeder
{
    public function run(): void
    {
        $ambon = Cabang::where('nama_cabang', 'Ambon')->first();
        $lombok = Cabang::where('nama_cabang', 'Lombok')->first();

        if (!$ambon || !$lombok) {
            $this->command->error('Cabang Ambon atau Lombok tidak ditemukan. Pastikan CabangSeeder sudah dijalankan.');
            return;
        }

        // Data bahan baku untuk makanan cepat saji (KFC/McDonald's style)
        // Harga disesuaikan dengan harga pasar yang realistis (dalam Rupiah)
        // Format satuan: "angka + satuan" (contoh: "1 pcs", "500 gram", "250 ml")
        // Catatan: harga_satuan adalah harga per satuan yang ditulis (misalnya "500 gram" = harga untuk 500 gram)
        $bahanBakuData = [
            // Bahan untuk burger
            ['nama_bahan' => 'Roti Burger', 'satuan' => '1 pcs', 'stok' => 100, 'harga_satuan' => 3500], // Rp 3.500 per roti
            ['nama_bahan' => 'Daging Sapi', 'satuan' => '500 gram', 'stok' => 5000, 'harga_satuan' => 42500], // Rp 42.500 per 500 gram = Rp 85.000 per kg
            ['nama_bahan' => 'Daging Ayam', 'satuan' => '500 gram', 'stok' => 5000, 'harga_satuan' => 20000], // Rp 20.000 per 500 gram = Rp 40.000 per kg
            ['nama_bahan' => 'Keju Slice', 'satuan' => '1 pcs', 'stok' => 200, 'harga_satuan' => 3500], // Rp 3.500 per slice
            ['nama_bahan' => 'Selada', 'satuan' => '250 gram', 'stok' => 2000, 'harga_satuan' => 5000], // Rp 5.000 per 250 gram = Rp 20.000 per kg
            ['nama_bahan' => 'Tomat', 'satuan' => '250 gram', 'stok' => 2000, 'harga_satuan' => 5000], // Rp 5.000 per 250 gram = Rp 20.000 per kg
            ['nama_bahan' => 'Bawang Bombay', 'satuan' => '250 gram', 'stok' => 1000, 'harga_satuan' => 7500], // Rp 7.500 per 250 gram = Rp 30.000 per kg
            ['nama_bahan' => 'Saus Mayonaise', 'satuan' => '500 ml', 'stok' => 5000, 'harga_satuan' => 22000], // Rp 22.000 per 500 ml = Rp 44.000 per liter
            ['nama_bahan' => 'Saus Tomat', 'satuan' => '500 ml', 'stok' => 5000, 'harga_satuan' => 16000], // Rp 16.000 per 500 ml = Rp 32.000 per liter
            ['nama_bahan' => 'Saus Sambal', 'satuan' => '500 ml', 'stok' => 3000, 'harga_satuan' => 18000], // Rp 18.000 per 500 ml = Rp 36.000 per liter
            
            // Bahan untuk ayam goreng
            ['nama_bahan' => 'Ayam Potong', 'satuan' => '1 pcs', 'stok' => 100, 'harga_satuan' => 22000], // Rp 22.000 per potong
            ['nama_bahan' => 'Tepung Bumbu', 'satuan' => '1 kg', 'stok' => 10000, 'harga_satuan' => 22000], // Rp 22.000 per kg
            ['nama_bahan' => 'Minyak Goreng', 'satuan' => '1 liter', 'stok' => 20000, 'harga_satuan' => 19000], // Rp 19.000 per liter
            
            // Bahan untuk kentang goreng
            ['nama_bahan' => 'Kentang', 'satuan' => '1 kg', 'stok' => 10000, 'harga_satuan' => 25000], // Rp 25.000 per kg
            ['nama_bahan' => 'Garam', 'satuan' => '500 gram', 'stok' => 5000, 'harga_satuan' => 3000], // Rp 3.000 per 500 gram = Rp 6.000 per kg
            
            // Bahan untuk hotdog
            ['nama_bahan' => 'Roti Hotdog', 'satuan' => '1 pcs', 'stok' => 100, 'harga_satuan' => 3000], // Rp 3.000 per roti
            ['nama_bahan' => 'Sosis Ayam', 'satuan' => '1 pcs', 'stok' => 150, 'harga_satuan' => 5500], // Rp 5.500 per sosis
            ['nama_bahan' => 'Sosis Sapi', 'satuan' => '1 pcs', 'stok' => 150, 'harga_satuan' => 6500], // Rp 6.500 per sosis
            
            // Bahan untuk minuman
            ['nama_bahan' => 'Coca Cola', 'satuan' => '1 liter', 'stok' => 50000, 'harga_satuan' => 15000], // Rp 15.000 per liter
            ['nama_bahan' => 'Sprite', 'satuan' => '1 liter', 'stok' => 50000, 'harga_satuan' => 15000], // Rp 15.000 per liter
            ['nama_bahan' => 'Es Batu', 'satuan' => '1 kg', 'stok' => 10000, 'harga_satuan' => 2000], // Rp 2.000 per kg
            
            // Bahan untuk nasi
            ['nama_bahan' => 'Beras', 'satuan' => '1 kg', 'stok' => 20000, 'harga_satuan' => 13000], // Rp 13.000 per kg
            
            // Bahan tambahan
            ['nama_bahan' => 'merica', 'satuan' => '1 gram', 'stok' => 1000, 'harga_satuan' => 50], // Rp 50 per gram = Rp 50.000 per kg
        ];

        // Insert untuk setiap cabang (gunakan updateOrCreate untuk menghindari duplikat)
        foreach ([$ambon, $lombok] as $cabang) {
            foreach ($bahanBakuData as $bahan) {
                BahanBaku::updateOrCreate(
                    [
                        'id_cabang' => $cabang->id_cabang,
                        'nama_bahan' => $bahan['nama_bahan'],
                    ],
                    [
                        'satuan' => $bahan['satuan'],
                        'stok' => $bahan['stok'],
                        'harga_satuan' => $bahan['harga_satuan'],
                    ]
                );
            }
        }

        $this->command->info('Bahan baku berhasil di-seed untuk semua cabang!');
    }
}

