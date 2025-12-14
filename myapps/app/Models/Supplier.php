<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    protected $fillable = [
        'id_cabang',
        'nama_supplier',
        'alamat',
    ];

    public function contacts()
    {
        return $this->hasMany(SupplierContact::class);
    }
}


