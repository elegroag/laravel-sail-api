<?php

namespace Database\Seeders;

use App\Models\Gener18;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Gener18Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();
        $rows = $legacy->select('SELECT * FROM gener18');

        $fillable = (new Gener18())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Gener18::updateOrCreate(
                ['coddoc' => $row['coddoc']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
