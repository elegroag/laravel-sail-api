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
        Route::get('/index', [ActualizaTrabajadorController::class, 'indexAction'])->name('actualiza_trabajador.index');
        Route::post('/buscar_empresa', [ActualizaTrabajadorController::class, 'buscarEmpresaAction']);
        Route::post('/guardar', [ActualizaTrabajadorController::class, 'guardarAction']);
        Route::post('/borrar_archivo', [ActualizaTrabajadorController::class, 'borrarArchivoAction']);
        Route::post('/guardar_archivo', [ActualizaTrabajadorController::class, 'guardarArchivoAction']);
        Route::post('/archivos_requeridos/{id}', [ActualizaTrabajadorController::class, 'archivosRequeridosAction']);
        Route::post('/enviar_caja', [ActualizaTrabajadorController::class, 'enviarCajaAction']);
        Route::post('/seguimiento/{id}', [ActualizaTrabajadorController::class, 'seguimientoAction']);
        Route::post('/params', [ActualizaTrabajadorController::class, 'paramsAction']);
        Route::get('/download_temp/{archivo}', [ActualizaTrabajadorController::class, 'downloadFileAction']);
        Route::get('/download_docs/{archivo}', [ActualizaTrabajadorController::class, 'downloadDocsAction']);

        Route::post('/search_request/{id}', [ActualizaTrabajadorController::class, 'searchRequestAction']);
        Route::post('/consulta_documentos/{id}', [ActualizaTrabajadorController::class, 'consultaDocumentosAction']);
        Route::post('/borrar', [ActualizaTrabajadorController::class, 'borrarAction']);
        Route::post('/params', [ActualizaTrabajadorController::class, 'paramsAction']);
        Route::post('/render_table', [ActualizaTrabajadorController::class, 'renderTableAction']);
        Route::post('/render_table/{estado}', [ActualizaTrabajadorController::class, 'renderTableAction']);

        Route::post('/valida', [ActualizaTrabajadorController::class, 'validaAction']);
        Route::post('/digito_verification', [ActualizaTrabajadorController::class, 'digitoVerificationAction']);
        Route::post('/infor', [ActualizaTrabajadorController::class, 'inforAction']);
    });
});
