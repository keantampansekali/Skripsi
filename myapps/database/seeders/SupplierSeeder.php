<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\SupplierContact;

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
                'alamat' => 'Jl. Pejanggik No. 45, Mataram, Lombok',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:42:54',
            ],
            [
                'id' => 11,
                'id_cabang' => 2,
                'nama_supplier' => 'CV Daging Segar Makmur',
                'alamat' => 'Jl. Selaparang No. 128, Mataram, Lombok',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:24:42',
            ],
            [
                'id' => 12,
                'id_cabang' => 2,
                'nama_supplier' => 'PT Sayur Segar Indonesia',
                'alamat' => 'Jl. Majapahit No. 89, Mataram, Lombok',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:43:49',
            ],
            [
                'id' => 13,
                'id_cabang' => 2,
                'nama_supplier' => 'UD Roti Bakar Nusantara',
                'alamat' => 'Jl. Raya Senggigi No. 12, Lombok Barat',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:44:02',
            ],
            [
                'id' => 14,
                'id_cabang' => 2,
                'nama_supplier' => 'PT Minuman Segar Abadi',
                'alamat' => 'Jl. Panca Usaha No. 78, Mataram, Lombok',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:45:16',
            ],
            [
                'id' => 15,
                'id_cabang' => 2,
                'nama_supplier' => 'CV Bumbu Dapur Lengkap',
                'alamat' => 'Jl. Raya Kuta No. 56, Lombok Selatan',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:46:05',
            ],
            [
                'id' => 16,
                'id_cabang' => 2,
                'nama_supplier' => 'PT Kemasan Modern',
                'alamat' => 'Jl. Langko No. 34, Mataram, Lombok',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:46:28',
            ],
            [
                'id' => 17,
                'id_cabang' => 2,
                'nama_supplier' => 'UD Ayam Potong Segar',
                'alamat' => 'Jl. Taman Mayura No. 23, Mataram, Lombok',
                'created_at' => '2025-11-27 16:24:42',
                'updated_at' => '2025-11-27 16:26:38',
            ],
        ];

        // Data kontak untuk setiap supplier
        $contactsData = [
            2 => [ // PT Sumber Pangan Sejahtera - Ambon
                ['tipe' => 'telp', 'nilai' => '086476846321'],
                ['tipe' => 'wa', 'nilai' => '088237894823'],
                ['tipe' => 'email', 'nilai' => 'sumberpangan@email.com'],
            ],
            3 => [ // CV Daging Segar Makmur - Ambon
                ['tipe' => 'telp', 'nilai' => '081234567890'],
                ['tipe' => 'wa', 'nilai' => '081234567890'],
                ['tipe' => 'email', 'nilai' => 'dagingsegar@email.com'],
            ],
            4 => [ // PT Sayur Segar Indonesia - Ambon
                ['tipe' => 'telp', 'nilai' => '082345678901'],
                ['tipe' => 'wa', 'nilai' => '082345678901'],
                ['tipe' => 'email', 'nilai' => 'sayursegar@email.com'],
            ],
            5 => [ // UD Roti Nusantara - Ambon
                ['tipe' => 'telp', 'nilai' => '083456789012'],
                ['tipe' => 'wa', 'nilai' => '083456789012'],
                ['tipe' => 'email', 'nilai' => 'rotinusantara@email.com'],
            ],
            6 => [ // PT Minuman Segar Abadi - Ambon
                ['tipe' => 'telp', 'nilai' => '084567890123'],
                ['tipe' => 'wa', 'nilai' => '084567890123'],
                ['tipe' => 'email', 'nilai' => 'minumansegar@email.com'],
            ],
            7 => [ // CV Bumbu Dapur Lengkap - Ambon
                ['tipe' => 'telp', 'nilai' => '085678901234'],
                ['tipe' => 'wa', 'nilai' => '085678901234'],
                ['tipe' => 'email', 'nilai' => 'bumbudapur@email.com'],
            ],
            8 => [ // PT Kemasan Modern - Ambon
                ['tipe' => 'telp', 'nilai' => '086789012345'],
                ['tipe' => 'wa', 'nilai' => '086789012345'],
                ['tipe' => 'email', 'nilai' => 'kemasanmodern@email.com'],
            ],
            9 => [ // UD Ayam Potong Segar - Ambon
                ['tipe' => 'telp', 'nilai' => '087890123456'],
                ['tipe' => 'wa', 'nilai' => '087890123456'],
                ['tipe' => 'email', 'nilai' => 'ayampotong@email.com'],
            ],
            10 => [ // PT Sumber Pangan Sejahtera - Lombok
                ['tipe' => 'telp', 'nilai' => '088901234567'],
                ['tipe' => 'wa', 'nilai' => '088901234567'],
                ['tipe' => 'email', 'nilai' => 'sumberpangan.lombok@email.com'],
            ],
            11 => [ // CV Daging Segar Makmur - Lombok
                ['tipe' => 'telp', 'nilai' => '089012345678'],
                ['tipe' => 'wa', 'nilai' => '089012345678'],
                ['tipe' => 'email', 'nilai' => 'dagingsegar.lombok@email.com'],
            ],
            12 => [ // PT Sayur Segar Indonesia - Lombok
                ['tipe' => 'telp', 'nilai' => '081123456789'],
                ['tipe' => 'wa', 'nilai' => '081123456789'],
                ['tipe' => 'email', 'nilai' => 'sayursegar.lombok@email.com'],
            ],
            13 => [ // UD Roti Bakar Nusantara - Lombok
                ['tipe' => 'telp', 'nilai' => '082234567890'],
                ['tipe' => 'wa', 'nilai' => '082234567890'],
                ['tipe' => 'email', 'nilai' => 'rotibakar.lombok@email.com'],
            ],
            14 => [ // PT Minuman Segar Abadi - Lombok
                ['tipe' => 'telp', 'nilai' => '083345678901'],
                ['tipe' => 'wa', 'nilai' => '083345678901'],
                ['tipe' => 'email', 'nilai' => 'minumansegar.lombok@email.com'],
            ],
            15 => [ // CV Bumbu Dapur Lengkap - Lombok
                ['tipe' => 'telp', 'nilai' => '084456789012'],
                ['tipe' => 'wa', 'nilai' => '084456789012'],
                ['tipe' => 'email', 'nilai' => 'bumbudapur.lombok@email.com'],
            ],
            16 => [ // PT Kemasan Modern - Lombok
                ['tipe' => 'telp', 'nilai' => '085567890123'],
                ['tipe' => 'wa', 'nilai' => '085567890123'],
                ['tipe' => 'email', 'nilai' => 'kemasanmodern.lombok@email.com'],
            ],
            17 => [ // UD Ayam Potong Segar - Lombok
                ['tipe' => 'telp', 'nilai' => '086678901234'],
                ['tipe' => 'wa', 'nilai' => '086678901234'],
                ['tipe' => 'email', 'nilai' => 'ayampotong.lombok@email.com'],
            ],
        ];

        foreach ($data as $item) {
            $supplier = Supplier::updateOrCreate(
                ['id' => $item['id']],
                $item
            );

            // Tambahkan kontak jika ada data kontak untuk supplier ini
            if (isset($contactsData[$item['id']])) {
                // Hapus kontak lama jika ada
                $supplier->contacts()->delete();
                
                // Tambahkan kontak baru
                foreach ($contactsData[$item['id']] as $contact) {
                    $supplier->contacts()->create([
                        'tipe' => $contact['tipe'],
                        'nilai' => $contact['nilai'],
                    ]);
                }
            }
        }

        $this->command->info('Supplier berhasil di-seed dengan kontak!');
    }
}
