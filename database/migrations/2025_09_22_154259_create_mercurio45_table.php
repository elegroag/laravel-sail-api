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
        Schema::create('mercurio45', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK bigint unsigned autoincremental
            $table->bigIncrements('id');

            // Columnas según SQL
            $table->integer('log');
            $table->char('cedtra', 15);
            $table->integer('codben');
            $table->char('nombre', 90);
            $table->date('fecha');
            $table->char('codcer', 4);
            $table->string('nomcer', 45);
            $table->char('archivo', 100);
            $table->integer('usuario');
            $table->char('estado', 1);
            $table->char('motivo', 100)->nullable();
            $table->date('fecest')->nullable();
            $table->char('codest', 2)->nullable();
            $table->char('tipo', 2);
            $table->char('coddoc', 2);
            $table->char('documento', 15);

            // Índices
            $table->unique('id', 'id');
            $table->index('codest', 'fk_mercurio45_mercurio111_idx');
            $table->index(['tipo', 'coddoc', 'documento'], 'fk_mercurio45_mercurio071_idx');

            // FKs
            $table->foreign(['tipo', 'coddoc', 'documento'], 'fk_mercurio45_mercurio071')
                ->references(['tipo', 'coddoc', 'documento'])
                ->on('mercurio07')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('codest', 'fk_mercurio45_mercurio111')
                ->references('codest')
                ->on('mercurio11')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio45', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio45_mercurio071');
            $table->dropForeign('fk_mercurio45_mercurio111');
            $table->dropIndex('fk_mercurio45_mercurio071_idx');
            $table->dropIndex('fk_mercurio45_mercurio111_idx');
            $table->dropUnique('id');
        });
        Schema::dropIfExists('mercurio45');
    }
};
