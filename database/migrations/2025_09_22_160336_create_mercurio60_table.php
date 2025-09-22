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
        Schema::create('mercurio60', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->increments('id');

            // Columnas según SQL
            $table->char('codinf', 30);
            $table->char('codser', 7);
            $table->integer('numero');
            $table->char('tipo', 2);
            $table->char('documento', 7);
            $table->char('coddoc', 2);
            $table->char('codcat', 1);
            $table->integer('valtot');
            $table->date('fecsis');
            $table->char('hora', 8);
            $table->enum('tipmov', ['B','U','P'])->nullable();
            $table->integer('online')->nullable();
            $table->char('consumo', 1);
            $table->date('feccon')->nullable();
            $table->integer('punuti')->nullable();
            $table->integer('puntos')->nullable();
            $table->enum('estado', ['A','P']);
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio60');
    }
};
