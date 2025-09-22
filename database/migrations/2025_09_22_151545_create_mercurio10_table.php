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
        Schema::create('mercurio10', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas
            $table->char('tipopc', 2); // NOT NULL
            $table->integer('numero'); // NOT NULL
            $table->integer('item'); // NOT NULL
            $table->char('estado', 1); // NOT NULL
            $table->string('nota', 800); // NOT NULL
            $table->char('codest', 2)->nullable();
            $table->date('fecsis'); // NOT NULL
            $table->string('campos_corregir', 200)->nullable();

            // PK compuesta
            $table->primary(['tipopc', 'numero', 'item']);

            // Índices
            $table->index('tipopc', 'fk_mercurio10_mercurio091_idx');
            $table->index('codest', 'fk_mercurio10_mercurio111_idx');

            // Claves foráneas
            $table->foreign('tipopc', 'fk_mercurio10_mercurio091')
                ->references('tipopc')
                ->on('mercurio09')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('codest', 'fk_mercurio10_mercurio111')
                ->references('codest')
                ->on('mercurio11')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio10', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio10_mercurio091');
            $table->dropForeign('fk_mercurio10_mercurio111');
            $table->dropIndex('fk_mercurio10_mercurio091_idx');
            $table->dropIndex('fk_mercurio10_mercurio111_idx');
        });
        Schema::dropIfExists('mercurio10');
    }
};
