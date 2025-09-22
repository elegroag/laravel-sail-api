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
        Schema::create('mercurio62', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas según SQL
            $table->char('tipo', 2);
            $table->char('documento', 15);
            $table->char('coddoc', 2);
            $table->integer('salgir');
            $table->integer('salrec');
            $table->integer('consumo');
            $table->integer('puntos');
            $table->integer('punuti');

            // PK compuesta
            $table->primary(['tipo', 'documento', 'coddoc']);
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio62');
    }
};
