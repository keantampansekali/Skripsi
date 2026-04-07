<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
			$dataCabang1 = [
				[
					'id' => 63,
					'id_cabang' => 1,
					'nama_produk' => 'Paha Atas',
					'deskripsi' => 'A La Carte',
					'harga' => 14000.00,
					'stok' => 15,
					'foto' => 'uploads/produk/1775335041_dada-dan-paha-atas-1.png',
				],
				[
					'id' => 64,
					'id_cabang' => 1,
					'nama_produk' => 'Dada',
					'deskripsi' => 'A La Carte',
					'harga' => 14000.00,
					'stok' => 10,
					'foto' => 'uploads/produk/1775335081_dada-dan-paha-atas-1.png',
				],
				[
					'id' => 66,
					'id_cabang' => 1,
					'nama_produk' => 'Paha Bawah',
					'deskripsi' => 'A La Carte',
					'harga' => 13000.00,
					'stok' => 10,
					'foto' => 'uploads/produk/1775335244_Paha-Bawah-20107057-Rp.jpg',
				],
				[
					'id' => 67,
					'id_cabang' => 1,
					'nama_produk' => 'Sayap',
					'deskripsi' => 'A La Carte',
					'harga' => 13000.00,
					'stok' => 10,
					'foto' => 'uploads/produk/1775335308_sayap_1.png',
				],
				[
					'id' => 68,
					'id_cabang' => 1,
					'nama_produk' => 'Nasi Putih',
					'deskripsi' => 'A La Carte',
					'harga' => 5000.00,
					'stok' => 15,
					'foto' => 'uploads/produk/1775335485_86e1daac88d18e6c8cf2c6f3.jpg',
				],
				[
					'id' => 69,
					'id_cabang' => 1,
					'nama_produk' => 'Paket 1',
					'deskripsi' => 'Nasi + Paha Bawah/Sayap',
					'harga' => 20000.00,
					'stok' => 10,
					'foto' => 'uploads/produk/1775335745_R.jpg',
				],
				[
					'id' => 70,
					'id_cabang' => 1,
					'nama_produk' => 'paket 2',
					'deskripsi' => 'Nasi + Paha Atas/Dada',
					'harga' => 22000.00,
					'stok' => 10,
					'foto' => 'uploads/produk/1775335894_hainanese-crispy-fried-c.jpg',
				],
				[
					'id' => 71,
					'id_cabang' => 1,
					'nama_produk' => 'Paket 3',
					'deskripsi' => 'Nasi + Paha Atas/Dada + Paha Bawah/Sayap',
					'harga' => 35000.00,
					'stok' => 5,
					'foto' => 'uploads/produk/1775335961_pancake-house-p145-weekd.jpg',
				],
				[
					'id' => 72,
					'id_cabang' => 1,
					'nama_produk' => 'Burger Sapi',
					'deskripsi' => 'A La Carte',
					'harga' => 23000.00,
					'stok' => 0,
					'foto' => 'uploads/produk/1775336101_istockphoto-1492033151-1.jpg',
				],
				[
					'id' => 73,
					'id_cabang' => 1,
					'nama_produk' => 'Burger Sapi Keju',
					'deskripsi' => 'A La Carte',
					'harga' => 25000.00,
					'stok' => 2,
					'foto' => 'uploads/produk/1775336232_622751393_cheeseburger.jpg',
				],
				[
					'id' => 74,
					'id_cabang' => 1,
					'nama_produk' => 'Burger Ayam',
					'deskripsi' => 'A La Carte',
					'harga' => 23000.00,
					'stok' => 10,
					'foto' => 'uploads/produk/1775336288_burger-ayam-crispy-foto.jpg',
				],
				[
					'id' => 75,
					'id_cabang' => 1,
					'nama_produk' => 'Burger Ayam Keju',
					'deskripsi' => 'A La Carte',
					'harga' => 25000.00,
					'stok' => 10,
					'foto' => 'uploads/produk/1775336732_61ef4b60ead1d.jpeg',
				],
				[
					'id' => 76,
					'id_cabang' => 1,
					'nama_produk' => 'Hotdog Sosis',
					'deskripsi' => 'A La Carte',
					'harga' => 15000.00,
					'stok' => 10,
					'foto' => 'uploads/produk/1775336690_R_1.jpg',
				],
				[
					'id' => 77,
					'id_cabang' => 1,
					'nama_produk' => 'Hotdog Sosis Keju',
					'deskripsi' => 'A La Carte',
					'harga' => 17000.00,
					'stok' => 15,
					'foto' => 'uploads/produk/1775336796_png-clipart-coney-island.jpg',
				],
			];

			$dataCabang2 = array_map(function ($item) {
				$item['id'] = $item['id'] + 100;
				$item['id_cabang'] = 2;
				return $item;
			}, $dataCabang1);

			$data = array_merge($dataCabang1, $dataCabang2);

        foreach ($data as $item) {
            Produk::updateOrCreate(
                ['id' => $item['id']],
                $item
            );
        }

        $this->command->info('Produk berhasil di-seed!');
    }
}
