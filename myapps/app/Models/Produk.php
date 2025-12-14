<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $fillable = [
        'id_cabang',
        'nama_produk',
        'deskripsi',
        'harga',
        'stok',
        'foto',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang', 'id_cabang');
    }

    public function resep()
    {
        return $this->hasOne(Resep::class, 'produk_id');
    }

    // Scope untuk filter berdasarkan cabang aktif
    public function scopeUntukCabangAktif($query)
    {
        if (session()->has('id_cabang')) {
            return $query->where('id_cabang', session('id_cabang'));
        }
        return $query;
    }

    // Global scope untuk otomatis filter berdasarkan cabang (opsional)
    protected static function booted()
    {
        static::addGlobalScope('cabang', function ($builder) {
            if (session()->has('id_cabang')) {
                $builder->where('id_cabang', session('id_cabang'));
            }
        });
    }
}

