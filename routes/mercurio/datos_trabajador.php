<?php

use App\Http\Controllers\Mercurio\ActualizaTrabajadorController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Actualiza datos trabajador  (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('mercurio/actualizadatostra')->group(function () {
        Route::get('/', function () {
            return redirect()->route('actualiza_trabajador.index');
        });
        Route::get('/index', [ActualizaTrabajadorController::class, 'index'])->name('actualiza_trabajador.index');
        Route::post('/buscar_empresa', [ActualizaTrabajadorController::class, 'buscarEmpresa']);
        Route::post('/guardar', [ActualizaTrabajadorController::class, 'guardar']);
        Route::post('/borrar_archivo', [ActualizaTrabajadorController::class, 'borrarArchivo']);
        Route::post('/guardar_archivo', [ActualizaTrabajadorController::class, 'guardarArchivo']);
        Route::post('/archivos_requeridos/{id}', [ActualizaTrabajadorController::class, 'archivosRequeridos']);
        Route::post('/enviar_caja', [ActualizaTrabajadorController::class, 'enviarCaja']);
        Route::post('/seguimiento/{id}', [ActualizaTrabajadorController::class, 'seguimiento']);
        Route::post('/params', [ActualizaTrabajadorController::class, 'params']);
        Route::get('/download_temp/{archivo}', [ActualizaTrabajadorController::class, 'downloadFile']);
        Route::get('/download_docs/{archivo}', [ActualizaTrabajadorController::class, 'downloadDocs']);

        Route::post('/search_request/{id}', [ActualizaTrabajadorController::class, 'searchRequest']);
        Route::post('/consulta_documentos/{id}', [ActualizaTrabajadorController::class, 'consultaDocumentos']);
        Route::post('/borrar', [ActualizaTrabajadorController::class, 'borrar']);
        Route::post('/params', [ActualizaTrabajadorController::class, 'params']);
        Route::post('/render_table', [ActualizaTrabajadorController::class, 'renderTable']);
        Route::post('/render_table/{estado}', [ActualizaTrabajadorController::class, 'renderTable']);

        Route::post('/valida', [ActualizaTrabajadorController::class, 'valida']);
        Route::post('/digito_verification', [ActualizaTrabajadorController::class, 'digitoVerification']);
        Route::post('/infor', [ActualizaTrabajadorController::class, 'infor']);
    });
});
