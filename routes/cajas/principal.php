<?php

use App\Http\Controllers\Cajas\PrincipalController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'cajas/principal',
    'middleware' => CajasCookieAuthenticated::class,
], function () {
    Route::get('/', [PrincipalController::class, 'index'])->name('cajas.principal');
    Route::get('/index', [PrincipalController::class, 'index'])->name('cajas.principal.index');
    Route::get('/dashboard', [PrincipalController::class, 'dashboard'])->name('cajas.dashboard');
    Route::post('/traer_usuarios_registrados', [PrincipalController::class, 'traerUsuariosRegistrados']);
    Route::post('/traer_opcion_mas_usada', [PrincipalController::class, 'traerOpcionMasUsuada']);
    Route::post('/traer_motivo_mas_usada', [PrincipalController::class, 'traerMotivoMasUsuada']);
    Route::post('/traer_carga_laboral', [PrincipalController::class, 'traerCargaLaboral']);
    Route::post('/download_global/{hash?}', [PrincipalController::class, 'downloadGlobal']);
    Route::post('/file_existe_global/{hash?}', [PrincipalController::class, 'fileExisteGlobal']);
});