<?php

use App\Http\Controllers\Mercurio\BeneficiarioController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

//  Beneficiario (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/beneficiario')->group(function () {
        Route::get('/', function () {
            return redirect()->route('beneficiario.index');
        });
        Route::get('/index', [BeneficiarioController::class, 'index'])->name('beneficiario.index');
        Route::post('/buscar_trabajador', [BeneficiarioController::class, 'buscarTrabajador']);
        Route::post('/guardar', [BeneficiarioController::class, 'guardar']);
        Route::post('/borrar_archivo', [BeneficiarioController::class, 'borrarArchivo']);
        Route::post('/guardar_archivo', [BeneficiarioController::class, 'guardarArchivo']);
        Route::get('/archivos_requeridos/{id}', [BeneficiarioController::class, 'archivosRequeridos']);
        Route::post('/enviar_caja', [BeneficiarioController::class, 'enviarCaja']);
        Route::post('/seguimiento', [BeneficiarioController::class, 'seguimiento']);

        Route::post('/params', [BeneficiarioController::class, 'params']);
        Route::post('/render_table', [BeneficiarioController::class, 'renderTable']);
        Route::post('/render_table/{estado}', [BeneficiarioController::class, 'renderTable']);
        Route::post('/search_request/{id}', [BeneficiarioController::class, 'searchRequest']);
        Route::post('/consulta_documentos/{id}', [BeneficiarioController::class, 'consultaDocumentos']);
        Route::post('/valida', [BeneficiarioController::class, 'valida']);

        Route::get('/download_temp/{archivo}', [BeneficiarioController::class, 'downloadFile']);
        Route::get('/download_docs/{archivo}', [BeneficiarioController::class, 'downloadDocs']);
        Route::post('/borrar', [BeneficiarioController::class, 'borrar']);
        Route::post('/borrar/{id}', [BeneficiarioController::class, 'borrar']);
    });
});
