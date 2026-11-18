<?php

use App\Http\Controllers\Cajas\ApruebaEmpresaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/aprobacionemp')->name('cajas.aprobacionemp.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('cajas.aprobacionemp.index');
        });
        Route::get('/index', [ApruebaEmpresaController::class, 'index'])->name('index');

        // API JSON compartida con el resto de módulos de solicitudes
        Route::post('/editar-formulario', [ApruebaEmpresaController::class, 'editarFormulario'])->name('editar_formulario');
        Route::post('/editar-solicitud', [ApruebaEmpresaController::class, 'editarSolicitud'])->name('editar_solicitud');

        Route::prefix('/{id}')->whereNumber('id')->group(function () {
            Route::get('/', [ApruebaEmpresaController::class, 'show'])->name('show');
            Route::get('/edit', [ApruebaEmpresaController::class, 'edit'])->name('edit');
            Route::put('/', [ApruebaEmpresaController::class, 'update'])->name('update');
            Route::get('/aportes', [ApruebaEmpresaController::class, 'aportes'])->name('aportes');
            Route::get('/deshacer', [ApruebaEmpresaController::class, 'deshacerForm'])->name('deshacer_form');
            Route::get('/archivos/{coddoc}', [ApruebaEmpresaController::class, 'archivo'])->name('archivo');

            Route::post('/aprobar', [ApruebaEmpresaController::class, 'aprobar'])->name('aprobar');
            Route::post('/devolver', [ApruebaEmpresaController::class, 'devolver'])->name('devolver');
            Route::post('/rechazar', [ApruebaEmpresaController::class, 'rechazar'])->name('rechazar');
            Route::post('/reaprobar', [ApruebaEmpresaController::class, 'reaprobar'])->name('reaprobar');
            Route::post('/deshacer', [ApruebaEmpresaController::class, 'deshacer'])->name('deshacer');
        });
    });
});
