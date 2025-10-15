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
        Route::get('/index', [ConyugeController::class, 'indexAction'])->name('conyuge.index');
        Route::post('/guardar', [ConyugeController::class, 'guardarAction']);
        Route::post('/borrar_archivo', [ConyugeController::class, 'borrarArchivoAction']);
        Route::post('/guardar_archivo', [ConyugeController::class, 'guardarArchivoAction']);

        Route::post('/enviar_caja', [ConyugeController::class, 'enviarCajaAction']);
        Route::get('/seguimiento/{id}', [ConyugeController::class, 'seguimientoAction']);

        Route::post('/params', [ConyugeController::class, 'paramsAction']);
        Route::post('/render_table', [ConyugeController::class, 'renderTableAction']);
        Route::post('/render_table/{estado}', [ConyugeController::class, 'renderTableAction']);
        Route::post('/search_request/{id}', [ConyugeController::class, 'searchRequestAction']);
        Route::post('/consulta_documentos/{id}', [ConyugeController::class, 'consultaDocumentosAction']);
        Route::post('/valida', [ConyugeController::class, 'validaAction']);

        Route::post('/download_docs/{archivo}', [ConyugeController::class, 'downloadDocumentosAction']);
        Route::post('/borrar', [ConyugeController::class, 'borrarAction']);
        Route::post('/borrar/{id}', [ConyugeController::class, 'borrarAction']);
        Route::post('/buscar_trabajador', [ConyugeController::class, 'buscarTrabajadorAction']);
    });
});
