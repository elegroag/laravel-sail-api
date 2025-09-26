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
        Schema::create('mercurio31', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK bigint unsigned autoincremental
            $table->bigIncrements('id');

            // Columnas
            $table->integer('log');
            $table->char('nit', 15);
            $table->string('razsoc', 100);
            $table->char('cedtra', 15);
            $table->char('tipdoc', 2);
            $table->char('priape', 20);
            $table->char('segape', 20)->nullable();
            $table->char('prinom', 20);
            $table->char('segnom', 20)->nullable();
            $table->date('fecnac');
            $table->char('ciunac', 5);
            $table->char('sexo', 1);
            $table->char('orisex', 1)->nullable();
            $table->char('estciv', 2);
            $table->char('cabhog', 1);
            $table->char('codciu', 5)->nullable();
            $table->char('codzon', 9)->nullable();
            $table->string('direccion', 120)->nullable();
            $table->string('barrio', 48)->nullable();
            $table->char('telefono', 13)->nullable();
            $table->char('celular', 20)->nullable();
            $table->char('fax', 13)->nullable();
            $table->string('email', 100)->nullable();
            $table->date('fecsol');
            $table->date('fecing');
            $table->integer('salario');
            $table->char('tipsal', 1)->nullable();
            $table->char('captra', 1)->nullable();
            $table->char('tipdis', 2)->nullable();
            $table->char('nivedu', 3)->nullable();
            $table->char('rural', 1);
            $table->integer('horas');
            $table->char('tipcon', 1);
            $table->char('trasin', 1)->nullable();
            $table->char('vivienda', 4);
            $table->char('tipafi', 2);
            $table->string('profesion', 45)->nullable();
            $table->char('cargo', 9)->nullable();
            $table->char('autoriza', 1)->nullable();
            $table->integer('usuario');
            $table->char('estado', 1);
            $table->char('codest', 2)->nullable();
            $table->string('motivo', 500)->nullable();
            $table->date('fecest')->nullable();
            $table->char('tipo', 2);
            $table->char('coddoc', 2);
            $table->char('documento', 15);
            $table->char('facvul', 2)->nullable();
            $table->char('peretn', 2)->nullable();
            $table->string('dirlab', 150)->nullable();
            $table->char('ruralt', 1)->nullable();
            $table->char('comision', 1)->nullable();
            $table->char('tipjor', 1)->nullable();
            $table->char('codsuc', 3)->default('001');
            $table->char('fecha_giro', 10)->nullable();
            $table->char('tippag', 1)->default('T');
            $table->unsignedBigInteger('numcue')->default(0);
            $table->string('otra_empresa', 100)->nullable();
            $table->char('resguardo_id', 5)->nullable();
            $table->char('pub_indigena_id', 3)->nullable();
            $table->char('codban', 4)->nullable();
            $table->char('tipcue', 1)->nullable();
            $table->date('fecafi')->nullable()->comment('Fecha afiliación trabajador');
            $table->date('fecapr')->nullable()->comment('Fecha de aprobación');

            // Índices
            $table->unique('id', 'id');
            $table->index('log', 'log');
            $table->index('codest', 'fk_mercurio31_mercurio111_idx');
            $table->index(['tipo', 'coddoc', 'documento'], 'fk_mercurio31_mercurio071_idx');

            // FKs
            $table->foreign(['tipo', 'coddoc', 'documento'], 'fk_mercurio31_mercurio071')
                ->references(['tipo', 'coddoc', 'documento'])
                ->on('mercurio07')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('codest', 'fk_mercurio31_mercurio111')
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
        Schema::table('mercurio31', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio31_mercurio071');
            $table->dropForeign('fk_mercurio31_mercurio111');
            $table->dropIndex('fk_mercurio31_mercurio071_idx');
            $table->dropIndex('fk_mercurio31_mercurio111_idx');
            $table->dropIndex('log');
            $table->dropUnique('id');
        });
        Schema::dropIfExists('mercurio31');
    }
};
