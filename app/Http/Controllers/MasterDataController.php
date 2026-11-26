<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use App\Models\SukuCadang;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MasterDataController extends Controller
{
    public function kategoriIndex(Request $request)
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        return Inertia::render('Master/KategoriIndex', [
            'kategoris' => KategoriBarang::withCount('tiketServis')->orderBy('nama_kategori')->get(),
        ]);
    }

    public function kategoriStore(Request $request)
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $data = $request->validate(['nama_kategori' => 'required|string|max:100|unique:kategori_barangs,nama_kategori']);
        
        KategoriBarang::create([
            'kode_kategori' => 'KAT-' . strtoupper(Str::random(5)),
            'nama_kategori' => $data['nama_kategori'],
        ]);

        return back()->with('success', 'Kategori barang berhasil ditambahkan.');
    }

    public function kategoriDestroy(Request $request, KategoriBarang $kategoriBarang)
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $kategoriBarang->delete();
        return back()->with('success', 'Kategori barang dihapus.');
    }

    public function sukuCadangIndex(Request $request)
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        return Inertia::render('Master/SukuCadangIndex', [
            'sukuCadangs' => SukuCadang::orderBy('nama_part')->get(),
        ]);
    }

    public function sukuCadangStore(Request $request)
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $data = $request->validate([
            'kode_part' => 'required|string|max:30|unique:suku_cadangs,kode_part',
            'nama_part' => 'required|string|max:100',
            'stok'      => 'required|integer|min:0',
            'harga'     => 'required|integer|min:0',
        ]);
        SukuCadang::create($data);
        return back()->with('success', 'Suku cadang berhasil ditambahkan.');
    }

    public function sukuCadangUpdate(Request $request, SukuCadang $sukuCadang)
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $data = $request->validate([
            'nama_part' => 'required|string|max:100',
            'stok'      => 'required|integer|min:0',
            'harga'     => 'required|integer|min:0',
        ]);
        $sukuCadang->update($data);
        return back()->with('success', 'Stok & harga suku cadang diperbarui.');
    }

    public function sukuCadangDestroy(Request $request, SukuCadang $sukuCadang)
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $sukuCadang->delete();
        return back()->with('success', 'Suku cadang dihapus.');
    }

    public function apiTokenIndex(Request $request)
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        return Inertia::render('Master/ApiTokenIndex', [
            'tokens' => $request->user()->tokens()->latest()->get(['id', 'name', 'last_used_at', 'created_at']),
        ]);
    }

    public function apiTokenStore(Request $request)
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $request->validate(['name' => 'required|string|max:100']);
        $token = $request->user()->createToken($request->name)->plainTextToken;
        return back()->with('plainToken', $token);
    }

    public function apiTokenDestroy(Request $request, $tokenId)
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $request->user()->tokens()->where('id', $tokenId)->delete();
        return back()->with('success', 'Token API dicabut.');
    }
}