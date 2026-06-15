<?php

namespace Database\Seeders;

use App\Models\ServiciosCupos;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class ServiciosCuposSeeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM servicios_cupos LIMIT 1000');

        $fillable = (new ServiciosCupos())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            ServiciosCupos::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
