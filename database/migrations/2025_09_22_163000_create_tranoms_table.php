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
        Schema::create('tranoms', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK autoincremental (INT UNSIGNED)
            $table->increments('id');

            // Columnas según SQL
            $table->unsignedBigInteger('request');
            $table->char('cedtra', 18);
            $table->string('nomtra', 60);
            $table->char('apetra', 60);
            $table->char('saltra', 20);
            $table->date('fectra');
            $table->string('cartra', 120)->nullable();

            // Índice para request
            $table->index('request', 'fk_tranoms_mercurio30_idx');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('tranoms');
    }
};
