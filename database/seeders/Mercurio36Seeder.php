<?php

namespace Database\Seeders;

use App\Models\Mercurio36;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio36Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio36');

        // Campos permitidos del modelo
        $fillable = (new Mercurio36())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            // Clave compuesta por tipo, documento y cédula
            Mercurio36::updateOrCreate(
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
