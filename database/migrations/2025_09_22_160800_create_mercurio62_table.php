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
        Schema::create('mercurio62', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            $table->integer('id')->autoIncrement();
            $table->primary('id');
            // Columnas según SQL
            $table->char('tipo', 2);
            $table->char('documento', 15);
            $table->char('coddoc', 2);
            $table->integer('salgir');
            $table->integer('salrec');
            $table->integer('consumo');
            $table->integer('puntos');
            $table->integer('punuti');
            
            $table->index(['tipo', 'documento', 'coddoc'], 'idx_mercurio62');
            $table->unique(['tipo', 'documento', 'coddoc'], 'uk_mercurio62');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {

        Schema::table('mercurio62', function (Blueprint $table) {
           $table->dropUnique('uk_mercurio62');
           $table->dropIndex('idx_mercurio62');
        });
        Schema::dropIfExists('mercurio62');
    }
};
