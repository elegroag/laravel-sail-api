<?php

namespace Database\Seeders;

use App\Models\Mercurio32;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio32Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio32');

        // Campos permitidos del modelo
        $fillable = (new Mercurio32())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio32::updateOrCreate(
                ['cedtra' => $row['cedtra']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
