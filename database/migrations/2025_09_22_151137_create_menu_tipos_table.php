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
        Schema::create('menu_tipos', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('position');
            $table->unsignedInteger('menu_item')->nullable();
            $table->tinyInteger('is_visible')->default(1);
            $table->char('tipo', 1)->default('A');
            $table->index('menu_item', 'menu_item');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('menu_tipos', function (Blueprint $table) {
            $table->dropIndex('menu_item');
        });

        Schema::dropIfExists('menu_tipos');
    }
};
