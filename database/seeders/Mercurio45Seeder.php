<?php

namespace Database\Seeders;

use App\Models\Mercurio45;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio45Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio45');

        // Campos permitidos del modelo
        $fillable = (new Mercurio45())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            // Clave compuesta por cédula, código beneficio y fecha
            Mercurio45::updateOrCreate(
                [
                   'id' => $row['id']
                ],
                $data
            );
        }

        $legacy->disconnect();
    }
}
