<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $reference = DB::table('api_endpoints')
            ->where('connection_name', 'api-clisisu')
            ->where('service_name', 'CuotaMonetaria')
            ->first();

        if (! $reference) {
            return;
        }

        DB::table('api_endpoints')->updateOrInsert(
            [
                'service_name' => 'Movil',
                'endpoint_name' => 'movil',
            ],
            [
                'connection_name' => 'api-clisisu',
                'host_dev' => $reference->host_dev,
                'host_pro' => $reference->host_pro,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('api_endpoints')
            ->where('service_name', 'Movil')
            ->where('endpoint_name', 'movil')
            ->delete();
    }
};
