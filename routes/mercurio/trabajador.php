<?php

use App\Http\Controllers\Mercurio\TrabajadorController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Trabajador (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('mercurio/trabajador')->group(function () {

        Route::get('/', [TrabajadorController::class, 'indexAction']);
        Route::get('/index', [TrabajadorController::class, 'indexAction']);
        Route::post('buscar_trabajador', [TrabajadorController::class, 'buscarTrabajadorAction']);
        Route::post('guardar', [TrabajadorController::class, 'guardarAction']);
        Route::post('borrar_archivo', [TrabajadorController::class, 'borrarArchivoAction']);
        Route::post('guardar_archivo', [TrabajadorController::class, 'guardarArchivoAction']);
        Route::get('archivos_requeridos/{id}', [TrabajadorController::class, 'archivosRequeridosAction']);
        Route::post('enviar_caja', [TrabajadorController::class, 'enviarCajaAction']);
        Route::get('seguimiento/{id}', [TrabajadorController::class, 'seguimientoAction']);

        Route::post('params', [TrabajadorController::class, 'paramsAction']);
        Route::post('render_table', [TrabajadorController::class, 'renderTableAction']);
        Route::post('render_table/{estado}', [TrabajadorController::class, 'renderTableAction']);
        Route::post('search_request/{id}', [TrabajadorController::class, 'searchRequestAction']);
        Route::post('consulta_documentos/{id}', [TrabajadorController::class, 'consultaDocumentosAction']);
        Route::post('valida', [TrabajadorController::class, 'validaAction']);

        Route::post('borrar', [TrabajadorController::class, 'borrarAction']);
        Route::post('borrar/{id}', [TrabajadorController::class, 'borrarAction']);

        Route::post('valide_nit', [TrabajadorController::class, 'valideNitAction']);
        Route::post('traer_trabajador', [TrabajadorController::class, 'traerTrabajadorAction']);
    });
});
