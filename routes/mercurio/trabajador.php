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
        Route::get('/index', [TrabajadorController::class, 'indexAction'])->name('trabajador.index');
        Route::post('/buscar_trabajador', [TrabajadorController::class, 'buscarTrabajadorAction'])->name('trabajador.buscar_trabajador');
        Route::post('/guardar', [TrabajadorController::class, 'guardarAction'])->name('trabajador.guardar');
        Route::post('/borrar_archivo', [TrabajadorController::class, 'borrarArchivoAction'])->name('trabajador.borrar_archivo');
        Route::post('/guardar_archivo', [TrabajadorController::class, 'guardarArchivoAction'])->name('trabajador.guardar_archivo');
        Route::get('/archivos_requeridos/{id}', [TrabajadorController::class, 'archivosRequeridosAction'])->name('trabajador.archivos_requeridos');
        Route::post('/enviar_caja', [TrabajadorController::class, 'enviarCajaAction'])->name('trabajador.enviar_caja');
        Route::get('/seguimiento/{id}', [TrabajadorController::class, 'seguimientoAction'])->name('trabajador.seguimiento');

        Route::post('/params', [TrabajadorController::class, 'paramsAction']);
        Route::post('/render_table', [TrabajadorController::class, 'renderTableAction']);
        Route::post('/render_table/{estado}', [TrabajadorController::class, 'renderTableAction']);
        Route::post('/search_request/{id}', [TrabajadorController::class, 'searchRequestAction']);
        Route::post('/consulta_documentos/{id}', [TrabajadorController::class, 'consultaDocumentosAction']);
        Route::post('/valida', [TrabajadorController::class, 'validaAction']);

        Route::post('/borrar', [TrabajadorController::class, 'borrarAction']);
        Route::post('/borrar/{id}', [TrabajadorController::class, 'borrarAction']);

        Route::post('/valide_nit', [TrabajadorController::class, 'valideNitAction']);
        Route::post('/traer_trabajador', [TrabajadorController::class, 'traerTrabajadorAction']);
    });
});
