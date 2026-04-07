<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku';

    protected $fillable = [
        'id_cabang',
        'nama_bahan',
        'satuan',
        'stok',
        'harga_satuan',
    ];

    protected $casts = [
        'stok' => 'integer',
        'harga_satuan' => 'decimal:2',
    ];

    // Relasi dengan resep items
    public function resepItems()
    {
        return $this->hasMany(ResepItem::class, 'bahan_baku_id');
    }

    // Method untuk mendapatkan resep yang menggunakan bahan baku ini
    public function getUsedInReseps()
    {
        return $this->resepItems()
            ->with('resep')
            ->get()
            ->pluck('resep')
            ->unique('id');
    }
}


