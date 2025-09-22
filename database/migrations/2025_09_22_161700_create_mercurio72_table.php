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
        Schema::create('mercurio72', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->increments('numtur');

            // Columnas según SQL
            $table->string('archivo', 100);
            $table->integer('orden');
            $table->string('url', 100)->nullable();
            $table->string('estado', 1);

            // Comentario de tabla (si la versión de Laravel/DB lo soporta)
            $table->comment('Tabla para mostrar promociones de turismo especificamente');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio72');
    }
};
