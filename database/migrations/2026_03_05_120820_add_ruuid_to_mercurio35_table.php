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
        Schema::table('mercurio35', function (Blueprint $table) {
            $table->string('ruuid', 20)->nullable()->after('documento');
            $table->index('ruuid', 'ruuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mercurio35', function (Blueprint $table) {
            $table->dropIndex('ruuid');
            $table->dropColumn('ruuid');
        });
    }
};
