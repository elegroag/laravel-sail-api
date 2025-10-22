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
        Schema::create('mercurio34', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK bigint unsigned autoincremental
            $table->bigIncrements('id');

            // Columnas según SQL
            $table->integer('log');
            $table->char('nit', 15)->nullable();
            $table->char('cedtra', 15);
            $table->char('cedcon', 15)->nullable();
            $table->char('numdoc', 15);
            $table->char('tipdoc', 2);
            $table->char('priape', 20);
            $table->char('segape', 20)->nullable();
            $table->char('prinom', 20);
            $table->char('segnom', 20)->nullable();
            $table->date('fecnac');
            $table->char('ciunac', 5);
            $table->char('sexo', 1);
            $table->char('parent', 13);
            $table->char('huerfano', 1);
            $table->char('tiphij', 1)->nullable();
            $table->char('nivedu', 3)->nullable();
            $table->char('captra', 1)->nullable();
            $table->char('tipdis', 2)->nullable();
            $table->char('calendario', 1)->nullable();
            $table->integer('usuario');
            $table->char('estado', 1);
            $table->char('codest', 2)->nullable();
            $table->string('motivo', 500)->nullable();
            $table->date('fecest')->nullable();
            $table->integer('codben')->nullable();
            $table->char('tipo', 2);
            $table->char('coddoc', 2);
            $table->char('documento', 15);
            $table->char('cedacu', 20)->nullable();
            $table->char('resguardo_id', 4)->nullable();
            $table->char('pub_indigena_id', 4)->nullable();
            $table->char('peretn', 2)->nullable();
            $table->char('codban', 4)->nullable();
            $table->unsignedBigInteger('numcue')->nullable();
            $table->char('tippag', 1)->nullable();
            $table->char('tipcue', 1)->nullable();
            $table->char('biocedu', 18)->nullable();
            $table->char('biotipdoc', 3)->nullable();
            $table->string('bioprinom', 34)->nullable();
            $table->string('biosegnom', 34)->nullable();
            $table->string('biopriape', 34)->nullable();
            $table->string('biosegape', 34)->nullable();
            $table->string('bioemail', 142)->nullable();
            $table->char('biophone', 12)->nullable();
            $table->char('biocodciu', 5)->nullable();
            $table->string('biodire', 142)->nullable();
            $table->char('biourbana', 1)->nullable();
            $table->char('biodesco', 1)->nullable();

            $table->date('fecsol')->nullable()->comment('Fecha de solicitud');
            $table->date('fecapr')->nullable()->comment('Fecha de aprobación');
            $table->uuid('ruuid');

            // Índices
            $table->unique('id', 'id');
            $table->index('log', 'log');
            $table->index('codest', 'fk_mercurio34_mercurio111_idx');
            $table->index(['tipo', 'coddoc', 'documento'], 'fk_mercurio34_mercurio071_idx');
            $table->index('ruuid', 'fk_mercurio34_ruuid');
            $table->unique('ruuid', 'unique_mercurio34_ruuid');

            // FKs
            $table->foreign(['tipo', 'coddoc', 'documento'], 'fk_mercurio34_mercurio071')
                ->references(['tipo', 'coddoc', 'documento'])
                ->on('mercurio07')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('codest', 'fk_mercurio34_mercurio111')
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
        Schema::table('mercurio34', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio34_mercurio071');
            $table->dropForeign('fk_mercurio34_mercurio111');
            $table->dropIndex('fk_mercurio34_mercurio071_idx');
            $table->dropIndex('fk_mercurio34_mercurio111_idx');
            $table->dropIndex('log');
            $table->dropUnique('id');
            $table->dropIndex('fk_mercurio34_ruuid');
            $table->dropUnique('unique_mercurio34_ruuid');
        });
        Schema::dropIfExists('mercurio34');
    }
};
