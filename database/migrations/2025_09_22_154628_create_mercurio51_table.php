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
        Schema::create('mercurio51', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Clave primaria entera NO autoincremental
            $table->integer('codcat');
            $table->primary('codcat');

            // Columnas
            $table->string('detalle', 45);
            $table->char('tipo', 2);
            $table->enum('estado', ['A', 'I']);
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio51');
    }
};
