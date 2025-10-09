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
        Schema::create('mercurio56', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK CHAR(30)
            $table->char('codinf', 30);
            $table->primary('codinf');

            // Columnas
            $table->string('archivo', 100)->nullable();
            $table->string('email', 60);
            $table->char('telefono', 10)->nullable();
            $table->string('nota', 255);
            $table->enum('estado', ['A', 'I']);
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio56');
    }
};
