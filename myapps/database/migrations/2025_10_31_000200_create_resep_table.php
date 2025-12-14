<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cabang');
            $table->string('nama_resep');
            $table->unsignedBigInteger('produk_id')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->foreign('id_cabang')->references('id_cabang')->on('tabel_cabang')->onDelete('cascade');
            $table->foreign('produk_id')->references('id')->on('produk')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep');
    }
};


