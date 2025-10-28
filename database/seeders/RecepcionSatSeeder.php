<?php

namespace Database\Seeders;

use App\Models\RecepcionSat;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class RecepcionSatSeeder extends Seeder
{
   /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM recepcionsat');

        // Campos permitidos del modelo
        $fillable = (new RecepcionSat())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            RecepcionSat::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
