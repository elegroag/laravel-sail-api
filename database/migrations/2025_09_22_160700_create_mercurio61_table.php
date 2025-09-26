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
        Schema::create('mercurio61', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas según SQL
            $table->unsignedInteger('numero');
            $table->integer('item');
            $table->string('tipo', 2);
            $table->string('documento', 15);
            $table->integer('cantidad');
            $table->integer('valor');

            // PK compuesta
            $table->primary(['numero', 'item']);

            // FK hacia mercurio60(id) con RESTRICT (NO ACTION)
            $table->foreign('numero')
                ->references('id')
                ->on('mercurio60')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio61', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio61_mercurio601');
            $table->dropIndex('fk_mercurio61_mercurio601');
        });
        Schema::dropIfExists('mercurio61');
    }
};
