<?php

use App\Http\Controllers\Mercurio\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('/web')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::get('/register/company', [AuthController::class, 'registerCompany'])->name('register.company');
    Route::get('/register/worker', [AuthController::class, 'registerWorker'])->name('register.worker');
    Route::get('/password/request', [AuthController::class, 'resetPassword'])->name('password.request');
    Route::post('/load_session', [AuthController::class, 'loadSession'])->name('load.session');
    Route::post('/salir', [AuthController::class, 'logout'])->name('login.salir');
    Route::get('/salir', [AuthController::class, 'logout'])->name('logout');
    Route::get('/params-login', [AuthController::class, 'paramsLogin'])->name('login.params');
    Route::get('/noty_cambio_correo', [AuthController::class, 'notyCambioCorreo'])->name('web.noty_cambio_correo');
    Route::post('/cambio_correo', [AuthController::class, 'cambioCorreo'])->name('web.cambio_correo');

    Route::get('/verify/{tipo}/{coddoc}/{documento}/{option_request?}', [AuthController::class, 'verifyShow'])->name('verify.show');
    Route::post('/verify', [AuthController::class, 'verify'])->name('verify.request');
    Route::post('/verify_action', [AuthController::class, 'verify'])->name('verify.action');
});
