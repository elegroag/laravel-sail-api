<?php

use App\Http\Controllers\Mercurio\ActualizaEmpresaController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Actualiza datos empresa  (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/actualizadatos')->group(function () {
        Route::get('/', function () {
            return redirect()->route('actualiza_empresa.index');
        });
        Route::get('/index', [ActualizaEmpresaController::class, 'index'])->name('actualiza_empresa.index');
        Route::post('/buscar_empresa', [ActualizaEmpresaController::class, 'buscarEmpresa']);
        Route::post('/guardar', [ActualizaEmpresaController::class, 'guardar']);
        Route::post('/borrar_archivo', [ActualizaEmpresaController::class, 'borrarArchivo']);
        Route::post('/guardar_archivo', [ActualizaEmpresaController::class, 'guardarArchivo']);
        Route::post('/archivos_requeridos/{id}', [ActualizaEmpresaController::class, 'archivosRequeridos']);
        Route::post('/enviar_caja', [ActualizaEmpresaController::class, 'enviarCaja']);
        Route::post('/seguimiento/{id}', [ActualizaEmpresaController::class, 'seguimiento']);
        Route::post('/params', [ActualizaEmpresaController::class, 'params']);
        Route::get('/download_temp/{archivo}', [ActualizaEmpresaController::class, 'downloadFile']);
        Route::get('/download_docs/{archivo}', [ActualizaEmpresaController::class, 'downloadDocs']);

        Route::post('/search_request/{id}', [ActualizaEmpresaController::class, 'searchRequest']);
        Route::post('/consulta_documentos/{id}', [ActualizaEmpresaController::class, 'consultaDocumentos']);
        Route::post('/borrar', [ActualizaEmpresaController::class, 'borrar']);
        Route::post('/params', [ActualizaEmpresaController::class, 'params']);
        Route::post('/render_table', [ActualizaEmpresaController::class, 'renderTable']);
        Route::post('/render_table/{estado}', [ActualizaEmpresaController::class, 'renderTable']);

        Route::post('/valida', [ActualizaEmpresaController::class, 'valida']);
        Route::post('/digito_verification', [ActualizaEmpresaController::class, 'digitoVerification']);
        Route::post('/empresa_sisu', [ActualizaEmpresaController::class, 'empresaSisu']);
    });
});
