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
        Schema::create('mercurio14', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas
            $table->char('tipopc', 2); // NOT NULL
            $table->char('tipsoc', 2); // NOT NULL
            $table->integer('coddoc'); // NOT NULL
            $table->char('obliga', 1); // NOT NULL
            $table->tinyInteger('auto_generado')->default(0); // tinyint(4) DEFAULT '0'
            $table->string('nota', 255)->nullable();

            // PK compuesta
            $table->primary(['tipopc', 'tipsoc', 'coddoc']);

            // Índices
            $table->index('tipopc', 'fk_mercurio14_mercurio091_idx');
            $table->index('coddoc', 'fk_mercurio14_mercurio121_idx');

            // FKs
            $table->foreign('tipopc', 'fk_mercurio14_mercurio091')
                ->references('tipopc')
                ->on('mercurio09')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('coddoc', 'fk_mercurio14_mercurio121')
                ->references('coddoc')
                ->on('mercurio12')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio14', function (Blueprint $table) {
            $table->dropForeign('fk_mercurio14_mercurio091');
            $table->dropForeign('fk_mercurio14_mercurio121');
            $table->dropIndex('fk_mercurio14_mercurio091_idx');
            $table->dropIndex('fk_mercurio14_mercurio121_idx');
        });
        Schema::dropIfExists('mercurio14');
    }
};
