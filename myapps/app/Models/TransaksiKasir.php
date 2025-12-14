<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKasir extends Model
{
    use HasFactory;

    protected $table = 'transaksi_kasir';

    protected $fillable = [
        'id_cabang',
        'pegawai_id',
        'no_transaksi',
        'subtotal',
        'diskon',
        'tipe_diskon',
        'nilai_diskon',
        'tax',
        'total',
        'bayar',
        'kembalian',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'nilai_diskon' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(TransaksiKasirItem::class);
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang', 'id_cabang');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}

