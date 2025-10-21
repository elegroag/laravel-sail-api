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
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 100);
            $table->string('default_url', 255)->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('color', 50)->default('text-primary');
            $table->text('nota')->nullable();
            $table->unsignedInteger('parent_id')->nullable();
            $table->char('codapl', 2)->default('CA');
            $table->index('parent_id', 'parent_id');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex('parent_id');
        });

        Schema::dropIfExists('menu_items');
    }
};
