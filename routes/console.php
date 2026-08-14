<?php

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schedule;

Event::listen(CommandStarting::class, function ($event) {});

/*
 * Limpieza nocturna de precompras PE huérfanas (usuario cerró navegador
 * sin pasar por onClose). TTL por defecto: 7 días.
 */
Schedule::command('precompras:marcar-abandonadas --dias=7')
    ->dailyAt('02:00')
    ->withoutOverlapping();
