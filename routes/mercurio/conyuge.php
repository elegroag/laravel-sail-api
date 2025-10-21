<?php

use App\Http\Controllers\Mercurio\ConyugeController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Conyuge (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/conyuge')->group(function () {
        Route::get('/', function () {
            return redirect()->route('conyuge.index');
        });
        Route::get('/index', [ConyugeController::class, 'index'])->name('conyuge.index');
        Route::post('/guardar', [ConyugeController::class, 'guardar']);
        Route::post('/borrar_archivo', [ConyugeController::class, 'borrarArchivo']);
        Route::post('/guardar_archivo', [ConyugeController::class, 'guardarArchivo']);

        Route::post('/enviar_caja', [ConyugeController::class, 'enviarCaja']);
        Route::get('/seguimiento/{id}', [ConyugeController::class, 'seguimiento']);

        Route::post('/params', [ConyugeController::class, 'params']);
        Route::post('/render_table', [ConyugeController::class, 'renderTable']);
        Route::post('/render_table/{estado}', [ConyugeController::class, 'renderTable']);
        Route::post('/search_request/{id}', [ConyugeController::class, 'searchRequest']);
        Route::post('/consulta_documentos/{id}', [ConyugeController::class, 'consultaDocumentos']);
        Route::post('/valida', [ConyugeController::class, 'valida']);

        Route::post('/download_docs/{archivo}', [ConyugeController::class, 'downloadDocumentos']);
        Route::post('/borrar', [ConyugeController::class, 'borrar']);
        Route::post('/borrar/{id}', [ConyugeController::class, 'borrar']);
        Route::post('/buscar_trabajador', [ConyugeController::class, 'buscarTrabajador']);
    });
});
