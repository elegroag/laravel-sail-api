<?php

use App\Http\Controllers\Cajas\UsuarioController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/usuario')->group(function () {
        Route::get('/', function () {
            return redirect()->route('usuario.index');
        });
        Route::get('/index', [UsuarioController::class, 'indexAction'])->name('usuario.index');
        Route::post('/show_perfil', [UsuarioController::class, 'showPerfilAction']);
        Route::post('/params', [UsuarioController::class, 'paramsAction']);
        Route::post('/guardar', [UsuarioController::class, 'guardarAction']);
    });
});
