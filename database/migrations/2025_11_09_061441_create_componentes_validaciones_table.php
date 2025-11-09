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
        Schema::create('componentes_validaciones', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->unsignedBigInteger('componente_id');
            $table->string('pattern', 255)->nullable();
            $table->text('default_value')->nullable();
            $table->integer('max_length')->nullable();
            $table->integer('min_length')->nullable();
            $table->string('numeric_range', 50)->nullable();
            $table->integer('field_size')->default(42);
            $table->text('detail_info')->nullable();
            $table->boolean('is_required')->default(false);
            $table->json('custom_rules')->nullable();
            $table->json('error_messages')->nullable();
            $table->timestamps();

            $table->foreign('componente_id')->references('id')->on('componentes_dinamicos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('componentes_validaciones');
    }
};
