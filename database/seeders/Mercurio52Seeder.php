<?php

namespace Database\Seeders;

use App\Models\Mercurio52;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio52Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio52');

        // Campos permitidos del modelo
        $fillable = (new Mercurio52())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio52::updateOrCreate(
                ['codmen' => $row['codmen']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
