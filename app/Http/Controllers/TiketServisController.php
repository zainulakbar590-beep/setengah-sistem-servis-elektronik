<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTiketServisRequest;
use App\Http\Requests\UpdateTiketServisRequest;
use App\Models\KategoriBarang;
use App\Models\SukuCadang;
use App\Models\TiketServis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TiketServisController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $baseQuery = TiketServis::when($user->hasRole('pelanggan'), fn ($q) => $q->where('user_id', $user->id));

        $stats = [
            'total'    => (clone $baseQuery)->count(),
            'menunggu' => (clone $baseQuery)->where('status', 'menunggu')->count(),
            'diproses' => (clone $baseQuery)->where('status', 'diproses')->count(),
            'selesai'  => (clone $baseQuery)->whereIn('status', ['selesai', 'diambil'])->count(),
        ];

        $tikets = (clone $baseQuery)
            ->with(['kategoriBarang', 'pelanggan', 'teknisi', 'sukuCadangs'])
            ->when($request->filled('cari'), function ($q) use ($request) {
                $q->where(function ($w) use ($request) {
                    $w->where('kode_tiket', 'like', '%' . $request->cari . '%')
                      ->orWhere('nama_barang', 'like', '%' . $request->cari . '%');
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('TiketServis/Index', [
            'tikets'    => $tikets,
            'kategoris' => KategoriBarang::orderBy('nama_kategori')->get(),
            'filters'   => $request->only(['cari', 'status']),
            'stats'     => $stats,
        ]);
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->hasRole('pelanggan'), 403, 'Hanya pelanggan yang dapat mengajukan tiket servis.');

        return Inertia::render('TiketServis/Create', [
            'kategoris' => KategoriBarang::orderBy('nama_kategori')->get(),
        ]);
    }

    public function store(StoreTiketServisRequest $request)
    {
        abort_unless($request->user()->hasRole('pelanggan'), 403, 'Hanya pelanggan yang dapat mengajukan tiket servis.');

        $tiket = new TiketServis($request->validated());
        $tiket->kode_tiket = 'SRV-' . strtoupper(Str::random(6));
        $tiket->user_id = $request->user()->id;
        $tiket->status = 'menunggu';
        $tiket->tanggal_masuk = now()->toDateString();
        $tiket->save();

        return redirect()->route('tiket-servis.index')
            ->with('success', 'Tiket servis berhasil diajukan.');
    }

    public function edit($tiketServis)
    {
        $tiket = $tiketServis instanceof TiketServis ? $tiketServis : TiketServis::findOrFail($tiketServis);
        Gate::authorize('update', $tiket);

        return Inertia::render('TiketServis/Edit', [
            'tiket'       => $tiket->load(['kategoriBarang', 'pelanggan', 'teknisi', 'sukuCadangs']),
            'kategoris'   => KategoriBarang::orderBy('nama_kategori')->get(),
            'teknisis'    => User::role('teknisi')->get(['id', 'name']),
            'sukuCadangs' => SukuCadang::where('stok', '>', 0)->orderBy('nama_part')->get(),
        ]);
    }

    public function update(UpdateTiketServisRequest $request, $tiketServis)
    {
        $tiket = $tiketServis instanceof TiketServis ? $tiketServis : TiketServis::findOrFail($tiketServis);
        Gate::authorize('update', $tiket);

        $data = $request->validated();

        if (($data['status'] ?? $tiket->status) === 'diambil' && !in_array($tiket->status, ['selesai', 'diambil'], true)) {
            return back()->withErrors([
                'status' => 'Tiket harus berstatus Selesai terlebih dahulu sebelum ditandai sebagai Diambil.',
            ]);
        }

        if ($request->user()->hasRole('admin')) {
            if ($request->has('teknisi_id')) {
                $data['teknisi_id'] = $request->teknisi_id;
            }
        } elseif ($request->user()->hasRole('teknisi')) {
            unset($data['teknisi_id']);
            if (!$tiket->teknisi_id) {
                $data['teknisi_id'] = $request->user()->id;
            }
        } else {
            unset($data['teknisi_id']);
        }

        if (($data['status'] ?? $tiket->status) === 'diambil') {
            $data['tanggal_diambil'] = $tiket->tanggal_diambil ?? now()->toDateString();
        } elseif (isset($data['status']) && $data['status'] !== 'diambil') {
            $data['tanggal_diambil'] = null;
        }

        $tiket->update($data);

        return redirect()->route('tiket-servis.index')
            ->with('success', 'Data tiket servis berhasil diperbarui.');
    }

    public function tambahPart(Request $request, $tiketServis)
    {
        $tiket = $tiketServis instanceof TiketServis ? $tiketServis : TiketServis::findOrFail($tiketServis);
        Gate::authorize('update', $tiket);
        $request->validate([
            'suku_cadang_id' => 'required|exists:suku_cadangs,id',
            'jumlah'         => 'required|integer|min:1',
        ]);

        $part = SukuCadang::findOrFail($request->suku_cadang_id);
        abort_if($part->stok < $request->jumlah, 422, 'Stok suku cadang tidak mencukupi.');

        $existing = $tiket->sukuCadangs()->where('suku_cadang_id', $part->id)->first();
        if ($existing) {
            $tiket->sukuCadangs()->updateExistingPivot($part->id, [
                'jumlah'       => $existing->pivot->jumlah + $request->jumlah,
                'harga_satuan' => $part->harga,
            ]);
        } else {
            $tiket->sukuCadangs()->attach($part->id, [
                'jumlah'       => $request->jumlah,
                'harga_satuan' => $part->harga,
            ]);
        }

        $part->decrement('stok', $request->jumlah);
        return back()->with('success', 'Suku cadang ditambahkan ke tiket.');
    }

    public function hapusPart($tiketServis, SukuCadang $sukuCadang)
    {
        $tiket = $tiketServis instanceof TiketServis ? $tiketServis : TiketServis::findOrFail($tiketServis);
        Gate::authorize('update', $tiket);
        $pivot = $tiket->sukuCadangs()->where('suku_cadang_id', $sukuCadang->id)->first();
        if ($pivot) {
            $sukuCadang->increment('stok', $pivot->pivot->jumlah);
            $tiket->sukuCadangs()->detach($sukuCadang->id);
        }
        return back()->with('success', 'Suku cadang dihapus dari tiket.');
    }

    public function destroy($tiketServis)
    {
        $tiket = $tiketServis instanceof TiketServis ? $tiketServis : TiketServis::findOrFail($tiketServis);
        Gate::authorize('delete', $tiket);
        $tiket->delete();

        return redirect()->route('tiket-servis.index')
            ->with('success', 'Tiket servis berhasil dihapus.');
    }
}