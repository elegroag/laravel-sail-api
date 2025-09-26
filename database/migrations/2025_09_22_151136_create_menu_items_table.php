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
        Schema::create('menu_items', function (Blueprint $table) {
            // Motor InnoDB como en el SQL
            $table->engine = 'InnoDB';

            // PK autoincremental
            $table->increments('id');

            // Columnas
            $table->string('title', 100);
            $table->string('default_url', 255)->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('color', 50)->default('text-primary');
            $table->text('nota')->nullable();
            $table->integer('position'); // NOT NULL
            $table->unsignedInteger('parent_id')->nullable();
            $table->tinyInteger('is_visible')->default(1); // tinyint(1) DEFAULT '1'
            $table->char('codapl', 2)->default('CA');
            $table->char('tipo', 1)->default('A');

            // Índice y FK auto-referenciada
            $table->index('parent_id', 'parent_id');
            $table->foreign('parent_id', 'menu_items_ibfk_1')
                ->references('id')
                ->on('menu_items')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropForeign('menu_items_ibfk_1');
            $table->dropIndex('parent_id');
        });

        Schema::dropIfExists('menu_items');
    }
};
