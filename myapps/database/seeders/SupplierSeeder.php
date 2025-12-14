<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\SupplierContact;
use App\Models\Cabang;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $ambon = Cabang::where('nama_cabang', 'Ambon')->first();
        $lombok = Cabang::where('nama_cabang', 'Lombok')->first();

        if (!$ambon || !$lombok) {
            $this->command->error('Cabang Ambon atau Lombok tidak ditemukan. Pastikan CabangSeeder sudah dijalankan.');
            return;
        }

        // Data supplier untuk makanan cepat saji
        $suppliersData = [
            [
                'nama_supplier' => 'PT Sumber Pangan Sejahtera',
                'alamat' => 'Jl. Raya Sudirman No. 45, Jakarta Pusat',
                'kontak' => [
                    ['tipe' => 'telp', 'nilai' => '021-5551234'],
                    ['tipe' => 'wa', 'nilai' => '081234567890'],
                    ['tipe' => 'email', 'nilai' => 'info@sumberpangan.com'],
                ],
            ],
            [
                'nama_supplier' => 'CV Daging Segar Makmur',
                'alamat' => 'Jl. Gatot Subroto No. 128, Jakarta Selatan',
                'kontak' => [
                    ['tipe' => 'telp', 'nilai' => '021-5555678'],
                    ['tipe' => 'wa', 'nilai' => '081234567891'],
                    ['tipe' => 'email', 'nilai' => 'order@dagingsegar.com'],
                ],
            ],
            [
                'nama_supplier' => 'PT Sayur Segar Indonesia',
                'alamat' => 'Jl. Pasar Minggu Raya No. 89, Jakarta Selatan',
                'kontak' => [
                    ['tipe' => 'telp', 'nilai' => '021-5559012'],
                    ['tipe' => 'wa', 'nilai' => '081234567892'],
                ],
            ],
            [
                'nama_supplier' => 'UD Roti Bakar Nusantara',
                'alamat' => 'Jl. Cikini Raya No. 12, Jakarta Pusat',
                'kontak' => [
                    ['tipe' => 'telp', 'nilai' => '021-5553456'],
                    ['tipe' => 'wa', 'nilai' => '081234567893'],
                    ['tipe' => 'email', 'nilai' => 'sales@roti-nusantara.com'],
                ],
            ],
            [
                'nama_supplier' => 'PT Minuman Segar Abadi',
                'alamat' => 'Jl. Thamrin No. 78, Jakarta Pusat',
                'kontak' => [
                    ['tipe' => 'telp', 'nilai' => '021-5557890'],
                    ['tipe' => 'wa', 'nilai' => '081234567894'],
                    ['tipe' => 'email', 'nilai' => 'order@minumansegar.com'],
                ],
            ],
            [
                'nama_supplier' => 'CV Bumbu Dapur Lengkap',
                'alamat' => 'Jl. Kebon Jeruk No. 56, Jakarta Barat',
                'kontak' => [
                    ['tipe' => 'telp', 'nilai' => '021-5552345'],
                    ['tipe' => 'wa', 'nilai' => '081234567895'],
                ],
            ],
            [
                'nama_supplier' => 'PT Kemasan Modern',
                'alamat' => 'Jl. Industri Raya No. 34, Tangerang',
                'kontak' => [
                    ['tipe' => 'telp', 'nilai' => '021-5556789'],
                    ['tipe' => 'wa', 'nilai' => '081234567896'],
                    ['tipe' => 'email', 'nilai' => 'info@kemasanmodern.com'],
                ],
            ],
            [
                'nama_supplier' => 'UD Ayam Potong Segar',
                'alamat' => 'Jl. Pasar Induk Kramat Jati, Jakarta Timur',
                'kontak' => [
                    ['tipe' => 'telp', 'nilai' => '021-5550123'],
                    ['tipe' => 'wa', 'nilai' => '081234567897'],
                ],
            ],
        ];

        // Insert untuk setiap cabang
        foreach ([$ambon, $lombok] as $cabang) {
            foreach ($suppliersData as $supplierData) {
                $supplier = Supplier::updateOrCreate(
                    [
                        'id_cabang' => $cabang->id_cabang,
                        'nama_supplier' => $supplierData['nama_supplier'],
                    ],
                    [
                        'alamat' => $supplierData['alamat'],
                    ]
                );

                // Hapus kontak lama untuk supplier ini (jika ada) dan buat yang baru
                $supplier->contacts()->delete();

                // Tambahkan kontak
                foreach ($supplierData['kontak'] as $kontak) {
                    $supplier->contacts()->create([
                        'tipe' => $kontak['tipe'],
                        'nilai' => $kontak['nilai'],
                    ]);
                }
            }
        }

        $this->command->info('Supplier berhasil di-seed untuk semua cabang!');
    }
}

