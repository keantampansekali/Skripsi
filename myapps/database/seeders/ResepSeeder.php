<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Resep;
use App\Models\ResepItem;
use App\Models\Produk;
use App\Models\BahanBaku;
use App\Models\Cabang;

class ResepSeeder extends Seeder
{
    public function run(): void
    {
        $ambon = Cabang::where('nama_cabang', 'Ambon')->first();
        $lombok = Cabang::where('nama_cabang', 'Lombok')->first();

        if (!$ambon || !$lombok) {
            $this->command->error('Cabang Ambon atau Lombok tidak ditemukan.');
            return;
        }

        foreach ([$ambon, $lombok] as $cabang) {
            $this->seedResepForCabang($cabang);
        }

        $this->command->info('Resep berhasil di-seed untuk semua cabang!');
    }

    private function seedResepForCabang($cabang)
    {
        // Helper function untuk mendapatkan bahan baku
        $getBahan = function($namaBahan) use ($cabang) {
            $bahan = BahanBaku::where('id_cabang', $cabang->id_cabang)
                ->where('nama_bahan', $namaBahan)
                ->first();
            
            if (!$bahan) {
                $this->command->warn("Bahan baku '{$namaBahan}' tidak ditemukan di cabang {$cabang->nama_cabang}");
            }
            
            return $bahan;
        };

        // Helper function untuk mendapatkan produk
        $getProduk = function($namaProduk) use ($cabang) {
            $produk = Produk::where('id_cabang', $cabang->id_cabang)
                ->where('nama_produk', $namaProduk)
                ->first();
            
            if (!$produk) {
                $this->command->warn("Produk '{$namaProduk}' tidak ditemukan di cabang {$cabang->nama_cabang}");
            }
            
            return $produk;
        };

        // Helper function untuk membuat resep dengan bahan baku
        $createResep = function($namaResep, $produk, $bahanList) use ($cabang, $getBahan) {
            if (!$produk) {
                return;
            }

            $resep = Resep::create([
                'id_cabang' => $cabang->id_cabang,
                'nama_resep' => $namaResep,
                'produk_id' => $produk->id,
                'deskripsi' => "Resep untuk membuat {$produk->nama_produk}",
            ]);

            $items = [];
            foreach ($bahanList as $bahan) {
                $bahanBaku = $getBahan($bahan['nama']);
                if ($bahanBaku) {
                    $items[] = ['bahan_baku_id' => $bahanBaku->id, 'qty' => $bahan['qty']];
                }
            }
            
            if (!empty($items)) {
                $resep->items()->createMany($items);
            }
        };

        // Resep Burger Sapi 1
        $createResep(
            'Resep Burger Sapi 1',
            $getProduk('Burger Sapi 1'),
            [
                ['nama' => 'Roti Burger', 'qty' => 2],
                ['nama' => 'Daging Sapi', 'qty' => 150],
                ['nama' => 'Keju Slice', 'qty' => 1],
                ['nama' => 'Selada', 'qty' => 20],
                ['nama' => 'Tomat', 'qty' => 30],
                ['nama' => 'Bawang Bombay', 'qty' => 20],
                ['nama' => 'Saus Mayonaise', 'qty' => 15],
                ['nama' => 'Saus Tomat', 'qty' => 10],
            ]
        );

        // Resep Burger Sapi 2 (Double)
        $createResep(
            'Resep Burger Sapi 2',
            $getProduk('Burger Sapi 2'),
            [
                ['nama' => 'Roti Burger', 'qty' => 2],
                ['nama' => 'Daging Sapi', 'qty' => 300], // Double daging
                ['nama' => 'Keju Slice', 'qty' => 2], // Double keju
                ['nama' => 'Selada', 'qty' => 30],
                ['nama' => 'Tomat', 'qty' => 40],
                ['nama' => 'Bawang Bombay', 'qty' => 30],
                ['nama' => 'Saus Mayonaise', 'qty' => 20],
                ['nama' => 'Saus Tomat', 'qty' => 15],
            ]
        );

        // Resep Hotdog Sosis Ayam
        $createResep(
            'Resep Hotdog Sosis Ayam',
            $getProduk('Hotdog Sosis Ayam'),
            [
                ['nama' => 'Roti Hotdog', 'qty' => 1],
                ['nama' => 'Sosis Ayam', 'qty' => 1],
                ['nama' => 'Saus Tomat', 'qty' => 20],
                ['nama' => 'Saus Mayonaise', 'qty' => 15],
                ['nama' => 'Bawang Bombay', 'qty' => 15],
            ]
        );

        // Resep Hotdog Sosis Sapi
        $createResep(
            'Resep Hotdog Sosis Sapi',
            $getProduk('Hotdog Sosis Sapi'),
            [
                ['nama' => 'Roti Hotdog', 'qty' => 1],
                ['nama' => 'Sosis Sapi', 'qty' => 1],
                ['nama' => 'Saus Tomat', 'qty' => 20],
                ['nama' => 'Saus Mayonaise', 'qty' => 15],
                ['nama' => 'Bawang Bombay', 'qty' => 15],
            ]
        );

        // Resep Kentang Goreng
        $createResep(
            'Resep Kentang Goreng',
            $getProduk('Kentang Goreng'),
            [
                ['nama' => 'Kentang', 'qty' => 200],
                ['nama' => 'Minyak Goreng', 'qty' => 50],
                ['nama' => 'Garam', 'qty' => 5],
            ]
        );

        // Resep Coca Cola
        $createResep(
            'Resep Coca Cola',
            $getProduk('Coca Cola'),
            [
                ['nama' => 'Coca Cola', 'qty' => 350],
                ['nama' => 'Es Batu', 'qty' => 100],
            ]
        );

        // Resep Sprite
        $createResep(
            'Resep Sprite',
            $getProduk('Sprite'),
            [
                ['nama' => 'Sprite', 'qty' => 350],
                ['nama' => 'Es Batu', 'qty' => 100],
            ]
        );

        // Resep Paket A (Burger Sapi 1 + Kentang + Minuman)
        $createResep(
            'Resep Paket A',
            $getProduk('Paket A'),
            [
                // Bahan untuk Burger Sapi 1
                ['nama' => 'Roti Burger', 'qty' => 2],
                ['nama' => 'Daging Sapi', 'qty' => 150],
                ['nama' => 'Keju Slice', 'qty' => 1],
                ['nama' => 'Selada', 'qty' => 20],
                ['nama' => 'Tomat', 'qty' => 30],
                ['nama' => 'Bawang Bombay', 'qty' => 20],
                ['nama' => 'Saus Mayonaise', 'qty' => 15],
                ['nama' => 'Saus Tomat', 'qty' => 10],
                // Bahan untuk Kentang Goreng
                ['nama' => 'Kentang', 'qty' => 200],
                ['nama' => 'Minyak Goreng', 'qty' => 50],
                ['nama' => 'Garam', 'qty' => 5],
                // Bahan untuk Minuman (Coca Cola)
                ['nama' => 'Coca Cola', 'qty' => 350],
                ['nama' => 'Es Batu', 'qty' => 100],
            ]
        );

        // Resep Paket B (Hotdog Sosis Ayam + Kentang + Minuman)
        $createResep(
            'Resep Paket B',
            $getProduk('Paket B'),
            [
                // Bahan untuk Hotdog
                ['nama' => 'Roti Hotdog', 'qty' => 1],
                ['nama' => 'Sosis Ayam', 'qty' => 1],
                ['nama' => 'Saus Tomat', 'qty' => 20],
                ['nama' => 'Saus Mayonaise', 'qty' => 15],
                ['nama' => 'Bawang Bombay', 'qty' => 15],
                // Bahan untuk Kentang Goreng
                ['nama' => 'Kentang', 'qty' => 200],
                ['nama' => 'Minyak Goreng', 'qty' => 50],
                ['nama' => 'Garam', 'qty' => 5],
                // Bahan untuk Minuman
                ['nama' => 'Coca Cola', 'qty' => 350],
                ['nama' => 'Es Batu', 'qty' => 100],
            ]
        );

        // Resep Paket C (Burger Sapi 2 + Kentang + Minuman)
        $createResep(
            'Resep Paket C',
            $getProduk('Paket C'),
            [
                // Bahan untuk Burger Sapi 2 (Double)
                ['nama' => 'Roti Burger', 'qty' => 2],
                ['nama' => 'Daging Sapi', 'qty' => 300],
                ['nama' => 'Keju Slice', 'qty' => 2],
                ['nama' => 'Selada', 'qty' => 30],
                ['nama' => 'Tomat', 'qty' => 40],
                ['nama' => 'Bawang Bombay', 'qty' => 30],
                ['nama' => 'Saus Mayonaise', 'qty' => 20],
                ['nama' => 'Saus Tomat', 'qty' => 15],
                // Bahan untuk Kentang Goreng
                ['nama' => 'Kentang', 'qty' => 200],
                ['nama' => 'Minyak Goreng', 'qty' => 50],
                ['nama' => 'Garam', 'qty' => 5],
                // Bahan untuk Minuman
                ['nama' => 'Coca Cola', 'qty' => 350],
                ['nama' => 'Es Batu', 'qty' => 100],
            ]
        );
    }
}
