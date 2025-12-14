<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cabang;

class CabangSeeder extends Seeder
{
    public function run(): void
    {
        $cabangs = [
            ['nama_cabang' => 'Ambon'],
            ['nama_cabang' => 'Lombok'],
        ];

        foreach ($cabangs as $cabang) {
            Cabang::firstOrCreate(
                ['nama_cabang' => $cabang['nama_cabang']],
                $cabang
            );
        }
    }
}

