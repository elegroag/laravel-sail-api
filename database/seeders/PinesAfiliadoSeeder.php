<?php

namespace Database\Seeders;

use App\Models\PinesAfiliado;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class PinesAfiliadoSeeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService;

        $rows = $legacy->select('SELECT * FROM pines_afiliado LIMIT 5000');

        Log::info('PinesAfiliadoSeeder - registros legados encontrados', ['count' => count($rows)]);

        $fillable = (new PinesAfiliado)->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            PinesAfiliado::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
