<?php

use App\Http\Controllers\Cajas\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/cajas/login', [AuthController::class, 'indexAction'])->name('login');
Route::post('/cajas/autenticar', [AuthController::class, 'authenticateAction']);
Route::post('/cajas/salir', [AuthController::class, 'logoutAction'])->name('login.salir');
Route::get('/cajas/salir', [AuthController::class, 'logoutAction']);
Route::post('/cajas/cambio_correo', [AuthController::class, 'cambioCorreoAction']);
