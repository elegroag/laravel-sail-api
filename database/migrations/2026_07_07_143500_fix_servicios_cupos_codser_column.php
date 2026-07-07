<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios_cupos', function (Blueprint $table) {
            $table->string('codser', 20)->change();
            $table->string('estado', 1)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('servicios_cupos', function (Blueprint $table) {
            $table->char('codser', 1)->change();
            $table->integer('estado')->change();
        });
    }
};
