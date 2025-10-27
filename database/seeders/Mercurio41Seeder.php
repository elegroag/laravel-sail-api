<?php

namespace Database\Seeders;

use App\Models\Mercurio41;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio41Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio41');

        // Campos permitidos del modelo
        $fillable = (new Mercurio41())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            // Clave compuesta
            Mercurio41::updateOrCreate(
                [
                    'tipo' => $row['tipo'],
                    'documento' => $row['documento'],
                    'cedtra' => $row['cedtra']
                ],
                $data
            );
        }

        $legacy->disconnect();
    }
}
