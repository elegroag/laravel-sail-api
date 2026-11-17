<?php

use App\Http\Controllers\Cajas\BannerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/banners')->group(function () {
        Route::get('/index', [BannerController::class, 'index'])->name('cajas.banners.index');
        Route::post('/', [BannerController::class, 'store'])->name('cajas.banners.store');
        Route::post('/{id}', [BannerController::class, 'update'])->name('cajas.banners.update')->whereNumber('id');
        Route::delete('/{id}', [BannerController::class, 'destroy'])->name('cajas.banners.destroy')->whereNumber('id');
    });
});
