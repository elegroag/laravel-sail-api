<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\AdmserviciosController;
use App\Http\Controllers\Cajas\ConciliacionServiciosController;
use App\Http\Controllers\Cajas\DevolucionServiciosController;
use App\Http\Controllers\Cajas\Mercurio59Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/servicios')->group(function () {

        Route::get('/index', [Mercurio59Controller::class, 'index'])->name('mercurio59.index');
        Route::post('/aplicar-filtro', [Mercurio59Controller::class, 'aplicarFiltro'])->name('mercurio59.aplicar-filtro');
        Route::post('/change-cantidad-pagina', [Mercurio59Controller::class, 'changeCantidadPagina'])->name('mercurio59.change-cantidad-pagina');
        Route::get('/buscar', [Mercurio59Controller::class, 'buscar'])->name('mercurio59.buscar');
        Route::get('/editar', [Mercurio59Controller::class, 'editar'])->name('mercurio59.editar');
        Route::delete('/borrar', [Mercurio59Controller::class, 'borrar'])->name('mercurio59.borrar');
        Route::post('/guardar', [Mercurio59Controller::class, 'guardar'])->name('mercurio59.guardar');
        Route::post('/valide-pk', [Mercurio59Controller::class, 'validePk'])->name('mercurio59.valide-pk');
        Route::get('/reporte/{format?}', [Mercurio59Controller::class, 'reporte'])->name('mercurio59.reporte');
    });

    // Consulta administrativa de ventas/precompras de servicios del portal
    Route::prefix('/cajas/admservicios')->group(function () {
        Route::get('/index', [AdmserviciosController::class, 'index'])->name('admservicios.index');
        Route::get('/ver/{id}', [AdmserviciosController::class, 'ver'])->whereNumber('id')->name('admservicios.ver');
        Route::post('/detalle/{id}', [AdmserviciosController::class, 'detalle'])->name('admservicios.detalle');
        Route::post('/registrar-subsidio/{id}', [AdmserviciosController::class, 'registrarSubsidio'])->name('admservicios.registrar-subsidio');
        Route::post('/marcar-vencida/{id}', [AdmserviciosController::class, 'marcarVencida'])->whereNumber('id')->name('admservicios.marcar-vencida');
        Route::post('/devolucion/{id}/en-tramite', [AdmserviciosController::class, 'devolucionEnTramite'])->whereNumber('id')->name('admservicios.devolucion-en-tramite');
        Route::post('/devolucion/{id}/aprobar', [AdmserviciosController::class, 'devolucionAprobar'])->whereNumber('id')->name('admservicios.devolucion-aprobar');
        Route::post('/devolucion/{id}/rechazar', [AdmserviciosController::class, 'devolucionRechazar'])->whereNumber('id')->name('admservicios.devolucion-rechazar');
        Route::get('/reporte/{format?}', [AdmserviciosController::class, 'reporte'])->name('admservicios.reporte');
    });

    // Conciliación precompras PA vs ventas Subsidio
    Route::prefix('/cajas/conciliacion-servicios')->group(function () {
        Route::get('/index', [ConciliacionServiciosController::class, 'index'])->name('conciliacion-servicios.index');
        Route::get('/reporte/{format?}', [ConciliacionServiciosController::class, 'reporte'])->name('conciliacion-servicios.reporte');
    });

    // Bandeja de solicitudes de devolución (Mercurio → Cajas) — Inertia
    Route::prefix('/cajas/devolucion-servicios')->group(function () {
        Route::get('/index', [DevolucionServiciosController::class, 'index'])->name('devolucion-servicios.index');
        Route::post('/{id}/en-tramite', [DevolucionServiciosController::class, 'enTramite'])->whereNumber('id')->name('devolucion-servicios.en-tramite');
        Route::post('/{id}/aprobar', [DevolucionServiciosController::class, 'aprobar'])->whereNumber('id')->name('devolucion-servicios.aprobar');
        Route::post('/{id}/rechazar', [DevolucionServiciosController::class, 'rechazar'])->whereNumber('id')->name('devolucion-servicios.rechazar');
    });
});
