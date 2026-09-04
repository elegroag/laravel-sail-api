<?php

use App\Http\Controllers\Cajas\BannerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/banners')->group(function () {
        Route::get('/index', [BannerController::class, 'index']);
        Route::post('/galeria', [BannerController::class, 'galeria']);
        Route::post('/editar', [BannerController::class, 'editar']);
        Route::post('/guardar', [BannerController::class, 'guardar']);
        Route::post('/borrar', [BannerController::class, 'borrar']);
    });
});
