<?php

use App\Http\Controllers\Mercurio\UsuarioController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/usuario')->group(function () {
        Route::get('/', function () {
            return redirect()->route('mercurio.usuario.index');
        });
        Route::get('/index', [UsuarioController::class, 'index'])->name('mercurio.usuario.index');
        Route::post('/show_perfil', [UsuarioController::class, 'showPerfil']);
        Route::post('/params', [UsuarioController::class, 'params']);
        Route::post('/guardar', [UsuarioController::class, 'guardar']);
    });
});
