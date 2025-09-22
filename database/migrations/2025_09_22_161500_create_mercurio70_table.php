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
        Schema::create('mercurio70', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->increments('numero');

            // Columnas según SQL
            $table->char('tipo', 2);
            $table->char('documento', 7);
            $table->char('coddoc', 2);
            $table->char('codser', 7);
            $table->integer('puntos');
            $table->integer('calificacion');
            $table->string('nota', 255)->nullable();
            $table->date('fecsis');
            $table->string('hora', 8);
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio70');
    }
};
