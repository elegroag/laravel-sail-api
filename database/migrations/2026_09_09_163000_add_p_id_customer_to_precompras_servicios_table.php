<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * php artisan migrate --path=database/migrations/2026_09_09_163000_add_p_id_customer_to_precompras_servicios_table.php
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('precompras_servicios', function (Blueprint $table) {
            $table->string('p_id_customer', 80)
                ->nullable()
                ->after('valor')
                ->comment('P_CUST_ID_CLIENTE (EpaycoCuenta.p_id_customer)');
            $table->index('p_id_customer');
        });
    }

    public function down(): void
    {
        Schema::table('precompras_servicios', function (Blueprint $table) {
            $table->dropIndex(['p_id_customer']);
            $table->dropColumn('p_id_customer');
        });
    }
};
