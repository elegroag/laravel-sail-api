<?php


use App\Http\Controllers\TaskController;
use App\Http\Controllers\WebController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/* Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard'); */

Route::resource('tasks', TaskController::class);

Route::get('/web', [WebController::class, 'dashboard'])->name('dashboard');
Route::get('/web/empresas', [WebController::class, 'empresas'])->name('empresas.index');
Route::get('/web/trabajadores', [WebController::class, 'trabajadores'])->name('trabajadores.index');
Route::get('/web/nucleos-familiares', [WebController::class, 'nucleosFamiliares'])->name('nucleos-familiares.index');
Route::get('/web/empresas/api', [WebController::class, 'pruebaApiEmpresas']);


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
