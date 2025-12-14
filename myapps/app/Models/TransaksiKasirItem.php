<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKasirItem extends Model
{
    use HasFactory;

    protected $table = 'transaksi_kasir_items';

    protected $fillable = [
        'transaksi_kasir_id',
        'produk_id',
        'nama_produk',
        'harga',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    public function transaksi()
    {
        return $this->belongsTo(TransaksiKasir::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}

