<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update satuan yang masih format lama menjadi format baru (angka + satuan)
        DB::table('bahan_baku')->get()->each(function ($bahan) {
            $satuanLama = $bahan->satuan;
            $satuanBaru = $satuanLama;
            
            // Jika satuan belum dalam format "angka + satuan", konversi
            if (!preg_match('/^\d+(?:\.\d+)?\s+/', $satuanLama)) {
                // Mapping satuan lama ke format baru
                $mapping = [
                    'pcs' => '1 pcs',
                    'gram' => '500 gram',
                    'ml' => '500 ml',
                    'kg' => '1 kg',
                    'liter' => '1 liter',
                    'ons' => '1 ons',
                    'bungkus' => '1 bungkus',
                    'botol' => '1 botol',
                    'kaleng' => '1 kaleng',
                ];
                
                $satuanBaru = $mapping[strtolower($satuanLama)] ?? '1 ' . $satuanLama;
            }
            
            DB::table('bahan_baku')
                ->where('id', $bahan->id)
                ->update(['satuan' => $satuanBaru]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert ke format lama (ambil hanya bagian satuan)
        DB::table('bahan_baku')->get()->each(function ($bahan) {
            $satuanBaru = $bahan->satuan;
            
            // Extract hanya bagian satuan (hilangkan angka)
            if (preg_match('/^\d+(?:\.\d+)?\s+(.+)$/', $satuanBaru, $matches)) {
                $satuanLama = $matches[1];
                DB::table('bahan_baku')
                    ->where('id', $bahan->id)
                    ->update(['satuan' => $satuanLama]);
            }
        });
    }
};

