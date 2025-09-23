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
        Schema::create('xml4b087', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas según SQL
            $table->integer('codpob'); // PK sin autoincremento
            $table->char('nombre', 100);

            // PK
            $table->primary('codpob');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('xml4b087');
    }
};
