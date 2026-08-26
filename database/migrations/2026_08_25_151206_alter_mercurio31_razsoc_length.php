<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aumenta razsoc de 100 a 150 en mercurio31 (solicitudes de trabajador).
     */
    public function up(): void
    {
        Schema::table('mercurio31', function (Blueprint $table) {
            $table->string('razsoc', 150)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mercurio31', function (Blueprint $table) {
            $table->string('razsoc', 100)->change();
        });
    }
};
