<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resep_id');
            $table->unsignedBigInteger('bahan_baku_id');
            $table->decimal('qty', 10, 2);
            $table->timestamps();

            $table->foreign('resep_id')->references('id')->on('resep')->onDelete('cascade');
            $table->foreign('bahan_baku_id')->references('id')->on('bahan_baku')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_items');
    }
};


