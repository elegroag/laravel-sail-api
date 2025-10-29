<?php

namespace Database\Seeders;

use App\Models\Mercurio73;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio73Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio73');

        // Campos permitidos del modelo
        $fillable = (new Mercurio73())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio73::updateOrCreate(
                ['numedu' => $row['numedu']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
