<?php

namespace Database\Seeders;

use App\Models\Mercurio56;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio56Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio56');

        // Campos permitidos del modelo
        $fillable = (new Mercurio56())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio56::updateOrCreate(
                ['codinf' => $row['codinf']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
