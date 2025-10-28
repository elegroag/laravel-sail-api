<?php

namespace Database\Seeders;

use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;
use App\Models\Mercurio69;

class Mercurio69Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio69');

        // Campos permitidos del modelo
        $fillable = (new Mercurio69())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio69::updateOrCreate(
                ['numero' => $row['numero']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
