<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    {
        Schema::create('servicios_cupos', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->increments('id');

            // Columnas según SQL
            $table->char('codser', 1);
            $table->string('cupos', 10);
            $table->string('servicio', 100);
            $table->integer('estado');
            $table->string('url', 255);
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios_cupos');
    }
};
