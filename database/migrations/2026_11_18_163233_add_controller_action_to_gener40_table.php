<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gener40', function (Blueprint $table) {
            $table->string('controller', 150)->nullable()->after('detalle');
            $table->string('action', 150)->nullable()->after('controller');
        });
    }

    public function down(): void
    {
        Schema::table('gener40', function (Blueprint $table) {
            $table->dropColumn(['controller', 'action']);
        });
    }
};
