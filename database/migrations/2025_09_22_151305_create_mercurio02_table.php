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
        Schema::create('mercurio02', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Clave primaria
            $table->char('codcaj', 6);
            $table->primary('codcaj');

            // Columnas según SQL
            $table->char('nit', 13);
            $table->char('razsoc', 60);
            $table->char('sigla', 30);
            $table->char('email', 90);
            $table->char('direccion', 90);
            $table->char('telefono', 20);
            $table->char('codciu', 5);
            $table->char('pagweb', 45);
            $table->char('pagfac', 45)->nullable();
            $table->char('pagtwi', 45)->nullable();
            $table->char('pagyou', 45)->nullable();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio02');
    }
};
