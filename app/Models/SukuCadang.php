<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SukuCadang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_part',
        'nama_part',
        'stok',
        'harga',
    ];

    public function tiketServis(): BelongsToMany
    {
        return $this->belongsToMany(TiketServis::class, 'tiket_servis_suku_cadang')
            ->withPivot(['jumlah', 'harga_satuan'])
            ->withTimestamps();
    }
}