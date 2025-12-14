<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenyesuaianItem extends Model
{
    use HasFactory;

    protected $table = 'penyesuaian_items';

    protected $fillable = [
        'penyesuaian_stok_id',
        'bahan_baku_id',
        'stok_lama',
        'stok_baru',
        'selisih',
        'keterangan',
    ];

    public function bahan()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }
}

