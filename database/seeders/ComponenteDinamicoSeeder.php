<?php

namespace Database\Seeders;

use App\Models\ComponenteDinamico;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class ComponenteDinamicoSeeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM componentes_dinamicos');

        $fillable = (new ComponenteDinamico())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            ComponenteDinamico::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
