<?php

namespace App\Http\Resources\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TiketServisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'kode_tiket'         => $this->kode_tiket,
            'nama_barang'        => $this->nama_barang,
            'keluhan'            => $this->keluhan,
            'diagnosa_kerusakan' => $this->diagnosa_kerusakan,
            'tindakan_perbaikan'  => $this->tindakan_perbaikan,
            'status'             => $this->status,
            'biaya_servis'       => $this->biaya_servis,
            'tanggal_masuk'      => $this->tanggal_masuk ? Carbon::parse($this->tanggal_masuk)->format('Y-m-d') : null,
            'tanggal_diambil'    => $this->tanggal_diambil ? Carbon::parse($this->tanggal_diambil)->format('Y-m-d') : null,
            'kategori'           => $this->whenLoaded('kategoriBarang', fn () => $this->kategoriBarang->nama_kategori),
            'pelanggan'          => $this->whenLoaded('pelanggan', fn () => $this->pelanggan?->name),
            'teknisi'            => $this->whenLoaded('teknisi', fn () => $this->teknisi?->name ?? 'Menunggu Penugasan Teknisi'),
            'suku_cadangs'       => $this->whenLoaded('sukuCadangs', fn () => $this->sukuCadangs->map(fn ($part) => [
                'id'        => $part->id,
                'nama_part' => $part->nama_part,
                'harga'     => $part->harga,
                'pivot'     => [
                    'jumlah'       => $part->pivot->jumlah,
                    'harga_satuan' => $part->pivot->harga_satuan,
                ],
            ])),
        ];
    }
}