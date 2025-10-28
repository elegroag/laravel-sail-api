<?php

namespace Database\Seeders;

use App\Models\Mercurio39;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio39Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio39');

        // Campos permitidos del modelo
        $fillable = (new Mercurio39())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio39::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
