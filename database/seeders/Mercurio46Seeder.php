<?php

namespace Database\Seeders;

use App\Models\Mercurio46;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio46Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        // Leer registros desde la base legada
        $rows = $legacy->select('SELECT * FROM mercurio46');

        // Usar fillable del modelo para construir los datos
        $fillable = (new Mercurio46())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio46::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
