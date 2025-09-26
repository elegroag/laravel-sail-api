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
        Schema::create('mercurio39', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK bigint unsigned autoincremental
            $table->bigIncrements('id');

            // Columnas
            $table->integer('log');
            $table->char('cedtra', 15);
            $table->char('tipdoc', 2);
            $table->char('priape', 20);
            $table->char('segape', 20)->nullable();
            $table->char('prinom', 20);
            $table->char('segnom', 20)->nullable();
            $table->date('fecnac');
            $table->char('ciunac', 5);
            $table->char('sexo', 1);
            $table->char('estciv', 2);
            $table->char('cabhog', 1);
            $table->char('codciu', 5);
            $table->char('codzon', 9);
            $table->char('direccion', 45);
            $table->char('barrio', 8)->nullable();
            $table->char('telefono', 13);
            $table->char('celular', 20)->nullable();
            $table->char('fax', 13)->nullable();
            $table->char('email', 45);
            $table->date('fecing');
            $table->integer('salario');
            $table->char('captra', 1)->nullable();
            $table->char('tipdis', 2)->nullable();
            $table->char('nivedu', 3)->nullable();
            $table->char('rural', 1);
            $table->char('vivienda', 4);
            $table->char('tipafi', 2);
            $table->char('autoriza', 1)->nullable();
            $table->char('codact', 6);
            $table->char('calemp', 1);
            $table->integer('usuario');
            $table->char('estado', 1);
            $table->char('codest', 2)->nullable();
            $table->char('motivo', 100)->nullable();
            $table->date('fecest')->nullable();
            $table->char('tipo', 2);
            $table->char('coddoc', 2);
            $table->char('documento', 15);

            // Índices
            $table->unique('id', 'id');
            $table->index('log', 'log');
            $table->index('codest', 'fk_mercurio31_mercurio111_idx');
            $table->index(['tipo', 'coddoc', 'documento'], 'fk_mercurio31_mercurio071_idx');

            // FKs (usando los nombres indicados en el SQL)
            $table->foreign(['tipo', 'coddoc', 'documento'], 'fk_mercurio31_mercurio071000')
                ->references(['tipo', 'coddoc', 'documento'])
                ->on('mercurio07')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('codest', 'fk_mercurio31_mercurio111000')
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
        Schema::table('mercurio39', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio31_mercurio071000');
            $table->dropForeign('fk_mercurio31_mercurio111000');
            $table->dropIndex('fk_mercurio31_mercurio071_idx');
            $table->dropIndex('fk_mercurio31_mercurio111_idx');
            $table->dropIndex('log');
            $table->dropUnique('id');
        });
        Schema::dropIfExists('mercurio39');
    }
};
