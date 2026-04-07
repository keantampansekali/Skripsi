<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Resep;
use App\Models\Produk;

class ResepSeeder extends Seeder
{
    public function run(): void
    {
		// Resep disesuaikan dengan ProdukSeeder (ID produk 63–77, cabang 1)
		$dataCabang1 = [
			['id_cabang' => 1, 'nama_resep' => 'Resep Paha Atas', 'produk_id' => 63, 'deskripsi' => 'Resep otomatis untuk Paha Atas'],
			['id_cabang' => 1, 'nama_resep' => 'Resep Dada', 'produk_id' => 64, 'deskripsi' => 'Resep otomatis untuk Dada'],
			['id_cabang' => 1, 'nama_resep' => 'Resep Paha Bawah', 'produk_id' => 66, 'deskripsi' => 'Resep otomatis untuk Paha Bawah'],
			['id_cabang' => 1, 'nama_resep' => 'Resep Sayap', 'produk_id' => 67, 'deskripsi' => 'Resep otomatis untuk Sayap'],
			['id_cabang' => 1, 'nama_resep' => 'Resep Nasi Putih', 'produk_id' => 68, 'deskripsi' => 'Resep otomatis untuk Nasi Putih'],
			['id_cabang' => 1, 'nama_resep' => 'Resep Paket 1', 'produk_id' => 69, 'deskripsi' => 'Resep otomatis untuk Paket 1'],
			['id_cabang' => 1, 'nama_resep' => 'Resep Paket 2', 'produk_id' => 70, 'deskripsi' => 'Resep otomatis untuk Paket 2'],
			['id_cabang' => 1, 'nama_resep' => 'Resep Paket 3', 'produk_id' => 71, 'deskripsi' => 'Resep otomatis untuk Paket 3'],
			['id_cabang' => 1, 'nama_resep' => 'Resep Burger Sapi', 'produk_id' => 72, 'deskripsi' => 'Resep otomatis untuk Burger Sapi'],
			['id_cabang' => 1, 'nama_resep' => 'Resep Burger Sapi Keju', 'produk_id' => 73, 'deskripsi' => 'Resep otomatis untuk Burger Sapi Keju'],
			['id_cabang' => 1, 'nama_resep' => 'Resep Burger Ayam', 'produk_id' => 74, 'deskripsi' => 'Resep otomatis untuk Burger Ayam'],
			['id_cabang' => 1, 'nama_resep' => 'Resep Burger Ayam Keju', 'produk_id' => 75, 'deskripsi' => 'Resep otomatis untuk Burger Ayam Keju'],
			['id_cabang' => 1, 'nama_resep' => 'Resep Hotdog Sosis', 'produk_id' => 76, 'deskripsi' => 'Resep otomatis untuk Hotdog Sosis'],
			['id_cabang' => 1, 'nama_resep' => 'Resep Hotdog Sosis Keju', 'produk_id' => 77, 'deskripsi' => 'Resep otomatis untuk Hotdog Sosis Keju'],
		];

		$dataCabang2 = array_map(function ($item) {
			$item['id_cabang'] = 2;
			$item['produk_id'] = $item['produk_id'] + 100;
			return $item;
		}, $dataCabang1);

		$data = array_merge($dataCabang1, $dataCabang2);

        foreach ($data as $item) {
            Resep::updateOrCreate(
				[
					'id_cabang' => $item['id_cabang'],
					'produk_id' => $item['produk_id'],
				],
                $item
            );
        }

		// Item/bahan per resep belum diisi karena mapping bahan baku untuk menu baru
		// belum ditentukan. Tambahkan sesuai kebutuhan bisnis Anda di kemudian hari.

        $this->command->info('Resep berhasil di-seed!');
    }
}
