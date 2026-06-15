<?php

namespace Database\Seeders;

use App\Models\Mercurio01;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio01Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM mercurio01');

        $fillable = (new Mercurio01())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio01::updateOrCreate(
                ['codapl' => $row['codapl']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
