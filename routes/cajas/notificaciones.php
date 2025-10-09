<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\NotificacionesController;
use Illuminate\Support\Facades\Route;

Route::prefix('notificaciones')->group(function () {
    // Definir rutas para el controlador Notificaciones
    Route::get('/', [NotificacionesController::class, 'indexAction'])->name('notificaciones.index'); // Mostrar lista de notificaciones
    Route::get('/refresh', [NotificacionesController::class, 'refreshAction'])->name('notificaciones.refresh'); // Refrescar notificaciones
    Route::post('/refresh-pagination', [NotificacionesController::class, 'refresh_paginationAction'])->name('notificaciones.refresh.pagination'); // Refrescar notificaciones con paginación
    Route::get('/detalle/{id}', [NotificacionesController::class, 'detalleAction'])->name('notificaciones.detalle'); // Ver detalle de notificación
    Route::post('/create', [NotificacionesController::class, 'createAction'])->name('notificaciones.create'); // Crear notificación
    Route::post('/change-state', [NotificacionesController::class, 'change_stateAction'])->name('notificaciones.change.state'); // Cambiar estado de notificación
});
