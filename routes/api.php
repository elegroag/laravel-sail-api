<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthMercurioController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('authenticate', [AuthMercurioController::class, 'authenticateAction'])->name('api.authenticate');
Route::post('register', [AuthMercurioController::class, 'registerAction'])->name('api.register');
Route::post('verify_store', [AuthMercurioController::class, 'verifyStore'])->name('api.verify_store');
