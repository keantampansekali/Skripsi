<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'id_cabang' => 1,
                'nama_supplier' => 'test',
                'alamat' => null,
                'created_at' => '2025-10-31 13:35:16',
                'updated_at' => '2025-10-31 13:35:16',
            ],
            [
                'id' => 2,
                'id_cabang' => 1,
                'nama_supplier' => 'PT Sumber Pangan Sejahtera',
                'alamat' => 'Jl. Karang panjang No. 45, Kota Ambon',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:29:45',
            ],
            [
                'id' => 3,
                'id_cabang' => 1,
                'nama_supplier' => 'CV Daging Segar Makmur',
                'alamat' => 'Jl. Laha No. 128, Kota Ambon',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:30:20',
            ],
            [
                'id' => 4,
                'id_cabang' => 1,
                'nama_supplier' => 'PT Sayur Segar Indonesia',
                'alamat' => 'Jl. Gunung Nona No. 89, Kota Ambon',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:30:48',
            ],
            [
                'id' => 5,
                'id_cabang' => 1,
                'nama_supplier' => 'UD Roti Nusantara',
                'alamat' => 'Jl. passo. 12, Kota Ambon',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:31:22',
            ],
            [
                'id' => 6,
                'id_cabang' => 1,
                'nama_supplier' => 'PT Minuman Segar Abadi',
                'alamat' => 'Jl. Kebun Cengkeh No. 78, Kota Ambon',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:31:12',
            ],
            [
                'id' => 7,
                'id_cabang' => 1,
                'nama_supplier' => 'CV Bumbu Dapur Lengkap',
                'alamat' => 'Jl. Waihaong No. 56, Kota Ambon',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:38:37',
            ],
            [
                'id' => 8,
                'id_cabang' => 1,
                'nama_supplier' => 'PT Kemasan Modern',
                'alamat' => 'Jl. Pattimura No. 34, Kota Ambon',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:39:01',
            ],
            [
                'id' => 9,
                'id_cabang' => 1,
                'nama_supplier' => 'UD Ayam Potong Segar',
                'alamat' => 'Jl. Ay patty, Kota Ambon',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:43:01',
            ],
            [
                'id' => 10,
                'id_cabang' => 2,
                'nama_supplier' => 'PT Sumber Pangan Sejahtera',
                'alamat' => 'Jl. Rijali No. 45, Kota Ambon',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:42:54',
            ],
            [
                'id' => 11,
                'id_cabang' => 2,
                'nama_supplier' => 'CV Daging Segar Makmur',
                'alamat' => 'Jl. Gatot Subroto No. 128, Jakarta Selatan',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:24:42',
            ],
            [
                'id' => 12,
                'id_cabang' => 2,
                'nama_supplier' => 'PT Sayur Segar Indonesia',
                'alamat' => 'Jl. Latuhalat No. 89, Kota Ambon',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:43:49',
            ],
            [
                'id' => 13,
                'id_cabang' => 2,
                'nama_supplier' => 'UD Roti Bakar Nusantara',
                'alamat' => 'Jl. Lampu Lima, Jakarta Pusat',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:44:02',
            ],
            [
                'id' => 14,
                'id_cabang' => 2,
                'nama_supplier' => 'PT Minuman Segar Abadi',
                'alamat' => 'Jl. Galala. 78, Kota Ambon',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:45:16',
            ],
            [
                'id' => 15,
                'id_cabang' => 2,
                'nama_supplier' => 'CV Bumbu Dapur Lengkap',
                'alamat' => 'Jl. Lateri. 56, Kota Ambon',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:46:05',
            ],
            [
                'id' => 16,
                'id_cabang' => 2,
                'nama_supplier' => 'PT Kemasan Modern',
                'alamat' => 'Jl. Nania No. 34, Kota Ambon',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:46:28',
            ],
            [
                'id' => 17,
                'id_cabang' => 2,
                'nama_supplier' => 'UD Ayam Potong Segar',
                'alamat' => 'Jl. Urimessing, kota ambon',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:26:38',
            ],
        ];

        foreach ($data as $item) {
            Supplier::updateOrCreate(
                ['id' => $item['id']],
                $item
            );
        }

        $this->command->info('Supplier berhasil di-seed!');
    }
}
