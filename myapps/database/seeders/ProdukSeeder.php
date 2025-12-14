<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;
use App\Models\Cabang;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $ambon = Cabang::where('nama_cabang', 'Ambon')->first();
        $lombok = Cabang::where('nama_cabang', 'Lombok')->first();

        if (!$ambon || !$lombok) {
            $this->command->error('Cabang Ambon atau Lombok tidak ditemukan. Pastikan CabangSeeder sudah dijalankan.');
            return;
        }

        // Data produk makanan cepat saji untuk cabang Ambon
        $produkAmbon = [
            // Burger
            [
                'id_cabang' => $ambon->id_cabang,
                'nama_produk' => 'Burger Sapi 1',
                'deskripsi' => 'Burger dengan daging sapi, keju, dan sayuran',
                'harga' => 25000,
                'stok' => 50,
            ],
            [
                'id_cabang' => $ambon->id_cabang,
                'nama_produk' => 'Burger Sapi 2',
                'deskripsi' => 'Burger double dengan daging sapi',
                'harga' => 35000,
                'stok' => 30,
            ],
            // Hotdog
            [
                'id_cabang' => $ambon->id_cabang,
                'nama_produk' => 'Hotdog Sosis Ayam',
                'deskripsi' => 'Hotdog dengan sosis ayam dan saus',
                'harga' => 20000,
                'stok' => 40,
            ],
            [
                'id_cabang' => $ambon->id_cabang,
                'nama_produk' => 'Hotdog Sosis Sapi',
                'deskripsi' => 'Hotdog dengan sosis sapi premium',
                'harga' => 25000,
                'stok' => 35,
            ],
            // Side dishes
            [
                'id_cabang' => $ambon->id_cabang,
                'nama_produk' => 'Kentang Goreng',
                'deskripsi' => 'Kentang goreng crispy',
                'harga' => 15000,
                'stok' => 60,
            ],
            // Minuman
            [
                'id_cabang' => $ambon->id_cabang,
                'nama_produk' => 'Coca Cola',
                'deskripsi' => 'Minuman bersoda Coca Cola',
                'harga' => 8000,
                'stok' => 100,
            ],
            [
                'id_cabang' => $ambon->id_cabang,
                'nama_produk' => 'Sprite',
                'deskripsi' => 'Minuman bersoda Sprite',
                'harga' => 8000,
                'stok' => 100,
            ],
            // Paket
            [
                'id_cabang' => $ambon->id_cabang,
                'nama_produk' => 'Paket A',
                'deskripsi' => 'Burger Sapi 1 + Kentang Goreng + Minuman',
                'harga' => 40000,
                'stok' => 25,
            ],
            [
                'id_cabang' => $ambon->id_cabang,
                'nama_produk' => 'Paket B',
                'deskripsi' => 'Hotdog Sosis Ayam + Kentang Goreng + Minuman',
                'harga' => 35000,
                'stok' => 30,
            ],
            [
                'id_cabang' => $ambon->id_cabang,
                'nama_produk' => 'Paket C',
                'deskripsi' => 'Burger Sapi 2 + Kentang Goreng + Minuman',
                'harga' => 50000,
                'stok' => 20,
            ],
        ];

        // Data produk makanan cepat saji untuk cabang Lombok (sama dengan Ambon)
        $produkLombok = [
            // Burger
            [
                'id_cabang' => $lombok->id_cabang,
                'nama_produk' => 'Burger Sapi 1',
                'deskripsi' => 'Burger dengan daging sapi, keju, dan sayuran',
                'harga' => 25000,
                'stok' => 50,
            ],
            [
                'id_cabang' => $lombok->id_cabang,
                'nama_produk' => 'Burger Sapi 2',
                'deskripsi' => 'Burger double dengan daging sapi',
                'harga' => 35000,
                'stok' => 30,
            ],
            // Hotdog
            [
                'id_cabang' => $lombok->id_cabang,
                'nama_produk' => 'Hotdog Sosis Ayam',
                'deskripsi' => 'Hotdog dengan sosis ayam dan saus',
                'harga' => 20000,
                'stok' => 40,
            ],
            [
                'id_cabang' => $lombok->id_cabang,
                'nama_produk' => 'Hotdog Sosis Sapi',
                'deskripsi' => 'Hotdog dengan sosis sapi premium',
                'harga' => 25000,
                'stok' => 35,
            ],
            // Side dishes
            [
                'id_cabang' => $lombok->id_cabang,
                'nama_produk' => 'Kentang Goreng',
                'deskripsi' => 'Kentang goreng crispy',
                'harga' => 15000,
                'stok' => 60,
            ],
            // Minuman
            [
                'id_cabang' => $lombok->id_cabang,
                'nama_produk' => 'Coca Cola',
                'deskripsi' => 'Minuman bersoda Coca Cola',
                'harga' => 8000,
                'stok' => 100,
            ],
            [
                'id_cabang' => $lombok->id_cabang,
                'nama_produk' => 'Sprite',
                'deskripsi' => 'Minuman bersoda Sprite',
                'harga' => 8000,
                'stok' => 100,
            ],
            // Paket
            [
                'id_cabang' => $lombok->id_cabang,
                'nama_produk' => 'Paket A',
                'deskripsi' => 'Burger Sapi 1 + Kentang Goreng + Minuman',
                'harga' => 40000,
                'stok' => 25,
            ],
            [
                'id_cabang' => $lombok->id_cabang,
                'nama_produk' => 'Paket B',
                'deskripsi' => 'Hotdog Sosis Ayam + Kentang Goreng + Minuman',
                'harga' => 35000,
                'stok' => 30,
            ],
            [
                'id_cabang' => $lombok->id_cabang,
                'nama_produk' => 'Paket C',
                'deskripsi' => 'Burger Sapi 2 + Kentang Goreng + Minuman',
                'harga' => 50000,
                'stok' => 20,
            ],
        ];

        // Gunakan updateOrCreate untuk menghindari duplikat
        foreach ($produkAmbon as $produk) {
            Produk::updateOrCreate(
                [
                    'id_cabang' => $produk['id_cabang'],
                    'nama_produk' => $produk['nama_produk'],
                ],
                [
                    'deskripsi' => $produk['deskripsi'],
                    'harga' => $produk['harga'],
                    'stok' => $produk['stok'],
                ]
            );
        }

        foreach ($produkLombok as $produk) {
            Produk::updateOrCreate(
                [
                    'id_cabang' => $produk['id_cabang'],
                    'nama_produk' => $produk['nama_produk'],
                ],
                [
                    'deskripsi' => $produk['deskripsi'],
                    'harga' => $produk['harga'],
                    'stok' => $produk['stok'],
                ]
            );
        }
    }
}
