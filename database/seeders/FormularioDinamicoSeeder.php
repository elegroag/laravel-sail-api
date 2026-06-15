<?php

namespace Database\Seeders;

use App\Models\FormularioDinamico;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class FormularioDinamicoSeeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM formularios_dinamicos');

        $fillable = (new FormularioDinamico())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            FormularioDinamico::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
