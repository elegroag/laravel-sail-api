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
        Schema::create('mercurio71', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->increments('numero');

            // Columnas según SQL
            $table->char('tipo', 2);
            $table->char('documento', 7);
            $table->char('coddoc', 2);
            $table->string('email', 60);
            $table->enum('estado', ['P', 'A']);
            $table->date('fecsis');
            $table->string('hora', 8);
            $table->date('fecest')->nullable();
            $table->char('codigo', 8)->nullable();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio71');
    }
};
