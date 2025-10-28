<?php

namespace Database\Seeders;

use App\Models\Gener21;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Gener21Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM gener21');

        // Campos permitidos del modelo
        $fillable = (new Gener21())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Gener21::updateOrCreate(
                ['tipfun' => $row['tipfun']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
