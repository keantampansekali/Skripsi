<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('waste_items', function (Blueprint $table) {
            // Hapus foreign key lama dulu
            $table->dropForeign(['bahan_baku_id']);
            
            // Ubah bahan_baku_id jadi nullable
            $table->unsignedBigInteger('bahan_baku_id')->nullable()->change();
            
            // Tambah kolom baru
            $table->string('tipe')->default('bahan_baku')->after('waste_id');
            $table->unsignedBigInteger('produk_id')->nullable()->after('bahan_baku_id');
            
            // Tambah foreign key baru
            $table->foreign('bahan_baku_id')->references('id')->on('bahan_baku')->onDelete('cascade');
            $table->foreign('produk_id')->references('id')->on('produk')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('waste_items', function (Blueprint $table) {
            // Hapus foreign key dan kolom baru
            $table->dropForeign(['produk_id']);
            $table->dropForeign(['bahan_baku_id']);
            $table->dropColumn(['tipe', 'produk_id']);
            
            // Restore bahan_baku_id sebagai required
            $table->unsignedBigInteger('bahan_baku_id')->nullable(false)->change();
            $table->foreign('bahan_baku_id')->references('id')->on('bahan_baku')->onDelete('cascade');
        });
    }
};

