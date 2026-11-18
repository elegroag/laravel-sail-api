<?php

namespace App\Http\Routing;

use Illuminate\Support\Facades\Route;

/**
 * Rutas del flujo común de aprobación de solicitudes (ver ApruebaSolicitudController).
 */
class SolicitudRoutes
{
    /**
     * @param  class-string  $controller
     * @param  array{editar?: bool, aportes?: bool, deshacer?: bool, reaprobar?: bool, devolver?: bool, multiafiliacion?: bool, trayectoria?: bool}  $features
     */
    public static function register(string $path, string $name, string $controller, array $features = []): void
    {
        Route::middleware(['cajas.auth'])->group(function () use ($path, $name, $controller, $features) {
            Route::prefix($path)->name($name.'.')->group(function () use ($name, $controller, $features) {
                Route::get('/', fn () => redirect()->route($name.'.index'))->name('root');
                Route::get('/index', [$controller, 'index'])->name('index');

                if ($features['editar'] ?? false) {
                    // API JSON compartida con el resto de módulos de solicitudes
                    Route::post('/editar-formulario', [$controller, 'editarFormulario'])->name('editar_formulario');
                    Route::post('/editar-solicitud', [$controller, 'editarSolicitud'])->name('editar_solicitud');
                }

                Route::prefix('/{id}')->whereNumber('id')->group(function () use ($controller, $features) {
                    Route::get('/', [$controller, 'show'])->name('show');
                    Route::get('/archivos/{coddoc}', [$controller, 'archivo'])->name('archivo');

                    if ($features['editar'] ?? false) {
                        Route::get('/edit', [$controller, 'edit'])->name('edit');
                        Route::put('/', [$controller, 'update'])->name('update');
                    }
                    if ($features['aportes'] ?? false) {
                        Route::get('/aportes', [$controller, 'aportes'])->name('aportes');
                    }
                    if ($features['deshacer'] ?? false) {
                        Route::get('/deshacer', [$controller, 'deshacerForm'])->name('deshacer_form');
                        Route::post('/deshacer', [$controller, 'deshacer'])->name('deshacer');
                    }
                    if ($features['reaprobar'] ?? false) {
                        Route::post('/reaprobar', [$controller, 'reaprobar'])->name('reaprobar');
                    }
                    if ($features['multiafiliacion'] ?? false) {
                        Route::post('/validar-multiafiliacion', [$controller, 'validarMultiafiliacion'])->name('validar_multiafiliacion');
                    }
                    if ($features['trayectoria'] ?? false) {
                        Route::get('/trayectoria', [$controller, 'trayectoria'])->name('trayectoria');
                    }
                    if ($features['devolver'] ?? true) {
                        Route::post('/devolver', [$controller, 'devolver'])->name('devolver');
                    }

                    Route::post('/aprobar', [$controller, 'aprobar'])->name('aprobar');
                    Route::post('/rechazar', [$controller, 'rechazar'])->name('rechazar');
                });
            });
        });
    }
}
