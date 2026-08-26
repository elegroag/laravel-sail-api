<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aumenta razsoc de 100 a 150 en mercurio30 (solicitudes de empresa).
     */
    public function up(): void
    {
        Schema::table('mercurio30', function (Blueprint $table) {
            $table->string('razsoc', 150)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mercurio30', function (Blueprint $table) {
            $table->char('razsoc', 100)->change();
        });
    }
};
