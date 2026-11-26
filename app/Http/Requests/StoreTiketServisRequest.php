<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTiketServisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_barang_id' => ['required', 'exists:kategori_barangs,id'],
            'nama_barang'        => ['required', 'string', 'min:3', 'max:100'],
            'keluhan'            => ['required', 'string', 'min:5'],
        ];
    }

    public function messages(): array
    {
        return [
            'required'                  => ':attribute wajib diisi.',
            'kategori_barang_id.exists' => 'Kategori barang tidak valid.',
            'nama_barang.min'           => 'Nama barang minimal :min karakter.',
            'keluhan.min'               => 'Deskripsi keluhan minimal :min karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'kategori_barang_id' => 'kategori barang',
            'nama_barang'        => 'nama barang',
            'keluhan'            => 'keluhan',
        ];
    }
}