<?php

namespace Database\Seeders;

use App\Models\Mercurio68;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio68Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio68');

        // Campos permitidos del modelo
        $fillable = (new Mercurio68())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio68::updateOrCreate(
                ['numero' => $row['numero']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
