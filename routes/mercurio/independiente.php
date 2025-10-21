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
        Route::get('/index', [IndependienteController::class, 'index'])->name('independiente.index');
        Route::post('/buscar_empresa', [IndependienteController::class, 'buscarEmpresa']);
        Route::post('/guardar', [IndependienteController::class, 'guardar']);
        Route::post('/borrar_archivo', [IndependienteController::class, 'borrarArchivo']);
        Route::post('/guardar_archivo', [IndependienteController::class, 'guardarArchivo']);
        Route::post('/archivos_requeridos/{id}', [IndependienteController::class, 'archivosRequeridos']);
        Route::post('/enviar_caja', [IndependienteController::class, 'enviarCaja']);
        Route::post('/seguimiento/{id}', [IndependienteController::class, 'seguimiento']);
        Route::post('/params', [IndependienteController::class, 'params']);
        Route::get('/download_temp/{archivo}', [IndependienteController::class, 'downloadFile']);
        Route::get('/download_docs/{archivo}', [IndependienteController::class, 'downloadDocs']);

        Route::post('/search_request/{id}', [IndependienteController::class, 'searchRequest']);
        Route::post('/consulta_documentos/{id}', [IndependienteController::class, 'consultaDocumentos']);
        Route::post('/borrar', [IndependienteController::class, 'borrar']);
        Route::post('/params', [IndependienteController::class, 'params']);
        Route::post('/render_table', [IndependienteController::class, 'renderTable']);
        Route::post('/render_table/{estado}', [IndependienteController::class, 'renderTable']);

        Route::post('/valida', [IndependienteController::class, 'valida']);
        Route::post('/digito_verification', [IndependienteController::class, 'digitoVerification']);
    });
});
