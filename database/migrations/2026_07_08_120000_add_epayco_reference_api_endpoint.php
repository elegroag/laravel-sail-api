<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('api_endpoints')->updateOrInsert(
            [
                'service_name' => 'Epayco-Reference',
                'endpoint_name' => 'reference',
            ],
            [
                'connection_name' => 'api-epayco',
                'host_dev' => 'https://secure.epayco.co/validation/v1',
                'host_pro' => 'https://secure.epayco.co/validation/v1',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('api_endpoints')
            ->where('service_name', 'Epayco-Reference')
            ->where('endpoint_name', 'reference')
            ->delete();
    }
};
