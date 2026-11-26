<?php

use App\Http\Controllers\Api\V1\TiketServisApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {
    Route::get('cek-tiket/{kode}', [TiketServisApiController::class, 'show'])->name('cek-tiket');

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('tiket-servis', TiketServisApiController::class);
    });
});
