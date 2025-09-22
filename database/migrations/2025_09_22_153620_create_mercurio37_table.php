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
        Schema::create('mercurio37', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas
            $table->char('tipopc', 2); // NOT NULL
            $table->integer('numero'); // NOT NULL
            $table->integer('coddoc'); // NOT NULL
            $table->char('archivo', 255); // NOT NULL
            $table->string('fhash', 800)->nullable();

            // PK compuesta
            $table->primary(['tipopc', 'numero', 'coddoc']);

            // Índice y FK
            $table->index('tipopc', 'fk_mercurio37_mercurio091_idx');
            $table->foreign('tipopc', 'fk_mercurio37_mercurio091')
                ->references('tipopc')
                ->on('mercurio09')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio37', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio37_mercurio091');
            $table->dropIndex('fk_mercurio37_mercurio091_idx');
        });
        Schema::dropIfExists('mercurio37');
    }
};
