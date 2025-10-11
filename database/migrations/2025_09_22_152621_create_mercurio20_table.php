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
        Schema::create('mercurio20', function (Blueprint $table) {
            // Motor InnoDB como en el SQL
            $table->engine = 'InnoDB';

            // PK bigint(20) firmado AUTO_INCREMENT
            $table->bigInteger('log')->autoIncrement();

            // Columnas
            $table->char('tipo', 2)->nullable();
            $table->char('coddoc', 2)->nullable();
            $table->char('documento', 14)->nullable();
            $table->char('ip', 20);
            $table->date('fecha');
            $table->char('hora', 8);
            $table->char('accion', 100);
            $table->string('nota', 300)->nullable();

            // Índices
            $table->unique('log', 'id'); // UNIQUE KEY `id` (`log`)
            $table->index(['tipo', 'coddoc', 'documento'], 'fk_mercurio20_mercurio071_idx');

            // FK compuesta hacia mercurio07
            $table->foreign(['tipo', 'coddoc', 'documento'], 'fk_mercurio20_mercurio071')
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
        Schema::table('mercurio20', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio20_mercurio071');
            $table->dropIndex('fk_mercurio20_mercurio071_idx');
        });
        Schema::dropIfExists('mercurio20');
    }
};
