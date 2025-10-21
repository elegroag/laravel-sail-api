<?php

use App\Http\Controllers\Cajas\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/cajas/salir', [AuthController::class, 'logout'])->name('cajas.salir');
Route::get('/cajas/login', [AuthController::class, 'index'])->name('cajas.login');
Route::post('/cajas/autenticar', [AuthController::class, 'authenticate'])->name('cajas.autenticar');
Route::post('/cajas/cambio_correo', [AuthController::class, 'cambioCorreo'])->name('cajas.cambio_correo');
