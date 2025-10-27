<?php

namespace Database\Seeders;

use App\Models\Mercurio34;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio34Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio34');

        // Campos permitidos del modelo
        $fillable = (new Mercurio34())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio34::updateOrCreate(
                ['numdoc' => $row['numdoc']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
