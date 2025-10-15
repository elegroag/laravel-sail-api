<?php

use App\Http\Controllers\Mercurio\IndependienteController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Independiente (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/independiente')->group(function () {
        Route::get('/', function () {
            return redirect()->route('independiente.index');
        });
        Route::get('/index', [IndependienteController::class, 'indexAction'])->name('independiente.index');
        Route::post('/buscar_empresa', [IndependienteController::class, 'buscarEmpresaAction']);
        Route::post('/guardar', [IndependienteController::class, 'guardarAction']);
        Route::post('/borrar_archivo', [IndependienteController::class, 'borrarArchivoAction']);
        Route::post('/guardar_archivo', [IndependienteController::class, 'guardarArchivoAction']);
        Route::post('/archivos_requeridos/{id}', [IndependienteController::class, 'archivosRequeridosAction']);
        Route::post('/enviar_caja', [IndependienteController::class, 'enviarCajaAction']);
        Route::post('/seguimiento/{id}', [IndependienteController::class, 'seguimientoAction']);
        Route::post('/params', [IndependienteController::class, 'paramsAction']);
        Route::get('/download_temp/{archivo}', [IndependienteController::class, 'downloadFileAction']);
        Route::get('/download_docs/{archivo}', [IndependienteController::class, 'downloadDocsAction']);

        Route::post('/search_request/{id}', [IndependienteController::class, 'searchRequestAction']);
        Route::post('/consulta_documentos/{id}', [IndependienteController::class, 'consultaDocumentosAction']);
        Route::post('/borrar', [IndependienteController::class, 'borrarAction']);
        Route::post('/params', [IndependienteController::class, 'paramsAction']);
        Route::post('/render_table', [IndependienteController::class, 'renderTableAction']);
        Route::post('/render_table/{estado}', [IndependienteController::class, 'renderTableAction']);

        Route::post('/valida', [IndependienteController::class, 'validaAction']);
        Route::post('/digito_verification', [IndependienteController::class, 'digitoVerificationAction']);
    });
});
