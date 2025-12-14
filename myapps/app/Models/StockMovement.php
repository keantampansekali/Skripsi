<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $table = 'stock_movements';

    protected $fillable = [
        'bahan_baku_id',
        'tipe',
        'qty',
        'ref_type',
        'ref_id',
        'id_cabang',
        'keterangan',
    ];
}


