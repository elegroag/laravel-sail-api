<?php

use App\Http\Controllers\Cajas\AuthController;
use App\Http\Controllers\Cajas\PrincipalController;
use Illuminate\Support\Facades\Route;

Route::get('/cajas/login', [AuthController::class, 'indexAction'])->name('login');
Route::post('/cajas/autenticar', [AuthController::class, 'authenticateAction']);
Route::post('/cajas/salir', [AuthController::class, 'logoutAction'])->name('login.salir');
Route::get('/cajas/salir', [AuthController::class, 'logoutAction']);
Route::post('/cajas/cambio_correo', [AuthController::class, 'cambioCorreoAction']);

Route::get('/cajas/principal', [PrincipalController::class, 'indexAction']);
Route::get('/cajas/dashboard', [PrincipalController::class, 'dashboardAction']);
Route::get('/cajas/traer_usuarios_registrados', [PrincipalController::class, 'traerUsuariosRegistradosAction']);
Route::get('/cajas/traer_opcion_mas_usuada', [PrincipalController::class, 'traerOpcionMasUsuadaAction']);
Route::get('/cajas/traer_motivo_mas_usuada', [PrincipalController::class, 'traerMotivoMasUsuadaAction']);
Route::get('/cajas/traer_carga_laboral', [PrincipalController::class, 'traerCargaLaboralAction']);
Route::get('/cajas/download_global', [PrincipalController::class, 'downloadGlobalAction']);
Route::get('/cajas/file_existe_global', [PrincipalController::class, 'fileExisteGlobalAction']);
