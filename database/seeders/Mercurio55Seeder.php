<?php

namespace Database\Seeders;

use App\Models\Mercurio55;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio55Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM mercurio55');

        $fillable = (new Mercurio55())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio55::updateOrCreate(
                ['codare' => $row['codare']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
