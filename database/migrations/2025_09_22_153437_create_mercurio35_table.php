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
        Schema::create('mercurio35', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK bigint unsigned autoincremental
            $table->bigIncrements('id');

            // Columnas según SQL
            $table->integer('log');
            $table->string('nit', 15);
            $table->char('tipdoc', 2);
            $table->char('cedtra', 15);
            $table->char('nomtra', 90);
            $table->string('codest', 4);
            $table->date('fecret');
            $table->char('nota', 250);
            $table->integer('usuario');
            $table->char('archivo', 100);
            $table->char('estado', 1);
            $table->date('fecest')->nullable();
            $table->char('motivo', 255)->nullable();
            $table->char('motrec', 2)->nullable();
            $table->char('tipo', 2);
            $table->char('coddoc', 2);
            $table->char('documento', 15);

            // Índices
            $table->unique('id', 'id');
            $table->index('log', 'log');
            $table->index('motrec', 'fk_mercurio35_mercurio111_idx');
            $table->index(['tipo', 'coddoc', 'documento'], 'fk_mercurio35_mercurio071_idx');

            // FKs
            $table->foreign(['tipo', 'coddoc', 'documento'], 'fk_mercurio35_mercurio071')
                ->references(['tipo', 'coddoc', 'documento'])
                ->on('mercurio07')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('motrec', 'fk_mercurio35_mercurio111')
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
        Schema::table('mercurio35', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio35_mercurio071');
            $table->dropForeign('fk_mercurio35_mercurio111');
            $table->dropIndex('fk_mercurio35_mercurio071_idx');
            $table->dropIndex('fk_mercurio35_mercurio111_idx');
            $table->dropIndex('log');
            $table->dropUnique('id');
        });
        Schema::dropIfExists('mercurio35');
    }
};
