<?php

use App\Http\Controllers\Cajas\Gener42Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/gener42')->group(function () {
        Route::get('/index', [Gener42Controller::class, 'index'])->name('cajas.gener42.index');
        Route::post('/assign', [Gener42Controller::class, 'assign'])->name('cajas.gener42.assign');
        Route::post('/revoke', [Gener42Controller::class, 'revoke'])->name('cajas.gener42.revoke');
    });
});
