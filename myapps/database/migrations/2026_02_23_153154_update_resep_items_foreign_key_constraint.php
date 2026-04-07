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
        Schema::table('resep_items', function (Blueprint $table) {
            // Drop foreign key constraint lama
            $table->dropForeign(['bahan_baku_id']);
            
            // Tambah foreign key constraint baru dengan restrict
            $table->foreign('bahan_baku_id')
                ->references('id')
                ->on('bahan_baku')
                ->onDelete('restrict'); // Mencegah penghapusan jika masih digunakan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resep_items', function (Blueprint $table) {
            // Kembalikan ke cascade seperti semula
            $table->dropForeign(['bahan_baku_id']);
            
            $table->foreign('bahan_baku_id')
                ->references('id')
                ->on('bahan_baku')
                ->onDelete('cascade');
        });
    }
};
