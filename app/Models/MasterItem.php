<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    const DAFTAR_SUPPLIER = ['Tokopaedi', 'Bukulapuk', 'TokoBagas', 'E Commurz', 'Blublu'];
    const DAFTAR_JENIS = ['Obat', 'Alkes', 'Matkes', 'Umum', 'ATK'];

    public function kategori()
    {
        return $this->belongsToMany(KategoriItem::class);
    }
}
