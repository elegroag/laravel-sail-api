<?php

namespace Database\Seeders;

use App\Models\Mercurio18;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio18Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM mercurio18');

        $fillable = (new Mercurio18())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio18::updateOrCreate(
                ['codigo' => $row['codigo']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
