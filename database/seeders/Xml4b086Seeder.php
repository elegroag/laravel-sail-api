<?php

namespace Database\Seeders;

use App\Models\Xml4b086;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Xml4b086Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM xml4b086 LIMIT 1000');

        $fillable = (new Xml4b086())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Xml4b086::updateOrCreate(
                ['codgru' => $row['codgru']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
