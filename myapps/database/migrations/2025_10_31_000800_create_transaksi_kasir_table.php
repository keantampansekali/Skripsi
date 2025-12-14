<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_kasir', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cabang');
            $table->string('no_transaksi')->unique();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('diskon', 12, 2)->default(0);
            $table->string('tipe_diskon')->default('rp'); // 'rp' or 'percent'
            $table->decimal('nilai_diskon', 12, 2)->default(0); // nilai yang diinput user
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->decimal('bayar', 12, 2);
            $table->decimal('kembalian', 12, 2);
            $table->timestamps();

            $table->foreign('id_cabang')->references('id_cabang')->on('tabel_cabang')->onDelete('cascade');
            $table->index('id_cabang');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_kasir');
    }
};

