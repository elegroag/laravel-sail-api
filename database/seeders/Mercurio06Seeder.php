<?php

namespace Database\Seeders;

use App\Models\Mercurio06;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio06Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM mercurio06');

        $fillable = (new Mercurio06())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio06::updateOrCreate(
                ['tipo' => $row['tipo']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
