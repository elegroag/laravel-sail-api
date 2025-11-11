<?php

use App\Http\Controllers\Api\AuthMercurioController;
use App\Http\Controllers\Cajas\ComponenteDinamicoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('authenticate', [AuthMercurioController::class, 'authenticateAction'])->name('api.authenticate');
Route::post('register', [AuthMercurioController::class, 'registerAction'])->name('api.register');
Route::post('verify_store', [AuthMercurioController::class, 'verifyStore'])->name('api.verify_store');
Route::post('recovery_send', [AuthMercurioController::class, 'recoverySend'])->name('api.recovery_send');

// Componentes dinámicos por formulario (público temporalmente; considerar auth:sanctum o JWT)
Route::get('cajas/formularios/{formularioId}/componentes', [ComponenteDinamicoController::class, 'byFormulario'])
    ->name('api.cajas.componentes.by-formulario');
Route::post('cajas/formularios/{formularioId}/componentes', [ComponenteDinamicoController::class, 'byFormulario'])
    ->name('api.cajas.componentes.by-formulario.post');


Route::fallback(function (Request $request) {
    $ruta = $request->url();

    return response()->json([
        'status' => false,
        'message' => "Ruta {$ruta} no está disponible para acceder.",
        'code' => 404,
    ], 404);
});
