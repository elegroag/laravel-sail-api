<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mercurio47', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->autoIncrement();

            $table->char('documento', 15);
            $table->char('tipo', 2);
            $table->char('coddoc', 2);
            $table->char('estado', 1);
            $table->date('fecest');
            $table->char('codest', 2);

            $table->char('tipact', 1);
            $table->integer('usuario')->nullable();
            $table->index('documento', 'documento');
            $table->index('tipo', 'tipo');
            $table->index('coddoc', 'coddoc');

            $table->date('fecsol')->nullable()->comment('Fecha de solicitud');
            $table->date('fecapr')->nullable()->comment('Fecha apobación resolucion');
            $table->uuid('ruuid');

            $table->index('ruuid', 'fk_mercurio47_ruuid');
            $table->unique('ruuid', 'unique_mercurio47_ruuid');

            $table->foreign(['tipo', 'coddoc', 'documento'], 'fk_mercurio47_mercurio071')
                ->references(['tipo', 'coddoc', 'documento'])
                ->on('mercurio07')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('codest', 'fk_mercurio47_mercurio111')
                ->references('codest')
                ->on('mercurio11')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('mercurio47', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio47_mercurio071');
            $table->dropForeign('fk_mercurio47_mercurio111');
            $table->dropIndex('fk_mercurio47_ruuid');
            $table->dropUnique('unique_mercurio47_ruuid');
        });
        Schema::dropIfExists('mercurio47');
    }
};
