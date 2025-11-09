<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('formularios_dinamicos', function (Blueprint $table) {
            // Motor InnoDB
            $table->engine = 'InnoDB';

            // PK
            $table->id();

            // Columnas
            $table->string('name', 255)->unique();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('module', 100);
            $table->string('endpoint', 255);
            $table->string('method', 10)->default('POST');
            $table->boolean('is_active')->default(true);
            $table->json('layout_config')->nullable();
            $table->json('permissions')->nullable();

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formularios_dinamicos');
    }
};
