<?php

use App\Http\Controllers\Cajas\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/usuario')->name('cajas.usuario.')->group(function () {
        Route::get('/index', [UsuarioController::class, 'index'])->name('index');

        $usuarioKey = [
            'documento' => '[0-9]+',
            'tipo' => '[A-Za-z]',
            'coddoc' => '[0-9]+',
        ];

        Route::get('/{documento}/{tipo}/{coddoc}/edit', [UsuarioController::class, 'edit'])
            ->where($usuarioKey)
            ->name('edit');

        Route::put('/{documento}/{tipo}/{coddoc}', [UsuarioController::class, 'update'])
            ->where($usuarioKey)
            ->name('update');

        Route::get('/{documento}/{tipo}/{coddoc}', [UsuarioController::class, 'show'])
            ->where($usuarioKey)
            ->name('show');

        // API JSON legacy (compatibilidad)
        Route::post('/show_user', [UsuarioController::class, 'showUser'])->name('show_user');
        Route::post('/params', [UsuarioController::class, 'params'])->name('params');
        Route::post('/borrar_usuario', [UsuarioController::class, 'borrarUsuario'])->name('borrar');
        Route::post('/guardar', [UsuarioController::class, 'guardar'])->name('guardar');
    });
});
