<?php

use App\Http\Controllers\Api\AuthMercurioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('authenticate', [AuthMercurioController::class, 'authenticateAction'])->name('api.authenticate');
Route::post('register', [AuthMercurioController::class, 'registerAction'])->name('api.register');
Route::post('verify_store', [AuthMercurioController::class, 'verifyStore'])->name('api.verify_store');
Route::post('recovery_send', [AuthMercurioController::class, 'recoverySend'])->name('api.recovery_send');


Route::fallback(function (Request $request) {
    $ruta = $request->url();

    return response()->json([
        'status' => false,
        'message' => "Ruta {$ruta} no está disponible para acceder.",
        'code' => 404,
    ], 404);
});
