<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResepItem extends Model
{
    use HasFactory;

    protected $table = 'resep_items';

    protected $fillable = [
        'resep_id',
        'bahan_baku_id',
        'qty',
    ];

    public function bahan()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }
}


