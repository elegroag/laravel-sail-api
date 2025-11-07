<?php

use App\Http\Controllers\Mercurio\EmpresaController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Empresa (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/empresa')->group(function () {
        Route::get('/', function () {
            return redirect()->route('empresa.index');
        });
        Route::get('/index', [EmpresaController::class, 'index'])->name('empresa.index');
        Route::get('/download_temp/{archivo}', [EmpresaController::class, 'downloadFile']);
        Route::get('/download_docs/{archivo}', [EmpresaController::class, 'downloadDocs']);
        Route::get('/miempresa', [EmpresaController::class, 'miEmpresa'])->name('empresa.miempresa');

        Route::post('/buscar_empresa', [EmpresaController::class, 'buscarEmpresa']);
        Route::post('/guardar', [EmpresaController::class, 'guardar']);
        Route::post('/borrar_archivo', [EmpresaController::class, 'borrarArchivo']);
        Route::post('/guardar_archivo', [EmpresaController::class, 'guardarArchivo']);
        Route::post('/archivos_requeridos/{id}', [EmpresaController::class, 'archivosRequeridos']);
        Route::post('/enviar_caja', [EmpresaController::class, 'enviarCaja']);
        Route::post('/seguimiento', [EmpresaController::class, 'seguimiento']);
        Route::post('/params', [EmpresaController::class, 'params']);
        Route::post('/search_request/{id}', [EmpresaController::class, 'searchRequest']);
        Route::post('/consulta_documentos/{id}', [EmpresaController::class, 'consultaDocumentos']);
        Route::post('/borrar', [EmpresaController::class, 'borrar']);
        Route::post('/render_table', [EmpresaController::class, 'renderTable']);
        Route::post('/render_table/{estado}', [EmpresaController::class, 'renderTable']);
        Route::post('/valida', [EmpresaController::class, 'valida']);
        Route::post('/digito_verification', [EmpresaController::class, 'digitoVerification']);
    });
});
