<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CabangSeeder::class,
            UserSeeder::class,
            ProdukSeeder::class,
            BahanBakuSeeder::class,
            ResepSeeder::class,
            SupplierSeeder::class,
            RestockSeeder::class,
            PenyesuaianStokSeeder::class,
            WasteSeeder::class,
        ]);
    }
}
