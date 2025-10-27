<?php

namespace Database\Seeders;

use App\Models\Mercurio38;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio38Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio38');

        // Campos permitidos del modelo
        $fillable = (new Mercurio38())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio38::updateOrCreate(
                ['documento' => $row['documento']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
