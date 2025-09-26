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
        Schema::create('mercurio55', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK entera NO autoincremental
            $table->integer('codare');
            $table->primary('codare');

            // Columnas
            $table->string('detalle', 45);
            $table->integer('codcat');
            $table->char('tipo', 2);
            $table->enum('estado', ['A', 'I']);

            // Índice y FK
            $table->index('codcat', 'fk_mercurio55_mercurio51_idx');
            $table->foreign('codcat', 'fk_mercurio55_mercurio51')
                ->references('codcat')
                ->on('mercurio51')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio55', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio55_mercurio51');
            $table->dropIndex('fk_mercurio55_mercurio51_idx');
        });
        Schema::dropIfExists('mercurio55');
    }
};
