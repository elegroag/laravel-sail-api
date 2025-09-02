<?php

use App\Http\Controllers\Mercurio\LoginController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;


Route::get('/mercurio/login/', LoginController::class, 'index');
Route::post('/mercurio/autenticar/', LoginController::class, 'autenticarAction');
Route::post('/mercurio/salir/', LoginController::class, 'salirAction');
Route::post('/mercurio/recuperar_clave/', LoginController::class, 'recuperar_claveAction');
Route::post('/mercurio/registro/', LoginController::class, 'registroAction');
Route::get('/mercurio/fuera_servicio/', LoginController::class, 'fuera_servicioAction');

Route::get('/mercurio/verify/', LoginController::class, 'verifyAction');
Route::get('/mercurio/tokenParticular/', LoginController::class, 'tokenParticularAction');
Route::get('/mercurio/autoFirma/', LoginController::class, 'autoFirmaAction');
Route::get('/mercurio/cambio_correo/', LoginController::class, 'cambio_correoAction');
Route::get('/mercurio/paramsLogin/', LoginController::class, 'paramsLoginAction');
