<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';

    protected $fillable = [
        'nama',
        'username',
        'role',
        'email',
        'password',
        'no_telp',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Mutator untuk hash password otomatis saat create/update
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    // Relasi many-to-many dengan Cabang
    public function cabangs()
    {
        return $this->belongsToMany(Cabang::class, 'pegawai_cabang', 'pegawai_id', 'id_cabang', 'id', 'id_cabang');
    }

    // Check apakah pegawai bisa akses cabang tertentu
    public function canAccessCabang($idCabang)
    {
        // Owner atau super admin (untuk backward compatibility) bisa akses semua cabang
        if ($this->role === 'owner' || $this->role === 'super admin') {
            return true;
        }
        
        // Admin dan pegawai hanya bisa akses cabang yang ditentukan
        // Gunakan tabel eksplisit untuk menghindari ambiguous column
        return $this->cabangs()->where('pegawai_cabang.id_cabang', $idCabang)->exists();
    }

    // Get semua cabang yang bisa diakses
    public function getAccessibleCabangs()
    {
        // Owner atau super admin (untuk backward compatibility) bisa akses semua cabang
        if ($this->role === 'owner' || $this->role === 'super admin') {
            return Cabang::all();
        }
        
        // Admin dan pegawai hanya cabang yang ditentukan
        return $this->cabangs;
    }

    // Relasi dengan TransaksiKasir
    public function transaksiKasir()
    {
        return $this->hasMany(TransaksiKasir::class, 'pegawai_id');
    }
}

