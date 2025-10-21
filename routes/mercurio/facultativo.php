<?php

use App\Http\Controllers\Mercurio\FacultativoController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Facultativo (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/facultativo')->group(function () {
        Route::get('/', function () {
            return redirect()->route('facultativo.index');
        });
        Route::get('/index', [FacultativoController::class, 'index'])->name('facultativo.index');
        Route::post('/buscar_empresa', [FacultativoController::class, 'buscarEmpresa']);
        Route::post('/guardar', [FacultativoController::class, 'guardar']);
        Route::post('/borrar_archivo', [FacultativoController::class, 'borrarArchivo']);
        Route::post('/guardar_archivo', [FacultativoController::class, 'guardarArchivo']);
        Route::post('/archivos_requeridos/{id}', [FacultativoController::class, 'archivosRequeridos']);
        Route::post('/enviar_caja', [FacultativoController::class, 'enviarCaja']);
        Route::post('/seguimiento/{id}', [FacultativoController::class, 'seguimiento']);
        Route::post('/params', [FacultativoController::class, 'params']);
        Route::get('/download_temp/{archivo}', [FacultativoController::class, 'downloadFile']);
        Route::get('/download_docs/{archivo}', [FacultativoController::class, 'downloadDocs']);

        Route::post('/search_request/{id}', [FacultativoController::class, 'searchRequest']);
        Route::post('/consulta_documentos/{id}', [FacultativoController::class, 'consultaDocumentos']);
        Route::post('/borrar', [FacultativoController::class, 'borrar']);
        Route::post('/borrar/{id}', [FacultativoController::class, 'borrar']);

        Route::post('/params', [FacultativoController::class, 'params']);
        Route::post('/render_table', [FacultativoController::class, 'renderTable']);
        Route::post('/render_table/{estado}', [FacultativoController::class, 'renderTable']);

        Route::post('/valida', [FacultativoController::class, 'valida']);
        Route::post('/digito_verification', [FacultativoController::class, 'digitoVerification']);
    });
});
