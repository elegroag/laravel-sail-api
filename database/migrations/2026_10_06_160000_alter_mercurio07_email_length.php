<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aumenta email de 60 a 100 en mercurio07 (usuarios), igual al maximo aceptado por Subsidio.
     */
    public function up(): void
    {
        Schema::table('mercurio07', function (Blueprint $table) {
            $table->char('email', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mercurio07', function (Blueprint $table) {
            $table->char('email', 60)->change();
        });
    }
};
