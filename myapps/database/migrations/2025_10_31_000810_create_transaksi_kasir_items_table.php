<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_kasir_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaksi_kasir_id');
            $table->unsignedBigInteger('produk_id');
            $table->string('nama_produk');
            $table->decimal('harga', 10, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            $table->foreign('transaksi_kasir_id')->references('id')->on('transaksi_kasir')->onDelete('cascade');
            $table->foreign('produk_id')->references('id')->on('produk')->onDelete('cascade');
            $table->index('transaksi_kasir_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_kasir_items');
    }
};

