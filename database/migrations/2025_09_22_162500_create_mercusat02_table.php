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
        Schema::create('mercusat02', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->increments('id');

            // Columnas según SQL
            $table->char('fecsol', 10)->nullable();
            $table->char('fecapr', 10)->nullable();
            $table->char('indeti', 15)->nullable()->default('0');
            $table->char('numtrasat', 18)->nullable();
            $table->char('documento', 16)->nullable()->comment('0');
            $table->char('coddoc', 2)->nullable()->comment('0');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercusat02');
    }
};
