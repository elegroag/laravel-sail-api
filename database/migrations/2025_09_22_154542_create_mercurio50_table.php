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
        Schema::create('mercurio50', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Clave primaria CHAR(2)
            $table->char('codapl', 2);
            $table->primary('codapl');

            // Columnas
            $table->string('webser', 50);
            $table->string('path', 45);
            $table->string('urlonl', 60)->nullable();
            $table->integer('puncom')->nullable(); // int(11) NULL
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio50');
    }
};
