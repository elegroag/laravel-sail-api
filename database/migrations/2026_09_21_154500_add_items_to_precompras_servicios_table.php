<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('precompras_servicios')) {
            return;
        }

        Schema::table('precompras_servicios', function (Blueprint $table) {
            if (! Schema::hasColumn('precompras_servicios', 'items')) {
                $table->json('items')->nullable()->after('codben');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('precompras_servicios')) {
            return;
        }

        Schema::table('precompras_servicios', function (Blueprint $table) {
            if (Schema::hasColumn('precompras_servicios', 'items')) {
                $table->dropColumn('items');
            }
        });
    }
};
