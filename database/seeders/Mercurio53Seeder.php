<?php

namespace Database\Seeders;

use App\Models\Mercurio53;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio53Seeder extends Seeder
{

    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio53');

        // Campos permitidos del modelo
        $fillable = (new Mercurio53())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio53::updateOrCreate(
                ['numero' => $row['numero']],
                $data
            );
        }

        $legacy->disconnect();
    }

}
