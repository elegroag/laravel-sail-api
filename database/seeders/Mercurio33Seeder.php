<?php

namespace Database\Seeders;

use App\Models\Mercurio33;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio33Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio33');

        // Campos permitidos del modelo
        $fillable = (new Mercurio33())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            // Clave compuesta
            Mercurio33::updateOrCreate(
                [
                    'tipo' => $row['tipo'],
                    'documento' => $row['documento'],
                    'campo' => $row['campo']
                ],
                $data
            );
        }

        $legacy->disconnect();
    }
}
