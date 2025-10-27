<?php

namespace Database\Seeders;

use App\Models\Mercurio67;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio67Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio67');

        // Campos permitidos del modelo
        $fillable = (new Mercurio67())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio67::updateOrCreate(
                ['codcla' => $row['codcla']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
