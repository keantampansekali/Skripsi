<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cabang;

class TabelCabangSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id_cabang' => 1,
                'nama_cabang' => 'Ambon',
                'created_at' => '2025-10-31 00:01:01',
                'updated_at' => '2025-10-31 00:01:01',
            ],
            [
                'id_cabang' => 2,
                'nama_cabang' => 'Lombok',
                'created_at' => '2025-10-31 00:01:01',
                'updated_at' => '2025-10-31 00:01:01',
            ],
        ];

        foreach ($data as $item) {
            Cabang::updateOrCreate(
                ['id_cabang' => $item['id_cabang']],
                $item
            );
        }

        $this->command->info('TabelCabang berhasil di-seed!');
    }
}
