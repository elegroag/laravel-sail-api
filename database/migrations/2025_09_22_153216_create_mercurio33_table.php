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
        Schema::create('mercurio33', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK bigint unsigned autoincremental
            $table->bigIncrements('id');

            // Columnas
            $table->bigInteger('log');
            $table->char('tipo', 2);
            $table->char('coddoc', 2);
            $table->char('documento', 14);
            $table->char('campo', 20);
            $table->string('antval', 45);
            $table->char('valor', 100);
            $table->integer('usuario');
            $table->char('estado', 1);
            $table->char('motivo', 100)->nullable();
            $table->date('fecest')->nullable();
            $table->char('codest', 2)->nullable();
            $table->integer('actualizacion')->default(0); // int(15) DEFAULT '0'

            // Índices
            $table->unique('id', 'id');
            $table->index('campo', 'fk_mercurio33_mercurio281_idx');
            $table->index('log', 'fk_mercurio33_mercurio201_idx');
            $table->index(['tipo', 'coddoc', 'documento'], 'fk_mercurio33_mercurio071_idx');
            $table->index('codest', 'fk_mercurio33_mercurio111_idx');
            $table->index(['tipo', 'campo'], 'fk_mercurio33_mercurio281');

            // FKs
            $table->foreign(['tipo', 'coddoc', 'documento'], 'fk_mercurio33_mercurio071')
                ->references(['tipo', 'coddoc', 'documento'])
                ->on('mercurio07')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('codest', 'fk_mercurio33_mercurio111')
                ->references('codest')
                ->on('mercurio11')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('log', 'fk_mercurio33_mercurio201')
                ->references('log')
                ->on('mercurio20')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign(['tipo', 'campo'], 'fk_mercurio33_mercurio281')
                ->references(['tipo', 'campo'])
                ->on('mercurio28')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio33', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio33_mercurio071');
            $table->dropForeign('fk_mercurio33_mercurio111');
            $table->dropForeign('fk_mercurio33_mercurio201');
            $table->dropForeign('fk_mercurio33_mercurio281');
            $table->dropIndex('fk_mercurio33_mercurio071_idx');
            $table->dropIndex('fk_mercurio33_mercurio111_idx');
            $table->dropIndex('fk_mercurio33_mercurio201_idx');
            $table->dropIndex('fk_mercurio33_mercurio281_idx');
            $table->dropIndex('fk_mercurio33_mercurio281');
            $table->dropUnique('id');
        });
        Schema::dropIfExists('mercurio33');
    }
};
