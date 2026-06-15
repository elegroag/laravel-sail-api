<?php

namespace Database\Seeders;

use App\Models\Xml4b064;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Xml4b064Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM xml4b064 LIMIT 1000');

        $fillable = (new Xml4b064())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Xml4b064::updateOrCreate(
                ['codare' => $row['codare']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
