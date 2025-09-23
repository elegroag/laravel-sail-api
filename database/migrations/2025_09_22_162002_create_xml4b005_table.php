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
        Schema::create('xml4b005', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas según SQL
            $table->integer('tipgen'); // PK sin autoincremento
            $table->char('nombre', 45);
            $table->char('codsex', 1)->nullable();

            // PK
            $table->primary('tipgen');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('xml4b005');
    }
};
