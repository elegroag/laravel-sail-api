<?php

namespace Database\Seeders;

use App\Models\Gener09;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Gener09Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM gener09');

        // Campos permitidos del modelo
        $fillable = (new Gener09())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Gener09::updateOrCreate(
                ['codzon' => $row['codzon']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
