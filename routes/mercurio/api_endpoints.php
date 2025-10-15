<?php

use App\Http\Controllers\Api\ApiEndpointController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::apiResource('api-endpoints', ApiEndpointController::class);
    Route::put('api-endpoints/service/{serviceName}/connection-name', [ApiEndpointController::class, 'updateConnectionName']);
    Route::post('api-endpoints/sync-defaults', [ApiEndpointController::class, 'syncDefaults']);
});
