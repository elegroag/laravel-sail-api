<?php

namespace Database\Seeders;

use App\Models\Mercurio72;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio72Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio72');

        // Campos permitidos del modelo
        $fillable = (new Mercurio72())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio72::updateOrCreate(
                ['numtur' => $row['numtur']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
