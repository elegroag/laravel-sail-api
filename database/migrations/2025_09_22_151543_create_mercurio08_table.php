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
        Schema::create('mercurio08', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // Columnas
            $table->char('codofi', 2); // NOT NULL
            $table->char('tipopc', 2); // NOT NULL
            $table->integer('usuario'); // NOT NULL
            $table->integer('orden'); // NOT NULL

            // PK compuesta
            $table->primary(['codofi', 'tipopc', 'usuario']);

            // Índices
            $table->index('codofi', 'fk_mercurio08_mercurio041_idx');
            $table->index('tipopc', 'fk_mercurio08_mercurio091_idx');
            $table->index('usuario', 'fk_mercurio08_gener021_idx');

            // Claves foráneas
            $table->foreign('codofi', 'mercurio08_ibfk_1')
                ->references('codofi')
                ->on('mercurio04')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('usuario', 'mercurio08_ibfk_2')
                ->references('usuario')
                ->on('gener02')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('tipopc', 'mercurio08_ibfk_3')
                ->references('tipopc')
                ->on('mercurio09')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('mercurio08', function (Blueprint $table) {
            $table->dropForeign('mercurio08_ibfk_1');
            $table->dropIndex('fk_mercurio08_mercurio041_idx');
            $table->dropForeign('mercurio08_ibfk_2');
            $table->dropIndex('fk_mercurio08_gener021_idx');
            $table->dropForeign('mercurio08_ibfk_3');
            $table->dropIndex('fk_mercurio08_mercurio091_idx');
        });
        Schema::dropIfExists('mercurio08');
    }
};
