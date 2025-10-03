<?php

use App\Http\Controllers\Mercurio\ConsultasEmpresaController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;


//Consultas de empresas
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/subsidioemp')->group(function () {
        Route::get('/consulta_trabajadores_view', [ConsultasEmpresaController::class, 'consultaTrabajadoresViewAction']);
        Route::get('/consulta_giro_view', [ConsultasEmpresaController::class, 'consultaGiroViewAction']);
        Route::get('/consulta_aportes_view', [ConsultasEmpresaController::class, 'consultaAportesViewAction']);
        Route::get('/consulta_nomina_view', [ConsultasEmpresaController::class, 'consultaNominaViewAction']);
        Route::get('/historial', [ConsultasEmpresaController::class, 'historialAction']);
        Route::get('/consulta_mora_presunta', [ConsultasEmpresaController::class, 'consultaMoraPresuntaAction']);

        Route::post('/consulta_nomina', [ConsultasEmpresaController::class, 'consultaNominaAction']);
        Route::post('/consulta_aportes', [ConsultasEmpresaController::class, 'consultaAportesAction']);
        Route::post('/consulta_giro', [ConsultasEmpresaController::class, 'consultaGiroAction']);
        Route::post('/consulta_trabajadores', [ConsultasEmpresaController::class, 'consultaTrabajadoresAction']);
        Route::post('/mora_presunta', [ConsultasEmpresaController::class, 'moraPresuntaAction']);
    });
});
