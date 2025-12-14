<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update semua role "super admin" menjadi "owner"
        DB::table('pegawai')
            ->where('role', 'super admin')
            ->update(['role' => 'owner']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert kembali dari "owner" ke "super admin"
        DB::table('pegawai')
            ->where('role', 'owner')
            ->update(['role' => 'super admin']);
    }
};
