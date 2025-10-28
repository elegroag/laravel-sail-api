<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\LegacyDatabaseService;
use App\Models\Mercurio03;

class Mercurio03Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio03');

        // Campos permitidos del modelo
        $fillable = (new Mercurio03())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio03::updateOrCreate(
                ['codfir' => $row['codfir']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
