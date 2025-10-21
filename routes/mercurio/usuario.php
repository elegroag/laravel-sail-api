<?php

use App\Http\Controllers\Cajas\UsuarioController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/usuario')->group(function () {
        Route::get('/', function () {
            return redirect()->route('usuario.index');
        });
        Route::get('/index', [UsuarioController::class, 'index'])->name('usuario.index');
        Route::post('/show_perfil', [UsuarioController::class, 'showPerfil']);
        Route::post('/params', [UsuarioController::class, 'params']);
        Route::post('/guardar', [UsuarioController::class, 'guardar']);
    });
});
