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
        Schema::create('mercurio19', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas según SQL
            $table->char('tipo', 2); // NOT NULL
            $table->char('coddoc', 2); // NOT NULL
            $table->char('documento', 16); // NOT NULL
            $table->char('codigo', 3)->default(''); // NOT NULL DEFAULT ''
            $table->char('respuesta', 60)->nullable();
            $table->char('codver', 10)->nullable();
            $table->string('token', 800)->nullable();
            $table->dateTime('inicio')->nullable();
            $table->smallInteger('intentos')->default(0); // smallint(2) DEFAULT '0'

            // PK compuesta
            $table->primary(['tipo', 'coddoc', 'documento']);

            // Índice compuesto y FK compuesta
            $table->index(['tipo', 'coddoc', 'documento'], 'fk_mercurio19_mercurio071_idx');
            $table->foreign(['tipo', 'coddoc', 'documento'], 'fk_mercurio19_mercurio071')
                ->references(['tipo', 'coddoc', 'documento'])
                ->on('mercurio07')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio19', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio19_mercurio071');
            $table->dropIndex('fk_mercurio19_mercurio071_idx');
        });
        Schema::dropIfExists('mercurio19');
    }
};
