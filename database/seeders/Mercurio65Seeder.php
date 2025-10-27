<?php

namespace Database\Seeders;

use App\Models\Mercurio65;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio65Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio65');

        // Campos permitidos del modelo
        $fillable = (new Mercurio65())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio65::updateOrCreate(
                ['codsed' => $row['codsed']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
