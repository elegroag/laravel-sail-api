<?php

namespace Database\Seeders;

use App\Models\Mercurio50;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio50Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio50');

        foreach ($rows as $row) {
            Mercurio50::updateOrCreate(
                ['codapl' => $row['codapl']],
                [
                    'webser' => $row['webser'] ?? null,
                    'path' => $row['path'] ?? null,
                    'urlonl' => $row['urlonl'] ?? null,
                    'puncom' => $row['puncom'] ?? null
                ]
            );
        }

        $legacy->disconnect();
    }
}
