<?php

namespace Database\Seeders;

use App\Models\Mercurio36;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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

            if ($data['documento'] < 5) continue;
            if (!is_numeric($data['coddoc'])) {
                continue;
            }
            if (!is_numeric($data['documento'])) {
                continue;
            }

            $data['ruuid'] = (string) Str::orderedUuid();

            // Clave compuesta por tipo, documento y cédula
            Mercurio36::updateOrCreate(
                [
                    'id' => $row['id'],
                ],
                $data
            );
        }

        $legacy->disconnect();
    }
}
