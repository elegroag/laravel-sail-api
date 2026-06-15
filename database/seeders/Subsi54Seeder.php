<?php

namespace Database\Seeders;

use App\Models\Subsi54;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Subsi54Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM subsi54 LIMIT 1000');

        $fillable = (new Subsi54())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Subsi54::updateOrCreate(
                ['id' => $row['id']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
