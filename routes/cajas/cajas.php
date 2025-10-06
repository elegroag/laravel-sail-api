<?php

use App\Http\Controllers\Cajas\AuthController;
use App\Http\Controllers\Cajas\PrincipalController;
use Illuminate\Support\Facades\Route;

Route::prefix('/cajas')->group(function () {
    // Redirige la raíz de Cajas a la ruta canónica de login para evitar nombres duplicados
    Route::get('/', function () {
        return redirect()->route('cajas.login');
    });
    Route::get('/login', [AuthController::class, 'indexAction'])->name('cajas.login');
    Route::post('/autenticar', [AuthController::class, 'authenticateAction'])->name('cajas.autenticar');
    Route::post('/salir', [AuthController::class, 'logoutAction'])->name('cajas.salir');
    Route::post('/cambio_correo', [AuthController::class, 'cambioCorreoAction'])->name('cajas.cambio_correo');
});

Route::prefix('/cajas/principal')->group(function () {
    Route::get('/index', [PrincipalController::class, 'indexAction'])->name('cajas.principal');
    Route::get('/dashboard', [PrincipalController::class, 'dashboardAction'])->name('cajas.dashboard');
    Route::get('/traer_usuarios_registrados', [PrincipalController::class, 'traerUsuariosRegistradosAction']);
    Route::get('/traer_opcion_mas_usuada', [PrincipalController::class, 'traerOpcionMasUsuadaAction']);
    Route::get('/traer_motivo_mas_usuada', [PrincipalController::class, 'traerMotivoMasUsuadaAction']);
    Route::get('/traer_carga_laboral', [PrincipalController::class, 'traerCargaLaboralAction']);
    Route::get('/download_global', [PrincipalController::class, 'downloadGlobalAction']);
    Route::get('/file_existe_global', [PrincipalController::class, 'fileExisteGlobalAction']);
});
