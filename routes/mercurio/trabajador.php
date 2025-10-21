<?php

use App\Http\Controllers\Mercurio\TrabajadorController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Trabajador (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/trabajador')->group(function () {
        Route::get('/', function () {
            return redirect()->route('trabajador.index');
        });
        Route::get('/index', [TrabajadorController::class, 'index'])->name('trabajador.index');
        Route::post('/buscar_trabajador', [TrabajadorController::class, 'buscarTrabajador'])->name('trabajador.buscar_trabajador');
        Route::post('/guardar', [TrabajadorController::class, 'guardar'])->name('trabajador.guardar');
        Route::post('/borrar_archivo', [TrabajadorController::class, 'borrarArchivo'])->name('trabajador.borrar_archivo');
        Route::post('/guardar_archivo', [TrabajadorController::class, 'guardarArchivo'])->name('trabajador.guardar_archivo');
        Route::get('/archivos_requeridos/{id}', [TrabajadorController::class, 'archivosRequeridos'])->name('trabajador.archivos_requeridos');
        Route::post('/enviar_caja', [TrabajadorController::class, 'enviarCaja'])->name('trabajador.enviar_caja');
        Route::get('/seguimiento/{id}', [TrabajadorController::class, 'seguimiento'])->name('trabajador.seguimiento');

        Route::post('/params', [TrabajadorController::class, 'params']);
        Route::post('/render_table', [TrabajadorController::class, 'renderTable']);
        Route::post('/render_table/{estado}', [TrabajadorController::class, 'renderTable']);
        Route::post('/search_request/{id}', [TrabajadorController::class, 'searchRequest']);
        Route::post('/consulta_documentos/{id}', [TrabajadorController::class, 'consultaDocumentos']);
        Route::post('/valida', [TrabajadorController::class, 'valida']);

        Route::post('/borrar', [TrabajadorController::class, 'borrar']);
        Route::post('/borrar/{id}', [TrabajadorController::class, 'borrar']);

        Route::post('/valide_nit', [TrabajadorController::class, 'valideNit']);
        Route::post('/traer_trabajador', [TrabajadorController::class, 'traerTrabajador']);
    });
});
