<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cabang');
            $table->string('nama_produk');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 10, 2);
            $table->integer('stok')->default(0);
            $table->timestamps();

            $table->foreign('id_cabang')->references('id_cabang')->on('tabel_cabang')->onDelete('cascade');
            $table->index('id_cabang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};

