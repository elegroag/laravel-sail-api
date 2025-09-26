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
        Schema::create('mercurio83', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->increments('id');

            // Columnas principales
            $table->integer('tipben');
            $table->integer('tipideben');
            $table->char('numideben', 15);
            $table->char('prinomben', 30);
            $table->char('segnomben', 30)->nullable();
            $table->char('priapeben', 30);
            $table->char('segapeben', 30)->nullable();
            $table->integer('tipgenben')->nullable();
            $table->date('fecnacben')->nullable();
            $table->integer('codpaiben')->nullable();
            $table->char('coddep_nac', 2)->nullable();
            $table->char('ciunacben', 5);
            $table->date('fecafiben')->nullable();
            $table->char('coddep_res', 2)->nullable();
            $table->char('ciuresben', 5);
            $table->integer('codareresben');
            $table->char('direccionben', 100)->nullable();
            $table->integer('codgru');
            $table->integer('codpob');
            $table->integer('facvul');
            $table->integer('tipjor');
            $table->date('fecina')->nullable();
            $table->char('motivo', 200)->nullable();
            $table->integer('codres')->nullable();
            $table->integer('codpue')->nullable();
            $table->integer('responsable')->nullable();
            $table->integer('nivedu')->nullable();
            $table->integer('codgra')->nullable();

            // Índices
            $table->index('tipben');
            $table->index('tipideben');
            $table->index('tipgenben');
            $table->index('codpaiben');
            $table->index('codareresben');
            $table->index('tipjor');
            $table->index('codgru');
            $table->index('codpob');
            $table->index('facvul');

            // FKs (NO ACTION => RESTRICT)
            $table->foreign('tipben', 'mercurio83_ibfk_1')
                ->references('tipben')->on('xml4b081')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('facvul', 'mercurio83_ibfk_10')
                ->references('facvul')->on('xml4b094')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('tipideben', 'mercurio83_ibfk_2')
                ->references('tipide')->on('xml4b004')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('tipgenben', 'mercurio83_ibfk_3')
                ->references('tipgen')->on('xml4b005')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('codpaiben', 'mercurio83_ibfk_4')
                ->references('codpai')->on('xml4b091')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('codareresben', 'mercurio83_ibfk_5')
                ->references('codare')->on('xml4b064')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('tipjor', 'mercurio83_ibfk_6')
                ->references('tipjor')->on('xml4b070')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            // Nota: ibfk_7 es duplicada en SQL, se omite por redundancia
            $table->foreign('codgru', 'mercurio83_ibfk_8')
                ->references('codgru')->on('xml4b086')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('codpob', 'mercurio83_ibfk_9')
                ->references('codpob')->on('xml4b087')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio83', function (Blueprint $table) {
            $table->dropForeign('mercurio83_ibfk_1');
            $table->dropForeign('mercurio83_ibfk_2');
            $table->dropForeign('mercurio83_ibfk_3');
            $table->dropForeign('mercurio83_ibfk_4');
            $table->dropForeign('mercurio83_ibfk_5');
            $table->dropForeign('mercurio83_ibfk_6');
            $table->dropForeign('mercurio83_ibfk_8');
            $table->dropForeign('mercurio83_ibfk_9');
        });
        Schema::dropIfExists('mercurio83');
    }
};
