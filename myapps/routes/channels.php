<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel untuk cabang - hanya user yang memiliki akses ke cabang tersebut
Broadcast::channel('cabang.{idCabang}', function ($user, $idCabang) {
    if (!$user) {
        return false;
    }
    
    // Cek apakah user memiliki akses ke cabang ini
    // Cek dari session atau dari relasi pegawai
    $sessionCabang = session('id_cabang');
    
    if ($sessionCabang && (int) $sessionCabang === (int) $idCabang) {
        return true;
    }
    
    // Alternatif: cek dari relasi pegawai jika ada
    try {
        $pegawai = \App\Models\Pegawai::where('username', $user->username)->first();
        if ($pegawai) {
            // Cek apakah pegawai memiliki akses ke cabang ini
            $hasAccess = $pegawai->cabangs()->where('pegawai_cabang.id_cabang', $idCabang)->exists();
            if ($hasAccess || $pegawai->role === 'owner') {
                return true;
            }
        }
    } catch (\Exception $e) {
        // Jika ada error, fallback ke session check
    }
    
    return false;
});
