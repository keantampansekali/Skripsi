<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockItem extends Model
{
    use HasFactory;

    protected $table = 'restock_items';

    protected $fillable = [
        'restock_id',
        'bahan_baku_id',
        'qty',
        'harga_satuan',
        'subtotal',
    ];

    public function bahan()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }
}


