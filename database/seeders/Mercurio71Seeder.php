<?php

namespace Database\Seeders;

use App\Models\Mercurio71;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio71Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio71');

        // Campos permitidos del modelo
        $fillable = (new Mercurio71())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio71::updateOrCreate(
                ['numero' => $row['numero']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
