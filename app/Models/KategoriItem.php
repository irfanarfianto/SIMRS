<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriItem extends Model
{
    use HasFactory;

    protected $fillable = ['kode', 'nama'];

    public function items()
    {
        return $this->belongsToMany(MasterItem::class);
    }
}
