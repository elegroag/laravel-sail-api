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
        Schema::create('mercurio03', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK
            $table->char('codfir', 3);
            $table->primary('codfir');

            // Columnas
            $table->char('nombre', 60);
            $table->char('cargo', 60);
            $table->char('archivo', 30)->nullable();
            $table->char('email', 60)->nullable();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercurio03');
    }
};
