<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTiketServisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_barang_id' => ['required', 'exists:kategori_barangs,id'],
            'teknisi_id'         => ['nullable', 'exists:users,id'],
            'nama_barang'        => ['required', 'string', 'max:255'],
            'keluhan'            => ['required', 'string'],
            'status'             => ['required', 'in:menunggu,diproses,selesai,diambil'],
            'biaya_servis'       => ['nullable', 'numeric', 'min:0'],
            'diagnosa_kerusakan' => ['nullable', 'string'],
                'tindakan_perbaikan' => ['nullable', 'string'],
        ];
    }
}