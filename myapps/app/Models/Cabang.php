<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;

    protected $table = 'tabel_cabang';
    protected $primaryKey = 'id_cabang';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nama_cabang',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

