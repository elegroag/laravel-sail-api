<?php

namespace Database\Seeders;

use App\Models\Mercurio57;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio57Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio57 LIMIT 1000');

        // Campos permitidos del modelo
        $fillable = (new Mercurio57())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio57::updateOrCreate(
                ['numpro' => $row['numpro']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
