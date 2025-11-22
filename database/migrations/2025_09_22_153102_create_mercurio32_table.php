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
        Schema::create('mercurio32', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK bigint unsigned autoincremental
            $table->bigIncrements('id');

            // Columnas
            $table->integer('log');
            $table->char('cedtra', 15);
            $table->char('cedcon', 15);
            $table->char('tipdoc', 2);
            $table->char('priape', 20);
            $table->char('segape', 20)->nullable();
            $table->char('prinom', 20);
            $table->char('segnom', 20)->nullable();
            $table->date('fecnac');
            $table->char('ciunac', 5);
            $table->char('sexo', 1);
            $table->char('estciv', 2);
            $table->char('comper', 1);
            $table->integer('tiecon')->default(0); // int(3) DEFAULT '0'
            $table->char('ciures', 5)->nullable();
            $table->char('codzon', 9)->nullable();
            $table->char('tipviv', 2)->nullable();
            $table->char('direccion', 45);
            $table->string('barrio', 45)->nullable();
            $table->char('telefono', 13);
            $table->char('celular', 10)->nullable();
            $table->char('email', 45);
            $table->char('nivedu', 3)->nullable();
            $table->date('fecing');
            $table->char('codocu', 5)->nullable();
            $table->integer('salario');
            $table->char('tipsal', 1)->nullable();
            $table->char('captra', 1)->nullable();
            $table->integer('usuario');
            $table->char('estado', 1);
            $table->char('codest', 2)->nullable();
            $table->string('motivo', 500)->nullable();
            $table->date('fecest')->nullable();
            $table->char('tipo', 2);
            $table->char('coddoc', 2);
            $table->char('documento', 15);
            $table->char('tippag', 1)->default('T');
            $table->bigInteger('numcue')->default(0);
            $table->string('empresalab', 100)->nullable();
            $table->char('codban', 4)->nullable();
            $table->char('resguardo_id', 5)->nullable();
            $table->char('pub_indigena_id', 3)->nullable();
            $table->char('tipcue', 1)->nullable();
            $table->char('tipdis', 2)->nullable();
            $table->char('peretn', 2)->default('7');
            $table->char('zoneurbana', 1)->default('N')->comment('Zona urbana: S/N');

            $table->date('fecsol')->nullable()->comment('Fecha de solicitud');
            $table->date('fecapr')->nullable()->comment('Fecha de aprobación');
            $table->uuid('ruuid');

            // Índices
            $table->unique('id', 'id');
            $table->index('log', 'log');
            $table->index('codest', 'fk_mercurio32_mercurio111_idx');
            $table->index(['tipo', 'coddoc', 'documento'], 'fk_mercurio32_mercurio071_idx');
            $table->index('ruuid', 'fk_mercurio32_ruuid');
            $table->unique('ruuid', 'unique_mercurio32_ruuid');

            // FKs
            $table->foreign(['tipo', 'coddoc', 'documento'], 'fk_mercurio32_mercurio071')
                ->references(['tipo', 'coddoc', 'documento'])
                ->on('mercurio07')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('codest', 'fk_mercurio32_mercurio111')
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
        Schema::table('mercurio32', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio32_mercurio071');
            $table->dropForeign('fk_mercurio32_mercurio111');
            $table->dropIndex('fk_mercurio32_mercurio071_idx');
            $table->dropIndex('fk_mercurio32_mercurio111_idx');
            $table->dropIndex('log');
            $table->dropUnique('id');
            $table->dropIndex('fk_mercurio32_ruuid');
            $table->dropUnique('unique_mercurio32_ruuid');
        });
        Schema::dropIfExists('mercurio32');
    }
};
