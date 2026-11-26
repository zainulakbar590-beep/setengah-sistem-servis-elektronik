<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TiketServis extends Model
{
    use HasFactory;

    protected $table = 'tiket_servis';

    protected $fillable = [
        'user_id',
        'kategori_barang_id',
        'teknisi_id',
        'kode_tiket',
        'nama_barang',
        'keluhan',
        'diagnosa_kerusakan',
        'tindakan_perbaikan',
        'status',
        'biaya_servis',
        'tanggal_masuk',
        'tanggal_diambil',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    public function kategoriBarang(): BelongsTo
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id');
    }

    public function sukuCadangs(): BelongsToMany
    {
        return $this->belongsToMany(SukuCadang::class, 'tiket_servis_suku_cadang')
            ->withPivot(['jumlah', 'harga_satuan'])
            ->withTimestamps();
    }
}