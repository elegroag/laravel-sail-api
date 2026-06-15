<?php

namespace Database\Seeders;

use App\Models\ComandoEstructuras;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class ComandoEstructuraSeeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM comando_estructuras');

        $fillable = (new ComandoEstructuras())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            ComandoEstructuras::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
