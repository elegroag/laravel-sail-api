<?php

namespace Database\Seeders;

use App\Models\Gener40;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Gener40Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM gener40');

        // Campos permitidos del modelo
        $fillable = (new Gener40())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Gener40::updateOrCreate(
                ['codigo' => $row['codigo']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
