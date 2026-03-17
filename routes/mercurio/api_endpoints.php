<?php

use App\Http\Controllers\Api\ApiEndpointController;
use Illuminate\Support\Facades\Route;

Route::middleware(['mercurio.auth'])->group(function () {
    Route::apiResource('api-endpoints', ApiEndpointController::class);
    Route::put('api-endpoints/service/{serviceName}/connection-name', [ApiEndpointController::class, 'updateConnectionName']);
    Route::post('api-endpoints/sync-defaults', [ApiEndpointController::class, 'syncDefaults']);
});
