<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * php artisan migrate --path=database/migrations/2026_09_09_143000_create_epayco_cuentas_table.php 
 * php artisan migrate:rollback --path=database/migrations/2026_09_09_143000_create_epayco_cuentas_table.php
 * php artisan migrate --path=database/migrations/2026_09_09_143000_create_epayco_cuentas_table.php
 */
return new class extends Migration
{
    /**
     * Cuentas ePayco (múltiples comercios / llaves por ambiente).
     */
    public function up(): void
    {
        Schema::create('epayco_cuentas', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('account', 120)->comment('Nombre identificador de la cuenta');
            $table->string('env_mode', 30)->comment('development|production|...');
            $table->string('public_key', 255);
            $table->string('private_key', 255);
            $table->string('p_key', 255)->comment('P_KEY firma webhook');
            $table->string('p_id_customer', 80)->comment('P_CUST_ID_CLIENTE');
            $table->timestamps();

            $table->unique('account');
            $table->index('env_mode');
            $table->unique('p_id_customer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('epayco_cuentas');
    }
};
