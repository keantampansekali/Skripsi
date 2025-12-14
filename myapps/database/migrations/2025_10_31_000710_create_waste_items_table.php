<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('waste_id');
            $table->unsignedBigInteger('bahan_baku_id');
            $table->decimal('qty', 12, 2);
            $table->string('alasan')->nullable();
            $table->timestamps();

            $table->foreign('waste_id')->references('id')->on('waste')->onDelete('cascade');
            $table->foreign('bahan_baku_id')->references('id')->on('bahan_baku')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_items');
    }
};

