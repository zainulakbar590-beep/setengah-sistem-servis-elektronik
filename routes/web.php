<?php

use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeknisiController;
use App\Http\Controllers\TiketServisController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('LacakPublik');
})->name('beranda');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('tiket-servis.index'))->name('dashboard');
    Route::resource('tiket-servis', TiketServisController::class)
        ->parameters(['tiket-servis' => 'tiketServis']);
    Route::post('tiket-servis/{tiketServis}/part', [TiketServisController::class, 'tambahPart'])->name('tiket-servis.part.store');
    Route::delete('tiket-servis/{tiketServis}/part/{sukuCadang}', [TiketServisController::class, 'hapusPart'])->name('tiket-servis.part.destroy');

    // Profil Pengguna (Admin, Teknisi, Pelanggan)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Kelola Teknisi (Admin)
    Route::get('/teknisi', [TeknisiController::class, 'index'])->name('teknisi.index');
    Route::post('/teknisi', [TeknisiController::class, 'store'])->name('teknisi.store');

    // Master Data & API Token (Admin)
    Route::get('kategori-barang', [MasterDataController::class, 'kategoriIndex'])->name('kategori.index');
    Route::post('kategori-barang', [MasterDataController::class, 'kategoriStore'])->name('kategori.store');
    Route::delete('kategori-barang/{kategoriBarang}', [MasterDataController::class, 'kategoriDestroy'])->name('kategori.destroy');

    Route::get('suku-cadang', [MasterDataController::class, 'sukuCadangIndex'])->name('sukucadang.index');
    Route::post('suku-cadang', [MasterDataController::class, 'sukuCadangStore'])->name('sukucadang.store');
    Route::put('suku-cadang/{sukuCadang}', [MasterDataController::class, 'sukuCadangUpdate'])->name('sukucadang.update');
    Route::delete('suku-cadang/{sukuCadang}', [MasterDataController::class, 'sukuCadangDestroy'])->name('sukucadang.destroy');

    Route::get('api-tokens', [MasterDataController::class, 'apiTokenIndex'])->name('apitoken.index');
    Route::post('api-tokens', [MasterDataController::class, 'apiTokenStore'])->name('apitoken.store');
    Route::delete('api-tokens/{tokenId}', [MasterDataController::class, 'apiTokenDestroy'])->name('apitoken.destroy');
});

require __DIR__ . '/auth.php';