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
        Schema::create('componentes_dinamicos', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->string('name', 255);
            $table->enum('type', ['input', 'select', 'textarea', 'dialog', 'date', 'number'])->default('input');
            $table->string('label', 255);
            $table->string('placeholder', 255)->nullable();
            $table->string('form_type', 50)->default('input');
            $table->integer('group_id')->default(1);
            $table->integer('order')->default(1);
            $table->text('default_value')->nullable();
            $table->boolean('is_disabled')->default(false);
            $table->boolean('is_readonly')->default(false);
            $table->json('data_source')->nullable();
            $table->string('css_classes', 255)->nullable();
            $table->text('help_text')->nullable();
            $table->integer('target')->default(-1);
            $table->json('event_config')->nullable();
            $table->string('search_type', 50)->nullable();
            $table->date('date_max')->nullable();
            $table->decimal('number_min', 10, 2)->nullable();
            $table->decimal('number_max', 10, 2)->nullable();
            $table->decimal('number_step', 10, 2)->default(1);
            $table->unsignedBigInteger('formulario_id');
            $table->timestamps();

            $table->index('name');
            $table->index('formulario_id');
            $table->index(['group_id', 'order']);
            $table->foreign('formulario_id')->references('id')->on('formularios_dinamicos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('componentes_dinamicos');
    }
};
