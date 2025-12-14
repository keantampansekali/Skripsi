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
        Schema::table('transaksi_kasir', function (Blueprint $table) {
            $table->unsignedBigInteger('pegawai_id')->nullable()->after('id_cabang');
            $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('set null');
            $table->index('pegawai_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_kasir', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->dropIndex(['pegawai_id']);
            $table->dropColumn('pegawai_id');
        });
    }
};
