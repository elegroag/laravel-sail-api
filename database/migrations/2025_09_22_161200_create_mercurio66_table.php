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
        Schema::create('mercurio66', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->increments('numero');

            // Columnas según SQL
            $table->unsignedInteger('codsed');
            $table->string('detalle', 100);
            $table->integer('valor');
            $table->date('fecsis');
            $table->string('hora', 8);
            $table->enum('estado', ['C', 'P']);
            $table->date('fecest')->nullable();
            $table->char('tipo', 2);
            $table->char('documento', 15);
            $table->char('coddoc', 2);

            // Índices según SQL
            $table->index('codsed', 'fk_mercurio66_mercurio651_idx');
            $table->index(['tipo', 'documento', 'coddoc'], 'fk_mercurio66_mercurio621_idx');

            // FKs
            $table->foreign('codsed', 'fk_mercurio66_mercurio651')
                ->references('codsed')
                ->on('mercurio65')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign(['tipo', 'documento', 'coddoc'], 'fk_mercurio66_mercurio621')
                ->references(['tipo', 'documento', 'coddoc'])
                ->on('mercurio62')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio66', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio66_mercurio651');
            $table->dropIndex('fk_mercurio66_mercurio651_idx');
            $table->dropForeign('fk_mercurio66_mercurio621');
            $table->dropIndex('fk_mercurio66_mercurio621_idx');
        });
        Schema::dropIfExists('mercurio66');
    }
};
