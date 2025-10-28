<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\LegacyDatabaseService;
use App\Models\Mercurio20;

class Mercurio20Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio20');

        // Campos permitidos del modelo
        $fillable = (new Mercurio20())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio20::updateOrCreate(
                ['log' => $row['log']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
