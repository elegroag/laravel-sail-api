<?php

namespace Database\Seeders;

use App\Models\ApiEndpoint;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class ApiEndpointsSeeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM api_endpoints');

        $fillable = (new ApiEndpoint())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            ApiEndpoint::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
