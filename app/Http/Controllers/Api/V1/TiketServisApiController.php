<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTiketServisRequest;
use App\Http\Requests\UpdateTiketServisRequest;
use App\Http\Resources\V1\TiketServisResource;
use App\Models\TiketServis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class TiketServisApiController extends Controller
{
    public function index(Request $request)
    {
        $data = TiketServis::with(['kategoriBarang', 'pelanggan', 'teknisi', 'sukuCadangs'])
            ->when($request->filled('cari'), function ($q) use ($request) {
                $term = '%' . $request->cari . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('kode_tiket', 'like', $term)
                        ->orWhere('nama_barang', 'like', $term);
                });
            })
            ->latest()
            ->paginate(10);

        return TiketServisResource::collection($data);
    }

    public function show(string $kode)
    {
        $tiket = TiketServis::with(['kategoriBarang', 'pelanggan', 'teknisi', 'sukuCadangs'])
            ->where('kode_tiket', $kode)
            ->firstOrFail();

        return new TiketServisResource($tiket);
    }

    public function store(StoreTiketServisRequest $request)
    {
        do {
            $kodeTiket = 'SRV-' . strtoupper(Str::random(6));
        } while (TiketServis::where('kode_tiket', $kodeTiket)->exists());

        $tiket = new TiketServis($request->validated());
        $tiket->kode_tiket = $kodeTiket;
        $tiket->user_id = $request->user()->id;
        $tiket->save();

        return (new TiketServisResource($tiket->load(['kategoriBarang', 'pelanggan', 'sukuCadangs'])))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateTiketServisRequest $request, TiketServis $tiketServis)
    {
        Gate::authorize('update', $tiketServis);
        $tiketServis->update($request->validated());

        return new TiketServisResource($tiketServis->load(['kategoriBarang', 'pelanggan', 'teknisi', 'sukuCadangs']));
    }

    public function destroy(TiketServis $tiketServis)
    {
        Gate::authorize('delete', $tiketServis);
        $tiketServis->delete();

        return response()->noContent();
    }
}