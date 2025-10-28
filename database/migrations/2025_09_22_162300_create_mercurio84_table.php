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
        Schema::create('mercurio84', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas
            $table->integer('id')->autoIncrement();
            $table->primary('id');
            $table->integer('evento');
            $table->integer('beneficiario');

            // PK compuesta
            $table->index(['evento', 'beneficiario'], 'mercurio84_idx');
            $table->unique(['evento', 'beneficiario'], 'mercurio84_unique_idx');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio84', function (Blueprint $table) {
            $table->dropIndex('mercurio84_idx');
            $table->dropUnique('mercurio84_unique_idx');
        });
        Schema::dropIfExists('mercurio84');
    }
};
