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
        Schema::create('mercurio30', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK bigint unsigned autoincremental
            $table->bigIncrements('id');

            // Columnas
            $table->integer('log');
            $table->char('nit', 15);
            $table->char('tipdoc', 2);
            $table->char('razsoc', 100);
            $table->char('priape', 40)->nullable();
            $table->char('segape', 40)->nullable();
            $table->char('prinom', 40)->nullable();
            $table->char('segnom', 40)->nullable();
            $table->char('sigla', 45)->nullable();
            $table->char('digver', 1)->nullable();
            $table->enum('tipper', ['N', 'J'])->nullable();
            $table->char('calemp', 1);
            $table->char('cedrep', 13);
            $table->string('repleg', 140);
            $table->char('direccion', 45);
            $table->char('codciu', 5);
            $table->char('codzon', 9)->nullable();
            $table->char('telefono', 13)->nullable();
            $table->char('celular', 10)->nullable();
            $table->char('fax', 13)->nullable();
            $table->char('email', 45);
            $table->char('codact', 6);
            $table->date('fecini')->nullable();
            $table->integer('tottra');
            $table->integer('valnom');
            $table->char('tipsoc', 3);
            $table->char('estado', 1);
            $table->char('codest', 2)->nullable();
            $table->string('motivo', 800)->nullable();
            $table->date('fecest')->nullable();
            $table->integer('usuario');
            $table->string('dirpri', 60)->nullable();
            $table->string('ciupri', 5)->nullable();
            $table->string('telpri', 10)->nullable();
            $table->string('celpri', 13)->nullable();
            $table->string('emailpri', 60)->nullable();
            $table->char('tipo', 2);
            $table->char('coddoc', 2);
            $table->char('documento', 15);
            $table->string('tipemp', 50)->nullable();
            $table->char('matmer', 12)->nullable();
            $table->char('coddocrepleg', 3)->nullable();
            $table->char('priaperepleg', 25)->nullable();
            $table->char('segaperepleg', 25)->nullable();
            $table->char('prinomrepleg', 25)->nullable();
            $table->char('segnomrepleg', 25)->nullable();
            $table->char('codcaj', 2)->nullable();
            $table->date('fecsol')->nullable();
            $table->date('fecapr')->nullable()->comment('Fecha de aprobación resolucion');

            // Índices
            $table->index('log', 'log');
            $table->index(['tipo', 'coddoc', 'documento'], 'fk_mercurio30_mercurio071_idx');
            $table->index('codest', 'fk_mercurio30_mercurio111_idx');

            // FKs
            $table->foreign(['tipo', 'coddoc', 'documento'], 'fk_mercurio30_mercurio071')
                ->references(['tipo', 'coddoc', 'documento'])
                ->on('mercurio07')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('codest', 'fk_mercurio30_mercurio111')
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
        Schema::table('mercurio30', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio30_mercurio071');
            $table->dropForeign('fk_mercurio30_mercurio111');
            $table->dropIndex('fk_mercurio30_mercurio071_idx');
            $table->dropIndex('fk_mercurio30_mercurio111_idx');
            $table->dropIndex('log');
        });
        Schema::dropIfExists('mercurio30');
    }
};
