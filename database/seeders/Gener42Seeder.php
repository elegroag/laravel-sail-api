<?php

namespace Database\Seeders;

use App\Models\Gener42;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Gener42Seeder extends Seeder
{

    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM gener42');

        // Campos permitidos del modelo
        $fillable = (new Gener42())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Gener42::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
