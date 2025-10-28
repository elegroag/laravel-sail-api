<?php

namespace Database\Seeders;

use App\Models\Mercurio63;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio63Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM mercurio63');

        $fillable = (new Mercurio63())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio63::updateOrCreate(
                ['numero' => $row['numero']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
