<?php

namespace Database\Seeders;

use App\Models\Xml4b087;
use App\Services\LegacyDatabaseService;
use Illuminate\Database\Seeder;

class Xml4b087Seeder extends Seeder
{
    public function run(): void
    {
        $legacy = new LegacyDatabaseService();

        $rows = $legacy->select('SELECT * FROM xml4b087 LIMIT 1000');

        $fillable = (new Xml4b087())->getFillable();

        foreach ($rows as $row) {
            $data = [];
            foreach ($fillable as $field) {
                $data[$field] = $row[$field] ?? null;
            }

            Xml4b087::updateOrCreate(
                ['codpob' => $row['codpob']],
                $data
            );
        }

        $legacy->disconnect();
    }
}
