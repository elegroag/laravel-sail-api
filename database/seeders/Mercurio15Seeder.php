<?php

namespace Database\Seeders;

use App\Models\Mercurio15;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Mercurio15Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM mercurio15');

        $fillable = (new Mercurio15())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Mercurio15::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
