<?php

namespace Database\Seeders;

use App\Models\Xml4b004;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Xml4b004Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM xml4b004 LIMIT 1000');

        $fillable = (new Xml4b004())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Xml4b004::updateOrCreate(
                ['tipide' => $row['tipide']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
