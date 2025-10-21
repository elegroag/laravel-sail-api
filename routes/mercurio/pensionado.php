<?php

use App\Http\Controllers\Mercurio\PensionadoController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Pensionado (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/pensionado')->group(function () {
        Route::get('/', function () {
            return redirect()->route('pensionado.index');
        });

        Route::get('/index', [PensionadoController::class, 'index'])->name('pensionado.index');
        Route::post('/buscar_empresa', [PensionadoController::class, 'buscarEmpresa']);
        Route::post('/guardar', [PensionadoController::class, 'guardar']);
        Route::post('/borrar_archivo', [PensionadoController::class, 'borrarArchivo']);
        Route::post('/guardar_archivo', [PensionadoController::class, 'guardarArchivo']);
        Route::post('/enviar_caja', [PensionadoController::class, 'enviarCaja']);
        Route::post('/seguimiento/{id}', [PensionadoController::class, 'seguimiento']);
        Route::post('/params', [PensionadoController::class, 'params']);
        Route::get('/download_temp/{archivo}', [PensionadoController::class, 'downloadFile']);
        Route::get('/download_docs/{archivo}', [PensionadoController::class, 'downloadDocs']);

        Route::post('/search_request/{id}', [PensionadoController::class, 'searchRequest']);
        Route::post('/consulta_documentos/{id}', [PensionadoController::class, 'consultaDocumentos']);

        Route::post('/borrar', [PensionadoController::class, 'borrar']);
        Route::post('/borrar/{id}', [PensionadoController::class, 'borrar']);
        Route::post('/params', [PensionadoController::class, 'params']);

        Route::post('/render_table', [PensionadoController::class, 'renderTable']);
        Route::post('/render_table/{estado}', [PensionadoController::class, 'renderTable']);

        Route::post('/valida', [PensionadoController::class, 'valida']);
        Route::post('/digito_verification', [PensionadoController::class, 'digitoVerification']);
    });
});
