<?php

namespace Database\Seeders;

use App\Models\Mercurio51;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio51Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio51');

        // Campos permitidos del modelo
        $fillable = (new Mercurio51())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio51::updateOrCreate(
                ['codcat' => $row['codcat']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
