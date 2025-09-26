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
        Schema::create('mercurio85', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas según SQL
            $table->unsignedInteger('id'); // PK sin autoincremento
            $table->integer('tipideacu');
            $table->char('numideacu', 15);
            $table->char('prinomacu', 30);
            $table->char('segnomacu', 30)->nullable();
            $table->char('priapeacu', 30);
            $table->char('segapeacu', 30)->nullable();
            $table->char('telacu', 30)->nullable();

            // PK
            $table->primary('id');

            // Índices
            $table->index('tipideacu');

            // FKs (RESTRICT)
            $table->foreign('tipideacu', 'mercurio85_ibfk_2')
                ->references('tipide')->on('xml4b004')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('id', 'mercurio85_ibfk_3')
                ->references('id')->on('mercurio83')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio85', function (Blueprint $table) {
            $table->dropForeign('mercurio85_ibfk_2');
            $table->dropIndex('mercurio85_ibfk_2_idx');
            $table->dropForeign('mercurio85_ibfk_3');
            $table->dropIndex('mercurio85_ibfk_3_idx');
        });
        Schema::dropIfExists('mercurio85');
    }
};
