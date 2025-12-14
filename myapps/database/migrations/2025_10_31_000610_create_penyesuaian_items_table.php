<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyesuaian_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penyesuaian_stok_id');
            $table->unsignedBigInteger('bahan_baku_id');
            $table->decimal('stok_lama', 12, 2);
            $table->decimal('stok_baru', 12, 2);
            $table->decimal('selisih', 12, 2);
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('penyesuaian_stok_id')->references('id')->on('penyesuaian_stok')->onDelete('cascade');
            $table->foreign('bahan_baku_id')->references('id')->on('bahan_baku')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyesuaian_items');
    }
};

