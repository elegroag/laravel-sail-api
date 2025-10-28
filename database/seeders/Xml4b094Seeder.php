<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\LegacyDatabaseService;
use App\Models\Xml4b094;

class Xml4b094Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM xml4b094');

        // Campos permitidos del modelo
        $fillable = (new Xml4b094())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Xml4b094::updateOrCreate(
                ['facvul' => $row['facvul']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
