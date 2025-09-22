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
            $table->unsignedInteger('menu_item_id'); // NOT NULL
            $table->integer('role_id'); // NOT NULL
            $table->tinyInteger('can_view')->default(1); // tinyint(1) DEFAULT '1'

            // Índice y FK
            $table->index('menu_item_id', 'menu_item_id');
            $table->foreign('menu_item_id', 'menu_permissions_ibfk_1')
                ->references('id')
                ->on('menu_items')
                ->onDelete('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('menu_permissions', function (Blueprint $table) {
            $table->dropForeign('menu_permissions_ibfk_1');
            $table->dropIndex('menu_item_id');
        });

        Schema::dropIfExists('menu_permissions');
    }
};
