<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\TrabajadorController;
use App\Http\Controllers\Api\NucleoFamiliarController;
use App\Http\Controllers\Api\ApiEndpointController;
use App\Http\Controllers\Api\AuthMercurioController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas API para gestión de empresas, trabajadores y núcleo familiar
Route::apiResource('empresas', EmpresaController::class);
Route::apiResource('trabajadores', TrabajadorController::class);
Route::apiResource('nucleos-familiares', NucleoFamiliarController::class);

// Rutas API para gestión de endpoints
Route::apiResource('api-endpoints', ApiEndpointController::class);

// Ruta para actualizar solo el nombre de conexión
Route::put('api-endpoints/service/{serviceName}/connection-name', [ApiEndpointController::class, 'updateConnectionName']);

// Ruta para sincronizar con valores por defecto
Route::post('api-endpoints/sync-defaults', [ApiEndpointController::class, 'syncDefaults']);

// Rutas adicionales para relaciones específicas
Route::get('empresas/{empresa}/trabajadores', [TrabajadorController::class, 'index'])
    ->where('empresa', '[0-9]+');
Route::get('trabajadores/{trabajador}/nucleos-familiares', [NucleoFamiliarController::class, 'index'])
    ->where('trabajador', '[0-9]+');

Route::post('api/authenticate', [AuthMercurioController::class, 'authenticateAction'])->name('api.authenticate');
