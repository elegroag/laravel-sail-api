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
        Schema::create('mercurio36', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK bigint(15) autoincremental (firmado)
            $table->bigInteger('id')->autoIncrement();

            // Columnas (tipos y longitudes según SQL)
            $table->char('tipo', 2);
            $table->char('coddoc', 2);
            $table->char('documento', 18);
            $table->char('calemp', 1);
            $table->integer('log')->nullable();
            $table->unsignedBigInteger('cedtra'); // bigint(18)
            $table->char('tipdoc', 2);
            $table->char('priape', 30)->nullable();
            $table->char('segape', 30)->nullable();
            $table->char('prinom', 30)->nullable();
            $table->char('segnom', 30)->nullable();
            $table->date('fecnac')->nullable();
            $table->char('ciunac', 6)->nullable();
            $table->char('sexo', 2)->nullable();
            $table->char('estciv', 2)->nullable();
            $table->char('cabhog', 2)->nullable();
            $table->char('codciu', 6)->nullable();
            $table->char('codzon', 6)->nullable();
            $table->string('direccion', 120)->nullable();
            $table->char('barrio', 45)->nullable();
            $table->char('telefono', 10)->nullable();
            $table->char('celular', 10)->nullable();
            $table->char('email', 120)->nullable();
            $table->date('fecini')->nullable();
            $table->char('salario', 10)->nullable();
            $table->char('captra', 1)->nullable();
            $table->char('tipdis', 2)->nullable();
            $table->char('nivedu', 2)->nullable();
            $table->char('rural', 1)->nullable();
            $table->char('vivienda', 1)->nullable();
            $table->char('tipafi', 2)->nullable();
            $table->char('autoriza', 1)->nullable();
            $table->string('motivo', 300)->nullable();
            $table->string('repleg', 140)->nullable();
            $table->char('codact', 6);
            $table->char('estado', 1);
            $table->char('codest', 2)->nullable();
            $table->date('fecest')->nullable();
            $table->integer('usuario')->nullable();
            $table->char('coddocrepleg', 3);
            $table->char('peretn', 2);
            $table->char('resguardo_id', 4);
            $table->char('pub_indigena_id', 4);
            $table->char('facvul', 2);
            $table->char('orisex', 3);
            $table->date('fecsol')->nullable();
            $table->char('tippag', 2);
            $table->char('numcue', 20)->nullable();
            $table->char('codcaj', 3);
            $table->char('cargo', 5);
            $table->char('codban', 3)->nullable();
            $table->char('tipcue', 3)->nullable();
            $table->date('fecapr')->nullable()->comment('Fecha de aprobación');

            // Índice y FK compuesta
            $table->index(['tipo', 'coddoc', 'documento'], 'mercurio36_FK');
            $table->foreign(['tipo', 'coddoc', 'documento'], 'mercurio36_FK')
                ->references(['tipo', 'coddoc', 'documento'])
                ->on('mercurio07')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio36', function (Blueprint $table) {
            $table->dropForeign('mercurio36_FK');
            $table->dropIndex('mercurio36_FK');
        });
        Schema::dropIfExists('mercurio36');
    }
};
