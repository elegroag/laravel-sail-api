<?php

namespace Database\Seeders;

use App\Models\Mercurio35;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio35Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio35');

        // Campos permitidos del modelo
        $fillable = (new Mercurio35())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            // Clave compuesta por cédula y fecha retiro
            Mercurio35::updateOrCreate(
                [
                    'id' => $row['id']
                ],
                $data
            );
        }

        $legacy->disconnect();
    }
}
