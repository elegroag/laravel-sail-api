<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio14Controller;
use Illuminate\Support\Facades\Route;

// Rutas corregidas para Mercurio14Controller - Documentos y Empleador
Route::get('/documentos-empleador/index', [Mercurio14Controller::class, 'indexAction'])->name('mercurio14.index')->middleware('auth');
Route::get('/documentos-empleador/buscar', [Mercurio14Controller::class, 'buscarAction'])->name('mercurio14.buscar')->middleware('auth');
Route::get('/documentos-empleador/infor', [Mercurio14Controller::class, 'inforAction'])->name('mercurio14.infor')->middleware('auth');
Route::post('/documentos-empleador/guardar', [Mercurio14Controller::class, 'guardarAction'])->name('mercurio14.guardar')->middleware('auth');
Route::delete('/documentos-empleador/borrar', [Mercurio14Controller::class, 'borrarAction'])->name('mercurio14.borrar')->middleware('auth');
