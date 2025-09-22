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
        Schema::create('mercurio73', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->increments('numedu');

            // Columnas según SQL
            $table->string('archivo', 100);
            $table->integer('orden');
            $table->string('url', 100)->nullable();
            $table->string('estado', 1);

            // Comentario
            $table->comment('Tabla para mostrar promociones de educacion especificamente');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio73');
    }
};
