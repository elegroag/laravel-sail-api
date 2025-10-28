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
        Schema::create('mercurio53', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK entera NO autoincremental
            $table->integer('numero')->autoIncrement();
            $table->primary('numero');

            // Columnas
            $table->string('archivo', 100);
            $table->integer('orden');
            $table->string('url', 100)->nullable();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio53');
    }
};
