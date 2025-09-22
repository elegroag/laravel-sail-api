<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Registra comandos de clase para Artisan (Laravel 11)
Artisan::starting(function ($artisan) {
    // Registrar el comando que genera migraciones por tabla desde database/db.sql
});
