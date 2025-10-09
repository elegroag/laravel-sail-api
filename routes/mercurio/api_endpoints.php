<?php

use App\Http\Controllers\Api\ApiEndpointController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Endpoints Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API endpoint routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Rutas API para gestión de endpoints
Route::apiResource('api-endpoints', ApiEndpointController::class);

// Ruta para actualizar solo el nombre de conexión
Route::put('api-endpoints/service/{serviceName}/connection-name', [ApiEndpointController::class, 'updateConnectionName']);

// Ruta para sincronizar con valores por defecto
Route::post('api-endpoints/sync-defaults', [ApiEndpointController::class, 'syncDefaults']);
