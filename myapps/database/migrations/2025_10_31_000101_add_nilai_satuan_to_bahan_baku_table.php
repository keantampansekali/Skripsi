<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            // Migration untuk menambahkan kolom nilai_satuan jika diperlukan
            // Kolom ini sepertinya tidak digunakan berdasarkan konteks sebelumnya
            // Tapi tetap dibuat untuk menghindari error
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            //
        });
    }
};
