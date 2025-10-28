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
            $table->integer('id')->autoIncrement();
            $table->primary('id');
            // Columnas según SQL
            $table->integer('numero');
            $table->integer('item');
            $table->string('tipo', 2);
            $table->string('documento', 15);
            $table->integer('cantidad');
            $table->integer('valor');
            
            $table->unique(['numero', 'item'], 'uk_mercurio61');
            $table->index(['numero', 'item'], 'idx_mercurio61');

            $table->foreign('numero', 'fk_mercurio61_mercurio60')
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
            $table->dropForeign('fk_mercurio61_mercurio60');
            $table->dropUnique('uk_mercurio61');
            $table->dropIndex('idx_mercurio61');
        });
        Schema::dropIfExists('mercurio61');
    }
};
