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
        Schema::create('menu_permissions', function (Blueprint $table) {
            // Motor InnoDB como en el SQL
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->increments('id');

            // Columnas
            $table->unsignedInteger('menu_item');
            $table->char('tipfun', 5);
            $table->tinyInteger('can_view')->default(1);
            $table->json('opciones')->nullable()->comment('{"mostrar": true, "editar":true,"guardar":true, "borrar": false}');

            // Índice y FK
            $table->index('menu_item', 'menu_item');
            $table->index('tipfun', 'tipfun');

            $table->foreign('menu_item', 'menu_permissions_ibfk_1')
                ->references('id')
                ->on('menu_items')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('tipfun', 'menu_permissions_ibfk_2')
                ->references('tipfun')
                ->on('gener21')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('menu_permissions', function (Blueprint $table) {
            $table->dropForeign('menu_permissions_ibfk_1');
            $table->dropIndex('menu_item');
            $table->dropForeign('menu_permissions_ibfk_2');
            $table->dropIndex('tipfun');
        });

        Schema::dropIfExists('menu_permissions');
    }
};
